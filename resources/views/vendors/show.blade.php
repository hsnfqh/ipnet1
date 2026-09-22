@extends('layouts.app')

@section('title', 'View Vendor - ' . $vendor->name)

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="vendorShowPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Vendor'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- 1. BREADCRUMB (Matching Screenshot 3) --}}
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                <a href="{{ route('vendors.index') }}" class="hover:text-gray-800">Vendor</a>
                <span>&gt;</span>
                <span class="text-gray-700">{{ $vendor->name }}</span>
                <span>&gt;</span>
                <span class="text-gray-900 font-bold">Edit</span>
            </div>

            {{-- 2. TITLE WITH ICON --}}
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center border border-red-200">
                    <svg class="w-4.5 h-4.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-[#1E293B] tracking-tight">View Vendor</h1>
            </div>

            {{-- 3. MAIN FORM CARD (Matching Screenshot 3) --}}
            <div class="ipnet-card p-6 sm:p-8 space-y-6">
                <form action="{{ route('vendors.update', $vendor->id) }}" method="POST" class="space-y-6 text-xs font-semibold">
                    @csrf
                    @method('PUT')

                    {{-- NAME --}}
                    <div>
                        <label class="block text-gray-500 uppercase text-[10px] font-bold tracking-wider mb-2">NAME</label>
                        <input type="text" name="name" value="{{ $vendor->name }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500 shadow-2xs">
                    </div>

                    {{-- ADDRESS 1 --}}
                    <div>
                        <label class="block text-gray-500 uppercase text-[10px] font-bold tracking-wider mb-2">ADDRESS 1</label>
                        <textarea name="address" rows="3" 
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 text-xs text-gray-900 font-normal leading-relaxed focus:ring-2 focus:ring-red-500/20 focus:border-red-500 shadow-2xs">{{ $vendor->address ?: 'Centennial Tower 12/1 - Jl. Gatot Subroto Kav. 24-25 Jakarta 12930' }}</textarea>
                        
                        <div class="mt-2">
                            <button type="button" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                                + CREATE SECONDARY ADDRESS
                            </button>
                        </div>
                    </div>

                    {{-- DEPARTMENT 1 --}}
                    <div class="space-y-3 pt-2">
                        <div>
                            <label class="block text-gray-500 uppercase text-[10px] font-bold tracking-wider mb-2">DEPARTMENT 1</label>
                            <input type="text" name="department" value="{{ $vendor->department ?: 'DEPT01' }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500 shadow-2xs">
                        </div>

                        {{-- PIC Table Row for Dept 1 --}}
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-center">
                            <div>
                                <label class="block text-gray-400 text-[10px] font-bold mb-1">PIC NAME 1</label>
                                <input type="text" name="channel_manager" value="{{ $vendor->channel_manager ?: 'Ferila' }}"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs text-gray-900">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-[10px] font-bold mb-1">PIC EMAIL 1</label>
                                <input type="text" name="email" value="{{ $vendor->email ?: 'ferila@bluepowertechnology.com' }}"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs text-gray-900">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-[10px] font-bold mb-1">PIC PHONE 1</label>
                                <input type="text" name="phone" value="{{ $vendor->phone ?: '081298839806' }}"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs text-gray-900">
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label class="block text-gray-400 text-[10px] font-bold mb-1">PIC POSITION 1</label>
                                    <input type="text" value="Channel Manager"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs text-gray-900">
                                </div>
                                <button type="button" class="mt-4 text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- CREATE PIC BUTTON --}}
                        <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200 text-center">
                            <button type="button" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                                + CREATE PIC DEPT01
                            </button>
                        </div>
                    </div>

                    {{-- DEPARTMENT 2 --}}
                    <div class="space-y-3 pt-2">
                        <div>
                            <label class="block text-gray-500 uppercase text-[10px] font-bold tracking-wider mb-2">DEPARTMENT 2</label>
                            <input type="text" value="Extreme"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500 shadow-2xs">
                        </div>

                        {{-- PIC Table Row for Dept 2 --}}
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-center">
                            <div>
                                <label class="block text-gray-400 text-[10px] font-bold mb-1">PIC NAME 1</label>
                                <input type="text" value="Silfani Putri Kartika"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs text-gray-900">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-[10px] font-bold mb-1">PIC EMAIL 1</label>
                                <input type="text" value="silfani.k@bluepowertechnology.com"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs text-gray-900">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-[10px] font-bold mb-1">PIC PHONE 1</label>
                                <input type="text" value="6288812830"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs text-gray-900">
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label class="block text-gray-400 text-[10px] font-bold mb-1">PIC POSITION 1</label>
                                    <input type="text" value="Product Manager"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-200 text-xs text-gray-900">
                                </div>
                                <button type="button" class="mt-4 text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- CREATE PIC EXTREME BUTTON --}}
                        <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200 text-center">
                            <button type="button" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                                + CREATE PIC EXTREME
                            </button>
                        </div>
                    </div>

                    {{-- CREATE DEPARTMENT LINK --}}
                    <div>
                        <button type="button" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                            + CREATE DEPARTMENT
                        </button>
                    </div>

                    {{-- SAVE BUTTON (Matching Screenshot 3 Bottom Right) --}}
                    <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                        <button type="submit" class="btn-ipnet-gradient px-7 py-2.5 rounded-xl text-white font-bold text-xs shadow-md hover:shadow-lg cursor-pointer transition">
                            Save
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    function vendorShowPage() {
        return {};
    }
</script>
@endsection
