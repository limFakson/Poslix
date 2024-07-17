<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_sales', function (Blueprint $table) {
            $table->json('extras')->nullable()->after('tax_rate');
            $table->json('extra_names')->nullable()->after('extras');
            $table->double('extra')->nullable()->after('extra_names');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_sales', function (Blueprint $table) {
            $table->dropColumn('extras');
            $table->dropColumn('extra_names');
            $table->dropColumn('extra');
        });
    }
};
