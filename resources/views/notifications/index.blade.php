@extends('layouts.app')

@section('title', 'Semua Notifikasi')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Semua Notifikasi'])

        <div class="p-[26px] animate-fade-in" x-data="{
            confirmOpen: false,
            confirmMessage: '',
            targetForm: null,
            triggerDelete(formId, message) {
                this.targetForm = document.getElementById(formId);
                this.confirmMessage = message;
                this.confirmOpen = true;
            },
            confirmDeleteAction() {
                this.confirmOpen = false;
                if (this.targetForm) {
                    this.targetForm.submit();
                }
            }
        }">
            @if(session('success'))
            <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 text-lg leading-none">&times;</button>
            </div>
            @endif

            <div class="wms-card overflow-hidden">
                <!-- Header Toolbar (Inbox style) -->
                <div class="p-4 sm:px-6 sm:py-4 bg-gray-50/80 border-b border-wms-line flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-[15px] font-bold text-wms-ink-900 leading-tight">Kotak Notifikasi</h2>
                            <p class="text-[12px] text-wms-ink-500">
                                Total: <span class="font-semibold text-wms-ink-800">{{ $notifications->count() }}</span>
                                @if($notifications->where('is_read', false)->count() > 0)
                                    &bull; <span class="text-red-600 font-semibold">{{ $notifications->where('is_read', false)->count() }} belum dibaca</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($notifications->where('is_read', false)->isNotEmpty())
                        <form action="{{ route('notifications.markAllRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-[12px] font-semibold text-wms-ink-700 bg-white border border-wms-line hover:bg-gray-50 transition-colors shadow-xs flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-wms-ink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Tandai semua dibaca
                            </button>
                        </form>
                        @endif

                        @if($notifications->isNotEmpty())
                        <form id="deleteAllForm" action="{{ route('notifications.destroyAll') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    @click="triggerDelete('deleteAllForm', 'Semua notifikasi akan dihapus permanen.')"
                                    class="px-3 py-1.5 rounded-lg text-[12px] font-semibold text-red-600 bg-white border border-wms-line hover:bg-red-50 hover:border-red-200 transition-colors shadow-xs flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Semua
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="divide-y divide-wms-line2">
                    @forelse($notifications as $notif)
                    <div class="group flex items-start sm:items-center justify-between gap-4 p-4 sm:px-6 hover:bg-gray-50/80 transition-colors {{ !$notif->is_read ? 'bg-red-50/30' : '' }}">
                        <div class="flex items-start gap-3.5 flex-1 min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full mt-1.5 sm:mt-0 flex-shrink-0 {{ !$notif->is_read ? 'bg-red-600 shadow-xs' : 'bg-wms-line' }}"></span>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-[14px] font-semibold text-wms-ink-900">{{ $notif->title }}</span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-100 text-wms-ink-500 font-medium">{{ $notif->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-[12.5px] text-wms-ink-600 mt-1 leading-relaxed">{{ $notif->message }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <form id="deleteForm-{{ $notif->id }}" action="{{ route('notifications.destroy', $notif->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        @click="triggerDelete('deleteForm-{{ $notif->id }}', 'Notifikasi &quot;{{ addslashes($notif->title) }}&quot; akan dihapus permanen.')"
                                        class="p-2 text-wms-ink-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" 
                                        title="Hapus notifikasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 rounded-full bg-gray-100 text-wms-ink-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <h3 class="text-[15px] font-semibold text-wms-ink-900">Belum ada notifikasi</h3>
                        <p class="text-[12.5px] text-wms-ink-500 mt-1 max-w-sm mx-auto">Notifikasi aktivitas tugas, proyek, dan pembaruan sistem akan muncul di sini.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Confirmation Delete Modal (Centered Popup) -->
            <div x-show="confirmOpen" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
                 @click.self="confirmOpen = false"
                 style="display: none;">
                <div class="bg-white rounded-2xl w-[420px] max-w-full overflow-y-auto animate-fade-in-up shadow-[0_20px_60px_rgba(14,13,18,0.2)]">
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
                            <button @click="confirmDeleteAction()" 
                                    class="flex-1 py-2.5 px-4 rounded-lg bg-[#C81E2C] text-white font-semibold text-[14px] hover:brightness-105 transition-all">
                                Yakin
                            </button>
                            <button @click="confirmOpen = false" 
                                    class="flex-1 py-2.5 px-4 rounded-lg bg-white text-[#3D3A44] border border-[#E7E5E3] font-semibold text-[14px] hover:bg-[#F8F7F6] transition-all">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection