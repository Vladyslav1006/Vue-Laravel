<?php

namespace App\Http\Receivers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Receiver\Providers\AbstractProvider;
use Illuminate\Foundation\Bus\Dispatchable;

class Wp extends AbstractProvider
{
    use Dispatchable;

    /**
     * @param Request $request
     * @return string
     */
    public function getEvent(Request $request): string
    {
        return $request->input('event')??'none';
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getData(Request $request): array
    {
        return $request->all();
    }



}
