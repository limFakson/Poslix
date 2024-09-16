<?php
namespace App\Traits;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

trait JwtHelper {

    protected static $secretKey;
    protected static $algorithm = 'HS256';

    // call secret key from auth config
    public static function init() {
        self::$secretKey = config( 'auth.secret_key' );
    }

    // Encode data to generate a JWT token
    public static function encode( $data ) {
        $payload = array_merge( $data, [
            'iat' => time(),
            'exp' => time() + ( 60 * 60 * 12 ),
        ] );

        return JWT::encode( $payload, self::$secretKey, self::$algorithm );
    }

    // Decode the JWT token to retrieve the data
    public static function decode( $token ) {
        try {
            return JWT::decode( $token, new Key( self::$secretKey, self::$algorithm ) );
        } catch ( \Exception $e ) {
            return $e;
        }
    }
}
