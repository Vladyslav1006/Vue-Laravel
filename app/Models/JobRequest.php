<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'salut', 'full_name', 'email', 'whts_no', 'start_date', 'date_flex', 'no_of_kids',
        'dob_kid', 'dob_young_kid', 'dob_old_kid', 'job_loc', 'job_loc_addr', 'job_loc_addr_unit', 'job_loc_addr2', 'job_loc_addr_unit2', 'pets',
        'pet_muzzle', 'pet_description', 'job_alone', 'job_whoelse', 'job_schdl', 'job_restriction', 'job_crit','pay_pref', 'expc_budget', 'budget_flex','other_info','remark','status_s2c'
    ];

    protected $appends = ['s_full_name'];


    public function getSFullNameAttribute()
    {
        return $this->salut . ' ' . $this->full_name;
    }

    public function scopeCreatedAtSearch($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        $end   = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->whereBetween('job_requests.created_at', [$start->startOfDay(), $end->endOfDay()]);
    }

    public function scopeCreatedAtStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('job_requests.created_at', '>=', $start->startOfDay());

    }
    public function scopeCreatedAtEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('job_requests.created_at', '<=', $end->endOfDay());

    }

    public function scopeStartDateSearch($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        $end   = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->whereBetween('job_requests.start_date', [$start->startOfDay(), $end->endOfDay()]);
    }

    public function scopeStartDateStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('job_requests.start_date', '>=', $start->startOfDay());

    }
    public function scopeStartDateEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('job_requests.start_date', '<=', $end->endOfDay());

    }


    public function scopeDobKidSearch($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        $end   = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->whereBetween('job_requests.dob_kid', [$start->startOfDay(), $end->endOfDay()]);
    }

    public function scopeDobKidStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('job_requests.dob_kid', '>=', $start->startOfDay());

    }
    public function scopeDobKidEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('job_requests.dob_kid', '<=', $end->endOfDay());

    }

    public function scopeDobYoungKidSearch($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        $end   = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->whereBetween('job_requests.dob_young_kid', [$start->startOfDay(), $end->endOfDay()]);
    }

    public function scopeDobYoungKidStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('job_requests.dob_young_kid', '>=', $start->startOfDay());

    }
    public function scopeDobYoungKidEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('job_requests.dob_young_kid', '<=', $end->endOfDay());

    }

    public function scopeDobOldKidSearch($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        $end   = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->whereBetween('job_requests.dob_old_kid', [$start->startOfDay(), $end->endOfDay()]);
    }

    public function scopeDobOldKidStart($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('job_requests.dob_old_kid', '>=', $start->startOfDay());

    }
    public function scopeDobOldKidEnd($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('job_requests.dob_old_kid', '<=', $end->endOfDay());

    }


    public function scopeEntryStartDate($query, $sd)
    {
        $start = ($sd instanceof Carbon) ? $sd : Carbon::parse($sd);
        return $query->where('created_at', '>=', $start->startOfDay());

    }
    public function scopeEntryEndDate($query, $ed)
    {
        $end   = ($ed instanceof Carbon) ? $ed : Carbon::parse($ed);
        return $query->where('created_at', '<=', $end->endOfDay());

    }
}
