<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\Tenant;
use App\Models\GeneralSetting;
use App\Models\landlord\Package;
use App\Traits\TenantInfo;
use DB;
use App\Mail\TenantCreate;
use App\Models\MailSetting;
use Mail;

class ClientController extends Controller
{
    use TenantInfo;
    use \App\Traits\CacheForget;
    use \App\Traits\MailInfo;

    public function index()
    {
        //$connection = DB::connection()->getConfig();

        //\Artisan::call('optimize:clear');
        //\Artisan::call('tenants:migrate');
        // Dump and die to inspect the connection details
        //dd($connection);
        if(cache()->has('general_setting')){
                $general_setting = cache()->get('general_setting'); 
        }
        else{
            $general_setting = DB::table('general_settings')->latest()->first();
        }
        $lims_client_all = Tenant::all();
        $lims_package_all = Package::where('is_active', true)->get();
        return view('landlord.client.index', compact('lims_client_all', 'lims_package_all', 'general_setting'));
    }

   public function store(Request $request)
    {
        
   $CENTRAL_DOMAIN = env('CENTRAL_DOMAIN', 'poslix.net');
  
        // echo $value;
        // exit;
        /*if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');*/
        //return $request;
        if(cache()->has('general_setting')) {
            $general_setting = cache()->get('general_setting'); 
        }
        else{
            $general_setting = DB::table('general_settings')->latest()->first();
        }
        $package = Package::select('is_free_trial', 'features')->find($request->package_id);
        $features = json_decode($package->features);
        $modules = [];
        if(array_search('woocommerce', $features))
            $modules[] = 'woocommerce';
        if(array_search('ecommerce', $features))
            $modules[] = 'ecommerce';
        if(count($modules))
            $modules = implode(",", $modules);
        else
            $modules = Null;

        if($request->subscription_type == 'free')
            $numberOfDaysToExpired = $general_setting->free_trial_limit;
        elseif($request->subscription_type == 'monthly')
            $numberOfDaysToExpired = 30;
        elseif($request->subscription_type == 'yearly')
            $numberOfDaysToExpired = 365;
   
        $tenant = Tenant::create(['id' => $request->tenant]);  
        
        //Log::debug($tenant);
        //$tenant  =  DB::table('tenants')->insert(['id' => $request->tenant]);
        $domain = $tenant->domains()->create(['domain' => $request->tenant.'.'.$CENTRAL_DOMAIN]);
        Log::debug($domain);
//         if($tenant){
//   DB::table('domains')->insert(['domain' => $request->tenant.'.'. $CENTRAL_DOMAIN,'tenant_id'=> $request->tenant]);
            
//         }

        //updating general setting info in the sql file which tenant will import
        $sqlFile = fopen("public/tenant_necessary_base.sql", "r");
        dd($sqlFile);
        $baseSqlData = fread($sqlFile,filesize("public/tenant_necessary_base.sql"));
        fclose($sqlFile);
        $newSqlDataForSetting = str_replace("(1, 'SalePro', '20220905125905.png', 0, '1', 0, 'monthly', 'own', 'd/m/Y', 'Lioncoders', 'standard', 1, 'default.css', Null, '2018-07-06 06:13:11', '2022-09-05 06:59:05', 'prefix', '1970-01-01');", "(1, '".$general_setting->site_title."', '".$general_setting->site_logo."', 0, '1', ".$request->package_id.", "."'".$request->subscription_type."', 'own', 'd/m/Y', '".$general_setting->developed_by."', 'standard', 1, 'default.css', '".$modules."', '2018-07-06 06:13:11', '2022-09-05 06:59:05', 'prefix', '".date("Y-m-d", strtotime("+".$numberOfDaysToExpired." days"))."');", $baseSqlData);
        $sqlFile = fopen("public/tenant_necessary.sql", "w");
        fwrite($sqlFile, $newSqlDataForSetting);
        fclose($sqlFile);
        
        //updating user information
        $encryptedPass = '$2y$10$DWAHTfjcvwCpOCXaJg11MOhsqns03uvlwiSUOQwkHL2YYrtrXPcL6';
        $newEncryptedPass = bcrypt($request->password);
        $newSqlDataForUser = str_replace("(1, 'admin', 'admin@gmail.com', '".$encryptedPass."', '6mN44MyRiQZfCi0QvFFIYAU9LXIUz9CdNIlrRS5Lg8wBoJmxVu8auzTP42ZW', '12112', 'lioncoders', 1, NULL, NULL, 1, 0, '2018-06-02 03:24:15', '2018-09-05 00:14:15')", "(1, '".$request->name."', '".$request->email."', '".$newEncryptedPass."', '6mN44MyRiQZfCi0QvFFIYAU9LXIUz9CdNIlrRS5Lg8wBoJmxVu8auzTP42ZW', ".$request->phone_number.",  '".$request->company_name."', 1, NULL, NULL, 1, 0, '2018-06-02 03:24:15', '2018-09-05 00:14:15')", $newSqlDataForSetting);
        $sqlFile = fopen("public/tenant_necessary.sql", "w");
        fwrite($sqlFile, $newSqlDataForUser);
        fclose($sqlFile);
        //updating permission info in the sql file which tenant will import
        $packageData = Package::find($request->package_id);
        $this->setPermission($packageData);
        //updating tenant others information on landlord DB
        $tenant->update(['package_id' => $request->package_id, 'subscription_type' => $request->subscription_type, 'company_name' => $request->company_name, 'phone_number' => $request->phone_number, 'email' => $request->email, 'expiry_date' => date("Y-m-d", strtotime("+".$numberOfDaysToExpired." days"))]);
        //sending welcome message to tenant
        $mail_setting = MailSetting::latest()->first();
        $message = 'Client created successfully';
        
        if($mail_setting) {
            $this->setMailInfo($mail_setting);
            $mail_data['email'] = $request->email;
            $mail_data['company_name'] = $request->company_name;
            $mail_data['superadmin_company_name'] = $general_setting->site_title;
            $mail_data['subdomain'] = $request->tenant;
            $mail_data['name'] = $request->name;
            $mail_data['password'] = $request->password;
            $mail_data['superadmin_email'] = $general_setting->email;
            try {
                Mail::to($mail_data['email'])->send(new TenantCreate($mail_data));
            }
            catch(\Exception $e){
                $message = 'Client created successfully. Please setup your <a href="../mail_setting">mail setting</a> to send mail.';
            }
        }
        return redirect()->back()->with('message', 'Client created successfully');
    }

    public function renew(Request $request)
    {
        /*if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');*/
        $tenant = Tenant::find($request->id);
        $tenant->update(['expiry_date' => date('Y-m-d', strtotime($request->expiry_date)), 'subscription_type' => $request->subscription_type]);
        foreach ($tenant->domains as $domain) {
            return \Redirect::to('https://'.$domain->domain.'/renew-subscription?expiry_date='.date('Y-m-d', strtotime($request->expiry_date)).'&subscription_type='.$request->subscription_type);
        }
    }

    public function changePackage(Request $request)
    {
        /*if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');*/
        $packageData = Package::select('permission_id', 'features')->find($request->package_id);
        $features = json_decode($packageData->features);
        $modules = [];
        if(in_array("ecommerce", $features))
            $modules[] = "ecommerce";
        if(in_array("woocommerce", $features))
            $modules[] = "woocommerce";
        if(count($modules))
            $modules = implode(",", $modules);
        else
            $modules = '';
        //return $modules;
        $permission_ids = explode(",", $packageData->permission_id);
        $tenant = Tenant::find($request->client_id);
        $packageData = Package::select('permission_id')->find($tenant->package_id);
        $prev_permission_ids = explode(",", $packageData->permission_id);
        $abandoned_permission_ids = [];
        //collecting permission ids which needs to be deleted
        foreach ($prev_permission_ids as $key => $perv_permission_id) {
            if(!in_array($perv_permission_id, $permission_ids)) {
                $abandoned_permission_ids[] = $perv_permission_id;
            }
        }
        //updating tenant package id in superadmin db
        $tenant->update(['package_id' => $request->package_id]);
        $allDomains[] = $tenant->id.'.'.env('CENTRAL_DOMAIN');
        return \Redirect::to('https://'.$allDomains[0].'/change-permission?key=0&length=1&package_id='.$request->package_id.'&allDomains='.json_encode($allDomains).'&abandoned_permission_ids='.json_encode($abandoned_permission_ids).'&permission_ids='.json_encode($permission_ids).'&modules='.$modules.'&routeName='.env('CENTRAL_DOMAIN').'/superadmin/clients');
    }

    public function changePermission(Request $request)
    {
        $key = $request->key;
        $length = $request->length;
        $allDomains = json_decode($request->allDomains);
        $abandoned_permission_ids = json_decode($request->abandoned_permission_ids);
        $permission_ids = json_decode($request->permission_ids);
        if(count($abandoned_permission_ids)) {
            DB::table('role_has_permissions')->whereIn('permission_id', $abandoned_permission_ids)->delete();
        }
        foreach ($permission_ids as $permission_id) {
            if(!(DB::table('role_has_permissions')->where([ ['role_id', 1], ['permission_id', $permission_id] ])->first())) {
                DB::table('role_has_permissions')->insert(['role_id' => 1, 'permission_id' => $permission_id]);
            }
        }
        //updating package id, expiry date and subscription type in general_settings table of tenant DB if necessary
        if(isset($request->package_id) && isset($request->expiry_date) && isset($request->subscription_type)) {
            GeneralSetting::latest()->first()->update(['package_id' => $request->package_id, 'expiry_date' => $request->expiry_date, 'subscription_type' => $request->subscription_type, 'modules' => $request->modules]);
        }
        elseif(isset($request->package_id)) {
            GeneralSetting::latest()->first()->update(['package_id' => $request->package_id, 'modules' => $request->modules]);
        }
        if($key < ($length-1)) {
            return \Redirect::to('https://'.$allDomains[$key+1].'/change-permission?key='.($key+1).'&length='.$length.'&allDomains='.json_encode($allDomains).'&abandoned_permission_ids='.json_encode($abandoned_permission_ids).'&permission_ids='.json_encode($permission_ids).'&routeName='.$request->routeName);
        }
        else {
            return redirect('https://'.$request->routeName)->with('message', 'Data updated successfully');
        }
    }

    public function renewSubscription(Request $request)
    {
        /*if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');*/
        //return $request;
        GeneralSetting::latest()->first()->update(['expiry_date' => $request->expiry_date, 'subscription_type' => $request->subscription_type]);
        if(isset($request->package_id)) {
            return \Redirect::to('https://'.$request->domain.'/switch-package?package_id='.$request->package_id.'&role_permission_values='.$request->role_permission_values.'&domain='.$request->domain);
        }
        else
            return redirect()->route('clients.index')->with('message', 'Subscription renewed successfully!');
    }

    public function resetDB(Request $request)
    {
        //clearing all the cached queries
        $this->cacheForget('biller_list');
        $this->cacheForget('brand_list');
        $this->cacheForget('category_list');
        $this->cacheForget('coupon_list');
        $this->cacheForget('customer_list');
        $this->cacheForget('customer_group_list');
        $this->cacheForget('product_list');
        $this->cacheForget('product_list_with_variant');
        $this->cacheForget('warehouse_list');
        $this->cacheForget('table_list');
        $this->cacheForget('tax_list');
        $this->cacheForget('currency');
        $this->cacheForget('general_setting');
        $this->cacheForget('pos_setting');
        $this->cacheForget('user_role');
        $this->cacheForget('permissions');
        $this->cacheForget('role_has_permissions');
        $this->cacheForget('role_has_permissions_list');
        //clearing all data from the DB
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $tables = DB::select('SHOW TABLES');
        $str = 'Tables_in_'.env('DB_PREFIX').$this->getTenantId();
        foreach ($tables as $table) {
            DB::table($table->$str)->truncate();    
        }
        //importing data from DB
        DB::unprepared(file_get_contents('public/'.env('DB_PREFIX').$this->getTenantId().'.sql'));
        $key = $request->key;
        $length = $request->length;
        $allDomains = json_decode($request->allDomains);
        if($key < ($length-1)) {
            return \Redirect::to('https://'.$allDomains[$key+1].'/reset-db?key='.($key+1).'&length='.$length.'&allDomains='.json_encode($allDomains));
        }
        return 'client DB cleared successfully';
    }

    public function destroy($id)
    {
        /*if(!env('USER_VERIFIED'))
            return redirect()->back()->with('not_permitted', 'This feature is disable for demo!');*/
        /*$host = 'localhost';
        $login = 'admin';
        $password = '8qFnd65vPdz^lKxm';

        $client = new \App\PleskApiClient($host);
        $client->setCredentials($login, $password);

        $request = <<<EOF
        <packet>
            <database>
                <add-db>
                   <webspace-id>1</webspace-id>
                   <name>$database</name>
                   <type>mysql</type>
                </add-db>
            </database>
        </packet>
        EOF;

        $response = $client->request($request);
        echo $response;*/
        $tenant = Tenant::find($id);
        foreach ($tenant->domains as $key => $domain) {
            $domain->delete();
        }
        $tenant->delete();
        return redirect()->back()->with('message', 'Client deleted successfully');
    }
}
