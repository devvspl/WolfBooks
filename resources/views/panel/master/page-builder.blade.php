@extends('panel.master')

@php $tab = 'page-builder'; @endphp

@section('master-content')
@php
$fieldTypes = [
    'title'    => ['Title',       'Short Text',               'M4 6h16M4 10h16M4 14h8'],
    'content'  => ['Content',     'Long text for description', 'M4 6h16M4 10h16M4 14h16M4 18h12'],
    'number'   => ['Numbers',     'Integer numbers',           'M7 20l4-16m2 16l4-16M6 9h14M4 15h14'],
    'decimal'  => ['Decimal',     'Decimal numbers',           'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    'datetime' => ['Date & Time', 'Date & Time with calendar', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
    'time'     => ['Time',        'Time picker',               'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    'checkbox' => ['Checkbox',    'Single checkbox',           'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    'email'    => ['Email',       'Email input',               'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
    'image'    => ['Image',       'Image upload',              'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
    'file'     => ['File',        'Upload documents & files',  'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13'],
    'select'   => ['Select',      'Dropdown options',          'M8 9l4-4 4 4m0 6l-4 4-4-4'],
];
@endphp

<div x-data="{
    open: false,
    loading: false,
    page: null,
    fields: [],
    fieldInputs: {},
    fieldErrors: {},
    csrfToken: '{{ csrf_token() }}',

    openCanvas(pageId, pageName) {
        this.page = { id: pageId, page_name: pageName };
        this.fields = [];
        this.fieldInputs = {};
        this.fieldErrors = {};
        this.open = true;
        this.loadFields();
    },

    async loadFields() {
        this.loading = true;
        const res = await fetch(`/master/page-builder/${this.page.id}/fields/json`);
        const data = await res.json();
        this.fields = data.fields;
        this.loading = false;
    },

    async addField(type) {
        const name = (this.fieldInputs[type] || '').trim();
        this.fieldErrors[type] = '';
        if (!name) { this.fieldErrors[type] = 'Field name is required.'; return; }

        const res = await fetch(`/master/page-builder/${this.page.id}/fields`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ field_name: name, field_type: type }),
        });
        const data = await res.json();
        if (!res.ok) {
            this.fieldErrors[type] = data.errors?.field_name?.[0] ?? 'Error adding field.';
        } else {
            this.fieldInputs[type] = '';
            this.fields.push(data.field);
        }
    },

    async removeField(fieldId) {
        if (!confirm('Remove this field?')) return;
        await fetch(`/master/page-builder/${this.page.id}/fields/${fieldId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
        });
        this.fields = this.fields.filter(f => f.id !== fieldId);
    },
}">

{{-- Page table --}}
<div class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
    <div class="px-6 py-5 border-b border-stone-100 flex items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-semibold text-stone-800">Page Builder</h3>
            <p class="text-xs text-stone-400 mt-0.5">{{ $pages->total() }} {{ Str::plural('page', $pages->total()) }}</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('master.page-builder') }}">
                <div class="flex items-center gap-2 border border-stone-300 rounded-xl px-3 py-2
                            focus-within:border-red-700 focus-within:ring-2 focus-within:ring-red-700/10 transition bg-white">
                    <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search pages…"
                           autocomplete="off"
                           class="text-sm outline-none border-none p-0 bg-transparent text-stone-700 placeholder-stone-400 w-40"
                           oninput="clearTimeout(window._st); window._st = setTimeout(() => this.form.submit(), 400)">
                    @if($search)
                    <a href="{{ route('master.page-builder') }}" class="text-stone-400 hover:text-stone-600 transition shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </form>
            <a href="{{ route('master.page-builder.create') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-800 hover:bg-red-700
                      text-white text-sm font-medium transition-colors shadow-sm whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Page
            </a>
        </div>
    </div>

    @if($pages->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-14 h-14 rounded-2xl bg-stone-100 flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-stone-600">{{ $search ? 'No results for "'.$search.'"' : 'No pages yet' }}</p>
        <p class="text-xs text-stone-400 mt-1">{{ $search ? 'Try a different search term.' : 'Click "New Page" to get started.' }}</p>
    </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-stone-100 bg-stone-50 text-left">
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider w-12">#</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Page Name</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Updated</th>
                <th class="px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-100">
            @foreach($pages as $index => $page)
            <tr class="hover:bg-stone-50 transition-colors">
                <td class="px-6 py-4 text-stone-400">{{ $pages->firstItem() + $index }}</td>
                <td class="px-6 py-4 font-medium text-stone-800">{{ $page->page_name }}</td>
                <td class="px-6 py-4 text-stone-400">{{ $page->created_at->format('d M Y, h:i A') }}</td>
                <td class="px-6 py-4 text-stone-400">{{ $page->updated_at->format('d M Y, h:i A') }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="inline-flex items-center gap-2">
                        <a href="{{ route('master.page-builder.fields', $page) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium
                                  bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                            </svg>
                            Fields
                        </a>
                        <a href="{{ route('master.page-builder.edit', $page) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium
                                  bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('master.page-builder.destroy', $page) }}"
                              onsubmit="return confirm('Delete this page?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium
                                           bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($pages->hasPages())
    <div class="px-6 py-4 border-t border-stone-100 flex items-center justify-between gap-4">
        <p class="text-xs text-stone-400">
            Showing {{ $pages->firstItem() }}–{{ $pages->lastItem() }} of {{ $pages->total() }} results
        </p>
        <div class="flex items-center gap-1">
            @if($pages->onFirstPage())
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-300 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </span>
            @else
            <a href="{{ $pages->previousPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-500 hover:bg-stone-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            @endif
            @foreach($pages->getUrlRange(1, $pages->lastPage()) as $pg => $url)
            <a href="{{ $url }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-medium transition-colors {{ $pg == $pages->currentPage() ? 'bg-red-800 text-white' : 'text-stone-600 hover:bg-stone-100' }}">{{ $pg }}</a>
            @endforeach
            @if($pages->hasMorePages())
            <a href="{{ $pages->nextPageUrl() }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-500 hover:bg-stone-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @else
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-300 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

{{-- Offcanvas overlay --}}
<div x-show="open"
     x-transition:enter="transition-opacity duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/40 z-40"
     @click="open = false"></div>

{{-- Offcanvas panel --}}
<div x-show="open"
     x-transition:enter="transition-transform duration-300 ease-out"
     x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
     x-transition:leave="transition-transform duration-200 ease-in"
     x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
     class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50 flex flex-col">

    {{-- Canvas header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-stone-200 shrink-0">
        <div>
            <h3 class="text-sm font-semibold text-stone-800" x-text="page?.page_name + ' — Fields'"></h3>
            <p class="text-xs text-stone-400 mt-0.5" x-text="fields.length + ' field' + (fields.length !== 1 ? 's' : '') + ' added'"></p>
        </div>
        <button @click="open = false"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-stone-400 hover:bg-stone-100 hover:text-stone-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Added fields list --}}
    <div class="shrink-0 border-b border-stone-100">
        <template x-if="loading">
            <div class="flex items-center justify-center py-6">
                <svg class="w-5 h-5 text-stone-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
            </div>
        </template>
        <template x-if="!loading && fields.length === 0">
            <p class="text-xs text-stone-400 text-center py-5">No fields yet. Add from below.</p>
        </template>
        <template x-if="!loading && fields.length > 0">
            <ul class="divide-y divide-stone-100 max-h-48 overflow-y-auto">
                <template x-for="field in fields" :key="field.id">
                    <li class="flex items-center gap-3 px-5 py-2.5">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-stone-800 truncate" x-text="field.field_name"></p>
                            <p class="text-xs text-stone-400 capitalize" x-text="field.field_type"></p>
                        </div>
                        <button @click="removeField(field.id)"
                                class="w-7 h-7 flex items-center justify-center rounded-lg text-stone-400
                                       hover:bg-red-50 hover:text-red-500 transition-colors shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </li>
                </template>
            </ul>
        </template>
    </div>

    {{-- Field type grid --}}
    <div class="flex-1 overflow-y-auto p-4">
        <p class="text-xs font-semibold text-stone-500 uppercase tracking-wider mb-3">Add New Field</p>
        <div class="grid grid-cols-2 gap-3">
            @foreach($fieldTypes as $type => [$label, $desc, $icon])
            <div class="border border-stone-200 rounded-xl p-3 hover:border-stone-300 transition-colors">
                <div class="flex items-center gap-2 mb-2.5">
                    <div class="w-7 h-7 rounded-lg bg-stone-100 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-stone-700 leading-tight">{{ $label }}</p>
                        <p class="text-[10px] text-stone-400 leading-tight truncate">{{ $desc }}</p>
                    </div>
                </div>
                <div class="flex gap-1.5">
                    <input type="text"
                           x-model="fieldInputs['{{ $type }}']"
                           @keydown.enter.prevent="addField('{{ $type }}')"
                           placeholder="Field name"
                           class="flex-1 min-w-0 px-2.5 py-1.5 text-xs border border-stone-300 rounded-lg outline-none
                                  focus:border-red-700 focus:ring-1 focus:ring-red-700/10 transition">
                    <button @click="addField('{{ $type }}')"
                            class="px-2.5 py-1.5 rounded-lg bg-stone-800 hover:bg-stone-700 text-white text-xs font-medium transition-colors shrink-0">
                        Add
                    </button>
                </div>
                <p x-show="fieldErrors['{{ $type }}']" x-text="fieldErrors['{{ $type }}']"
                   class="mt-1 text-[10px] text-red-600"></p>
            </div>
            @endforeach
        </div>
    </div>
</div>

</div>
@endsection
