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
        Schema::create('bbapplicants', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('full_bio')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email');
            $table->string('whts_no');
            $table->string('whts_no2')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('ethnicity')->nullable();
            $table->text('exp')->nullable();
            $table->string('fulltime')->nullable();
            $table->string('resid')->nullable();
            $table->string('wheretodo')->nullable();
            $table->text('workloc')->nullable();
            $table->text('bonustask')->nullable();
            $table->string('NOKname')->nullable();
            $table->string('NOKrs')->nullable();
            $table->string('NOKhp')->nullable();
            $table->string('telegram')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('remail')->nullable();
            $table->string('paynownum')->nullable();
            $table->string('bankname')->nullable();
            $table->string('banknumb')->nullable();
            $table->integer('last_edited')->nullable();
            $table->string('BUniqueID')->nullable();
            $table->string('BUNO')->nullable();
            $table->string('aceptdate')->nullable();
            $table->string('bal')->nullable();
            $table->text('review')->nullable();
            $table->text('comment')->nullable();
            $table->string('WA-BL')->nullable();
            $table->string('BBSCR')->nullable();
            $table->string('day_avl')->nullable();
            $table->string('cav')->nullable();

            $table->fullText(['full_name',  'exp', 'fulltime',  'resid',   'wheretodo',   'workloc',  'bonustask', 'review', 'comment', 'BBSCR', 'day_avl'], 'fulltextindex')->language('english');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bbapplicants');
    }
};
