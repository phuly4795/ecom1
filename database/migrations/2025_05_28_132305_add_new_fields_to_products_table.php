<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('original_price', 15, 2)->nullable()->after('price');
            $table->decimal('discount_percentage', 5, 2)->default(0)->after('original_price');
            $table->date('discount_start_date')->nullable()->after('discount_percentage');
            $table->date('discount_end_date')->nullable()->after('discount_start_date');
            $table->text('specifications')->nullable()->after('status');
            $table->integer('warranty_period')->nullable()->after('specifications');
            $table->string('warranty_policy')->nullable()->after('warranty_period');
            $table->string('meta_title')->nullable()->after('warranty_policy');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->text('variants')->nullable()->after('meta_keywords');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'original_price',
                'discount_percentage',
                'discount_start_date',
                'discount_end_date',
                'specifications',
                'warranty_period',
                'warranty_policy',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'variants',
            ]);
        });
    }
}
