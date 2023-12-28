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
        Schema::create('ols_access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('server_id');
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade');
            $table->unsignedBigInteger('application_id');
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->string('ip')->nullable();
            $table->string('time')->nullable();
            $table->text('url')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('bytes')->nullable();
            $table->text('referrer_url')->nullable();
            $table->text('referrer_domain')->nullable();
            $table->boolean('is_bot_request')->default(false);
            $table->boolean('is_sitemap_url')->default(false);
            $table->boolean('is_robots_txt')->default(false);
            $table->boolean('is_xmlrpc_request')->default(false);
            $table->string('platform')->nullable();
            $table->string('platform_version')->nullable();
            $table->string('device')->nullable(); // Desktop/Tablet/Mobile
            $table->string('bot_name')->nullable();
            $table->string('method')->nullable();
            $table->text('browser')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('document_type')->nullable();
            $table->string('protocol')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ols_access_logs');
    }
};