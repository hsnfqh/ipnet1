@extends('layouts.app')

@section('title', 'Dashboard - Engineer')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in">
            <!-- Metric Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5 mb-4 sm:mb-5">
                <x-metric-card label="Task Saya" value="{{ $myTasksCount }}" icon="ListChecks" :accent="true" href="{{ route('tasks.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </x-metric-card>
                
                <x-metric-card label="Jadwal Hari Ini" value="{{ $todaySchedulesCount }}" icon="CalendarDays" href="{{ route('schedules.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </x-metric-card>
                
                <x-metric-card label="Deadline Terdekat" value="{{ $nearestDeadline ? $nearestDeadline->deadline->format('d M') : '-' }}" icon="AlertTriangle" href="{{ route('tasks.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </x-metric-card>
                
                <x-metric-card label="Rata-rata Progress" value="{{ $avgProgress }}%" icon="CheckCircle2" href="{{ route('tasks.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </x-metric-card>
            </div>

            <!-- My Tasks & Today's Schedule -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- My Tasks -->
                <div class="wms-card overflow-hidden">
                    <div class="p-4 pb-0">
                        <h3 class="font-display text-[15px] font-semibold text-wms-ink-900">Task Saya</h3>
                    </div>
                    <div class="p-2 overflow-y-auto" style="max-height: 420px;">
                        @forelse($myTasks as $task)
                        <div class="p-3 border-t border-wms-line2">
                            <div class="flex flex-wrap justify-between items-start gap-2 mb-1.5">
                                <span class="text-[13.5px] font-medium text-wms-ink-900 break-words min-w-0 flex-1">{{ $task->title }}</span>
                                <x-priority-flag level="{{ $task->priority }}" />
                            </div>
                            <div class="text-[11.5px] text-wms-ink-500 mb-2 break-words">{{ $task->project?->name ?? 'No Project' }}</div>
                            <x-progress-bar value="{{ $task->progress }}" />
                            <div class="flex flex-wrap justify-between items-center gap-2 mt-1.5">
                                <span class="text-[10.5px] text-wms-ink-400">{{ $task->status }}</span>
                                <span class="text-[10.5px] text-wms-ink-400">{{ $task->deadline->format('d M Y') }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-wms-ink-500">
                            <p class="text-[13.5px]">Belum ada task</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="wms-card overflow-hidden">
                    <div class="p-4 pb-3 flex items-center justify-between border-b border-[#EFEDEB]">
                        <div class="flex items-center gap-2">
                            <h3 class="font-display text-[15px] font-semibold text-[#17151C]">Jadwal Hari Ini</h3>
                            @if(count($todaySchedules) > 0)
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-[#C81E2C]/10 text-[#C81E2C]">
                                    {{ count($todaySchedules) }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[11.5px] font-medium text-[#75727C]">{{ now()->format('d M Y') }}</span>
                    </div>
                    <div class="p-3 space-y-2.5 overflow-y-auto" style="max-height: 420px;">
                        @forelse($todaySchedules as $schedule)
                        <div class="p-3.5 rounded-xl bg-[#FBFBFA] border border-[#E7E5E3] hover:border-[#C81E2C]/30 hover:bg-white transition-all duration-200 shadow-sm">
                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <h4 class="text-[13.5px] font-semibold text-[#17151C] leading-snug break-words flex-1">
                                    {{ $schedule->title }}
                                </h4>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-[#FDF1F2] text-[#C81E2C] shrink-0 border border-[#FADADF]">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </span>
                            </div>
                            
                            <p class="text-[12px] font-medium text-[#75727C] mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#948F99] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <span class="truncate">{{ $schedule->project?->name ?? 'Tanpa Project' }}</span>
                            </p>

                            <div class="pt-2 border-t border-[#EFEDEB] flex items-center text-[11.5px] text-[#75727C]">
                                <span class="flex items-center gap-1 truncate">
                                    <svg class="w-3.5 h-3.5 text-[#948F99] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="truncate">{{ $schedule->location }}</span>
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10 text-[#75727C]">
                            <div class="w-12 h-12 rounded-xl bg-[#F1F0EE] flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 opacity-60 text-[#75727C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-[13.5px] font-medium text-[#3D3A44]">Tidak ada jadwal hari ini</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection