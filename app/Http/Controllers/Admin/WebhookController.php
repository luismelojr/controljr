<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebhookCall;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WebhookController extends Controller
{
    public function index(Request $request): Response
    {
        $webhooks = WebhookCall::latest()
            ->paginate(15)
            ->through(function ($call) {
                return [
                    'id' => $call->id,
                    'uuid' => $call->uuid,
                    'type' => $call->type,
                    'payload' => $call->payload,
                    'exception' => $call->exception,
                    'created_at' => $call->created_at->format('d/m/Y H:i:s'),
                ];
            });

        return Inertia::render('admin/webhooks/index', [
            'webhooks' => $webhooks,
        ]);
    }
}
