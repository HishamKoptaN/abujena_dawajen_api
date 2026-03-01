<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_collections_details', function (Blueprint $table) {
          $table->id();
          $table->foreignId('daily_collection_id')->constrained('daily_collections')->cascadeOnDelete();
          $table->decimal('amount', 12, 2); 
          $table->string('note')->nullable();
          $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('collection_daily_details');
    }
};
