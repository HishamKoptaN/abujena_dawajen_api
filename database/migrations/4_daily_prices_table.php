<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_daily_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['product_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_daily_prices');
    }
};
