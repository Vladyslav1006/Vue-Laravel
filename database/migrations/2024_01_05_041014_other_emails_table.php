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
        Schema::create('other_emails', function (Blueprint $table) {
            $table->id();
            $table->integer('jobId')->index();
            $table->string('email')->nullable()->index();;
            $table->tinyInteger('is_primary')->default(0)->comment('1=yes 0=no');
            $table->string('label')->nullable();
            $table->timestamps();
            $table->unique(['email', 'jobId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_emails');
    }
};
