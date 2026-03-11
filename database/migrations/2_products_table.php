<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        $products = [
          ['name' => 'تسمين', ],
          ['name' => 'امهات', ],
        ];
        foreach ($products as $product) {
            Product::create($product);
        }
    }
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
