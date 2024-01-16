<?php

namespace App\Models;

use Exception;
use App\Mail\MeideMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bbapplicant extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_bio','full_name', 'email', 'WA-BL', 'whts_no', 'whts_no2', 'dob', 'gender',
        'nationality', 'ethnicity', 'exp', 'fulltime', 'resid', 'wheretodo', 'workloc', 'bonustask', 'NOKname',
        'NOKrs', 'NOKhp', 'telegram', 'whatsapp', 'remail', 'paynownum', 'bankname','banknumb',  'review','comment','cav'
    ];

    public function scopeCreatedAtStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('bbapplicants.created_at', '>=', $start->startOfDay());

    }
    public function scopeCreatedAtEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('bbapplicants.created_at', '<=', $end->endOfDay());

    }

    public function scopeDobStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('bbapplicants.dob', '>=', $start->startOfDay());

    }
    public function scopeDobEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('bbapplicants.dob', '<=', $end->endOfDay());

    }

    public function scopeAceptdateStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('bbapplicants.aceptdate', '>=', $start->format('Y-m-d'));

    }
    public function scopeAceptdateEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('bbapplicants.aceptdate', '<=', $end->format('Y-m-d'));

    }

    public static function newWelcomeMail($mailcontents)
    {
        try {
            $details = [
                'title' => '🐣 Welcome to MEIDE Babysitting',
                'subject' => '🐣 Welcome to MEIDE Babysitting',
                'body' =>   '
<p>Congratulations on your successful application to MEIDE Babysitting!</p>
<p>We are pleased to inform you that your application is:<br> <b>[ SUCCESSFUL ]</b> </p>
<p>Now, you can view all available jobs listed on our telegram channel. Please read on for the details!</p>
<p>Additionally, you will receive job notifications whenever there are suitable ones that match your requirements and profile. The best way to view available jobs and get jobs is via our Telegram Channel.</p>
<p>We will send out the job notifications in 3 ways:</p>
<p style="margin-left:30px">
    1. Email (like this message, but you cannot view the list)<br>
    2. WhatsApp (only for specific jobs, and you cannot view the available list of jobs)<br>
    3. Telegram (BEST Way)<br>
</p>
<p style="margin-left:60px">
        A. Please note that this channel is <b>STRICTLY CONFIDENTIAL</b> and should only be used by you. Do NOT share the link to anyone else.<br>
        B. Join Telegram Channel Here:<br>
        <span style="margin-left:10px">https://t.me/joinchat/ZvaeSRwpCNQ4ZDJl</span><br>
        C. You can view all available jobs here on the telegram channel, and receive the FASTEST notifications on Telegram.<br>
        D. We recommend using Telegram to avoid missing out on the popular and high-income jobs!<br>
</p>

<p>If you have friends who are keen to join MEIDE, and looking for side income or more jobs, simply join us at:</p>
<p>https://meide.sg/career</p>

<p>Other than babysitting jobs, we have plenty of home cleaning jobs that pay $15-18 per hour on average, and you can work at your convenience (own time, own target, own date). You can choose location nearest to your house too :D</p>

<p><b>Simply sign up here, because we are looking for more home helpers!</b><br>
  https://meide.sg/career/#apply</p>

  <p><b>In the meantime, whilst you are awaiting for babysitting jobs, why not earn some extra income? Do some housekeeping too! &#128512;</b></p>

  <p>Thank you and read up on our fun articles at meide.sg/blog or baby.meide.sg/blog if you are an enthusiastic learner!<p>
                '
            ];

            $superadmin = User::where('id', 1)->first();

            Mail::to($mailcontents['toemail'])->bcc($superadmin->email)->send(new MeideMail($details));

        } catch (Exception $e) {
            info("Error: " . $e->getMessage());
        }
    }
}
