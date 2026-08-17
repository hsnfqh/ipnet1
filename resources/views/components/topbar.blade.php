@php
    $user = auth()->user();

    // Hitung notifikasi belum dibaca dengan aman — kalau tabel notifications
    // belum ada (migration belum dijalankan), badge cuma nggak tampil, bukan error.
    $unreadNotifCount = 0;
    if ($user) {
        try {
            $unreadNotifCount = \App\Models\Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        } catch (\Throwable $e) {
            $unreadNotifCount = 0;
        }
    }
@endphp

<div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-[28px] sm:py-[18px] border-b border-wms-line bg-white sticky top-0 z-10">

    <!-- Judul: menyusut & terpotong dengan elipsis kalau kepanjangan -->
    <div class="min-w-0">
        <h1 class="font-display text-[18px] sm:text-[21px] font-semibold text-wms-ink-900 tracking-[-0.2px] truncate">{{ $title }}</h1>
    </div>

    <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
        {{-- Tombol Info Changelog --}}
        <button
            @click="$store.changelog.show()"
            class="wms-iconbtn p-2 text-wms-ink-700 relative inline-flex focus:outline-none"
            title="Apa yang baru?"
            style="color:#75727C; transition:color 0.15s;"
            onmouseover="this.style.color='#C81E2C'"
            onmouseout="this.style.color='#75727C'">
            <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/>
            </svg>
        </button>

        <!-- Notifications Dropdown -->
        <div class="relative" x-data="notificationDropdown()" x-init="init()">
            <button @click="toggleDropdown()" class="wms-iconbtn p-2 text-wms-ink-700 relative inline-flex focus:outline-none" title="Pemberitahuan">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <template x-if="unreadCount > 0">
                    <span class="absolute top-1.5 right-1.5 w-[8px] h-[8px] rounded-full bg-red-600 border-2 border-white animate-pulse"></span>
                </template>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-cloak
                 @click.outside="open = false"
                 class="absolute right-0 mt-2 w-[320px] sm:w-[360px] bg-white border border-wms-line rounded-xl shadow-lg z-50 overflow-hidden"
                 style="display: none;"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95">
                 
                <div class="px-4 py-3 border-b border-wms-line bg-gray-50 flex items-center justify-between gap-2">
                    <span class="text-[13px] font-bold text-wms-ink-900">Notifikasi</span>
                    <div class="flex items-center gap-2">
                        <template x-if="unreadCount > 0">
                            <button @click="markAllRead()" class="text-[11px] font-semibold text-red-600 hover:underline">
                                Tandai dibaca
                            </button>
                        </template>
                        <template x-if="notifications.length > 0">
                            <button @click="promptDeleteAll()" class="text-[11px] font-semibold text-wms-ink-500 hover:text-red-600 flex items-center gap-1 transition-colors" title="Hapus semua notifikasi">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus semua
                            </button>
                        </template>
                    </div>
                </div>

                <div class="max-h-[280px] overflow-y-auto divide-y divide-gray-100">
                    <template x-if="notifications.length === 0">
                        <div class="px-4 py-6 text-center text-[12.5px] text-wms-ink-500">
                            Tidak ada notifikasi baru.
                        </div>
                    </template>
                    <template x-for="notif in notifications" :key="notif.id">
                        <div class="relative group flex items-center justify-between px-4 py-3 hover:bg-gray-50/80 transition-colors"
                             :class="!notif.is_read ? 'bg-red-50/20' : ''">
                            <div class="flex-1 min-w-0 pr-2">
                                <div class="flex gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" :class="!notif.is_read ? 'bg-red-600' : 'bg-transparent'"></span>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-[12.5px] font-semibold text-wms-ink-900 leading-snug" x-text="notif.title"></div>
                                        <div class="text-[11.5px] text-wms-ink-500 mt-0.5" x-text="notif.message"></div>
                                        <div class="text-[10.5px] text-wms-ink-400 mt-1" x-text="notif.time_ago"></div>
                                    </div>
                                </div>
                            </div>
                            <button @click.stop.prevent="promptDeleteNotification(notif.id, notif.title)" 
                                    class="opacity-60 hover:opacity-100 text-wms-ink-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-all flex-shrink-0"
                                    title="Hapus notifikasi ini">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="border-t border-wms-line bg-gray-50 text-center">
                    <a href="{{ route('notifications.index') }}" class="block py-2 text-[12px] font-semibold text-wms-ink-700 hover:text-red-600 transition-colors">
                        Lihat Semua Notifikasi
                    </a>
                </div>
            </div>

            <!-- Topbar Confirmation Delete Modal (Centered Popup) -->
            <div x-show="confirmModalOpen" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
                 @click.self="confirmModalOpen = false"
                 style="display: none;">
                <div class="bg-white rounded-2xl w-[420px] max-w-full overflow-y-auto animate-fade-in-up shadow-[0_20px_60px_rgba(14,13,18,0.2)] text-left">
                    <div class="p-5 sm:p-6">
                        <!-- Icon -->
                        <div class="flex justify-center mb-4">
                            <div class="w-14 h-14 rounded-full bg-[#FEF2F2] flex items-center justify-center">
                                <svg class="w-7 h-7 text-[#C81E2C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Title -->
                        <h3 class="text-center font-display text-[18px] font-bold text-[#17151C] mb-2">
                            Yakin hapus data?
                        </h3>
                        
                        <!-- Description -->
                        <p class="text-center text-[14px] text-[#75727C] mb-6 break-words" x-text="confirmMessage"></p>
                        
                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button @click="executeConfirmAction()" 
                                    class="flex-1 py-2.5 px-4 rounded-lg bg-[#C81E2C] text-white font-semibold text-[14px] hover:brightness-105 transition-all">
                                Yakin
                            </button>
                            <button @click="confirmModalOpen = false" 
                                    class="flex-1 py-2.5 px-4 rounded-lg bg-white text-[#3D3A44] border border-[#E7E5E3] font-semibold text-[14px] hover:bg-[#F8F7F6] transition-all">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="hidden sm:block w-px h-[26px] bg-wms-line"></div>

        <!-- User Profile Link -->
        <a href="{{ route('profile.show') }}" class="flex items-center gap-[9px] hover:opacity-85 transition-opacity" title="Lihat Profil Saya">
            <x-avatar :name="$user->name" size="34" tone="#C81E2C" />
            <div class="hidden sm:block">
                <div class="text-[13px] font-semibold text-wms-ink-900 leading-tight">{{ $user->name }}</div>
                <div class="text-[11.5px] text-wms-ink-500">{{ $user->role_label }}</div>
            </div>
        </a>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        if (window.hasRegisteredNotificationDropdown) return;
        window.hasRegisteredNotificationDropdown = true;
        
        Alpine.data('notificationDropdown', () => ({
            open: false,
            notifications: [],
            unreadCount: 0,
            pollingInterval: null,
            confirmModalOpen: false,
            confirmMessage: '',
            pendingAction: null,

            init() {
                this.fetchNotifications();
                this.pollingInterval = setInterval(() => {
                    this.fetchNotifications();
                }, 30000);
            },

            async fetchNotifications() {
                try {
                    let response = await fetch('{{ route('notifications.latest') }}');
                    if (response.ok) {
                        let data = await response.json();
                        let isFirstRun = this.notifications.length === 0 && !localStorage.getItem('wms_seen_notif_ids');
                        let seenNotifIds = JSON.parse(localStorage.getItem('wms_seen_notif_ids') || '[]');

                        if (data.notifications && data.notifications.length > 0) {
                            data.notifications.forEach(notif => {
                                // Trigger desktop popup for unread notifications that haven't been shown yet
                                if (!notif.is_read && !seenNotifIds.includes(notif.id)) {
                                    if (!isFirstRun) {
                                        this.showDesktopNotification(notif);
                                    }
                                    seenNotifIds.push(notif.id);
                                }
                            });
                            localStorage.setItem('wms_seen_notif_ids', JSON.stringify(seenNotifIds));
                        }

                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                    }
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                }
            },

            showDesktopNotification(notif) {
                if (!('Notification' in window) || Notification.permission !== 'granted') {
                    return;
                }

                const title = notif.title || 'Pemberitahuan WMS IP-Net';
                const options = {
                    body: notif.message || '',
                    icon: '{{ asset('images/ipnet1.png') }}',
                    badge: '{{ asset('images/ipnet1.png') }}',
                    data: { url: '{{ route('notifications.index') }}' },
                    tag: 'wms-notif-' + notif.id,
                    renotify: true
                };

                try {
                    if (window.swRegistration && 'showNotification' in window.swRegistration) {
                        window.swRegistration.showNotification(title, options);
                    } else {
                        const desktopNotif = new Notification(title, options);
                        desktopNotif.onclick = function() {
                            window.focus();
                            this.close();
                        };
                    }
                } catch (e) {
                    console.warn('Desktop notification error:', e);
                }
            },

            toggleDropdown() {
                this.open = !this.open;
                if (this.open) {
                    this.fetchNotifications();
                }
            },

            async markAllRead() {
                try {
                    let response = await fetch('{{ route('notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    if (response.ok) {
                        this.fetchNotifications();
                    }
                } catch (error) {
                    console.error('Error marking all as read:', error);
                }
            },

            promptDeleteNotification(id, title) {
                this.confirmMessage = 'Notifikasi "' + (title || 'ini') + '" akan dihapus permanen.';
                this.pendingAction = () => this.deleteNotification(id);
                this.confirmModalOpen = true;
            },

            promptDeleteAll() {
                this.confirmMessage = 'Semua notifikasi akan dihapus permanen.';
                this.pendingAction = () => this.deleteAllNotifications();
                this.confirmModalOpen = true;
            },

            executeConfirmAction() {
                if (this.pendingAction) {
                    this.pendingAction();
                }
                this.confirmModalOpen = false;
            },

            async deleteNotification(id) {
                try {
                    let response = await fetch(`/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (response.ok) {
                        this.notifications = this.notifications.filter(n => n.id !== id);
                        this.fetchNotifications();
                    }
                } catch (error) {
                    console.error('Error deleting notification:', error);
                }
            },

            async deleteAllNotifications() {
                try {
                    let response = await fetch('{{ route('notifications.destroyAll') }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (response.ok) {
                        this.notifications = [];
                        this.unreadCount = 0;
                    }
                } catch (error) {
                    console.error('Error deleting all notifications:', error);
                }
            }
        }));
    });
</script>