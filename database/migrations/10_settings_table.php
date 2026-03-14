<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); 
            $table->string('description')->nullable();
            $table->timestamps();
        });
        DB::table('settings')->insert([
            [
                'key' => 'can_insert_previus_day_data',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'هل يمكن إدخال بيانات يوم سابق',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
