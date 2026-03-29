@extends('panel.master')

@php $tab = 'page-builder'; @endphp

@section('master-content')
    {{-- ── Start main card ── --}}
    <div class="bg-white border border-stone-200 overflow-hidden">

        {{-- ── Toolbar ── --}}
        <div class="px-4 py-2.5 border-b border-stone-100 flex items-center justify-between gap-2 min-h-[48px]">
            <h3 class="text-sm font-semibold text-stone-800 shrink-0">Page Builder</h3>
            <div class="flex items-center gap-1.5 ml-auto">

                {{-- Bulk bar (hidden until rows selected) --}}
                <div id="bulk-bar" class="hidden items-center gap-1.5">
                    <span id="sel-count"
                        class="inline-flex items-center h-4 px-1 text-[9px] rounded bg-stone-100 text-stone-600">
                        2 selected
                    </span>
                    {{-- Fields — single only --}}
                    <a id="btn-fields" href="#" class="tb-btn tb-btn-fields" style="display:none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                                 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2
                                                 m-6 9h6m-6 4h4" />
                        </svg>
                        Fields
                    </a>
                    {{-- Edit — single only --}}
                    <a id="btn-edit" href="#" class="tb-btn tb-btn-edit" style="display:none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                 m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    {{-- Delete --}}
                    <button id="btn-delete" class="tb-btn tb-btn-delete">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7
                                                 m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                    <button id="btn-clear" class="tb-btn tb-btn-clear">Clear</button>
                </div>

                {{-- Add Page --}}
                <a href="{{ route('master.page-builder.create') }}" class="tb-btn tb-btn-add">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Page
                </a>

            </div>
        </div>

        {{-- ── Table area ── --}}
        <div id="dt-wrapper" class="overflow-x-auto">
            <table id="pages-table" class="w-full">
                <thead>
                    <tr>
                        <th class="dt-center" style="width:40px;">
                            <input id="check-all" type="checkbox" class="cb-input">
                        </th>
                        <th style="width:40px;">#</th>
                        <th>Page Name</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        {{-- ── Empty state ── --}}
        <div id="empty-state" class="hidden flex-col items-center justify-center py-16 text-center">
            <div class="w-12 h-12 rounded-xl bg-stone-100 flex items-center justify-center mb-3 mx-auto">
                <svg class="w-6 h-6 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                         a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p id="empty-msg" class="text-sm font-medium text-stone-600">No pages yet</p>
            <p id="empty-sub" class="text-xs text-stone-400 mt-1">Click "Add Page" to get started.</p>
        </div>

    </div>
    {{-- ── End main card ── --}}
    <script>
        $(function () {

            const CSRF = '{{ csrf_token() }}';
            const fieldsUrl = id => `/master/page-builder/${id}/fields`;
            const editUrl = id => `/master/page-builder/${id}/edit`;

            /* ── SVG icon strings ─────────────────────────────────── */
            const ICO = {
                fields: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;display:block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2 M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2 m-6 9h6m-6 4h4"/></svg>`,
                edit: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;display:block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5 m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>`,
                trash: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;display:block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7 m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`,
            };

            /* ════════════════════════════════
               DataTable init
            ════════════════════════════════ */
            const table = $('#pages-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('master.page-builder.data') }}',
                dom: '<"top"lf>t<"bottom"ip>',
                columns: [
                    {
                        data: null, orderable: false, searchable: false,
                        className: 'td-center',
                        render: (d, t, row) =>
                            `<input type="checkbox" class="cb-input row-check"
                                            data-id="${row.id}"
                                            data-fields="${fieldsUrl(row.id)}"
                                            data-edit="${editUrl(row.id)}">`,
                    },
                    { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'td-num' },
                    { data: 'page_name', className: 'td-name' },
                    { data: 'created_at', className: 'td-date' },
                    { data: 'updated_at', className: 'td-date' },
                ],
                order: [[3, 'desc']],
                pageLength: 10,
                pagingType: 'simple_numbers',
                drawCallback: function () {
                    const info = this.api().page.info();
                    const total = info.recordsTotal;
                    if (total === 0) {
                        $('#pages-table, .dataTables_wrapper .bottom').addClass('d-none').hide();
                        $('#empty-state').removeClass('hidden').addClass('flex');
                    } else {
                        $('#pages-table, .dataTables_wrapper .bottom').show();
                        $('#empty-state').removeClass('flex').addClass('hidden');
                    }
                    $('#check-all').prop({ checked: false, indeterminate: false });
                    syncBulkBar();
                },
            });

            /* ════════════════════════════════
               Checkbox logic
            ════════════════════════════════ */
            function getChecked() { return $('#pages-table tbody .row-check:checked'); }

            function syncBulkBar() {
                const $checked = getChecked();
                const n = $checked.length;
                const total = $('#pages-table tbody .row-check').length;

                $('#check-all').prop({
                    checked: n > 0 && n === total,
                    indeterminate: n > 0 && n < total,
                });

                if (n === 0) {
                    $('#bulk-bar').removeClass('flex').addClass('hidden');
                    return;
                }

                $('#bulk-bar').removeClass('hidden').addClass('flex');
                $('#sel-count').text(`${n} selected`);

                if (n === 1) {
                    const $cb = $checked.first();
                    $('#btn-fields').attr('href', $cb.data('fields')).css('display', 'inline-flex');
                    $('#btn-edit').attr('href', $cb.data('edit')).css('display', 'inline-flex');
                } else {
                    $('#btn-fields, #btn-edit').css('display', 'none');
                }
            }

            $('#check-all').on('change', function () {
                $('#pages-table tbody .row-check').prop('checked', this.checked);
                syncBulkBar();
            });

            $(document).on('change', '.row-check', syncBulkBar);

            $('#btn-clear').on('click', function () {
                $('#pages-table tbody .row-check').prop('checked', false);
                $('#check-all').prop({ checked: false, indeterminate: false });
                syncBulkBar();
            });


            /* ════════════════════════════════
               Bulk delete
            ════════════════════════════════ */
            $('#btn-delete').on('click', async function () {
                const $checked = getChecked();
                const n = $checked.length;
                if (!n) return;
                if (!confirm(`Delete ${n === 1 ? 'this page' : `these ${n} pages`}? This cannot be undone.`)) return;
                const ids = $checked.map((i, el) => $(el).data('id')).get();
                try {
                    const res = await fetch('{{ route('master.page-builder.bulk-destroy') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ ids }),
                    });
                    res.ok ? table.ajax.reload(null, false) : alert('Something went wrong. Please try again.');
                } catch { alert('Network error. Please try again.'); }
            });

        });
    </script>
@endsection