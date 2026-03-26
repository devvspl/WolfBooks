<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PageFieldController extends Controller
{
    public function index(Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);

        $fields = $page->fields()->orderBy('sort_order')->get();


        $dbName = DB::getDatabaseName();

        $tables = DB::table('information_schema.tables')
            ->select('TABLE_NAME')
            ->where('TABLE_SCHEMA', $dbName)
            ->whereNotIn('TABLE_NAME', [
                'migrations',
                'password_reset_tokens',
                'failed_jobs',
                'jobs',
                'cache',
                'sessions'
            ])
            ->orderBy('TABLE_NAME')
            ->pluck('TABLE_NAME')
            ->toArray();

        return view('panel.master.page-fields', compact('page', 'fields', 'tables'));
    }

    public function store(Request $request, Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);

        $request->validate([
            'field_name' => ['required', 'string', 'max:255'],
            'field_type' => ['required', 'in:title,content,number,decimal,email,phone,url,password,slug,date,datetime,time,select,radio,checkbox,toggle,color,rating,currency,image,file,json,repeater'],
        ]);

        if ($page->fields()->where('field_name', $request->field_name)->exists()) {
            return back()
                ->withErrors(['field_name_' . $request->field_type => 'Field name already exists on this page.'])
                ->withInput();
        }

        $page->fields()->create([
            'field_name' => $request->field_name,
            'field_type' => $request->field_type,
            'sort_order' => $page->fields()->count(),
            // Repeater starts with one default column
            'repeater_columns' => $request->field_type === 'repeater' ? [
                ['key' => 'item', 'label' => 'Item', 'type' => 'text', 'required' => false, 'default' => ''],
            ] : null,
        ]);

        return back()->with('success', 'Field added successfully.');
    }

    public function updateSettings(Request $request, Page $page, PageField $field)
    {
        abort_if($page->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
            'column_name' => ['nullable', 'string', 'max:255'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'default_value' => ['nullable', 'string', 'max:255'],
            'col_span' => ['nullable', 'integer', 'min:1', 'max:3'],
            'is_required' => ['nullable', 'boolean'],
            'is_unique' => ['nullable', 'boolean'],
            'is_nullable' => ['nullable', 'boolean'],
            'column_length' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $data['is_required'] = $request->boolean('is_required');
        $data['is_unique'] = $request->boolean('is_unique');
        $data['is_nullable'] = $request->boolean('is_nullable');

        if ($request->filled('options_json')) {
            $data['options'] = json_decode($request->options_json, true);
        }

        $field->update($data);

        return back()->with('success', 'Field settings saved.');
    }

    public function updateRepeaterColumns(Request $request, Page $page, PageField $field)
    {
        abort_if($page->user_id !== Auth::id(), 403);
        abort_if($field->field_type !== 'repeater', 422);

        $request->validate([
            'columns' => ['required', 'array', 'min:1'],
            'columns.*.key' => ['required', 'string', 'max:64', 'regex:/^[a-z_][a-z0-9_]*$/'],
            'columns.*.label' => ['required', 'string', 'max:255'],
            'columns.*.type' => ['required', 'in:text,number,decimal,email,date,datetime,time,select,textarea,checkbox'],
            'columns.*.required' => ['nullable', 'boolean'],
            'columns.*.default' => ['nullable', 'string', 'max:255'],
        ]);

        // Ensure keys are unique within the repeater
        $keys = array_column($request->columns, 'key');
        if (count($keys) !== count(array_unique($keys))) {
            return back()->withErrors(['columns' => 'Column keys must be unique.']);
        }

        $columns = array_map(fn($c) => [
            'key' => $c['key'],
            'label' => $c['label'],
            'type' => $c['type'],
            'required' => !empty($c['required']),
            'default' => $c['default'] ?? '',
            'options' => !empty($c['options']) ? json_decode($c['options'], true) : [],
        ], $request->columns);

        $field->update(['repeater_columns' => $columns]);

        return back()->with('success', 'Repeater columns saved.');
    }

    public function destroy(Page $page, PageField $field)
    {
        abort_if($page->user_id !== Auth::id(), 403);
        $field->delete();
        return back()->with('success', 'Field removed.');
    }

    public function reorder(Request $request, Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);

        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $position => $fieldId) {
            $page->fields()->where('id', $fieldId)->update(['sort_order' => $position]);
        }

        return response()->json(['ok' => true]);
    }

    public function getColumns(Request $request)
    {
        $table = $request->query('table');
        if (!$table || !Schema::hasTable($table)) {
            return response()->json([]);
        }
        return response()->json(Schema::getColumnListing($table));
    }
}
