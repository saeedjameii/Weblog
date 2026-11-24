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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_code', 32)->unique();

            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->string('payment_method')->default('online');

            $table->string('discount_code')->nullable();
            $table->decimal('discount_amount', 15, 3)->default(0);

            $table->decimal('total_amount', 15, 3);
            $table->decimal('shipping_cost', 15, 3)->default(0);

            $table->string('recipient_name');
            $table->string('recipient_phone', 11);
            $table->string('province');
            $table->string('city');
            $table->text('address');
            $table->string('postal_code', 10);
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
