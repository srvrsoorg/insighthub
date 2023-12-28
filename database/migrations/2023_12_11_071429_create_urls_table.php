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
        Schema::create('urls', function (Blueprint $table) {
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
            $table->text('url')->nullable();
            $table->text('title')->nullable();
            $table->text('browser')->nullable();
            $table->string('method');
            $table->string('status');
            $table->string('bot_name')->nullable();
            $table->boolean('is_bot_request')->default(false);
            $table->boolean('is_sitemap_url')->default(false);
            $table->boolean('is_xmlrpc_request')->default(false);
            $table->bigInteger('count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urls');
    }
};
