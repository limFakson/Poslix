<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\app;
use App\Events\Sale;

class UploadController extends Controller {
    //

    public function index() {
        return view( 'asset.file' );
    }

    public function upload( Request $request ) {
        $data = new App();
        $file = $request->file;
        $version = $request->version;
        $build = $request->build;
        $signature = $request->sign;

        $filename = pathinfo( $file->getClientOriginalName(), PATHINFO_FILENAME ).'_'.time().'.'.$file->getClientOriginalExtension();
        $request->file->move( 'public/assets', $filename );

        $count = App::count();

        //To delete the oldest app saved
        if ( $count >= 5 ) {
            $oldestRecord = App::orderBy( 'created_at', 'asc' )->first();

            $this->deleteOldestFile( $oldestRecord->file );
            $oldestRecord->delete();
        }

        $data->file = $filename;
        $data->version = $version;
        $data->build = $build;
        $data->signature = $signature;

        $data->save();

        $downloadLink = $this->generateDownloadLink( $filename );
        return redirect()->back()->with( 'link', $downloadLink );
    }

    public function generateDownloadLink( $filename ) {
        return route( 'download', [ 'file' => $filename ] );
    }

    public function download( $filename ) {
        $filePath = public_path( 'assets/' . $filename );

        if ( file_exists( $filePath ) ) {
            return response()->download( $filePath );
        }

        return redirect()->back()->with( 'error', 'File not found.' );
    }

    private function deleteOldestFile( $filename ) {
        $filePath = public_path( 'assets/' . $filename );

        if ( file_exists( $filePath ) ) {
            unlink( $filePath );
        }
    }

    public function xmlDoc( Request $request ) {
        // Grab latest app name and date added from the db
        $app = App::orderBy( 'id', 'desc' )->first();
        $filename = $app->file;
        $version = $app->version;
        $build = $app->build;
        $signature = $app->signature;
        $date = $app->created_at;

        if ( !$filename ) {
            return response( [ 'error'=> 'File name not found' ], 404 );
        }
        ;

        // Get the download link of the filename gotten from the databse
        $downloadLink = $this->generateDownloadLink( $filename );

        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:sparkle="http://www.andymatuschak.org/xml-namespaces/sparkle">
    <channel>
        <title>Poslix Windows</title>
        <description>Most recent updates to Poslix Windows</description>
        <language>en</language>
        <item>
            <title>Version ' . htmlspecialchars( $version ) . '</title>
            <sparkle:releaseNotesLink>
                https://your_domain/your_path/release_notes.html
            </sparkle:releaseNotesLink>
            <pubDate>' . htmlspecialchars( $date ) . '</pubDate>
            <enclosure url="' . htmlspecialchars( $downloadLink ) . '"
                sparkle:dsaSignature="' . htmlspecialchars( $signature ) . '"
                sparkle:version="' . htmlspecialchars( $version ) . '+' . htmlspecialchars( $build ) . '"
                sparkle:os="windows" length="0" type="application/octet-stream" />
        </item>
        <query>Version ' . htmlspecialchars( $version ) . '</query>
    </channel>
</rss>';

        // Return the response with XML headers
        return response( $xmlString, 200 )
        ->header( 'Content-Type', 'application/xml' );
    }

    public function external() {
        return view( 'asset.dist.index' );
    }
}