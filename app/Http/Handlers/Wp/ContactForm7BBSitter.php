<?php

namespace App\Http\Handlers\Wp;

use App\Models\Bbapplicant;
use App\Events\MedideNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;

class ContactForm7BBSitter
{
    use Dispatchable;

    public function __construct(public string $event, public array $data) {}

    public function handle()
    {
        Log::debug(['handled event',$this->data]);
        $bbsa = [
            'full_name' => $this->data['data']['Full'],
            'email' => $this->data['data']['email'],
            'whts_no' => $this->data['data']['WA'],
            'whts_no2' => $this->data['data']['WA2'],
            'dob' => $this->data['data']['bday'],
            'gender' => $this->data['data']['gender'][0],
            'nationality' => $this->data['data']['nationality'][0],
            'ethnicity' => $this->data['data']['ethnicity'][0],

            'exp' => $this->data['data']['exp'],
            'fulltime' => $this->data['data']['full-time'][0],
            'resid' => $this->data['data']['resid'][0],
            'wheretodo' => $this->data['data']['whereabletodo'][0],
            'workloc' => isset($this->data['data']['workloc']) ? implode(PHP_EOL, $this->data['data']['workloc']) : '',
            'bonustask' => isset($this->data['data']['bonustask']) ? implode(PHP_EOL, $this->data['data']['bonustask']) : '',

            'NOKname' => $this->data['data']['NOKname'],
            'NOKrs' => $this->data['data']['NOKrs'],
            'NOKhp' => $this->data['data']['NOKhp'],
            'telegram' => $this->data['data']['telegram'][0],
            'whatsapp' => $this->data['data']['whatsapp'][0],
            'remail' => $this->data['data']['r-email'][0],
            'paynownum' => $this->data['data']['PayNowN'],
            'bankname' => $this->data['data']['BankName'][0],
            'banknumb' => $this->data['data']['BankNumb'],
            'cav' => isset($this->data['data']['cav'][0]) ? 'Yes' : 'No',
        ];

        $bbapplicant = Bbapplicant::create($bbsa);
        $bbapplicant->BUNO = 'UNV';
        $bbapplicant->BUniqueID = 'Unvalidated';
        $bbapplicant->full_bio = $bbapplicant->full_name . ' ' . ($bbapplicant->gender == 'Male / 男' ? 'Male' : ($bbapplicant->gender == 'Female / 女' ? 'Female' : $bbapplicant->gender)) . ' ' . date('ymd', strtotime($bbapplicant->dob)) . ' ' . ($bbapplicant->ethnicity == 'Chinese / 华人' ? 'Chi' : $bbapplicant->ethnicity) . ' ' . ($bbapplicant->nationality == 'Singapore Citizen / 新加坡人公民' ? 'SG' : ($bbapplicant->nationality == 'PR / 永久住民' ? 'PR' : ($bbapplicant->nationality == 'Foreigner / 外劳' ? 'Foreign' : ($bbapplicant->nationality == 'LTVP (Long Term Visit Pass)' ? 'LTVP' : $bbapplicant->nationality)))) . ' Meide BB Join' ;

        $bbapplicant->save();

        broadcast(new MedideNotification('Just In: New Bbapplicant Application!'))->toOthers();
    }
}
