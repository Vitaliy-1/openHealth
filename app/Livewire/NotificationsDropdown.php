<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotificationCollection;

/**
 * Real-time Notifications Dropdown Component
 *
 * This Livewire component manages user notifications with the following features:
 * - Automatic refresh on page load to ensure current notification state
 * - Real-time updates when notifications are marked as read (via @entangle)
 * - Auto-refresh every minute to catch new notifications
 * - Cross-browser compatibility with proper error handling
 *
 * The component works with Alpine.js frontend code using @entangle directives
 * to provide seamless real-time notification experience across page navigations.
 */
class NotificationsDropdown extends Component
{
    protected const int MAX_NOTIFICATIONS = 10;

    /** @var DatabaseNotificationCollection Collection of user's unread notifications (limited to 10) */
    public DatabaseNotificationCollection $notifications;

    /** @var int Total count of unread notifications (auto-synced with Alpine.js via @entangle) */
    public int $notificationCount;

    public function mount(): void
    {
        // Initialize notifications using the same logic as refresh
        $this->refreshNotifications();
    }

    /**
     * Mark a specific notification as read and update UI in real-time.
     *
     * This method:
     * 1. Marks the notification as read in the database
     * 2. Removes it from the current collection for immediate UI update
     * 3. Recalculates notification count
     * 4. Properties auto-sync to Alpine.js via @entangle for immediate UI updates
     *
     * @param string $id The ID of the notification to mark as read
     *
     * @return void
     */
    public function markAsRead(string $id): void
    {
        // Find the specific notification in user's unread notifications
        $notification = Auth::user()?->unreadNotifications()->findOrFail($id);

        if ($notification) {
            $notification->markAsRead();

            /*
             * Refresh the entire notifications list from database for reliability
             * This ensures Livewire properly updates the collection
             */
            $this->refreshNotifications();
        }
    }

    /**
     * Refresh notifications list from database (called from frontend JavaScript).
     *
     * This method is called in three scenarios:
     * 1. Automatically on page load (ensures red badge appears immediately after page navigation)
     * 2. Automatically every minute (by default) (catches new notifications for users staying on same page)
     * 3. Manually when user clicks the notification 'bell'
     *
     * The method:
     * - Fetches fresh data from database
     * - Limits results to 10 notifications for performance
     *
     * @return void
     */
    public function refreshNotifications(): void
    {
        /*
         * Fetch fresh notifications directly from database
         * This ensures users always see the most current notification state
         */
        $this->notifications = new DatabaseNotificationCollection(
            Auth::user()->unreadNotifications()->limit(self::MAX_NOTIFICATIONS)->get()->all()
        );

        // Recalculate notification count based on fresh data
        $this->notificationCount = count($this->notifications);
    }

    /**
     * @return View The notification dropdown view
     */
    public function render(): View
    {
        return view('livewire.notifications-dropdown');
    }
}
