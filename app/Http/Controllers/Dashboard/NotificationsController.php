<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Alerts\Services\AlertService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlertNotificationResource;
use App\Models\AlertNotification;
use Inertia\Inertia;
use Inertia\Response;

class NotificationsController extends Controller
{
    public function __construct(
        private AlertService $alertService
    ) {}

    /**
     * Display a listing of the notifications.
     */
    public function index(): Response
    {
        $notifications = AlertNotification::where('user_id', auth()->id())
            ->with('alert')
            ->latest()
            ->paginate(20);

        return Inertia::render('dashboard/notifications/index', [
            'notifications' => AlertNotificationResource::collection($notifications),
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(AlertNotification $notification)
    {
        // Ensure user owns the notification
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $this->alertService->markNotificationAsRead($notification);

        Toast::create('A notificação foi marcada como lida.')
            ->title('Sucesso')
            ->success()
            ->flash();

        return redirect()->back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $this->alertService->markAllAsRead(auth()->id());

        Toast::create('Todas as notificações foram marcadas como lidas.')
            ->title('Sucesso')
            ->success()
            ->flash();

        return redirect()->back();
    }
}
