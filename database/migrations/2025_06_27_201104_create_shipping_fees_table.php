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
        Schema::create('shipping_fees', function (Blueprint $table) {
            $table->id();
            $table->string('province_id'); // hoặc use province_code
            $table->string('district_id')->nullable(); // có thể bỏ qua nếu phí theo tỉnh
            $table->unsignedInteger('fee'); // đơn vị VNĐ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_fees');
    }
};
