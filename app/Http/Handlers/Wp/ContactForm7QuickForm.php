<?php

namespace App\Http\Handlers\Wp;

use App\Models\JobRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\MedideNotification;

class ContactForm7QuickForm
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
            'remark' => $this->data['data']['Remarks']
        ];

        JobRequest::create($jrq);
        broadcast(new MedideNotification('Just In: New BB Job Online Request!'))->toOthers();
    }
}
