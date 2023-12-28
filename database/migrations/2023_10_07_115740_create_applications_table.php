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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('server_id')->unsigned();
            $table->foreign('server_id')->references('id')->on('servers')->onDelete('cascade');
            $table->bigInteger('sa_application_id');
            $table->string('framework');
            $table->string('name');
            $table->string('primary_domain')->nullable();
            $table->float('php_version', 8, 1)->default(8.0);
            $table->string('ssl')->nullable();
            $table->float('size')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('application_user_server', function (Blueprint $table) {
            $table->bigInteger('user_server_id');
            $table->bigInteger('application_id');

            $table->primary(['application_id','user_server_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
        Schema::dropIfExists('application_user_server');
    }
};
