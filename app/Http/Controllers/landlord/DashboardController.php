<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $tenants = DB::table('tenants')->get();
        $active_tenants = $tenants->where('expiry_date', '>=', date('Y-m-d'));
        $packages = DB::table('packages')->where('is_active', 1)->get();
        $package_count = count($packages);
        $received_amount = DB::table('payments')->sum('amount');

        $subscription_value = 0;
        foreach ($tenants as $tenant) {
            $data = json_decode($tenant->data);
            if(isset($data->package_id) && isset($data->subscription_type)) {
                $package_id = $data->package_id;
                $subscription_type = $data->subscription_type;
                $package = $packages->where('id',$package_id)->first();
                if($package) {
                    if($subscription_type == 'monthly') {
                        $subscription_value += $package->monthly_fee;
                    }
                    elseif($subscription_type == 'yearly') {
                        $subscription_value += $package->yearly_fee;
                    }
                }
            }
        }
        return view('landlord.dashboard',compact('tenants', 'active_tenants', 'package_count', 'subscription_value', 'received_amount'));
    }
}
