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
        Schema::table('other_phones', function (Blueprint $table) {
            $table->tinyInteger('is_primary')->default(0)->comment('1=yes 0=no')->after('jobId');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('other_phones', function (Blueprint $table) {
            $table->dropColumn(['is_primary']);
        });
    }
};
