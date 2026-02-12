<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('saga_logs', function (Blueprint $table) {
            $table->id();
            $table->string('trace_id');
            $table->string('step_name');
            $table->string('action'); // ex: execute ou compensate
            $table->string('status'); // ex: success ou failure
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saga_logs');
    }
};
