{{--
    Real-time Notifications Dropdown Component

    This Alpine.js component works with the NotificationsDropdown Livewire component to provide:
    - Automatic notification refresh on page load (ensures red badge shows immediately)
    - Real-time updates when notifications are marked as read
    - Periodic auto-refresh every minute to catch new notifications
    - Bell icon with red notification badge
    - Dropdown menu with notification list
--}}
<div
    class="relative"
    x-data="{
        open: false,
        notificationCount: @entangle('notificationCount'),

        init() {
            this.refreshNotificationsOnLoad();
            this.setupNotificationRefresh();
        },

        // Force notification refresh when page loads (key feature for cross-page badge persistence)
        refreshNotificationsOnLoad() {
            // Call Livewire method to fetch fresh notification data from database. notificationCount will update automatically
            this.$wire.call('refreshNotifications').catch((error) => {
                console.error('Error on page load refresh:', error);
            });
        },

        // Setup automatic notification refresh timer for long-running sessions
        setupNotificationRefresh() {
            const refreshFn = () => {
                // Only refresh if browser tab is active (some performance optimization)
                if (!document.hidden) {
                    // Reuse the same refresh logic as on page load
                    this.refreshNotificationsOnLoad();
                }
            };

            // Auto-refresh every 60 sec. (by default) to catch new notifications
            setInterval(refreshFn, {{ config('ehealth.ui.notifications.refresh_interval') }});

            // Also refresh when user returns to the browser tab
            document.addEventListener('visibilitychange', refreshFn);
        },

        // Handle notification bell button click
        toggleDropdown() {
            // If dropdown is closed, refresh notifications before opening
            if (!this.open) {
                this.$wire.call('refreshNotifications');
            }

            // Toggle dropdown visibility
            this.open = !this.open;
        }
    }"
>
    {{-- Notification Bell Icon Button --}}
    {{-- Clickable bell icon that toggles dropdown and refreshes notifications --}}
    <button
            @click="toggleDropdown()"
            x-transition
            type="button"
            aria-label="Notifications"
            class="cursor-pointer p-2 mr-1 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
    >
        @icon('bell', 'w-6 h-6')
    </button>

    {{-- Red Notification Badge --}}
    {{-- Shows when notificationCount > 0, displays notification count, positioned over bell icon --}}
    <span
        x-cloak
        x-show="notificationCount > 0"
        wire:key="notification-count"
        class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full"
        style="font-size: 0.6rem; transform: translate(calc(50% - 7px), calc(-50% + 5px)); height: calc(1em + 6px);"
        x-text="notificationCount"
    ></span>

    {{-- Notifications Dropdown Menu --}}
    {{-- Sliding dropdown that appears when bell is clicked, closes when clicking outside --}}
    <div
        x-cloak
        x-show="open"
        @click.away="open = false"
        class="absolute right-0 mt-2 w-80 max-w-sm bg-white rounded-xl shadow-lg z-50 overflow-hidden dark:bg-gray-700 dark:divide-gray-600"
    >
        {{-- Dropdown Header --}}
        <div
            class="block py-2 px-4 text-base font-medium text-center text-gray-700 bg-gray-50 dark:bg-gray-600 dark:text-gray-300"
        >
            {{ __('forms.notifications') }}
        </div>

        {{-- Notifications List --}}
        <ul wire:key="notifications-list-{{ count($notifications) }}">
            @forelse($notifications as $notification)
                {{-- Individual Notification Item --}}
                <li wire:key="notification-{{ $notification->id }}"
                    wire:transition
                    class="flex items-center justify-between px-4 py-2 border-b last:border-b-0 border-gray-100 dark:border-gray-600"
                >
                    {{-- Notification Content --}}
                    <div>
                        <span class="text-gray-900 dark:text-white">{{ $notification->data['message'] ?? '' }}</span>
                        <small class="block text-xs text-gray-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>

                    {{-- Mark as Read Button --}}
                    {{-- Calls Livewire markAsRead method with notification ID --}}
                    <button wire:click="markAsRead('{{ $notification->id }}')"
                            type="button"
                            class="cursor-pointer ml-2 text-xs text-blue-600 hover:underline"
                    >
                        {{ __('forms.mark_as_read') }}
                    </button>
                </li>
            @empty
                {{-- Empty State Message --}}
                <li class="px-4 py-2 text-center text-gray-400">{{ __('forms.empty') }}</li>
            @endforelse
        </ul>
    </div>
</div>
