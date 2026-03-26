@extends('layouts.app')
@section('content')
<div class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-stone-100 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-semibold text-stone-800">New Air</h3>
            <p class="text-xs text-stone-400 mt-0.5">Fill in the details below.</p>
        </div>
        <a href="{{ route('generated.airs.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Back</a>
    </div>
    <form method="POST" action="{{ route('generated.airs.store') }}" enctype="multipart/form-data">
        @csrf 
        <div class="p-6">
            @if($errors->any())
            <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl">Please fix the errors below.</div>
            @endif
            <div class="grid grid-cols-3 gap-5">
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Mode</label>
                    <input type="text" name="mode" value="{{ old('mode') }}" placeholder="Mode" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('mode') border-red-400 bg-red-50 @enderror">
                    @error('mode')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Agent Name</label>
                    <input type="text" name="agent_name" value="{{ old('agent_name') }}" placeholder="Agent Name" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('agent_name') border-red-400 bg-red-50 @enderror">
                    @error('agent_name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">P N R Number</label>
                    <input type="text" name="p_n_r_number" value="{{ old('p_n_r_number') }}" placeholder="P N R Number" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('p_n_r_number') border-red-400 bg-red-50 @enderror">
                    @error('p_n_r_number')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Date Of Booking</label>
                    <input type="date" name="date_of_booking" value="{{ old('date_of_booking') }}" placeholder="Date Of Booking" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('date_of_booking') border-red-400 bg-red-50 @enderror">
                    @error('date_of_booking')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Journey Date</label>
                    <input type="date" name="journey_date" value="{{ old('journey_date') }}" placeholder="Journey Date" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('journey_date') border-red-400 bg-red-50 @enderror">
                    @error('journey_date')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Air Line</label>
                    <input type="text" name="air_line" value="{{ old('air_line') }}" placeholder="Air Line" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('air_line') border-red-400 bg-red-50 @enderror">
                    @error('air_line')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Ticket Number</label>
                    <input type="text" name="ticket_number" value="{{ old('ticket_number') }}" placeholder="Ticket Number" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('ticket_number') border-red-400 bg-red-50 @enderror">
                    @error('ticket_number')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Journey From</label>
                    <input type="text" name="journey_from" value="{{ old('journey_from') }}" placeholder="Journey From" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('journey_from') border-red-400 bg-red-50 @enderror">
                    @error('journey_from')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Journey Upto</label>
                    <input type="text" name="journey_upto" value="{{ old('journey_upto') }}" placeholder="Journey Upto" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('journey_upto') border-red-400 bg-red-50 @enderror">
                    @error('journey_upto')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Travel Class</label>
                    <select name="travel_class" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('travel_class') border-red-400 bg-red-50 @enderror"><option value="">-- Select --</option></select>
                    @error('travel_class')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Location</label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="Location" class="w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('location') border-red-400 bg-red-50 @enderror">
                    @error('location')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-3">
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Items</label>
                    <div x-data="repeaterField('items')" class="border border-stone-200 rounded-xl overflow-hidden">
                        <table class="w-full text-sm" id="repeater_items">
                            <thead class="bg-stone-800 text-white">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold w-8">#</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">#</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Employee</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Emp Code</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider">Department</th>
                                    <th class="px-3 py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="repeater_items_body">
@if(!empty(old('items')))
                    @foreach(old('items') as $__ri => $__row)
                    <tr>
                        <td class="px-3 py-1.5 text-stone-400 text-sm">{{ $__ri + 1 }}</td>
                        <td class="px-2 py-1.5"><input type="text" name="items[][]" value="{{ $__row['col'] ?? '' }}" class="w-full px-2.5 py-1.5 text-sm border border-stone-300 rounded-lg outline-none focus:border-red-700 transition"></td>
                        <td class="px-2 py-1.5"><input type="text" name="items[][]" value="{{ $__row['employee'] ?? '' }}" class="w-full px-2.5 py-1.5 text-sm border border-stone-300 rounded-lg outline-none focus:border-red-700 transition"></td>
                        <td class="px-2 py-1.5"><input type="text" name="items[][]" value="{{ $__row['emp_code'] ?? '' }}" class="w-full px-2.5 py-1.5 text-sm border border-stone-300 rounded-lg outline-none focus:border-red-700 transition"></td>
                        <td class="px-2 py-1.5"><input type="text" name="items[][]" value="{{ $__row['department'] ?? '' }}" class="w-full px-2.5 py-1.5 text-sm border border-stone-300 rounded-lg outline-none focus:border-red-700 transition"></td>
                        <td class="px-2 py-1.5 text-center"><button type="button" onclick="this.closest('tr').remove()" class="w-6 h-6 inline-flex items-center justify-center rounded bg-red-600 hover:bg-red-700 text-white text-xs font-bold">−</button></td>
                    </tr>
                    @endforeach
                    @endif
                            </tbody>
                        </table>
                        <div class="px-3 py-2 bg-stone-50 border-t border-stone-100">
                            <button type="button" @click="addRow()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-stone-800 hover:bg-stone-700 text-white text-xs font-medium transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Row
                            </button>
                        </div>
                    </div>
                    <template id="repeater_items_tpl">
                        <tr>
                            <td class="px-3 py-1.5 text-stone-400 text-sm row-num"></td>
                        <td class="px-2 py-1.5"><input type="number" name="items[__IDX__][col]" value=""  class="w-full px-2.5 py-1.5 text-sm border border-stone-300 rounded-lg outline-none focus:border-red-700 focus:ring-1 focus:ring-red-700/10 transition"></td>
                        <td class="px-2 py-1.5"><input type="text" name="items[__IDX__][employee]" value=""  class="w-full px-2.5 py-1.5 text-sm border border-stone-300 rounded-lg outline-none focus:border-red-700 focus:ring-1 focus:ring-red-700/10 transition"></td>
                        <td class="px-2 py-1.5"><input type="text" name="items[__IDX__][emp_code]" value=""  class="w-full px-2.5 py-1.5 text-sm border border-stone-300 rounded-lg outline-none focus:border-red-700 focus:ring-1 focus:ring-red-700/10 transition"></td>
                        <td class="px-2 py-1.5"><input type="text" name="items[__IDX__][department]" value=""  class="w-full px-2.5 py-1.5 text-sm border border-stone-300 rounded-lg outline-none focus:border-red-700 focus:ring-1 focus:ring-red-700/10 transition"></td>
                            <td class="px-2 py-1.5 text-center"><button type="button" onclick="this.closest('tr').remove(); window.renumberRepeater('items')" class="w-6 h-6 inline-flex items-center justify-center rounded bg-red-600 hover:bg-red-700 text-white text-xs font-bold">−</button></td>
                        </tr>
                    </template>                    @error('items')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-stone-50 border-t border-stone-100 flex items-center justify-end gap-3">
            <a href="{{ route('generated.airs.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-stone-600 bg-white border border-stone-300 hover:bg-stone-50 transition-colors">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-800 hover:bg-red-700 text-white text-sm font-medium transition-colors shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Create Record</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('repeaterField', (col) => ({
        addRow() {
            const tpl = document.getElementById('repeater_' + col + '_tpl');
            const body = document.getElementById('repeater_' + col + '_body');
            const clone = tpl.content.cloneNode(true);
            const idx = body.querySelectorAll('tr').length;
            clone.querySelectorAll('[name]').forEach(el => { el.name = el.name.replace(/__IDX__/g, idx); });
            body.appendChild(clone);
            window.renumberRepeater(col);
        }
    }));
});
window.renumberRepeater = function(col) {
    document.querySelectorAll('#repeater_' + col + '_body tr').forEach((tr, i) => {
        const num = tr.querySelector('.row-num');
        if (num) num.textContent = i + 1;
        tr.querySelectorAll('[name]').forEach(el => { el.name = el.name.replace(/\[\d+\]/g, '[' + i + ']'); });
    });
};
</script>
@endpush
