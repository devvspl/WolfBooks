@extends('layouts.app')
@section('content')
<div class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-stone-100 flex items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-semibold text-stone-800">Air</h3>
            <p class="text-xs text-stone-400 mt-0.5">{{ $airs->total() }} {{ Str::plural('record', $airs->total()) }}</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('generated.airs.index') }}">
                <div class="flex items-center gap-2 border border-stone-300 rounded-xl px-3 py-2 focus-within:border-red-700 focus-within:ring-2 focus-within:ring-red-700/10 transition bg-white">
                    <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search…" autocomplete="off" class="text-sm outline-none border-none p-0 bg-transparent text-stone-700 placeholder-stone-400 w-40" oninput="clearTimeout(window._st); window._st = setTimeout(() => this.form.submit(), 400)">
                    @if(!empty($search))
                    <a href="{{ route('generated.airs.index') }}" class="text-stone-400 hover:text-stone-600 transition shrink-0"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></a>
                    @endif
                </div>
            </form>
            <a href="{{ route('generated.airs.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-800 hover:bg-red-700 text-white text-sm font-medium transition-colors shadow-sm whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New
            </a>
            <div class="inline-flex rounded-xl overflow-hidden shadow-sm">
                <a href="{{ route('generated.airs.export') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-700 hover:bg-green-600 text-white text-sm font-medium transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export
                </a>
                <button onclick="openExportLog()" class="inline-flex items-center px-2.5 py-2 bg-green-800 hover:bg-green-700 text-white text-sm transition-colors border-l border-green-600" title="Export history">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </button>
            </div>
        </div>
    </div>
    @if(session('success'))
    <div class="mx-6 mt-4 px-4 py-2.5 bg-green-50 border border-green-200 text-green-700 text-xs rounded-lg">{{ session('success') }}</div>
    @endif
    @if($airs->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-14 h-14 rounded-2xl bg-stone-100 flex items-center justify-center mb-4"><svg class="w-7 h-7 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
        <p class="text-sm font-medium text-stone-600">No records yet</p>
        <p class="text-xs text-stone-400 mt-1">Click "Add New" to get started.</p>
    </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-stone-100 bg-stone-50 text-left">
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider w-12">#</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Mode</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Agent Name</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">P N R Number</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Date Of Booking</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Journey Date</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Air Line</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Ticket Number</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Journey From</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Journey Upto</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Travel Class</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Location</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Items</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @foreach($airs as $index => $air)
            <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-6 py-4 text-stone-400">{{ $airs->firstItem() + $index }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->mode ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->agent_name ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->p_n_r_number ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->date_of_booking ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->journey_date ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->air_line ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->ticket_number ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->journey_from ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->journey_upto ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->travel_class ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ $air->location ?? '—' }}</td>
                <td class="px-6 py-4 text-stone-700">{{ is_array($air->items) ? count($air->items).' row(s)' : ($air->items ?? '—') }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="inline-flex items-center gap-2">
                        <a href="{{ route('generated.airs.show', $air) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>View</a>
                        <a href="{{ route('generated.airs.edit', $air) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Edit</a>
                        <form method="POST" action="{{ route('generated.airs.destroy', $air) }}" onsubmit="return confirm('Delete this record?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($airs->hasPages())
    <div class="px-6 py-4 border-t border-stone-100 flex items-center justify-between gap-4">
        <p class="text-xs text-stone-400">Showing {{ $airs->firstItem() }}–{{ $airs->lastItem() }} of {{ $airs->total() }} results</p>
        <div class="flex items-center gap-1">
            @if($airs->onFirstPage())<span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-300 cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>@else<a href="{{ $airs->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-500 hover:bg-stone-100 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>@endif
            @foreach($airs->getUrlRange(1, $airs->lastPage()) as $pg => $url)<a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-medium transition-colors {{ $pg == $airs->currentPage() ? 'bg-red-800 text-white' : 'text-stone-600 hover:bg-stone-100' }}">{{ $pg }}</a>@endforeach
            @if($airs->hasMorePages())<a href="{{ $airs->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-500 hover:bg-stone-100 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>@else<span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-300 cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>@endif
        </div>
    </div>
    @endif
    @endif
</div>

{{-- Export Log Offcanvas --}}
<div id="exportLogOverlay" onclick="closeExportLog()" class="fixed inset-0 bg-black/40 z-40 hidden"></div>
<div id="exportLogPanel" class="fixed top-0 right-0 h-full w-96 bg-white shadow-2xl z-50 translate-x-full transition-transform duration-300 flex flex-col">
    <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100">
        <div>
            <h4 class="text-sm font-semibold text-stone-800">Export History</h4>
            <p class="text-xs text-stone-400 mt-0.5">Air</p>
        </div>
        <button onclick="closeExportLog()" class="w-8 h-8 flex items-center justify-center rounded-lg text-stone-400 hover:bg-stone-100 hover:text-stone-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="flex-1 overflow-y-auto p-4 space-y-2">
        @forelse($exportLogs as $log)
        <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl border border-stone-100 bg-stone-50 hover:bg-white hover:border-stone-200 transition-colors">
            <div class="min-w-0">
                <p class="text-xs font-medium text-stone-700 truncate">{{ $log->file_name }}</p>
                <p class="text-xs text-stone-400 mt-0.5">{{ $log->row_count }} rows &middot; {{ $log->created_at->format('d M Y, H:i') }}</p>
                @if($log->user)<p class="text-xs text-stone-400">by {{ $log->user->name }}</p>@endif
            </div>
            <a href="{{ route('generated.airs.export.download', $log) }}" class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>Download</a>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-12 h-12 rounded-2xl bg-stone-100 flex items-center justify-center mb-3"><svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <p class="text-sm font-medium text-stone-500">No exports yet</p>
            <p class="text-xs text-stone-400 mt-1">Click Export to generate your first file.</p>
        </div>
        @endforelse
    </div>
</div>
<script>
function openExportLog(){document.getElementById('exportLogOverlay').classList.remove('hidden');document.getElementById('exportLogPanel').classList.remove('translate-x-full');}
function closeExportLog(){document.getElementById('exportLogOverlay').classList.add('hidden');document.getElementById('exportLogPanel').classList.add('translate-x-full');}
</script>
@endsection
