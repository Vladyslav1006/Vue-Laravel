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
        Schema::table('job_searches', function (Blueprint $table) {
            $table->string('lcn')->nullable()->after('cn');
            $table->string('ety')->nullable()->after('full_name');
            $table->string('other_ety')->nullable()->after('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_searches', function (Blueprint $table) {
            $table->dropColumn(['lcn','ety', 'other_ety' ]);
        });
    }
};
