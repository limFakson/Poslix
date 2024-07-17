<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\landlord\Features;
use App\Traits\CacheForget;

class FeaturesController extends Controller
{
    use CacheForget; 

    public function index()
    {
        $features = DB::table('features')->orderBy('order', 'asc')->get();
        return view('landlord.feature', compact('features'));
    }

    public function store(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        foreach ($request->name as $key => $name) {
            $data = array(
                 'icon'=>$request->icon[$key],
                 'name'=>$request->name[$key],
                 'order'=> $key+1
            );

            Features::create($data);
        }
        $this->cacheForget('features');
        return redirect()->back()->with('message', 'Data inserted successfully');
    }

    public function update(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $input = array(
            'icon'          => $request->icon,
            'name'          => $request->name,
            'feature_id'     => $request->feature_id
        );
        Features::find($input['feature_id'])->update($input);
        $this->cacheForget('features');
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function sort(Request $request)
    {
        $features = Features::all();
        foreach ($features as $feature) {
            $feature->timestamps = false; // To disable update_at field updation
            foreach ($request->order as $order) {
                if ($order['id'] == $feature->id) {
                    $feature->update(['order' => $order['position']]);
                }
            }
        }
        return 'Order changed successfully';
    }

    public function delete($id)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $feature = Features::find($id);
        $feature->delete();
        $this->cacheForget('features');
        return redirect()->back()->with('message', 'Data deleted successfully');
    }
}
