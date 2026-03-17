<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('price_discounts', function (Blueprint $table) {
          $table->id();
          $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
          $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
          $table->decimal('discount_value', 8, 2)->default(0.00); 
          $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('price_discounts');
    }
};
