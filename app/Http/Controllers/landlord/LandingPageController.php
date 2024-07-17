<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\Tenant;
use DB;
use Cache;
use App\Models\GeneralSetting;
use App\Traits\TenantInfo;
use Stripe\Stripe;
use App\Models\landlord\Package;
use App\Models\landlord\TenantPayment;
use App\Models\landlord\Page;
use App\Models\MailSetting;
use App\Mail\ContactForm;
use Mail;
use ZipArchive;
use Nwidart\Modules\Facades\Module;

class LandingPageController extends Controller
{
    use TenantInfo;
    use \App\Traits\MailInfo;

    public function contactForm(Request $request)
    {
        $mail_data = $request->all();
        //return $mail_data;
        $mail_setting = MailSetting::latest()->first();
        if($mail_data['email'] && $mail_setting) {
            $this->setMailInfo($mail_setting);
            try {
                Mail::to($mail_data['email'])->send(new ContactForm($mail_data));
                $message = 'Mail sent successfully';
            }
            catch(\Exception $e){
                $message = 'Mail not sent';
            }
        }
        else
            $message = 'Please setup your mail setting';
        return redirect()->back()->with('message', $message);
    }

    public function addSubdomain()
    {
        $subdomain = 'alen';
        $url = "https://".env('CENTRAL_DOMAIN').":2083/json-api/delete_domain?api.version=1&domain=".$subdomain.".".env('CENTRAL_DOMAIN');
        //return $url;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //setting the curl headers
        $headers = array(
           "Authorization: cpanel ".env('CPANEL_USER_NAME').":".env('CPANEL_API_KEY'),
           "Content-Type: text/plain"
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        return dd($resp);
    }

    public function resetClientDB()
    {
        $allDomains = DB::table('domains')->pluck('domain');
        $length = count($allDomains);
        if($length) {
            return \Redirect::to('https://'.$allDomains[0].'/reset-db?key=0&length='.$length.'&allDomains='.json_encode($allDomains));
        }
    }
    
    public function index()
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

        $present_lang = DB::table('languages')->find($lang_id);
        \App::setLocale($present_lang->code);

        $general_setting =  Cache::remember('general_setting', 60*60*24*365, function () {
            return DB::table('general_settings')->latest()->first();
        });

        $packages =  Cache::remember('packages', 60*60*24*365, function () {
            return DB::table('packages')->where('is_active', true)->get();
        });

        $tenant_signup_description =  Cache::remember('tenant_signup_descriptions', 60*60*24*365, function () use ($lang_id) {
            return DB::table('tenant_signup_descriptions')->where('lang_id', $lang_id)->first();
        });
        if(!$tenant_signup_description)
            $tenant_signup_description = DB::table('tenant_signup_descriptions')->where('lang_id', 1)->first();

        $hero =  Cache::remember('hero', 60*60*24*365, function () use ($lang_id) {
            return DB::table('heroes')->where('lang_id', $lang_id)->first();
        });
        if(!$hero)
            $hero = DB::table('heroes')->where('lang_id', 1)->first();
     
        $faq_description =  Cache::remember('faq_descriptions', 60*60*24*365, function () use ($lang_id) {
            return DB::table('faq_descriptions')->where('lang_id', $lang_id)->first();
        });
        if(!$faq_description)
            $faq_description = DB::table('faq_descriptions')->where('lang_id', 1)->first();

        $faqs =  Cache::remember('faqs', 60*60*24*365, function () {
            return DB::table('faqs')->orderBy('order', 'asc')->get();
        });

        $module_description =  Cache::remember('module_descriptions', 60*60*24*365, function () use ($lang_id) {
            return DB::table('module_descriptions')->where('lang_id', $lang_id)->first();
        });
        if(!$module_description)
            $module_description = DB::table('module_descriptions')->where('lang_id', 1)->first();

        $modules =  Cache::remember('modules', 60*60*24*365, function () {
            return DB::table('modules')->orderBy('order', 'asc')->get();
        });

        $features =  Cache::remember('features', 60*60*24*365, function () {
            return DB::table('features')->orderBy('order', 'asc')->get();
        });

        $testimonials =  Cache::remember('testimonials', 60*60*24*365, function () {
            return DB::table('testimonials')->orderBy('order', 'asc')->get();
        });

        $socials =  Cache::remember('socials', 60*60*24*365, function () {
            return DB::table('socials')->orderBy('order', 'asc')->get();
        });

        $blogs =  Cache::remember('blogs', 60*60*24*30, function () {
            return DB::table('blogs')->latest()->take(3)->get();
        });

        $pages =  Cache::remember('pages', 60*60*24*30, function () {
            return DB::table('pages')->get();
        });

        $languages =  Cache::remember('languages', 60*60*24*30, function () {
            return DB::table('languages')->get();
        });

        if($general_setting->frontend_layout == 'regular') {
            return view('landlord.index', compact('general_setting', 'hero', 'packages', 'faq_description', 'faqs', 'modules', 'module_description', 'features', 'testimonials', 'socials','blogs', 'pages', 'languages', 'tenant_signup_description', 'present_lang'));
            
        }
        elseif($general_setting->frontend_layout == 'custom') {
            return view('landlord.custom_index', compact('general_setting', 'packages', 'tenant_signup_description'));
        }
    }

    public function signUp(Request $request)
    {
        $search = 'Terms';
        $terms_and_condition_page = Page::select('slug')->where('title', 'LIKE', "%{$search}%")->first();
        return view('landlord.signup', compact('request', 'terms_and_condition_page'));
    }
    
    public function updateTenantDB()
    {
        $tenants = Tenant::all();

        if ($tenants->isNotEmpty()) {
            \Artisan::call('tenants:migrate');
            // foreach ($tenants as $tenant) {
            //     $tenant->run(function () {
            //         \Artisan::call('migrate', ['--path' => 'database/migrations/tenant']);
            //         \Artisan::call('migrate', ['--path' => base_path('Modules/Woocommerce/Database/Migrations')]);
            //         \Artisan::call('migrate', ['--path' => base_path('Modules/Ecommerce/Database/Migrations')]);
            //     });
            // }

            return redirect()->back()->with('message', 'All tenant databases updated successfully!');
        } else {
            return redirect()->back()->with('message', 'No tenants found!');
        }
    }

    public function updateSuperadminDB()
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        \Artisan::call('migrate --path=/database/migrations');
        return redirect()->back()->with('message', 'SuperAdmin DB updated success!');
    }

    public function backupTenantDB()
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        $tenants = Tenant::select('id')->get();
        // Database configuration
        $host = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $zip = new ZipArchive();
        $zipFileName = 'db_backup_' . date("Ymdhis") . '.zip';
        $zip->open(public_path() . '/' . $zipFileName, ZipArchive::CREATE);
        foreach ($tenants as $key => $tenant) {
            $database_name = env('DB_PREFIX').$tenant->id;        
            // Get connection object and set the charset
            $conn = mysqli_connect($host, $username, $password, $database_name);
            $conn->set_charset("utf8");
            // Get All Table Names From the Database
            $tables = array();
            $sql = "SHOW TABLES";
            $result = mysqli_query($conn, $sql);
            $tables = [];
            while ($row = mysqli_fetch_row($result)) {
                $tables[] = $row[0];
            }
            $sqlScript = "";
            foreach ($tables as $table) {
                // Prepare SQLscript for creating table structure
                $query = "SHOW CREATE TABLE $table";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_row($result);
                
                $sqlScript .= "\n\n" . $row[1] . ";\n\n";
                
                $query = "SELECT * FROM $table";
                $result = mysqli_query($conn, $query);
                
                $columnCount = mysqli_num_fields($result);
                // Prepare SQLscript for dumping data for each table
                for ($i = 0; $i < $columnCount; $i ++) {
                    while ($row = mysqli_fetch_row($result)) {
                        $sqlScript .= "INSERT INTO $table VALUES(";
                        for ($j = 0; $j < $columnCount; $j ++) {
                            $row[$j] = $row[$j];
                            
                            if (isset($row[$j])) {
                                $sqlScript .= '"' . $row[$j] . '"';
                            } else {
                                $sqlScript .= '""';
                            }
                            if ($j < ($columnCount - 1)) {
                                $sqlScript .= ',';
                            }
                        }
                        $sqlScript .= ");\n";
                    }
                }
                
                $sqlScript .= "\n"; 
            }
            if(!empty($sqlScript))
            {
                // Save the SQL script to a backup file
                $sqlFileName = $database_name . '_backup_' . date("Ymdhis") . '.sql';
                $backup_file_path = public_path().'/dbBackup/' . $sqlFileName;
                $fileHandler = fopen($backup_file_path, 'w+');
                $number_of_lines = fwrite($fileHandler, $sqlScript);
                fclose($fileHandler);
                //file added to the zip
                $zip->addFile($backup_file_path, $sqlFileName);
            }
        }
        $zip->close();
        $files = glob(public_path().'/dbBackup/*'); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file)) {
            unlink($file); // delete file
          }
        }
        return redirect('public/' . $zipFileName);
    }

    public function migrateDB(Request $request)
    {
        $key = $request->key;
        $length = $request->length;
        $allDomains = json_decode($request->allDomains);
        \Artisan::call('migrate --path=/database/migrations/tenant');
        if($key < ($length-1)) {
            return \Redirect::to('https://'.$allDomains[$key+1].'/migrate?key='.($key+1).'&length='.$length.'&allDomains='.json_encode($allDomains));
        }
        else{
            return redirect()->route('clients.index')->with('message', 'All domain DB updated successfully');
        }
    }

    public function contactForRenewal(Request $request)
    {
        $subdomain = $request->id;
        $general_setting = DB::table('general_settings')->select('meta_title', 'meta_description', 'site_logo', 'phone', 'email', 'active_payment_gateway', 'stripe_public_key', 'developed_by')->latest()->first();
        $packages = Package::where('is_active', true)->get();
        $socials =  Cache::remember('socials', 60*60*24*365, function () {
            return DB::table('socials')->get();
        });
        return view('landlord.renewal', compact('subdomain', 'general_setting', 'packages', 'socials'));
    }

    public function renewSubscription(Request $request)
    {
        if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');
        //return $request;
        $tenant = Tenant::find($request->id);
        if($tenant) {
            if(cache()->has('general_setting'))
                $general_setting = cache()->get('general_setting'); 
            else
                $general_setting = DB::table('general_settings')->latest()->first();
            
            $packageData = Package::select('monthly_fee', 'yearly_fee', 'permission_id')->find($request->package_id);
            $permission_ids = explode(",", $packageData->permission_id);
            $prevPackageData = Package::select('permission_id')->find($tenant->package_id);
            $prev_permission_ids = explode(",", $prevPackageData->permission_id);
            $abandoned_permission_ids = [];
            //collecting permission ids which needs to be deleted
            foreach ($prev_permission_ids as $key => $perv_permission_id) {
                if(!in_array($perv_permission_id, $permission_ids)) {
                    $abandoned_permission_ids[] = $perv_permission_id;
                }
            }
            if($request->subscription_type == 'monthly') {
                $request->price = $packageData->monthly_fee;
                $request->numberOfDaysToExpired = 30;
            }
            elseif($request->subscription_type == 'yearly') {
                $request->price = $packageData->yearly_fee;
                $request->numberOfDaysToExpired = 365;
            }
            $allDomains[] = $tenant->id.'.'.env('CENTRAL_DOMAIN');
            $request->allDomains = json_encode($allDomains);
            $request->permission_ids = json_encode($permission_ids);
            $request->abandoned_permission_ids = json_encode($abandoned_permission_ids);
            $request->renewal = 1;
            $request->email = $tenant->email;
            $active_payment_gateway = explode(",", $general_setting->active_payment_gateway);
            $search = 'Terms';
            $terms_and_condition_page = Page::select('slug')->where('title', 'LIKE', "%{$search}%")->first();
            return view('payment.tenant_checkout', compact('request', 'active_payment_gateway', 'terms_and_condition_page'));
        }
        else
            return redirect()->back()->with('message', 'This subdomain does not exist!');
    }
}