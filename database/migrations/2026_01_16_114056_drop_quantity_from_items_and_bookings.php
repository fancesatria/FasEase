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
        if (Schema::hasColumn('items', 'quantity')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }

        if (Schema::hasColumn('bookings', 'quantity')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }Schema::table('items', function (Blueprint $table) {
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            Schema::table('items', function (Blueprint $table) {
            $table->integer('quantity')->default(1);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('quantity')->default(1);
        });
        });
    }
};
