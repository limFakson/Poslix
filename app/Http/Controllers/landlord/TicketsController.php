<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\Tickets;
use DB;

class TicketsController extends Controller
{
    public function index(Request $request)
    {
        //$referer = request()->headers->get('referer');

        $referer = 'acme.saleprosaas.test';

        $tenant = explode('.', $referer)[0];

        $tickets = DB::table('tickets')->where('tenant_id',$tenant)->where('parent_ticket_id',NULL)->where('status',1)->orderBy('updated_at','DESC')->paginate(5);
        if ($request->ajax()) {
            $view = view('backend/tickets/index-load-more',compact('tickets'))->render();
            return response()->json(['html'=>$view]);  
        }
        return view('backend.tickets.index', compact('tickets','tenant','referer'));
    }

    public function ticket($id)
    {
        $parent = DB::table('tickets')->where('id',$id)->first();

        $children = DB::table('tickets')->where('parent_ticket_id',$id)->get();

        return view('backend.tickets.ticket-details', compact('parent','children'));
    }

    public function store(Request $request)
    {
        $data = array(
            'subject'     => $request->subject,
            'description' => $request->description,
            'tenant_id'   => $request->tenant,
        );

        Tickets::create($data);

        return redirect()->back();
    }

    public function update(Request $request)
    {
        $data = array(
            'subject'          => $request->subject,
            'description'      => $request->description,
            'tenant_id'        => $request->tenant_id,
            'parent_ticket_id' => $request->parent_ticket_id
        );

        Tickets::create($data);

        return redirect()->back();
    }
}
