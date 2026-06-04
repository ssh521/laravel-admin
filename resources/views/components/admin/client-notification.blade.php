<div
    x-data="{
        notifications: [],
        showNotification(message, type = 'success') {
            const id = `${Date.now()}-${Math.random().toString(16).slice(2)}`;
            this.notifications.push({
                id,
                message,
                type,
                show: true,
            });
            setTimeout(() => {
                this.hideNotification(id);
            }, 2000);
        },
        hideNotification(id) {
            const notification = this.notifications.find((item) => item.id === id);
            if (! notification) {
                return;
            }
            notification.show = false;
            setTimeout(() => {
                this.notifications = this.notifications.filter((item) => item.id !== id);
            }, 200);
        },
        clearNotifications() {
            this.notifications = [];
        },
    }"
    x-on:notification:show.window="
        showNotification(
            event.detail?.message ?? '',
            event.detail?.type ?? 'success'
        );
    "
    x-on:notification:hide.window="
        if (event.detail?.id) {
            hideNotification(event.detail.id);
        } else {
            clearNotifications();
        }
    "
    class="fixed right-4 top-4 z-[9999] space-y-2"
>
    <template x-for="notification in notifications" :key="notification.id">
        <div
            x-data="{ show: true }"
            x-init="$watch(() => notification.show, (value) => { show = value; })"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            class="max-w-sm w-full"
        >
            <div
                class="rounded-lg shadow-lg p-4"
                :class="{
                    'bg-green-500 text-white': notification.type === 'success',
                    'bg-red-500 text-white': notification.type === 'error',
                    'bg-yellow-500 text-white': notification.type === 'warning',
                    'bg-blue-500 text-white': notification.type === 'info',
                    'bg-red-600 text-white': notification.type === 'delete',
                    'bg-gray-500 text-white': notification.type !== 'success'
                        && notification.type !== 'error'
                        && notification.type !== 'warning'
                        && notification.type !== 'info'
                        && notification.type !== 'delete',
                }"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg x-show="notification.type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="notification.type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="notification.type === 'warning'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="notification.type === 'delete'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="notification.type !== 'success' && notification.type !== 'error' && notification.type !== 'warning' && notification.type !== 'delete'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" x-text="notification.message"></p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="ml-4 flex-shrink-0 rounded-md inline-flex text-white hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-white"
                        @click="hideNotification(notification.id)"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
