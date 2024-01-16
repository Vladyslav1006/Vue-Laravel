<?php

namespace App\Http\Handlers\Wp;

use App\Models\JobRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\MedideNotification;

class ContactForm7JobRequest
{
    use Dispatchable;

    public function __construct(public string $event, public array $data) {}

    public function handle()
    {
        Log::debug(['handled event',$this->data]);

        $jrq = [
            'salut' => $this->data['data']['Salutation'][0],
            'full_name' => $this->data['data']['FullName'],
            'email' => $this->data['data']['email'],
            'whts_no' => $this->data['data']['WA'],
            'start_date' => $this->data['data']['startdate'],
            'date_flex' => isset($this->data['data']['ScheduleFlex'][0]) ? 'Yes' : 'No',
            'no_of_kids' => $this->data['data']['kids'],
            'dob_kid' => $this->data['data']['bbday'],
            'dob_young_kid' => $this->data['data']['bbdayyoung'],
            'dob_old_kid' => $this->data['data']['bbdayold'],
            'job_loc' => $this->data['data']['loc'][0],
            'job_loc_addr' => $this->data['data']['Address1'] ?? '',
            'job_loc_addr_unit' => $this->data['data']['unit1'] ?? '',
            'job_loc_addr2' => $this->data['data']['Address2'] ?? '',
            'job_loc_addr_unit2' => $this->data['data']['unit2'] ?? '',
            'pets' => $this->data['data']['pets'][0] == 'Yes' ? 'Yes' : 'No',
            'pet_muzzle' => isset($this->data['data']['muzzle']) ? implode(PHP_EOL, $this->data['data']['muzzle']) : '',
            'pet_description' => $this->data['data']['petspec'],
            'job_alone' => $this->data['data']['alone'][0] == 'Yes' ? 'Yes' : 'No',
            'job_whoelse' => $this->data['data']['whoelse'],
            'job_schdl' => $this->data['data']['durations'],
            'job_restriction' => $this->data['data']['crit'][0] == 'Yes' ? 'Yes' : 'No',
            'job_crit' => $this->data['data']['Restrictions'],
            'pay_pref' => isset($this->data['data']['schedules']) ? implode(PHP_EOL, $this->data['data']['schedules']) : '',
            'expc_budget' => $this->data['data']['Budget'],
            'budget_flex' => $this->data['data']['Budgetneg'],
            'other_info' => $this->data['data']['JobDetails']
        ];

        JobRequest::create($jrq);
        broadcast(new MedideNotification('Just In: New BB Job Online Request!'))->toOthers();

    }
}
