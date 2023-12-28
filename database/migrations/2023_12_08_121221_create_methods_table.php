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
        Schema::create('methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('server_id');
            $table->foreign('server_id')
                  ->references('id')->on('servers')
                  ->onDelete('cascade'); // onDelete cascade
            $table->unsignedBigInteger('application_id');
            $table->foreign('application_id')
                  ->references('id')->on('applications')
                  ->onDelete('cascade'); // onDelete cascade
            $table->string('type')->nullable();
            $table->string('method')->nullable();
            $table->boolean('is_bot_request')->default(false);
            $table->bigInteger('count')->default(0);
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('methods');
    }
};