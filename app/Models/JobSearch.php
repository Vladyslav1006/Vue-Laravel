<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OtherAddrress;
use App\Models\OtherEmail;
use App\Models\OtherEty;
use App\Models\OtherPhone;

class JobSearch extends Model
{
    use HasFactory;
    protected $fillable = [
        'jobReqId', 'jobMBJ', 'gist', 'salut', 'full_name', 'cn', 'job_email',
        'job_addr', 'job_addr_unit', 'job_phn', 'CRDR1', 'CRDR2', 'fee', 'feepaid', 'owner_paid', 'payment_date',
        'reg_bank_detail', 'fee_balance', 'int_remark', 'ref_crit', 'ref_amt', 's1', 's2','s3', 's4', 's5','ref_due_date','start_date','comments', 'qoute_ate', 'extra_charge', 'duration', 'daysreq', 'freqreq', 'job_loc','bbplcradius', 'bbplcregion', 'bbplcequip','bbplcequipd','bbplcequipd','no_of_kids','ykid', 'okid', 'job_alone', 'job_whoelse', 'pets', 'pet_muzzle','pet_description', 'crit_restriction', 'mf','CleanReq','CleanReqDe','CookReq', 'CookReqDe', 'kidshealth', 'familyworkcon', 'budget','pay_schdl', 'phone_convo', 'invc','last_edited'
    ];

    protected $appends = ['gist_auto','s_full_name','sitting_contract','jc1','jc2','inv_detail','receipt_no'];


    public function getSFullNameAttribute()
    {
        return $this->salut . ' ' . $this->full_name;
    }


    public function scopePaymentDateStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('job_searches.payment_date', '>=', $start->startOfDay());

    }
    public function scopePaymentDateEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('job_searches.payment_date', '<=', $end->endOfDay());

    }

    public function scopeRefDueDateStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('job_searches.ref_due_date', '>=', $start->startOfDay());

    }
    public function scopeRefDueDateEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('job_searches.ref_due_date', '<=', $end->endOfDay());

    }

    public function scopeYkidStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('job_searches.ykid', '>=', $start->startOfDay());

    }
    public function scopeYkidEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('job_searches.ykid', '<=', $end->endOfDay());

    }

    public function scopeOkidStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('job_searches.okid', '>=', $start->startOfDay());

    }
    public function scopeOkidEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('job_searches.okid', '<=', $end->endOfDay());

    }

    public function scopeStartDateStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('job_searches.start_date', '>=', $start->startOfDay());

    }
    public function scopeStartDateEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('job_searches.start_date', '<=', $end->endOfDay());

    }

    public function scopeAllocatedBBLike($query, $term)
    {

        return $query->having('allctbbs', 'LIKE', "%{$term}%");

    }


    public function getSittingContractAttribute()
    {
        $bbsuniqids = explode("::", $this->bbsuniqids);
        $bbscontacts = explode("::", $this->bbscontacts);
        $fullnames = explode("::", $this->fullnames);

        return
"Agreement Number (For Records Purpose): MBB" . date('Ymd') . "-" . $this->cn . "-" . $this->jobMBJ . " 

Babysitter ID: " . $bbsuniqids[0] . " (" . $fullnames[0] . ")
Last 4 digits of babysitter contact: " . substr($bbscontacts[0], -4) . " 
Client Name: " . $this->salut . ' ' . $this->full_name . " 
Last 4 digits of client contact: " . substr($this->job_phn, -4) . " 
Postal Code of Babysitting Location (Unit Number and/or Full address will be sent via WhatsApp or Email):" . $this->job_addr ;
    }
    public function getJc1Attribute()
    {
        return
"Job " . $this->jobMBJ . " confirmed for you! Full address is " . $this->job_addr . " " . $this->job_addr_unit . ". =) Pls acknowledge, and do be punctual ok?";
    }
    public function getJc2Attribute()
    {
        return
"In case you require, for job " . $this->jobMBJ . ", owner handphone number is " . $this->job_phn . ". Thank you!";
    }
    public function getInvDetailAttribute()
    {
        $start = Carbon::parse($this->start_date);

        return
"Invoice Date: " . date('Y-m-d') . " 
Invoice Number: " . $start->format('Ymd') . "-CN" . $this->cn . "-MB" . $this->invc . " 
Client: " . $this->salut . ' ' . $this->full_name . " 
Address: " . $this->job_addr . " " . $this->job_addr_unit . " 
Email: " . $this->job_email . " 
Contact Number: " . $this->job_phn . " 
Service Date(s): " . $start->format('d-M-Y') . " 
Billed Charges: SGD $" . $this->fee . " 
Charges Due: SGD $" . ($this->fee - $this->feepaid) . "";
    }
    public function getReceiptNoAttribute()
    {
        $start = Carbon::parse($this->start_date);
        $start_ykid = Carbon::parse($this->ykid);
        $start_okid = $this->okid ? Carbon::parse($this->okid) : '';

        return
"Receipt Date: " . date('Y-m-d') . "; Receipt Number: MB" . $this->invc . "-" . $this->cn . "-" . $start->format('dmY') . "-" . date('dmY') . " 
Client: " . $this->salut . ' ' . $this->full_name . " 
Address: " . $this->job_addr . " " . $this->job_addr_unit . " 
Email: " . $this->job_email . " 
Contact Number: " . $this->job_phn . " 
Service Start Date: " . $start->format('d-M-Y') . " 
Number of Kids: " . $this->no_of_kids . " 
Kid(s) Birthday(s) (To Determine Age): " . $start_ykid->format('d-M-Y') . ($this->okid ? " , " . $start_okid->format('d-M-Y') : '') . " 
Amount Paid: SGD $" . $this->feepaid . "";
    }

    public function getGistAutoAttribute()
    {
        $start = Carbon::parse($this->start_date);
        $crdr1_email = $this->CRDR1 ? User::find($this->CRDR1)->email : 'meide@meide.sg';
        $crdr1_phn = $this->CRDR1 ? User::find($this->CRDR1)->phn : '+65 8595 8579';
        return
"New Babysitting Job: " . $this->jobMBJ . " 
Start: " . ($this->start_date ? $start->format('d-M-Y') : 'ASAP') . " 
Location of Work: " . $this->job_addr . " 
" . ($this->pets == 'No' ? 'No Pets.' : 'Got Pet(s) - See Below for More Details.') . " 
Baby Born In " . ($this->ykid ? Carbon::parse($this->ykid)->format('M-Y') : '(INFO not yet available)') . " 
Job Scope: 


More Details: 


Salary (Parent will pay you directly): SGD $ 
If you are interested, please email " . $crdr1_email . " OR WhatsApp  " . $crdr1_phn . "!";
    }


static function updateLCN($job_id){
  $job=self::where('id', $job_id)->first();
  if(empty($job)){
    return false;
  }
  $cn= $job->cn;
  $add=OtherAddrress::where([['jobId', $job_id],['prime',1]])->first();
  if(empty($add) && !empty($job->job_addr)){
    $otherAdd=OtherAddrress::where('jobId', $job_id)->count();
    $addSeq=$otherAdd+1;
  }elseif(empty($add)){
    $addSeq=0;
  }else{
    $otherAdd=OtherAddrress::where([['jobId', $job_id],['id', '<', $add->id]])->count();
    $addSeq=$otherAdd+1;
  }
  $email=OtherEmail::where([['jobId', $job_id],['is_primary',1]])->first();
  if(empty($email) && !empty($job->job_email)){
    $otherEmail=OtherEmail::where('jobId', $job_id)->count();
    $emailSeq=$otherEmail+1;
  }elseif(empty($email)){
    $emailSeq=0;
  }else{
    $otherEmail=OtherEmail::where([['jobId', $job_id],['id', '<', $email->id]])->count();
    $emailSeq=$otherEmail+1;
  }
  $ety=OtherEty::where([['jobId', $job_id],['is_primary',1]])->first();
  if(empty($ety) && !empty($job->ety)){
    $otherEty=OtherEty::where('jobId', $job_id)->count();
    $etySeq=$otherEty+1;
  }elseif(empty($ety)){
    $etySeq=0;
  }else{
    $otherEty=OtherEty::where([['jobId', $job_id],['id', '<', $ety->id]])->count();
    $etySeq=$otherEty+1;
  }
  $ph=OtherPhone::where([['jobId', $job_id],['is_primary',1]])->first();
  if(empty($ph) && !empty($job->job_phn)){
    $otherPh=OtherPhone::where('jobId', $job_id)->count();
    $phSeq=$otherPh+1;
  }elseif(empty($ph)){
    $phSeq=0;
  }else{
    $otherPh=OtherPhone::where([['jobId', $job_id],['id', '<', $ph->id]])->count();
    $phSeq=$otherPh+1;
  }
  //$lcn=b9999-E01-Y01-A01-P01
  $lcn= $cn.'-E'.sprintf("%02d", $emailSeq).'-Y'.sprintf("%02d", $etySeq).'-A'.sprintf("%02d", $addSeq).'-P'.sprintf("%02d", $phSeq);
  $job->lcn=$lcn;
  $job->save();
  return $lcn;

} 
}
