<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use Receiver\Facades\Receiver;
use Receiver\Providers\Webhook;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class WebhooksController extends Controller
{
    public function store(Request $request, string $driver)
    {
        return Receiver::driver($driver)
            ->receive($request)
            ->fallback(function (Webhook $webhook) {
                Log::debug(['unresolved event',$webhook->data]);
                abort(404);
            })
            ->ok();
    }
}
