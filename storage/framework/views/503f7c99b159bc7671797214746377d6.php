<div x-data="notificationBell()" x-init="init()" class="relative">
    <!-- Bell Icon -->
    <button @click="toggleDropdown" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount" 
              class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="showDropdown" 
         @click.away="showDropdown = false"
         x-transition
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto">
        
        <!-- Header -->
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-gray-900">Notifikasi</h3>
            <a href="<?php echo e(route('notifications.index')); ?>" class="text-sm text-blue-600 hover:text-blue-700">
                Lihat Semua
            </a>
        </div>

        <!-- Notifications List -->
        <div x-show="notifications.length > 0">
            <template x-for="notif in notifications.slice(0, 5)" :key="notif.id">
                <a :href="notif.action_url || '#'" 
                   @click="markAsRead(notif.id)"
                   class="block p-4 hover:bg-gray-50 border-b"
                   :class="{ 'bg-blue-50': !notif.is_read }">
                    <div class="flex items-start">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900" x-text="notif.title"></p>
                            <p class="text-sm text-gray-600 mt-1" x-text="notif.message"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="formatTime(notif.created_at)"></p>
                        </div>
                        <span x-show="!notif.is_read" class="ml-2 w-2 h-2 bg-blue-600 rounded-full"></span>
                    </div>
                </a>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="notifications.length === 0" class="p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <p class="text-gray-500 mt-2">Tidak ada notifikasi</p>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        showDropdown: false,
        unreadCount: 0,
        notifications: [],

        init() {
            this.fetchUnreadCount();
            this.fetchNotifications();
            
            // Poll every 30 seconds
            setInterval(() => {
                this.fetchUnreadCount();
                this.fetchNotifications();
            }, 30000);
        },

        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            if (this.showDropdown) {
                this.fetchNotifications();
            }
        },

        async fetchUnreadCount() {
            try {
                const response = await fetch('/notifications/unread-count');
                const data = await response.json();
                this.unreadCount = data.count;
            } catch (error) {
                console.error('Error fetching unread count:', error);
            }
        },

        async fetchNotifications() {
            try {
                const response = await fetch('/api/notifications');
                const data = await response.json();
                this.notifications = data.notifications || [];
            } catch (error) {
                console.error('Error fetching notifications:', error);
            }
        },

        async markAsRead(notificationId) {
            try {
                await fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Content-Type': 'application/json',
                    },
                });
                this.fetchUnreadCount();
                this.fetchNotifications();
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000); // seconds

            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
            return Math.floor(diff / 86400) + ' hari lalu';
        }
    }
}
</script>
<?php /**PATH E:\PROJEKU\pintar-menulis\resources\views\components\notification-bell.blade.php ENDPATH**/ ?>