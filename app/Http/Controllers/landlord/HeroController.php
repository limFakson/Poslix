<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\Hero;
use App\Models\Language;
use DB;
use App\Traits\CacheForget;

class HeroController extends Controller
{
    use CacheForget; 

    public function index()
    {
        $hero = DB::table('heroes')->where('lang_id', config('lang_id'))->first();
        $language_all = Language::get();
        return view('landlord.hero', compact('hero', 'language_all'));
    }

    public function store(Request $request)
    {
        //return $request;
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $this->validate($request, [
            'image' => 'image|mimes:jpg,jpeg,png,gif|max:100000',
        ]);
        $data = $request->except('site_logo');
        $hero = Hero::where('lang_id', $request->language_id)->first();
        if(!$hero) {
            $hero = new Hero;
        }
        $hero->heading = $data['heading'];
        $hero->sub_heading = $data['sub_heading'];
        $hero->button_text = $data['button_text'];
        $hero->lang_id = $data['language_id'];
        $image = $request->image;
        if ($image) {
            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = date("Ymdhis") . '.' . $ext;
            $image->move('public/landlord/images/', $imageName);
            $hero->image = $imageName;
        }
        $hero->save();
        $this->cacheForget('hero');        
        return redirect()->back()->with('message', 'Data updated successfully');
    }
}
