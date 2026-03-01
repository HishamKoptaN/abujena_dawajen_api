<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->default(0); // الكمية المتاحة
            $table->date('date'); // تاريخ الجرد
            $table->enum('transaction_type', ['in', 'out', 'adjustment']); // وارد، صادر، تعديل
            $table->decimal('transaction_quantity', 10, 2); // كمية الحركة
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
};
