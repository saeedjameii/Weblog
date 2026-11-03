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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number', 11)->nullable()->change();
            $table->date('birth_date')->nullable()->change();
            $table->string('national_code', 10)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     * Note: this fails if some rows already have NULL in these columns.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number', 11)->nullable(false)->change();
            $table->date('birth_date')->nullable(false)->change();
            $table->string('national_code', 10)->nullable(false)->change();
        });
    }
};