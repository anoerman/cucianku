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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_code');
            $table->datetime('order_date');
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('status')->default('waiting')->comment('waiting, progress, ready, done');
            $table->enum('discount_type',['fixed', 'percentage'])->nullable();
            $table->decimal('discount_value', 19, 2)->nullable()->comment('value for discount');
            $table->decimal('total_discount', 19, 2)->nullable()->comment('total discount after counting');
            $table->decimal('subtotal_price', 19, 2)->default(0)->comment('accumulate from details');
            $table->decimal('total_price', 19, 2)->default(0)->comment('subtotal - discount');
            $table->longText('remarks')->nullable();
            $table->foreignId('worker_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
