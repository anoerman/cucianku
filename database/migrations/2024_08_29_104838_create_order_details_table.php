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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->noActionOnDelete()->noActionOnUpdate();
            $table->foreignId('worker_id')->constrained('users')->noActionOnUpdate()->noActionOnDelete();
            $table->foreignId('product_id')->constrained('products')->noActionOnDelete()->noActionOnUpdate();
            $table->integer('qty')->default(1);
            $table->decimal('discount', 19, 2)->nullable();
            $table->decimal('price', 19, 2)->default(0)->comment('get from product');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
