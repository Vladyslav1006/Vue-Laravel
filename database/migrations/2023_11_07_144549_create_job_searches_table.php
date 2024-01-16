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
        Schema::create('job_searches', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('jobReqId');
            $table->string('jobMBJ');
            $table->text('gist')->nullable();
            $table->string('salut')->nullable();
            $table->string('full_name')->nullable();
            $table->string('cn');
            $table->string('job_email');
            $table->string('job_addr')->nullable();
            $table->string('job_addr_unit')->nullable();
            $table->string('job_phn');
            $table->integer('CRDR1')->nullable();
            $table->integer('CRDR2')->nullable();
            $table->decimal('fee', 7, 2)->nullable();
            $table->decimal('feepaid', 7, 2)->nullable();
            $table->string('owner_paid')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('reg_bank_detail')->nullable();
            $table->decimal('fee_balance', 7, 2)->nullable();
            $table->text('int_remark')->nullable();
            $table->string('ref_crit')->nullable();
            $table->decimal('ref_amt', 7, 2)->nullable();
            $table->string('s1')->nullable();
            $table->string('s2')->nullable();
            $table->string('s3')->nullable();
            $table->string('s4')->nullable();
            $table->string('s5')->nullable();
            $table->date('ref_due_date')->nullable();
            $table->date('start_date')->nullable();
            $table->text('start_time')->nullable();
            $table->text('comments')->nullable();
            $table->string('qoute_ate')->nullable();
            $table->string('extra_charge')->nullable();
            $table->string('duration')->nullable();
            $table->string('daysreq')->nullable();
            $table->string('freqreq')->nullable();
            $table->string('job_loc')->nullable();
            $table->string('bbplcradius')->nullable();
            $table->string('bbplcregion')->nullable();
            $table->string('bbplcequip')->nullable();
            $table->string('bbplcequipd')->nullable();
            $table->string('no_of_kids', 4)->nullable();
            $table->date('ykid')->nullable();
            $table->date('okid')->nullable();
            $table->string('job_alone')->nullable();
            $table->string('job_whoelse')->nullable();
            $table->string('pets')->nullable();
            $table->string('pet_muzzle')->nullable();
            $table->string('pet_description')->nullable();
            $table->text('crit_restriction')->nullable();
            $table->string('mf')->nullable();
            $table->string('CleanReq')->nullable();
            $table->string('CleanReqDe')->nullable();
            $table->string('CookReq')->nullable();
            $table->string('CookReqDe')->nullable();
            $table->string('kidshealth')->nullable();
            $table->string('familyworkcon')->nullable();
            $table->integer('declineFProof')->nullable();
            $table->string('budget')->nullable();
            $table->string('pay_schdl')->nullable();
            $table->text('phone_convo')->nullable();
            $table->string('invc')->nullable();
            $table->integer('last_edited')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_searches');
    }
};
