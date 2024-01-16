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
        Schema::create('other_addrresses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('jobId');
            $table->string('address')->nullable();
            $table->string('aunit')->nullable();
            $table->integer('rank')->nullable();
            $table->integer('prime')->nullable();
            $table->string('label')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_addrresses');
    }
};
