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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('order_code')->unique();
            $table->unsignedBigInteger('shipping_address_id')->nullable();
            $table->string('billing_full_name');
            $table->string('billing_email');
            $table->string('billing_address');
            $table->string('billing_province_id');
            $table->string('billing_district_id');
            $table->string('billing_ward_id');
            $table->string('billing_telephone');
            $table->string('payment_method');
            $table->text('note')->nullable();
            $table->decimal('shipping_fee', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->string('coupon_code')->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->string('status')->default('pending');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('shipping_address_id')->references('id')->on('shipping_addresses')->onDelete('set null');
            $table->foreign('billing_province_id')->references('code')->on('provinces')->onDelete('restrict');
            $table->foreign('billing_district_id')->references('code')->on('districts')->onDelete('restrict');
            $table->foreign('billing_ward_id')->references('code')->on('wards')->onDelete('restrict');
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
