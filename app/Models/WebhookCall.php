<?php

namespace App\Models;

use App\Traits\HasUuidCustom;
use Illuminate\Database\Eloquent\Model;

class WebhookCall extends Model
{
    use HasUuidCustom;

    protected $fillable = [
        'uuid',
        'type',
        'payload',
        'exception',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
