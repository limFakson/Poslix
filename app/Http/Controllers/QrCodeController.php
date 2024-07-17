<?php
// app/Http/Controllers/QrCodeController.php

// app/Http/Controllers/QrCodeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller {

    public function index() {
        $size = 200;
        // Example: retrieve from database or set dynamically
        $backgroundColor = [ 255, 255, 255 ];
        // Example: retrieve from database or set dynamically
        $foregroundColor = [ 0, 0, 0 ];
        // Example: retrieve from database or set dynamically
        $margin = 2;
        // Example: retrieve from database or set dynamically
        $style = 'square';

        return view( 'qrcode', compact('size','backgroundColor','foregroundColor','style', 'margin') );
    }

    public function generateQrCode( Request $request ) {
        // Validate the request
        $request->validate( [
            'background_color' => 'required|string',
            'foreground_color' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'data' => 'required|string',
        ] );

        // Parse colors
        list( $bgRed, $bgGreen, $bgBlue ) = sscanf( $request->background_color, '#%02x%02x%02x' );
        list( $fgRed, $fgGreen, $fgBlue ) = sscanf( $request->foreground_color, '#%02x%02x%02x' );

        // Handle image upload
        $logoPath = null;
        if ( $request->hasFile( 'image' ) ) {
            $image = $request->file( 'image' );
            $logoPath = $image->store( 'public/logos' );
            $logoPath = Storage::url( $logoPath );
            // Get the URL path
        }

        // Generate QR code
        $qrCode = QrCode::format( 'png' )
        ->size( 300 )
        ->margin( 10 )
        ->backgroundColor( $bgRed, $bgGreen, $bgBlue )
        ->color( $fgRed, $fgGreen, $fgBlue )
        ->generate( $request->data );

        // Add logo if exists
        if ( $logoPath ) {
            $qrCode = QrCode::format( 'png' )
            ->merge( $logoPath, .3, true )
            ->size( 300 )
            ->margin( 10 )
            ->backgroundColor( $bgRed, $bgGreen, $bgBlue )
            ->color( $fgRed, $fgGreen, $fgBlue )
            ->generate( $request->data );
        }
        $qrCodes = [];

        $qrCodes[ 'simple' ]        = QrCode::size( 150 )->generate( 'https://minhazulmin.github.io/' );
        $qrCodes[ 'changeColor' ]   = QrCode::size( 150 )->color( 255, 0, 0 )->generate( 'https://minhazulmin.github.io/' );
        $qrCodes[ 'changeBgColor' ] = QrCode::size( 150 )->backgroundColor( 255, 0, 0 )->generate( 'https://minhazulmin.github.io/' );
        $qrCodes[ 'styleDot' ]      = QrCode::size( 150 )->style( 'dot' )->generate( 'https://minhazulmin.github.io/' );
        $qrCodes[ 'styleSquare' ]   = QrCode::size( 150 )->style( 'square' )->generate( 'https://minhazulmin.github.io/' );
        $qrCodes[ 'styleRound' ]    = QrCode::size( 150 )->style( 'round' )->generate( 'https://minhazulmin.github.io/' );
        // Save the QR code to a file
        $qrCodePath = 'public/qrcodes/' . time() . '.png';
        Storage::put( $qrCodePath, $qrCode );
        $qr = '2';
        // Return the QR code URL
        return view( 'qrcode' )->with( 'qrCode', $qr );
    }
}

?>