<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('allocated_b_b_s', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('jobId');
            $table->integer('baysitterId');
            $table->integer('rank')->nullable();
            $table->string('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocated_b_b_s');
    }
};
