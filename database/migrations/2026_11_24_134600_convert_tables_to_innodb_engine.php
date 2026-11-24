<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['prices', 'orders', 'order_items', 'carts', 'cart_items', 'products', 'users'];

        foreach ($tables as $table) {
            try {
                DB::statement("ALTER TABLE `{$table}` ENGINE = InnoDB");
            } catch (\Throwable $e) {
                // نادیده گرفتن در صورت عدم وجود جدول یا خطای سطح دسترسی
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // نیازی به بازگردانی به MyISAM نیست چون InnoDB استاندارد مدرن است.
    }
};
