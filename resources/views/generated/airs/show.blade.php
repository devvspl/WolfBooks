@extends('layouts.app')
@section('content')
<div class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-stone-100 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-semibold text-stone-800">Air — Detail</h3>
            <p class="text-xs text-stone-400 mt-0.5">Record #{{ $air->id }}</p>
        </div>
        <a href="{{ route('generated.airs.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Back</a>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Mode</label>
                <input type="text" disabled value="{{ $air->mode ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Agent Name</label>
                <input type="text" disabled value="{{ $air->agent_name ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">P N R Number</label>
                <input type="text" disabled value="{{ $air->p_n_r_number ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Date Of Booking</label>
                <input type="text" disabled value="{{ $air->date_of_booking ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Journey Date</label>
                <input type="text" disabled value="{{ $air->journey_date ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Air Line</label>
                <input type="text" disabled value="{{ $air->air_line ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Ticket Number:</label>
                <input type="text" disabled value="{{ $air->ticket_number ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Journey From</label>
                <input type="text" disabled value="{{ $air->journey_from ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Journey Upto</label>
                <input type="text" disabled value="{{ $air->journey_upto ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Travel Class</label>
                <input type="text" disabled value="{{ $air->travel_class ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Location</label>
                <input type="text" disabled value="{{ $air->location ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1.5">Items</label>
                <input type="text" disabled value="{{ $air->items ?? '—' }}" class="w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed">
            </div>
        </div>
    </div>
    <div class="px-6 py-4 bg-stone-50 border-t border-stone-100 flex items-center justify-end">
        <a href="{{ route('generated.airs.edit', $air) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-800 hover:bg-red-700 text-white text-sm font-medium transition-colors shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Edit</a>
    </div>
</div>
@endsection
