<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsOnlineToCustomMethodsTable extends Migration
{
    public function up()
    {
        Schema::table('custom_methods', function (Blueprint $table) {
            $table->boolean('is_online')->default(false)->after('active');
        });
    }

    public function down()
    {
        Schema::table('custom_methods', function (Blueprint $table) {
            $table->dropColumn('is_online');
        });
    }
}