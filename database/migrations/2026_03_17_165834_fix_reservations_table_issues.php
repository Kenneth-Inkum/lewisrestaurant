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
        Schema::table('reservations', function (Blueprint $table) {
            // Change party_size from smallint to integer to support larger parties
            $table->integer('party_size')->change();
            
            // Add performance indexes for frequently queried columns
            $table->index('reservation_date');
            $table->index('status');
            $table->index('email');
            $table->index(['reservation_date', 'reservation_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['reservation_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['email']);
            $table->dropIndex(['reservation_date', 'reservation_time']);
            
            // Revert party_size back to smallint
            $table->unsignedSmallInteger('party_size')->change();
        });
    }
};
