@extends('layouts.app')

@section('title', 'View Client - ' . $client->name)

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="clientShowPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Client'])
        
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

            {{-- 1. BREADCRUMB (Matching Screenshot 2) --}}
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                <a href="{{ route('clients.index') }}" class="hover:text-gray-800">Client</a>
                <span>&gt;</span>
                <span class="text-gray-700">{{ $client->name }}</span>
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
                <h1 class="text-xl font-bold text-[#1E293B] tracking-tight">View Client</h1>
            </div>

            {{-- 3. 2-COLUMN LAYOUT (Matching Screenshot 2) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- LEFT FORM (2 Spans) --}}
                <div class="lg:col-span-2 ipnet-card p-6 sm:p-7 space-y-6">
                    <form action="{{ route('clients.update', $client->id) }}" method="POST" class="space-y-6 text-xs font-semibold">
                        @csrf
                        @method('PUT')

                        {{-- NAME --}}
                        <div>
                            <label class="block text-gray-500 uppercase text-[10px] font-bold tracking-wider mb-2">NAME</label>
                            <input type="text" name="name" value="{{ $client->name }}" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500 shadow-2xs">
                        </div>

                        {{-- ADDRESS 1 --}}
                        <div>
                            <label class="block text-gray-500 uppercase text-[10px] font-bold tracking-wider mb-2">ADDRESS 1</label>
                            <textarea name="address" rows="4" 
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 text-xs text-gray-900 font-normal leading-relaxed focus:ring-2 focus:ring-red-500/20 focus:border-red-500 shadow-2xs">{{ $client->address ?: 'Gedung: DBS Bank Tower (Ciputra World 1)' . "\n" . 'Lantai: Level 21, Suite 2102' . "\n" . 'Alamat Jalan: Jl. Prof. Dr. Satrio Kav. 3-5, Karet Kuningan Jakarta Selatan, DKI Jakarta 12940' }}</textarea>
                            
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
                                <input type="text" name="department" value="{{ $client->department ?: 'IPNET#1' }}"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500 shadow-2xs">
                            </div>

                            {{-- PIC Box --}}
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200 text-center">
                                <button type="button" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                                    + CREATE PIC IPNET#1
                                </button>
                            </div>
                        </div>

                        {{-- DEPARTMENT 2 (Optional Extra) --}}
                        <div class="space-y-3 pt-2">
                            <div>
                                <label class="block text-gray-500 uppercase text-[10px] font-bold tracking-wider mb-2">DEPARTMENT 2</label>
                                <input type="text" value="IT"
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500 shadow-2xs">
                            </div>

                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200 text-center">
                                <button type="button" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                                    + CREATE PIC IT
                                </button>
                            </div>
                        </div>

                        {{-- CREATE DEPARTMENT LINK --}}
                        <div>
                            <button type="button" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                                + CREATE DEPARTMENT
                            </button>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                            <a href="{{ route('clients.index') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md cursor-pointer transition">
                                Update Client
                            </button>
                        </div>
                    </form>
                </div>

                {{-- RIGHT PROJECTS PANEL (1 Span) (Matching Screenshot 2) --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-900">Projects</h3>
                    
                    @if($client->projects && $client->projects->count() > 0)
                        <div class="space-y-3">
                            @foreach($client->projects as $p)
                                <div class="ipnet-card p-4 space-y-2">
                                    <h4 class="text-xs font-bold text-gray-900">
                                        <a href="{{ route('projects.show', $p->id) }}" class="hover:text-red-600">
                                            {{ $p->name }}
                                        </a>
                                    </h4>
                                    <div class="flex items-center justify-between text-[11px] text-gray-400">
                                        <span>Rp {{ number_format($p->contract_value, 0, ',', '.') }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">{{ $p->status }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 rounded-2xl bg-gray-50 border border-dashed border-gray-200 text-center text-xs text-gray-400 font-medium">
                            Don't have any project, please add current or latest project here
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    function clientShowPage() {
        return {};
    }
</script>
@endsection
