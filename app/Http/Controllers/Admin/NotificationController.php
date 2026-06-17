<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function index(): View
    {
        $notifications = AdminNotification::with('job.client')
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function markRead(AdminNotification $notification): RedirectResponse
    {
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(): RedirectResponse
    {
        AdminNotification::unread()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
