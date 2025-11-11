<?php

namespace App\Notifications;

use App\Models\AlertNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertTriggeredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public AlertNotification $alertNotification
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject($this->alertNotification->title)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line($this->alertNotification->message);

        // Add additional lines based on notification type
        if ($this->alertNotification->type === 'danger') {
            $mailMessage->error();
        }

        $mailMessage
            ->action('Ver no Painel', url('/dashboard/notifications'))
            ->line('Você recebeu este alerta porque configurou notificações automáticas no ControlJr.');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_notification_id' => $this->alertNotification->id,
            'title' => $this->alertNotification->title,
            'message' => $this->alertNotification->message,
            'type' => $this->alertNotification->type,
        ];
    }
}
