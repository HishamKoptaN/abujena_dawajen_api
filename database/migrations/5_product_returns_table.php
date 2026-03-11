<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::transaction(function () {
            Schema::create('product_returns', function (Blueprint $table) {
                 $table->id();
                 $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
                 $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                 $table->decimal('weight', 10, 2);
                 $table->timestamps();
             });
        });
    }
    public function down()
    {
        Schema::dropIfExists('product_returns');
    }
};
