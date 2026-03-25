@extends('panel.master')

@php $tab = 'page-builder'; @endphp

@section('master-content')
@php
$fieldTypes = [
    'title'    => ['Title',       'Short text input',          'M4 6h16M4 10h16M4 14h8'],
    'content'  => ['Content',     'Long text / textarea',      'M4 6h16M4 10h16M4 14h16M4 18h12'],
    'number'   => ['Number',      'Integer numbers',           'M7 20l4-16m2 16l4-16M6 9h14M4 15h14'],
    'decimal'  => ['Decimal',     'Decimal / float numbers',   'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    'email'    => ['Email',       'Email address input',       'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
    'phone'    => ['Phone',       'Phone number input',        'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
    'url'      => ['URL',         'Website / link input',      'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
    'password' => ['Password',    'Masked password field',     'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
    'slug'     => ['Slug',        'URL-friendly identifier',   'M7 20l4-16m2 16l4-16M6 9h14M4 15h14'],
    'date'     => ['Date',        'Date picker only',          'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
    'datetime' => ['Date & Time', 'Date & time combined',      'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    'time'     => ['Time',        'Time picker only',          'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    'select'   => ['Select',      'Dropdown options',          'M8 9l4-4 4 4m0 6l-4 4-4-4'],
    'radio'    => ['Radio',       'Single choice options',     'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    'checkbox' => ['Checkbox',    'Single on/off toggle',      'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    'toggle'   => ['Toggle',      'Boolean switch',            'M8 9l4-4 4 4m0 6l-4 4-4-4'],
    'color'    => ['Color',       'Color picker input',        'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
    'rating'   => ['Rating',      'Star rating field',         'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
    'currency' => ['Currency',    'Money / price field',       'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    'image'    => ['Image',       'Image upload field',        'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
    'file'     => ['File',        'Upload documents & files',  'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13'],
    'json'     => ['JSON',        'Raw JSON / object data',    'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
];
$inputTypes = [
    'title'    => 'text',     'content'  => 'textarea',        'number'   => 'number',
    'decimal'  => 'number',   'email'    => 'email',           'phone'    => 'tel',
    'url'      => 'url',      'password' => 'password',        'slug'     => 'text',
    'date'     => 'date',     'datetime' => 'datetime-local',  'time'     => 'time',
    'select'   => 'select',   'radio'    => 'radio',           'checkbox' => 'checkbox',
    'toggle'   => 'checkbox', 'color'    => 'color',           'rating'   => 'number',
    'currency' => 'number',   'image'    => 'file',            'file'     => 'file',
    'json'     => 'textarea',
];
@endphp

<div x-data="{ builderOpen: false, settingsOpen: false, activeField: null, builderSearch: '' }">

    {{-- Form preview card --}}
    <div class="bg-white border border-stone-200 rounded-2xl overflow-hidden">
        <div class="px-6 py-5 border-b border-stone-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-stone-800">{{ $page->page_name }}</h3>
                <p class="text-xs text-stone-400 mt-0.5">{{ $fields->count() }} {{ Str::plural('field', $fields->count()) }} added</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="builderOpen = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                               bg-red-800 hover:bg-red-700 text-white transition-colors shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    Form Builder
                </button>

                @if(!$fields->isEmpty())
                <form method="POST" action="{{ route('master.page-builder.generate', $page) }}"
                      onsubmit="return confirm('Generate migration, model, controller and views for \'{{ $page->page_name }}\'?')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                                   {{ $page->is_generated ? 'bg-stone-500 hover:bg-stone-600' : 'bg-green-700 hover:bg-green-600' }}
                                   text-white transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        {{ $page->is_generated ? 'Re-Generate' : 'Generate Form' }}
                    </button>
                </form>
                @endif

                <a href="{{ route('master.page-builder') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
                          bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
            </div>
        </div>

        @if($fields->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-12 h-12 rounded-xl bg-stone-100 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h8m-8 6h16"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-stone-500">No fields yet</p>
            <p class="text-xs text-stone-400 mt-1">Click "Form Builder" to add fields.</p>
        </div>
        @else
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                @foreach($fields as $field)
                @php
                $fieldData = [
                    'id'            => $field->id,
                    'field_name'    => $field->field_name,
                    'label'         => $field->label ?? $field->field_name,
                    'column_name'   => $field->column_name ?? '',
                    'placeholder'   => $field->placeholder ?? '',
                    'default_value' => $field->default_value ?? '',
                    'is_required'   => $field->is_required ? 'true' : 'false',
                    'is_unique'     => $field->is_unique ? 'true' : 'false',
                    'is_nullable'   => $field->is_nullable ? 'true' : 'false',
                    'column_length' => $field->column_length ?? '',
                    'description'   => $field->description ?? '',
                    'settings_url'  => route('master.page-builder.fields.settings', [$page, $field]),
                    'destroy_url'   => route('master.page-builder.fields.destroy', [$page, $field]),
                ];
                @endphp
                <div class="group relative border border-stone-200 rounded-xl p-3 hover:border-stone-300 transition-colors cursor-pointer"
                     @click="activeField = {{ json_encode($fieldData) }}; settingsOpen = true">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-stone-700">{{ $field->label ?? $field->field_name }}</span>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                            <button type="button"
                                    @click="activeField = {{ json_encode($fieldData) }}; settingsOpen = true"
                                    class="w-6 h-6 flex items-center justify-center rounded text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('master.page-builder.fields.destroy', [$page, $field]) }}"
                                  onsubmit="return confirm('Remove this field?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-6 h-6 flex items-center justify-center rounded text-stone-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($field->field_type === 'content')
                        <textarea disabled placeholder="{{ $field->placeholder ?? $field->field_name }}"
                                  class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg bg-stone-50
                                         text-stone-400 cursor-not-allowed resize-none h-16 pointer-events-none"></textarea>
                    @elseif($field->field_type === 'checkbox')
                        <div class="flex items-center gap-2 px-3 py-2 border border-stone-200 rounded-lg bg-stone-50 pointer-events-none">
                            <input type="checkbox" disabled class="w-4 h-4 rounded border-stone-300 cursor-not-allowed">
                            <span class="text-sm text-stone-400">{{ $field->placeholder ?? $field->field_name }}</span>
                        </div>
                    @elseif($field->field_type === 'select')
                        <select disabled class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg bg-stone-50 text-stone-400 cursor-not-allowed pointer-events-none">
                            <option>{{ $field->placeholder ?? $field->field_name }}</option>
                        </select>
                    @elseif(in_array($field->field_type, ['image', 'file']))
                        <div class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg bg-stone-50
                                    text-stone-400 flex items-center gap-2 pointer-events-none">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $fieldTypes[$field->field_type][2] }}"/>
                            </svg>
                            <span>Choose {{ $field->field_type === 'image' ? 'image' : 'file' }}</span>
                        </div>
                    @else
                        <input type="{{ $inputTypes[$field->field_type] ?? 'text' }}" disabled
                               placeholder="{{ $field->placeholder ?? $field->field_name }}"
                               class="w-full px-3 py-2 text-sm border border-stone-200 rounded-lg bg-stone-50
                                      text-stone-400 cursor-not-allowed pointer-events-none">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── FORM BUILDER offcanvas ── --}}
    <div x-show="builderOpen"
         x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="builderOpen = false"
         style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9998"></div>

    <div x-show="builderOpen"
         x-transition:enter="transition-transform duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         style="position:fixed;top:0;right:0;height:100%;width:100%;max-width:24rem;z-index:9999"
         class="bg-stone-900 shadow-2xl flex flex-col">
        <div class="px-5 py-4 border-b border-white/10 shrink-0">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-white">Form Builder</h3>
                <button @click="builderOpen = false; builderSearch = ''" class="w-7 h-7 flex items-center justify-center rounded-lg text-stone-400 hover:bg-white/10 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-stone-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="builderSearch" placeholder="Search field types..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs bg-white/10 border border-white/20 rounded-lg
                              outline-none text-white placeholder-stone-500 focus:border-red-500 transition">
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-3">
            <div class="flex flex-col gap-1.5">
                @foreach($fieldTypes as $type => [$label, $desc, $icon])
                <form method="POST" action="{{ route('master.page-builder.fields.store', $page) }}">
                    @csrf
                    <input type="hidden" name="field_type" value="{{ $type }}">
                    <div class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 hover:bg-white/10 transition-colors"
                         x-show="builderSearch === '' || '{{ strtolower($label) }}'.includes(builderSearch.toLowerCase())">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                            </svg>
                            <span class="text-xs font-medium text-stone-300 w-20 shrink-0">{{ $label }}</span>
                            <input type="text" name="field_name" placeholder="Field name"
                                   class="flex-1 min-w-0 px-2 py-1 text-xs bg-white/10 border border-white/20
                                          rounded-md outline-none text-white placeholder-stone-600
                                          focus:border-red-500 focus:ring-1 focus:ring-red-700/10 transition">
                            <button type="submit" class="px-2.5 py-1 rounded-md bg-red-800 hover:bg-red-700 text-white text-xs font-semibold transition-colors shrink-0">Add</button>
                        </div>
                        @error('field_name_' . $type)
                            <p class="mt-1 text-[10px] text-red-400 pl-5">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── FIELD SETTINGS offcanvas ── --}}
    <div x-show="settingsOpen"
         x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="settingsOpen = false"
         style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9998"></div>

    <div x-show="settingsOpen"
         x-transition:enter="transition-transform duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         style="position:fixed;top:0;right:0;height:100%;width:100%;max-width:26rem;z-index:9999"
         class="bg-stone-900 shadow-2xl flex flex-col">

        <div class="flex items-center justify-between px-5 py-4 border-b border-white/10 shrink-0">
            <div>
                <p class="text-[10px] text-stone-500 uppercase tracking-wider">Settings For</p>
                <h3 class="text-sm font-semibold text-white" x-text="activeField?.field_name"></h3>
            </div>
            <button @click="settingsOpen = false" class="w-7 h-7 flex items-center justify-center rounded-lg text-stone-400 hover:bg-white/10 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5">
            <template x-if="activeField">
                <form method="POST" :action="activeField.settings_url" class="space-y-5">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-stone-400 mb-1.5">Label Name</label>
                            <input type="text" name="label" :value="activeField.label"
                                   class="w-full px-3 py-2 text-sm bg-white/10 border border-white/20 rounded-lg
                                          outline-none text-white placeholder-stone-500 focus:border-red-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-stone-400 mb-1.5">Column Name</label>
                            <input type="text" name="column_name" :value="activeField.column_name"
                                   class="w-full px-3 py-2 text-sm bg-white/10 border border-white/20 rounded-lg
                                          outline-none text-white placeholder-stone-500 focus:border-red-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-stone-400 mb-1.5">Placeholder</label>
                            <input type="text" name="placeholder" :value="activeField.placeholder"
                                   class="w-full px-3 py-2 text-sm bg-white/10 border border-white/20 rounded-lg
                                          outline-none text-white placeholder-stone-500 focus:border-red-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-stone-400 mb-1.5">Default Value</label>
                            <input type="text" name="default_value" :value="activeField.default_value"
                                   class="w-full px-3 py-2 text-sm bg-white/10 border border-white/20 rounded-lg
                                          outline-none text-white placeholder-stone-500 focus:border-red-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-stone-400 mb-2">Validation</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_required" value="1"
                                       :checked="activeField.is_required === 'true'"
                                       class="w-4 h-4 rounded border-white/20 bg-white/10 text-red-700 focus:ring-red-700 cursor-pointer">
                                <span class="text-sm text-stone-300">Required</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_unique" value="1"
                                       :checked="activeField.is_unique === 'true'"
                                       class="w-4 h-4 rounded border-white/20 bg-white/10 text-red-700 focus:ring-red-700 cursor-pointer">
                                <span class="text-sm text-stone-300">Unique</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_nullable" value="1"
                                       :checked="activeField.is_nullable === 'true'"
                                       class="w-4 h-4 rounded border-white/20 bg-white/10 text-red-700 focus:ring-red-700 cursor-pointer">
                                <span class="text-sm text-stone-300">Nullable</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-stone-400 mb-1.5">Column Length</label>
                        <input type="number" name="column_length" :value="activeField.column_length" min="1" max="65535"
                               class="w-full px-3 py-2 text-sm bg-white/10 border border-white/20 rounded-lg
                                      outline-none text-white placeholder-stone-500 focus:border-red-500 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-stone-400 mb-1.5">Description (Help Text)</label>
                        <textarea name="description" rows="3" :value="activeField.description"
                                  class="w-full px-3 py-2 text-sm bg-white/10 border border-white/20 rounded-lg
                                         outline-none text-white placeholder-stone-500 focus:border-red-500 transition resize-none"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-red-800 hover:bg-red-700 text-white
                                   text-sm font-semibold transition-colors">
                        Save Changes
                    </button>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection

