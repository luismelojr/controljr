<?php

namespace App\Services;

class ToastBuilder
{
    private string $message;
    private string $type = 'success';
    private array $options = [];
    private ToastService $service;

    const POSITIONS = [
        'top-left', 'top-center', 'top-right',
        'bottom-left', 'bottom-center', 'bottom-right'
    ];

    public function __construct(string $message, ToastService $service)
    {
        $this->message = $message;
        $this->service = $service;
    }

    public function type(string $type): self
    {
        if (!array_key_exists($type, ToastService::TYPES)) {
            throw new \InvalidArgumentException("Toast type '{$type}' is not supported.");
        }
        $this->type = $type;
        return $this;
    }

    public function title(string $title): self
    {
        $this->options['title'] = $title;
        return $this;
    }

    public function description(string $description): self
    {
        $this->options['description'] = $description;
        return $this;
    }

    public function icon(string $icon): self
    {
        $this->options['icon'] = $icon;
        return $this;
    }

    public function duration(int $duration): self
    {
        $this->options['duration'] = $duration;
        return $this;
    }

    public function position(string $position): self
    {
        if (!in_array($position, self::POSITIONS)) {
            throw new \InvalidArgumentException("Position '{$position}' is not valid. Available positions: " . implode(', ', self::POSITIONS));
        }
        $this->options['position'] = $position;
        return $this;
    }

    public function persistent(): self
    {
        $this->options['persistent'] = true;
        $this->options['duration'] = 0;
        return $this;
    }

    public function nonDismissible(): self
    {
        $this->options['dismissible'] = false;
        return $this;
    }

    public function action(string $label, string $url, string $method = 'GET'): self
    {
        if (!isset($this->options['actions'])) {
            $this->options['actions'] = [];
        }

        $this->options['actions'][] = [
            'label' => $label,
            'url' => $url,
            'method' => strtoupper($method)
        ];
        return $this;
    }

    public function data(array $data): self
    {
        $this->options['data'] = array_merge($this->options['data'] ?? [], $data);
        return $this;
    }

    public function sound(string $sound): self
    {
        $this->options['sound'] = $sound;
        return $this;
    }

    public function success(): self
    {
        return $this->type('success');
    }

    public function error(): self
    {
        return $this->type('error');
    }

    public function warning(): self
    {
        return $this->type('warning');
    }

    public function info(): self
    {
        return $this->type('info');
    }

    public function loading(): self
    {
        return $this->type('loading');
    }

    public function flash(): ToastService
    {
        return $this->service->addToast($this->message, $this->type, $this->options);
    }
}
