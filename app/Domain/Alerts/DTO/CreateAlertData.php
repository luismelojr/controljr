<?php

namespace App\Domain\Alerts\DTO;

use App\Enums\AlertTypeEnum;
use Illuminate\Http\Request;

readonly class CreateAlertData
{
    public function __construct(
        public string $userId,
        public AlertTypeEnum $type,
        public ?string $alertableType,
        public ?string $alertableId,
        public ?float $triggerValue,
        public ?array $triggerDays,
        public array $notificationChannels,
        public bool $isActive = true,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            userId: auth()->id(),
            type: AlertTypeEnum::from($request->input('type')),
            alertableType: $request->input('alertable_type'),
            alertableId: $request->input('alertable_id'),
            triggerValue: $request->input('trigger_value') ? (float) $request->input('trigger_value') : null,
            triggerDays: $request->input('trigger_days', []),
            notificationChannels: $request->input('notification_channels', ['database', 'mail']),
            isActive: $request->boolean('is_active', true),
        );
    }
}
