<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Ecommerce\Entities\Sliders;
use App\Models\Product;
use App\Models\Category;
use Modules\Ecommerce\Entities\Page;
use Modules\Ecommerce\Mail\ContactUs;
use App\Models\MailSetting;
use App\Models\ExtraCategory;
use App\Models\ProductExtraCategory;
use App\Models\Appearance;
use DB;
use Carbon\Carbon;
use Mail;
use Auth;
use Cache;
use Intervention\Image\Facades\Image;
use File;

class FrontController extends Controller
{
    use \App\Traits\MailInfo;

    
    public function index(Request $request)
    {
        $general_setting = DB::table('general_settings')->latest()->first();
        
        $categoryId = $request->input('category_id');
        if($categoryId && $categoryId !== 0) {
            $categories = DB::table('categories')->where('is_active', 1)->where('id', $categoryId)->get();
        } else {
            $categories = DB::table('categories')->where('is_active', 1)->get();
            $categoryId = 0;
        }
        
        $extras = DB::table('extras')->leftJoin('extra_categories', 'extras.extra_category_id', '=', 'extra_categories.id')->select('extras.*', 'extra_categories.category_name as extra_category_name')->get();

        $products = DB::table('products')->get();
        $products1 = DB::table('products')->leftJoin('product_extra_categories', 'products.id', '=', 'product_extra_categories.product_id')
             ->leftJoin('extra_categories', 'product_extra_categories.extra_category_id', '=', 'extra_categories.id')
             ->select('products.*', 'extra_categories.category_name as extra_category_name', 'extra_categories.is_multi as extra_category_is_multi')
             ->get();
             
        foreach ($products as $product) {
            $product->extra_category_names = array();
            foreach ($products1 as $product1) {
                if($product->id == $product1->id) {
                    if($product1->extra_category_name != null) {
                        array_push($product->extra_category_names, array($product1->extra_category_name, $product1->extra_category_is_multi));
                    }
                }
            } 
        }
        // dd($products);

        $sliders = DB::table('sliders')->orderBy('order', 'asc')->get();

        $color = '#fa9928';
        $logo = '1717418673.png';
        $menu_option = 'vertical';

        if(Auth::check()) {
            $user_id = Auth::user()->id;
            $appearance = Appearance::where('user_id', $user_id )->first();
            $color = $appearance -> color;
            $logo = $appearance -> logo;
            $menu_option = $appearance -> menu_option;
        }
        
        // $cart = session()->has('cart') ? session()->get('cart') : [];
		$total_qty = session()->has('total_qty') ? session()->get('total_qty') : 0;
		$subTotal = session()->has('subTotal') ? session()->get('subTotal') : 0;

		if($total_qty == 0){
			$subTotal = 0;
		}


        $ecommerce_setting = Cache::get('ecommerce_setting');

        if(isset($ecommerce_setting->home_page)) {
            $home = $ecommerce_setting->home_page;
        }
        
        $currentDayOfWeek = date('w');
        // Get the current time in the format HH:MM:SS
        // $currentTime = date('H:i:s');
        $currentTime = Carbon::now()->toTimeString();
        
        $dayColumns = [
            'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'
        ];
        $currentDayColumn = $dayColumns[$currentDayOfWeek];
        // Query the working hours for the current day from the database based on $currentDayOfWeek
        // For example, if using Eloquent ORM:
        // Check if working hours for the current day exist
        if(Auth::check()) {
        $workingHours = DB::table('workinghours')->where('user_id', Auth::user()->id)->first();} 
        else {
            $workingHours = false;
        }
        
        // Check if working hours for the current day exist
        $buttonEnabled = false;
        if ($workingHours) {
            // Determine if the current time falls within the first set of working hours
            $firstStartTime = Carbon::parse($workingHours->{$currentDayColumn . '_first_time_start'});
            $firstEndTime = Carbon::parse($workingHours->{$currentDayColumn . '_first_time_end'});
        
            if (Carbon::now()->between($firstStartTime, $firstEndTime)) {
                // Current time falls within the first set of working hours
                $buttonEnabled = true;
            } else {
                // Check if the second set of working hours is enabled and the current time falls within it
                $secondTimeEnabled = $workingHours->{$currentDayColumn . '_second_time_enable'};
                if ($secondTimeEnabled) {
                    $secondStartTime = Carbon::parse($workingHours->{$currentDayColumn . '_second_time_start'});
                    $secondEndTime = Carbon::parse($workingHours->{$currentDayColumn . '_second_time_end'});
        
                    if (Carbon::now()->between($secondStartTime, $secondEndTime)) {
                        // Current time falls within the second set of working hours and it is enabled
                        $buttonEnabled = true;
                    }
                }
            }
        }

        if(isset($home)){
            $page = DB::table('pages')->where('id',$home)->first();

            if(isset($page)){
                if($page->template == 'home'){
                    $widgets = DB::table('page_widgets')->where('page_id',$home)->orderBy('order','ASC')->get();
                }

                $recently_viewed = [];
                if(session()->has('recently_viewed')){
                    $recently_viewed = session()->get('recently_viewed');
                }

                return view('ecommerce::frontend/home', compact('sliders', 'categories', 'categoryId', 'widgets', 'recently_viewed','products', 'extras', 'color', 'logo','menu_option', 'general_setting', 'total_qty', 'subTotal', 'buttonEnabled'));
            }
        }

        return view('ecommerce::frontend/home', compact('sliders','categories', 'categoryId','products', 'extras', 'color', 'logo','menu_option', 'general_setting', 'total_qty', 'subTotal', 'buttonEnabled'));
    }
    
    public function main(Request $request)
    {
        $general_setting = DB::table('general_settings')->latest()->first();
        $ecommerce_setting = Cache::get('ecommerce_setting');

        if(isset($ecommerce_setting->home_page)) {
            $home = $ecommerce_setting->home_page;
        }
        
        $customer = DB::table('customers')->select('id','user_id','wishlist')->where('user_id', Auth::id())->first();
        if(isset($customer->wishlist)){
            $wishlist = $customer->wishlist;
            $wishlist_count = (count(explode(',',$customer->wishlist)) - 1);
        }else {
            $wishlist = 0;
            $wishlist_count = 0;
        }

        if(cache()->has('socials')){
            $socials = cache()->get('socials');
        } else {
            $socials =  Cache::remember('socials', 60*60*24*365, function () {
                return DB::table('social_links')->get();
            });
        }
        
        $color = '#fa9928';
        $logo = '1717418673.png';
        $menu_option = 'vertical';

        if(Auth::check()) {
            $user_id = Auth::user()->id;
            $appearance = Appearance::where('user_id', $user_id )->first();
            $color = $appearance -> color;
            $logo = $appearance -> logo;
        }
        
        $action_buttons = DB::table('action_buttons')->get();
        return view('ecommerce::frontend/main', compact('action_buttons','color','logo','wishlist', 'wishlist_count', 'socials', 'general_setting'));
    }

    public function page($slug)
    {
        $page = DB::table('pages')->where('slug', $slug)->where('status', 1)->first();

        if(!$page)
            $page = new Page();


        if(isset($page)){
            if($page->template == 'faq'){
                $categories = DB::table('faq_categories')->orderBy('order','ASC')->get();
                $faqs = DB::table('faqs')->orderBy('order','ASC')->get();
                return view('ecommerce::frontend.faq', compact('page','faqs','categories'));
            }

            if($page->template == 'contact'){
                return view('ecommerce::frontend.contact', compact('page'));
            }
        }

        return view('ecommerce::frontend.page-show', compact('page'));
    }

    public function blog()
    {
        $blogs = DB::table('blogs')->get();

        return view('ecommerce::frontend.blog', compact('blogs'));
    }

    public function blogPost($slug)
    {
        $post = DB::table('blogs')->where('slug', $slug)->first();

        return view('ecommerce::frontend.blog-details', compact('post'));
    }

    public function trackOrder($order_id='',$email='')
    {
        if(($order_id != '') && ($email != '')){
            $customer = DB::table('customers')->where('email',$email)->first();
            $sale = DB::table('sales')->where('customer_id',$customer->id)->where('reference_no',$order_id)->first();
            $delivery = DB::table('deliveries')->where('sale_id',$sale->id)->first();
            $product_sales = DB::table('product_sales')
                             ->join('products','product_sales.product_id','=','products.id')
                             ->select('products.name','products.image','products.is_variant','products.variant_option','product_sales.*')
                             ->where('sale_id',$sale->id)
                             ->get();
            if(!isset($delivery)){
                $delivery = 0;
            }
            return view('ecommerce::frontend.track-order', compact('sale','customer','delivery','product_sales'));
        } else {
            return view('ecommerce::frontend.track-order');
        }
    }

    public function search($product)
    {
        $search = $product;
        $data = DB::table('products')->select('id', 'image', 'name', 'slug')
            ->where('is_active', 1)
            ->where('is_online', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('tags', 'LIKE', '%' . $search . '%');
            })
            ->get();

        return response()->json($data);
    }

    public function searchProduct(Request $request)
    {
        $search = htmlspecialchars($request->input('search'));
        $products = DB::table('products')->where('is_active', 1)->where('is_online', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('tags', 'LIKE', '%' . $search . '%');
            })
            ->get();

        return view('ecommerce::frontend/products-search', compact('products', 'search'));
    }

    public function productDetails($product_name, $product_id)
    {
        $recently_viewed = session()->has('recently_viewed') ? session()->get('recently_viewed') : [];
        if(!array_key_exists($product_id, $recently_viewed)) {
            array_push($recently_viewed,$product_id);
            session(['recently_viewed' => $recently_viewed]);
    	}

        $product = DB::table('products')
                   ->where('id', $product_id)
                   ->where('is_active', 1)
                   ->where('is_online', 1)
                   ->first();

        if($product->variant_option) {
            $product->variant_option = json_decode($product->variant_option);
            $product->variant_value = json_decode($product->variant_value);
        }

        $brand = DB::table('brands')->where('id',$product->brand_id)->first();

        $categories = Cache::get('category_list');
        $category = $categories->where('id',$product->category_id)->first();

        $product_arr = explode(',',$product->related_products);
        $related_products = DB::table('products')->whereIn('id',$product_arr)->get();

 $lims_product_data =  DB::table('products')
                   ->where('id', $product_id)
                   ->where('is_active', 1)
                   ->where('is_online', 1)
                   ->first();

            $extraCategories = ExtraCategory::all();
           // $selectedCategories = $lims_product_data->extraCategories()->pluck('extra_category_id')->toArray();
      //  $extraCategories = $lims_product_data->extraCategories;
        $recently_viewed = [];
        if(session()->has('recently_viewed')){
            $recently_viewed = session()->get('recently_viewed');
        }

        return view('ecommerce::frontend/product-details', compact('extraCategories','product','brand','category','related_products','recently_viewed'));
    }

    public function allProducts()
    {
        $data = Product::select('id', 'image', 'name', 'price', 'promotion_price')->where('is_active', 1)->where('is_online', 1)->take(10)->get();

        return response()->json($data);
    }

    public function category($category, Request $request)
    {
        $category = DB::table('categories')
                    ->select('id','name','slug','page_title','short_description')
                    ->where('slug', $category)
                    ->where('is_active', 1)->first();

        $sub_categories = DB::table('categories')
                          ->select('id','name','slug','page_title','short_description')
                          ->where('parent_id', $category->id)
                          ->where('is_active', 1)->get();

        if (count($sub_categories) > 0) {

            $sub_cats = [];
            foreach($sub_categories as $cat){
                array_push($sub_cats, $cat->id);
            }

            $products = DB::table('products')
                        ->where('is_active', 1)
                        ->where('is_online', 1)
                        ->where(function($query) use ($category,$sub_cats){
                            $query->where('category_id', $category->id);
                            $query->orWhereIn('category_id',$sub_cats);
                        })
                        ->paginate(10);

            if ($request->ajax()) {
                $view = view('ecommerce::frontend/products-load-more', compact('products'))->render();
                return response()->json(['html' => $view]);
            }

            return view('ecommerce::frontend/products', compact('products', 'category'));
        } else {

            $products = DB::table('products')->where('category_id', $category->id)->where('is_active', 1)->where('is_online', 1)->paginate(10);

            if ($request->ajax()) {
                $view = view('ecommerce::frontend/products-load-more', compact('products'))->render();
                return response()->json(['html' => $view]);
            }

            return view('ecommerce::frontend/products', compact('products', 'category'));
        }
    }

    public function shop()
    {
        $categories = cache('category_list')->where('parent_id', Null);

        return view('ecommerce::frontend.shop', compact('categories'));
    }

    public function brandProducts($brand, Request $request)
    {
        $brand = DB::table('brands')->where('slug', $brand)->where('is_active', 1)->first();

        $products = DB::table('products')->where('brand_id', $brand->id)->where('is_active', 1)->where('is_online', 1)->paginate(5);

        if ($request->ajax()) {
            $view = view('ecommerce::frontend/brand-products-load-more', compact('products'))->render();
            return response()->json(['html' => $view]);
        }

        return view('ecommerce::frontend/brand-products', compact('products', 'brand'));
    }

    public function collectionProducts($collection, Request $request)
    {
        $collection = DB::table('collections')->where('slug', $collection)->where('status', 1)->first();

        $product_arr = explode(',',$collection->products);

        $products = DB::table('products')->whereIn('id', $product_arr)->where('is_active', 1)->where('is_online', 1)->get();

        return view('ecommerce::frontend/collection-products', compact('products', 'collection'));
    }

    public function contactMail(Request $request)
    {
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'message' => $request->input('message'),
        ];

        $email_to = Cache::get('ecommerce_setting')->contact_form_email;

        $mail_setting = MailSetting::latest()->first();
        $this->setMailInfo($mail_setting);
        Mail::to($email_to)->send(new ContactUs($data));

        return response()->json('success');
    }

    public function newsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'  => 'required|unique:newsletter,email',
        ]);

        if($validator->fails()) {
            $messages = $validator->messages();
            return $validator->errors();
        } else {

            $data = $request->except('_token');
            DB::table('newsletter')->insert($data);

            return response()->json('success');
        }
    }

    public function sessionRenew(Request $request)
    {
        return response()->json('success');
    }

}