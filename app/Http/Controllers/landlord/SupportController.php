<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\Tickets;
use DB;

class SupportController extends Controller
{

    public function index(Request $request)
    {
        $tickets = DB::table('tickets')->where('parent_ticket_id',NULL)->where('status',1)->orderBy('updated_at','DESC')->paginate(5);

        if ($request->ajax()) {
            $view = view('landlord/support-load-more',compact('tickets'))->render();
            return response()->json(['html'=>$view]);  
        }
        $tenants = DB::table('tenants')->select('id')->get();
        return view('landlord.support', compact('tickets','tenants'));
    }

    public function tenant(Request $request)
    {
        $tenant = $request->tenant;
        if($tenant == 'all'){
            $tickets = DB::table('tickets')->where('parent_ticket_id',NULL)->where('status',1)->orderBy('updated_at','DESC')->paginate(5);
        }else{
            $tickets = DB::table('tickets')->where('tenant_id',$tenant)->where('parent_ticket_id',NULL)->where('status',1)->orderBy('updated_at','DESC')->paginate(5);
        }

        if ($request->ajax()) {
            $view = view('landlord/support-load-more',compact('tickets'))->render();
            return response()->json(['html'=>$view]);  
        }
        $tenants = DB::table('tenants')->select('id')->get();
        return view('landlord.support', compact('tickets','tenants'));
    }

    public function ticket($id)
    {
        $parent = DB::table('tickets')->where('id',$id)->first();

        $children = DB::table('tickets')->where('parent_ticket_id',$id)->get();

        return view('landlord.ticket-details', compact('parent','children'));
    }

    public function store(Request $request)
    {
        $data = array(
            'subject'          => $request->subject,
            'description'      => $request->description,
            'tenant_id'        => $request->tenant_id,
            'superadmin'       => '1',
            'parent_ticket_id' => $request->parent_ticket_id
        );

        Tickets::create($data);

        return redirect()->back();
    }

}
