<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use App\Models\landlord\Blog;
use Cache;
use App\Traits\CacheForget;
use Intervention\Image\Facades\Image;

class BlogController extends Controller
{
    use CacheForget; 

    public function index()
    {
        $blogs = DB::table('blogs')->get();
        return view('landlord.blog', compact('blogs'));
    }

    public function list(Request $request)
    {
        if(isset($_COOKIE['landing_page_language'])) {
            $lang_id = $_COOKIE['landing_page_language'];
        } 
        else {
            $default_language = DB::table('languages')->where('is_default', true)->first();
            if($default_language)
                $lang_id = $default_language->id;
            else
                $lang_id = 1;
        }
        \App::setLocale( DB::table('languages')->find($lang_id)->code );
        
        if(cache()->has('general_settings')) {
            $general_setting = cache()->get('general_settings');
        } else {
            $general_setting =  Cache::remember('general_setting', 60*60*24*365, function () {
                return DB::table('general_settings')->latest()->first();
            });
        }
        if(cache()->has('socials')){
            $socials = cache()->get('socials');
        } else {
            $socials =  Cache::remember('socials', 60*60*24*365, function () {
                return DB::table('socials')->get();
            });
        }
        if(cache()->has('pages')){
            $pages = cache()->get('pages');
        }
        else {
            $pages =  Cache::remember('pages', 60*60*24*365, function () {
                return DB::table('pages')->get();
            });
        }
        $languages =  Cache::remember('languages', 60*60*24*30, function () {
            return DB::table('languages')->get();
        });
        
        $blogs = Blog::paginate(2);
        
        if ($request->ajax()) {
            $view = view('landlord.blog-list-load-more',compact('blogs'))->render();
            return response()->json(['html'=>$view]);  
        }
        return view('landlord.blog-list', compact('blogs','general_setting','socials','pages','languages'));
    }

    public function details($slug)
    {
        if(isset($_COOKIE['landing_page_language'])) {
            $lang_id = $_COOKIE['landing_page_language'];
        } 
        else {
            $default_language = DB::table('languages')->where('is_default', true)->first();
            if($default_language)
                $lang_id = $default_language->id;
            else
                $lang_id = 1;
        }
        \App::setLocale( DB::table('languages')->find($lang_id)->code );
        
        if(cache()->has('general_settings')){
            $general_setting = cache()->get('general_settings');
        } else {
            $general_setting =  Cache::remember('general_setting', 60*60*24*365, function () {
                return DB::table('general_settings')->latest()->first();
            });
        }
        if(cache()->has('socials')){
            $socials = cache()->get('socials');
        } else {
            $socials =  Cache::remember('socials', 60*60*24*365, function () {
                return DB::table('socials')->get();
            });
        }
        if(cache()->has('pages')){
            $pages = cache()->get('pages');
        }
        else {
            $pages =  Cache::remember('pages', 60*60*24*365, function () {
                return DB::table('pages')->get();
            });
        }
        $blog = DB::table('blogs')->where('slug',$slug)->first();
        $languages =  Cache::remember('languages', 60*60*24*30, function () {
            return DB::table('languages')->get();
        });
        return view('landlord.blog-details', compact('blog','general_setting','socials', 'pages', 'languages'));
    }

    public function store(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $this->validate($request, [
            'featured_image' => 'image|mimes:jpg,jpeg,png|max:100000',
        ]);

        $data = array(
            'title'=>$request->title,
            'slug'=>Str::slug($request->title, '-'),
            'description'=>$request->description,
            'meta_title'=>$request->meta_title,
            'meta_description'=>$request->meta_description,
            'og_title'=>$request->og_title,
            'og_description'=>$request->og_description,
        );

        if($request->featured_image){
            $image = $request->featured_image;
            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = date("Ymdhis") . '.' . strtolower($ext);
            $image->move('public/landlord/images/blog', $imageName);
            $img = Image::make('public/landlord/images/blog/'. $imageName);
            $img->fit(600, 400);
            $img->save();
            $data['featured_image'] = $imageName;
            
        }
        Blog::create($data);
        $this->cacheForget('blogs');
        return redirect()->back()->with('message', 'Data inserted successfully');
    }

    public function edit($id){
        $blog = Blog::find($id);
        
        return response()->json($blog);
    }

    public function update(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $input = $request->all();
        if($request->featured_image){
            $image = $request->featured_image;
            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = date("Ymdhis") . '.' . strtolower($ext);
            $image->move('public/landlord/images/blog', $imageName);
            $img = Image::make('public/landlord/images/blog/'. $imageName);
            $img->fit(600, 400);
            $img->save();
            $input['featured_image'] = $imageName;
            
        }
        Blog::find($input['blog_id'])->update($input);
        $this->cacheForget('blogs');
        return redirect()->back()->with('message', 'Data updated successfully');
    }

    public function sort(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $blogs = Blog::all();
        foreach ($blogs as $blog) {
            $blog->timestamps = false; // To disable update_at field updation
            foreach ($request->order as $order) {
                if ($order['id'] == $blog->id) {
                    $blog->update(['order' => $order['position']]);
                }
            }
        }
        $this->cacheForget('blogs');
        return 'Order changed successfully';
    }

    public function delete($id)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $blog = Blog::find($id);
        $blog->delete();
        $this->cacheForget('blogs');
        return redirect()->back()->with('message', 'Data deleted successfully');
    }
}
