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
        Schema::table('compare_list', function (Blueprint $table) {
            $table->unique(
                ['user_id', 'product_id'],
                'compare_list_user_product_unique'
            );
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compare_list', function (Blueprint $table) {
            $table->dropUnique('compare_list_user_product_unique');
        });
    }
};
