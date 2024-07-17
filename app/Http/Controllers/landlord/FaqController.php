<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\landlord\Faq;
use App\Models\landlord\FaqDescription;
use App\Models\Language;
use App\Traits\CacheForget;

class FaqController extends Controller
{
    use CacheForget; 

    public function index()
    {
        $faqs = DB::table('faqs')->orderBy('order', 'asc')->get();
        $faq_description = DB::table('faq_descriptions')->where('lang_id', config('lang_id'))->first();
        $language_all = Language::get();
        return view('landlord.faq', compact('faqs', 'faq_description', 'language_all'));
    }

    public function store(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        if(isset($request->question)) {
            foreach ($request->question as $key => $question) {
                $data = array(
                     'question'=>$request->question[$key],
                     'answer'=>$request->answer[$key],
                     'order'=> $key+1
                );
                Faq::create($data);
                $this->cacheForget('faqs');
            }
        }
        $faq_data = FaqDescription::where('lang_id', $request->language_id)->first();
        if($faq_data)
            $faq_data->update(['heading' => $request->heading, 'sub_heading' => $request->sub_heading]);
        else
            FaqDescription::create(['heading' => $request->heading, 'sub_heading' => $request->sub_heading, 'lang_id' => $request->language_id]);
        $this->cacheForget('faq_descriptions');
        return redirect()->back()->with('message', 'Data inserted successfully');
    }

    public function update(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $input = array(
            'question' => $request->question,
            'answer'   => $request->answer,
            'faq_id'   => $request->faq_id
        );
        faq::find($input['faq_id'])->update($input);
        $this->cacheForget('faqs');
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function sort(Request $request){
        $faqs = faq::all();
        foreach ($faqs as $faq) {
            $faq->timestamps = false; // To disable update_at field updation
            foreach ($request->order as $order) {
                if ($order['id'] == $faq->id) {
                    $faq->update(['order' => $order['position']]);
                }
            }
        }
        $this->cacheForget('faqs');
        return 'Order changed successfully';
    }

    public function delete($id){
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $faq = faq::find($id);
        $faq->delete();
        $this->cacheForget('faqs');
        return redirect()->back()->with('message', 'Data deleted successfully');
    }
}
