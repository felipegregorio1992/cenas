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
        Schema::table('characters', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->text('goals')->nullable();
            $table->text('fears')->nullable();
            $table->text('history')->nullable();
            $table->text('personality')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('goals');
            $table->dropColumn('fears');
            $table->dropColumn('history');
            $table->dropColumn('personality');
            $table->dropColumn('notes');
        });
    }
};
