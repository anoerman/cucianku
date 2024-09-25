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
        if (Schema::hasColumn('order_details', 'worker_id')) {
            Schema::table('order_details', function (Blueprint $table) {
                $table->dropForeign('order_details_worker_id_foreign');
                $table->dropColumn('worker_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('order_details', 'worker_id')) {
            Schema::table('order_details', function (Blueprint $table) {
                $table->foreignId('worker_id')->constrained('users')->noActionOnUpdate()->noActionOnDelete()->after('order_id');
            });
        }
    }
};
