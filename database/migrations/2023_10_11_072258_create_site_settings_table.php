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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name');
            $table->text('favicon')->nullable();
            $table->text('logo')->nullable();
            $table->text('icon')->nullable();
            $table->string('color_code')->nullable();
            $table->text('color_palette')->nullable();
            $table->bigInteger('retention_period')->nullable()->default(30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
