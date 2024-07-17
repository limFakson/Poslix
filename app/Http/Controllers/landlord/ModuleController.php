<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\landlord\Module;
use App\Models\landlord\ModuleDescription;
use App\Models\Language;
use App\Traits\CacheForget;

class ModuleController extends Controller
{
    use CacheForget; 

    public function index()
    {
        $modules = DB::table('modules')->orderBy('order', 'asc')->get();
        $module_description = DB::table('module_descriptions')->where('lang_id', config('lang_id'))->first();
        $language_all = Language::get();
        return view('landlord.module', compact('modules', 'module_description', 'language_all'));
    }

    public function store(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        if(isset($request->name)) {
            foreach ($request->name as $key => $name) {
                $data = array(
                     'icon'=>$request->icon[$key],
                     'name'=>$request->name[$key],
                     'description'=>$request->description[$key],
                     'order'=> $key+1
                );
                Module::create($data);
            }
            $this->cacheForget('modules');
        }
        $module_data = ModuleDescription::where('lang_id', $request->language_id)->first();
        $image = $request->image;
        if ($image) {
            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = date("Ymdhis") . '.' . $ext;
            $image->move('public/landlord/images/', $imageName);
            $request->imageName = $imageName;
        }
        else
            $request->imageName = null;
        if($module_data)
            $module_data->update(['heading' => $request->heading, 'sub_heading' => $request->sub_heading, 'image' => $request->imageName]);
        else
            ModuleDescription::create(['heading' => $request->heading, 'sub_heading' => $request->sub_heading, 'image' => $request->imageName, 'lang_id' => $request->language_id]);
        $this->cacheForget('module_descriptions');
        return redirect()->back()->with('message', 'Data inserted successfully');
    }

    public function update(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $input = array(
            'icon'          => $request->icon,
            'name'          => $request->name,
            'description'   => $request->description,
            'module_id'     => $request->module_id
        );
        Module::find($input['module_id'])->update($input);
        $this->cacheForget('modules');
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function sort(Request $request)
    {
        $modules = Module::all();
        foreach ($modules as $module) {
            $module->timestamps = false; // To disable update_at field updation
            foreach ($request->order as $order) {
                if ($order['id'] == $module->id) {
                    $module->update(['order' => $order['position']]);
                }
            }
        }
        $this->cacheForget('modules');
        return 'Order changed successfully';
    }

    public function delete($id)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $module = Module::find($id);
        $module->delete();
        $this->cacheForget('modules');
        return redirect()->back()->with('message', 'Data deleted successfully');
    }
}
