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
        Schema::table('applications', function (Blueprint $table) {
            // Add 'enable' column with default value true
            $table->boolean('enable')->default(true)->after('active');

            // Add 'priority' column with default value 'medium'
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium')->after('enable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('enable');
            $table->dropColumn('priority');
        });
    }
};
