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
        Schema::create('error_rates', function (Blueprint $table) {
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
            $table->bigInteger('total_request')->default(0);
            $table->bigInteger('failed_request')->default(0);
            $table->decimal('request_per', 8, 2)->default(0.00);
            $table->string('calculation_per')->default('second');
            $table->bigInteger('error_rate')->default(0);
            $table->boolean('is_bot_request')->default(false);
            $table->timestamp('log_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_rates');
    }
};
