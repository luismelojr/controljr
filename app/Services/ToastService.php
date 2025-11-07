<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ViewErrorBag;

class ToastService
{
    private array $toasts = [];
    private array $flashedToasts = [];

    const TYPES = [
        'success' => ['icon' => '✅', 'duration' => 4000],
        'error' => ['icon' => '❌', 'duration' => 6000],
        'warning' => ['icon' => '⚠️', 'duration' => 5000],
        'info' => ['icon' => 'ℹ️', 'duration' => 4000],
        'loading' => ['icon' => '⏳', 'duration' => 0],
    ];

    public function __call(string $method, array $args)
    {
        if (!array_key_exists($method, self::TYPES)) {
            $method = 'success';
        }

        $message = $args[0] ?? '';
        $options = is_array($args[1] ?? null) ? $args[1] : [];

        return $this->addToast($message, $method, $options);
    }

    public function create(string $message): ToastBuilder
    {
        return new ToastBuilder($message, $this);
    }

    public function addToast(string $message, string $type = 'success', array $options = []): self
    {
        $toast = $this->make($message, $type, $options);
        $this->flashedToasts[] = $toast;

        Session::flash('toasts', $this->flashedToasts);

        return $this;
    }

    public function persistent(string $message, string $type = 'info', array $options = []): self
    {
        $options['persistent'] = true;
        $options['duration'] = 0;
        return $this->addToast($message, $type, $options);
    }

    public function withActions(string $message, string $type, array $actions): self
    {
        return $this->addToast($message, $type, ['actions' => $actions]);
    }

    public function withSound(string $message, string $type, string $sound): self
    {
        return $this->addToast($message, $type, ['sound' => $sound]);
    }

    public function validation($targets)
    {
        Session::flash('validation_toasts', Arr::wrap($targets));

        return $this;
    }

    public function make(string $message, string $type = 'success', array $options = []): array
    {
        $defaults = self::TYPES[$type] ?? self::TYPES['success'];

        return [
            'id' => uniqid('toast_'),
            'type' => $type,
            'title' => $options['title'] ?? null,
            'text' => $message,
            'description' => $options['description'] ?? null,
            'icon' => $options['icon'] ?? $defaults['icon'],
            'duration' => $options['duration'] ?? $defaults['duration'],
            'position' => $options['position'] ?? 'top-right',
            'dismissible' => $options['dismissible'] ?? true,
            'persistent' => $options['persistent'] ?? false,
            'timestamp' => Carbon::now()->toISOString(),
            'actions' => $options['actions'] ?? [],
            'data' => $options['data'] ?? [],
            'sound' => $options['sound'] ?? null,
        ];
    }

    public function clear(): self
    {
        Session::forget(['toasts', 'validation_toasts']);
        $this->flashedToasts = [];
        return $this;
    }

    public function getToasts()
    {
        return Session::get('toasts') ?? [];
    }

    public function getValidationToasts()
    {
        $validationErrors = Session::get('errors', app(ViewErrorBag::class));

        $requestedValidationToasts = Session::get('validation_toasts') ?? [];

        $validationToasts = [];

        foreach ($requestedValidationToasts as $key) {
            if ($validationErrors->has($key)) {
                $validationToasts[] = $this->make($validationErrors->first($key), 'error');
            }
        }

        return $validationToasts;
    }

    public function all()
    {
        return array_merge(
            $this->getToasts(),
            $this->getValidationToasts()
        );
    }
}
