<?php

namespace Modules\Ecommerce\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use Session;
use DB;

class QrCodeController extends Controller {
    /**
    * Display a listing of the resource.
    * @return Renderable
    */

    public function index() {
        return view( 'ecommerce::index' );
    }

    public function showForm( Request $request ) {
        $auth_user_warehouse_id = Auth::user()->warehouse_id;
        $size = $request->size? $request->size :250;
        // Example: retrieve from database or set dynamically
        $backgroundColor = $request->backgroundColor?$this->hexToRgb( $request->input( 'backgroundColor' ) ):[ 255, 255, 255 ];
        // Example: retrieve from database or set dynamically
        $foregroundColor = $request->foregroundColor? $this->hexToRgb( $request->input( 'foregroundColor' ) ): [ 0, 0, 0 ];
        // Example: retrieve from database or set dynamically
        $margin = 2;
        // Example: retrieve from database or set dynamically
        $style = 'square';

        $tables = DB::table( 'tables' )->get();
        $warehouse = Warehouse::get();
        if(isset($auth_user_warehouse_id)){
            $user_warehouse = Warehouse::where('id', $auth_user_warehouse_id)->first();
            $user_warehouse = $user_warehouse->id;
        }else{
            $user_warehouse = 0;
        }
        // $qrCode = QrCode::size( $size )
        // ->backgroundColor( $backgroundColor[ 0 ], $backgroundColor[ 1 ], $backgroundColor[ 2 ] )
        // ->color( $foregroundColor[ 0 ], $foregroundColor[ 1 ], $foregroundColor[ 2 ] )
        // ->margin( $margin )
        // ->style( $style )
        // ->generate( 'https://minhazulmin.github.io/' );
        // return response( $qrCode );

        return view( 'ecommerce::backend.qrcode.generate-qr', compact( 'size', 'backgroundColor', 'foregroundColor', 'margin', 'style', 'tables', 'warehouse', 'user_warehouse' ) );
    }

    public function generate( Request $request ) {
        $data = $request->all();
        $size = $request->size? $request->size*250 :250;
        // Example: retrieve from database or set dynamically
        $backgroundColor = $request->backgroundColor?$this->hexToRgb( $request->input( 'backgroundColor' ) ):[ 255, 255, 255 ];
        // Example: retrieve from database or set dynamically
        $foregroundColor = $request->foregroundColor? $this->hexToRgb( $request->input( 'foregroundColor' ) ): [ 0, 0, 0 ];
        // Example: retrieve from database or set dynamically
        $margin = $request->padding ? $request->padding : 2;
        // Example: retrieve from database or set dynamically
        $style = $request->shape ? 'dot' : 'square';
        $uploadImage = $request->file( 'uploadImage' );
        if($request->data) {
            $table = DB::table('tables')->where('id', $request->table )->first();
        }
        $warehouse_id = $request->warehouse;

        $data = config('app.url').'/main/?warehouse_id='.$warehouse_id;
        // if(){}
        // dd( $request->uploadImage->extension() );
        if ( $request->hasFile( 'uploadImage' ) ) {
            // $logoPath = $request->file( 'uploadImage' )->store( 'qrlogos', 'public' );
            // $logoFullPath = storage_path( 'app/public/' . $logoPath );
            $filename = time() . '.' . $request->uploadImage->extension();
            $uploadPath = public_path( '/images/qrlogos' );
            $request->uploadImage->move( $uploadPath, $filename );

            // Get the full path of the uploaded logo image
            $logoFullPath = $uploadPath . '/' . $filename;

            // Generate the QR code with the logo
            $qrCode = QrCode::size( $size )
            ->backgroundColor( $backgroundColor[ 0 ], $backgroundColor[ 1 ], $backgroundColor[ 2 ] )
            ->color( $foregroundColor[ 0 ], $foregroundColor[ 1 ], $foregroundColor[ 2 ] )
            ->margin( $margin )
            ->style( $style == 'dot' ? 'dot' : 'square' )
            ->merge( $logoFullPath, 0.3, true ) // The second parameter is the size percentage of the logo
            ->generate( $data );
        } else {
            $qrCode = QrCode::size( $size )
            ->backgroundColor( $backgroundColor[ 0 ], $backgroundColor[ 1 ], $backgroundColor[ 2 ] )
            ->color( $foregroundColor[ 0 ], $foregroundColor[ 1 ], $foregroundColor[ 2 ] )
            ->margin( $margin )
            ->style( $style )
            ->generate( $data );
        }

        $qrCodeResponse = $request->hasFile( 'uploadImage' ) ?  $qrCode : $qrCode;
        return response( $qrCodeResponse )->header( 'Content-Type', 'image/png' );

        // return response( $qrCode );
    }

    private function hexToRgb( $hex ) {
        $hex = str_replace( '#', '', $hex );
        if ( strlen( $hex ) == 3 ) {
            $r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
            $g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
            $b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
        } else {
            $r = hexdec( substr( $hex, 0, 2 ) );
            $g = hexdec( substr( $hex, 2, 2 ) );
            $b = hexdec( substr( $hex, 4, 2 ) );
        }
        return [ $r, $g, $b ];
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