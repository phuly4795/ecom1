<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();         // Mã coupon, duy nhất
            $table->text('description')->nullable(); // Mô tả
            $table->enum('type', ['fixed', 'percent']); // Loại giảm giá: tiền cố định hoặc phần trăm
            $table->decimal('value', 10, 2);          // Giá trị giảm
            $table->dateTime('start_date')->nullable(); // Bắt đầu hiệu lực
            $table->dateTime('end_date')->nullable();   // Kết thúc hiệu lực
            $table->integer('usage_limit')->nullable(); // Giới hạn số lần dùng
            $table->integer('used')->default(0);        // Số lần đã dùng
            $table->boolean('is_active')->default(true); // Trạng thái kích hoạt
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
