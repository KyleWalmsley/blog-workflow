<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\OutgoingEmail;
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
        $activeTab = request('tab', 'incoming');

        $notifications = AdminNotification::with('job.client')
            ->latest()
            ->paginate(20);

        $outgoingEmails = OutgoingEmail::with('job', 'client')
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'outgoingEmails' => $outgoingEmails,
            'activeTab' => $activeTab,
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
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
