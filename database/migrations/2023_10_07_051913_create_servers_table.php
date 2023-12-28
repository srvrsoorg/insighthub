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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sa_organization_id');
            $table->bigInteger('sa_server_id');
            $table->string('ip');
            $table->string('name');
            $table->string('operating_system')->nullable();
            $table->string('version')->nullable();
            $table->bigInteger('cores');
            $table->string('web_server');
            $table->string('agent_status');
            $table->string('timezone')->nullable();
            $table->string('database');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
