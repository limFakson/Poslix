<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Models\landlord\Tenant;
use Illuminate\Routing\Controller;
use Modules\Ecommerce\Entities\Appearance;
use Auth;
use Session;
use DB;

class AppearanceController extends Controller {
    /**
    * Display a listing of the resource.
    * @return Renderable
    */

    public function index() {
        return view( 'ecommerce::index' );
    }
    //Design

    public function design( Request $request ) {
        $appearance = Appearance::where('user_id', Auth::user()->id )->first();
        return view( 'ecommerce::backend.appearance.design', ['appearance'=> $appearance ] );
    }

    public function designpost( Request $request ) {
        $appearance = Appearance::updateOrCreate(['user_id' => Auth::user()->id], ['color' => $request->color ] );
        $request->validate( [
            'logoImage' => 'mimes:jpg,jpeg,png|max:2048',
        ] );
        if ( $request->hasFile( 'logoImage' ) ) {
            $file = $request->file( 'logoImage' );
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move( public_path( 'logo' ), $filename );
            $appearance->logo = $filename;
        }
        $appearance->save();
        return back()->with( 'message', 'Saved successfully' );
    }

    //Menu
    public function menu( Request $request ) {
        $appearance = Appearance::where('user_id', Auth::user()->id )->first();
        return view( 'ecommerce::backend.appearance.menu', ['appearance'=> $appearance ] );
    }

    public function menupost( Request $request ) {
        $appearance = Appearance::updateOrCreate(['user_id' => Auth::user()->id], ['menu_option' => $request->enable_horizontal == true ? 'horizontal' : 'vertical' ] );
        $appearance->save();
        return back()->with( 'message', 'Saved successfully' );
    }

    public function appearanceapi(Request $request){
        $userId = $request->input('user_id');
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 404);
        } else if(!$userId){
            return response()->json(["message"=> "User id is needed to be passed as (user_id)"], 404);
        };

        // Connected to the tenant database
        $tenancyDb = $tenant->tenancy_db_name;

        config(['database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => $tenancyDb,
            'username' => config('app.db_username'),
            'password' => config('app.db_password'),
        ]]);
        DB::purge('tenant');
        DB::reconnect('tenant');
        $userid = DB::connection('tenant')->table('users')->where('id', $userId)->first();
        if (!$userid){
            return response()->json(["message"=> "User not found"], 404);
        }

        $appearance = DB::connection('tenant')->table('appearances')->where('user_id', $userId )->first();
        return response([$appearance]);
    }

    /**
    * Show the form for creating a new resource.
    * @return Renderable
    */

    public function create() {
        return view( 'ecommerce::create' );
    }

    /**
    * Store a newly created resource in storage.
    * @param Request $request
    * @return Renderable
    */

    public function store( Request $request ) {
        //
    }

    /**
    * Show the specified resource.
    * @param int $id
    * @return Renderable
    */

    public function show( $id ) {
        return view( 'ecommerce::show' );
    }

    /**
    * Show the form for editing the specified resource.
    * @param int $id
    * @return Renderable
    */

    public function edit( $id ) {
        return view( 'ecommerce::edit' );
    }

    /**
    * Update the specified resource in storage.
    * @param Request $request
    * @param int $id
    * @return Renderable
    */

    public function update( Request $request, $id ) {
        //
    }

    /**
    * Remove the specified resource from storage.
    * @param int $id
    * @return Renderable
    */

    public function destroy( $id ) {
        //
    }
}