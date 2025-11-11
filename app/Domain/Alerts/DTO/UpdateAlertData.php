<?php

namespace App\Domain\Alerts\DTO;

use Illuminate\Http\Request;

readonly class UpdateAlertData
{
    public function __construct(
        public ?float $triggerValue,
        public ?array $triggerDays,
        public ?array $notificationChannels,
        public ?bool $isActive,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            triggerValue: $request->has('trigger_value') ? (float) $request->input('trigger_value') : null,
            triggerDays: $request->input('trigger_days'),
            notificationChannels: $request->input('notification_channels'),
            isActive: $request->has('is_active') ? $request->boolean('is_active') : null,
        );
    }
}
