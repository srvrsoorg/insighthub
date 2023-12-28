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
        Schema::create('usages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('server_id')->unsigned();
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade');
            $table->float('five_min_load')->default(0);
            $table->float('fifteen_min_load')->default(0);
            $table->float('memory_in_pr')->default(0);
            $table->float('disk_in_pr')->default(0);
            $table->float('swap_in_pr')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usages');
    }
};
