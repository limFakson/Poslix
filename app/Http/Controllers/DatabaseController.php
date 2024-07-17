<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function grantPrivileges()
    {
        $rootUsername = config('app.db_username');
        $rootPassword = config('app.db_password');

        $hostname = env('DB_HOST', 'localhost');
        $tenantUsername = 'poslix_landlord';
        $tenantHostname = 'localhost';

        $grantCreatePrivilege = "GRANT ALL PRIVILEGES ON mysql.* TO '$tenantUsername'@'$tenantHostname';";
        $flushPrivileges = "FLUSH PRIVILEGES;";

        config(['database.connections.temp' => [
            'driver' => 'mysql',
            'host' => $hostname,
            'database' => 'mysql',
            'username' => $rootUsername,
            'password' => $rootPassword,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ]]);

        DB::connection('temp')->statement($grantCreatePrivilege);
        DB::connection('temp')->statement($flushPrivileges);

        return response()->json(['message' => 'Privileges granted successfully']);
    }
}
