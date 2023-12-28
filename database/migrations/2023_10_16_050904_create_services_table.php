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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('server_id')->unsigned();
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->boolean('status')->default(true);
            $table->float('cpu_usage')->default(0);
            $table->float('memory_usage')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
