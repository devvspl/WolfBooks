@extends('layouts.app')
@section('content')
<div class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-stone-100 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-semibold text-stone-800">Global Region — Detail</h3>
            <p class="text-xs text-stone-400 mt-0.5">Record #{{ $globalRegion->id }}</p>
        </div>
        <a href="{{ route('generated.global-regions.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Back</a>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Global Region Name</label>
                <input type="text" disabled value="{{ $globalRegion->global_region_name ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Global Region Code</label>
                <input type="text" disabled value="{{ $globalRegion->global_region_code ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">New Option</label>
                <input type="text" disabled value="{{ $globalRegion->new_option ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
        </div>
    </div>
    <div class="px-6 py-4 bg-stone-50 border-t border-stone-100 flex items-center justify-end">
        <a href="{{ route('generated.global-regions.edit', $globalRegion) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-800 hover:bg-red-700 text-white text-sm font-medium transition-colors shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Edit</a>
    </div>
</div>
@endsection
