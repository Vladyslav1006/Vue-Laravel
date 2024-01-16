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
        Schema::create('job_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('salut')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email');
            $table->string('whts_no');
            $table->date('start_date')->nullable();
            $table->string('date_flex')->nullable();
            $table->string('no_of_kids', 4)->nullable();
            $table->date('dob_kid')->nullable();
            $table->date('dob_young_kid')->nullable();
            $table->date('dob_old_kid')->nullable();
            $table->text('job_loc')->nullable();
            $table->string('job_loc_addr')->nullable();
            $table->string('job_loc_addr_unit')->nullable();
            $table->string('job_loc_addr2')->nullable();
            $table->string('job_loc_addr_unit2')->nullable();
            $table->string('pets')->nullable();
            $table->string('pet_muzzle')->nullable();
            $table->string('pet_description')->nullable();
            $table->string('job_alone')->nullable();
            $table->string('job_whoelse')->nullable();
            $table->text('job_schdl')->nullable();
            $table->string('job_restriction')->nullable();
            $table->text('job_crit')->nullable();
            $table->string('pay_pref')->nullable();
            $table->string('expc_budget')->nullable();
            $table->string('budget_flex')->nullable();
            $table->text('other_info')->nullable();
            $table->integer('CRDR1')->nullable();
            $table->string('CRDR1_reason')->nullable();
            $table->integer('CRDR2')->nullable();
            $table->string('CRDR2_reason')->nullable();
            $table->string('BStatus')->nullable();
            $table->string('rejectReason')->nullable();
            $table->string('MBJNo')->nullable();
            $table->integer('last_edited')->nullable();
            $table->text('int_c_bb')->nullable();
            $table->text('remark')->nullable();
            $table->string('duration')->nullable();
            $table->string('status_s2c')->nullable();

            $table->fullText(['full_name', 'job_loc', 'job_loc_addr', 'job_loc_addr2', 'pet_description', 'job_schdl', 'job_crit', 'pay_pref', 'expc_budget','budget_flex', 'other_info', 'CRDR1_reason', 'CRDR2_reason', 'rejectReason', 'int_c_bb', 'remark'], 'fulltextindex')->language('english');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_requests');
    }
};
