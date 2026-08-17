@extends('layouts.app')

@section('title', 'Hasil Pencarian')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Hasil Pencarian'])

        <div class="p-[26px] animate-fade-in">
            <p class="text-[13px] text-wms-ink-500 mb-5">
                Menampilkan hasil untuk <span class="font-semibold text-wms-ink-900">"{{ $q }}"</span>
            </p>

            @if($q === '')
                <div class="wms-card p-8 text-center text-wms-ink-500">
                    <p class="text-[13.5px]">Ketik kata kunci di kolom pencarian untuk mulai mencari.</p>
                </div>
            @elseif($projects->isEmpty() && $tasks->isEmpty())
                <div class="wms-card p-8 text-center text-wms-ink-500">
                    <p class="text-[13.5px]">Tidak ada hasil ditemukan.</p>
                </div>
            @else
                @if($projects->isNotEmpty())
                <div class="wms-card overflow-hidden mb-4">
                    <div class="p-4 pb-2">
                        <h3 class="font-display text-[15px] font-semibold text-wms-ink-900">Project ({{ $projects->count() }})</h3>
                    </div>
                    <div class="p-2">
                        @foreach($projects as $project)
                        <a href="{{ route('projects.show', $project->id) }}" class="flex justify-between items-center p-2.5 border-t border-wms-line2 hover:bg-wms-paper transition-colors">
                            <div>
                                <div class="text-[13px] font-medium text-wms-ink-900">{{ $project->name }}</div>
                                <div class="text-[11.5px] text-wms-ink-500">{{ $project->client }}</div>
                            </div>
                            <x-status-badge status="{{ $project->status }}" />
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($tasks->isNotEmpty())
                <div class="wms-card overflow-hidden">
                    <div class="p-4 pb-2">
                        <h3 class="font-display text-[15px] font-semibold text-wms-ink-900">Task ({{ $tasks->count() }})</h3>
                    </div>
                    <div class="p-2">
                        @foreach($tasks as $task)
                        <a href="{{ route('tasks.show', $task->id) }}" class="flex justify-between items-center p-2.5 border-t border-wms-line2 hover:bg-wms-paper transition-colors">
                            <div>
                                <div class="text-[13px] font-medium text-wms-ink-900">{{ $task->title }}</div>
                                <div class="text-[11.5px] text-wms-ink-500">{{ $task->project?->name ?? 'No Project' }} &middot; {{ $task->engineer?->name ?? 'Unassigned' }}</div>
                            </div>
                            <x-status-badge status="{{ $task->status }}" />
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection