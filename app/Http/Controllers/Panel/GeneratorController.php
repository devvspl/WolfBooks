<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GeneratorController extends Controller
{
    public function generate(Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);
        $fields = $page->fields;
        if ($fields->isEmpty()) {
            return back()->with('error', 'Add at least one field before generating.');
        }
        $modelName  = Str::studly(Str::singular($page->page_name));
        $tableName  = 'gen_' . Str::snake(Str::plural($page->page_name));
        $routeSlug  = Str::slug(Str::plural($page->page_name));
        $routeBase  = 'generated.' . $routeSlug;
        $viewFolder = 'generated/' . $routeSlug;
        try {
            $this->createMigration($tableName, $fields);
            $this->createModel($modelName, $tableName, $fields);
            $this->createExport($modelName, $fields);
            $this->createController($modelName, $routeBase, $routeSlug, $viewFolder, $fields);
            $this->createViews($modelName, $routeBase, $viewFolder, $fields);
            $this->appendRoutes($modelName, $routeSlug);
            Artisan::call('migrate', ['--force' => true]);
            $page->update(['is_generated' => true]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Generation failed: ' . $e->getMessage());
        }
        return back()->with('success', "'{$page->page_name}' generated. Visit /generated/{$routeSlug} to use it.");
    }

    // ── Migration ──────────────────────────────────────────────────────────────

    private function createMigration(string $tableName, $fields): void
    {
        $timestamp = now()->format('Y_m_d_His');

        if (!Schema::hasTable($tableName)) {
            // ── Fresh create migration ────────────────────────────────────────
            $cols = '';
            foreach ($fields as $f) {
                $col   = $f->column_name ?: Str::snake($f->field_name);
                $cols .= $this->migrationColumn($f, $col);
            }
            $stub = "<?php\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\nreturn new class extends Migration {\n    public function up(): void\n    {\n        if (Schema::hasTable('{$tableName}')) return;\n        Schema::create('{$tableName}', function (Blueprint \$table) {\n            \$table->id();\n{$cols}            \$table->timestamps();\n        });\n    }\n    public function down(): void { Schema::dropIfExists('{$tableName}'); }\n};\n";
            file_put_contents(database_path("migrations/{$timestamp}_create_{$tableName}_table.php"), $stub);
            return;
        }

        // ── Alter migration for Re-Generate ──────────────────────────────────
        $existingCols = array_map(
            fn($c) => $c->Field,
            DB::select("SHOW COLUMNS FROM `{$tableName}`")
        );
        // Reserved columns we never touch
        $reserved = ['id', 'created_at', 'updated_at'];

        $fieldCols = $fields->map(fn($f) => $f->column_name ?: Str::snake($f->field_name))->toArray();

        // Columns to ADD (in fields but not in table)
        $toAdd = [];
        foreach ($fields as $f) {
            $col = $f->column_name ?: Str::snake($f->field_name);
            if (!in_array($col, $existingCols)) {
                $toAdd[] = ['field' => $f, 'col' => $col];
            }
        }

        // Columns to DROP (in table but not in fields, not reserved, and have no data)
        $toDrop = [];
        foreach ($existingCols as $col) {
            if (in_array($col, $reserved)) continue;
            if (in_array($col, $fieldCols)) continue;
            // Only drop if the column is entirely empty/null
            $hasData = DB::table($tableName)->whereNotNull($col)->where($col, '!=', '')->exists();
            if (!$hasData) {
                $toDrop[] = $col;
            }
        }

        if (empty($toAdd) && empty($toDrop)) return;

        $addLines  = '';
        $dropLines = '';
        $downAdd   = '';
        $downDrop  = '';

        foreach ($toAdd as $item) {
            $addLines .= "            " . trim($this->migrationColumn($item['field'], $item['col']));
            $downDrop .= "            \$table->dropColumn('{$item['col']}');\n";
        }
        foreach ($toDrop as $col) {
            $dropLines .= "            \$table->dropColumn('{$col}');\n";
            $downAdd   .= "            \$table->string('{$col}')->nullable();\n";
        }

        $upBody = '';
        if ($addLines || $dropLines) {
            $upBody = "        Schema::table('{$tableName}', function (Blueprint \$table) {\n{$addLines}{$dropLines}        });\n";
        }
        $downBody = "        Schema::table('{$tableName}', function (Blueprint \$table) {\n{$downAdd}{$downDrop}        });\n";

        $stub = "<?php\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\nreturn new class extends Migration {\n    public function up(): void\n    {\n{$upBody}    }\n    public function down(): void\n    {\n{$downBody}    }\n};\n";
        file_put_contents(database_path("migrations/{$timestamp}_alter_{$tableName}_table.php"), $stub);
    }

    private function migrationColumn($field, string $col): string
    {
        $nullable = $field->is_nullable ? '->nullable()' : '';
        $unique   = $field->is_unique   ? '->unique()'   : '';
        $default  = ($field->default_value !== null && $field->default_value !== '') ? "->default('" . addslashes($field->default_value) . "')" : '';
        $len      = $field->column_length ? ", {$field->column_length}" : '';
        $type = match($field->field_type) {
            'number'             => "\$table->integer('{$col}')",
            'decimal', 'currency'=> "\$table->decimal('{$col}', 15, 2)",
            'toggle', 'checkbox' => "\$table->boolean('{$col}')",
            'date'               => "\$table->date('{$col}')",
            'datetime'           => "\$table->dateTime('{$col}')",
            'time'               => "\$table->time('{$col}')",
            'rating'             => "\$table->unsignedTinyInteger('{$col}')",
            'json'               => "\$table->json('{$col}')",
            'content'            => "\$table->text('{$col}')",
            default              => "\$table->string('{$col}'{$len})",
        };
        return "            {$type}{$nullable}{$unique}{$default};\n";
    }

    // ── Model ──────────────────────────────────────────────────────────────────

    private function createModel(string $modelName, string $tableName, $fields): void
    {
        $dir = app_path('Models/Generated');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $fillable = $fields->map(fn($f) => "'" . ($f->column_name ?: Str::snake($f->field_name)) . "'")->implode(', ');
        $casts = '';
        foreach ($fields as $f) {
            $col  = $f->column_name ?: Str::snake($f->field_name);
            $cast = match($f->field_type) {
                'toggle', 'checkbox'  => "'boolean'",
                'number'              => "'integer'",
                'decimal', 'currency' => "'float'",
                'json'                => "'array'",
                default               => null,
            };
            if ($cast) $casts .= "        '{$col}' => {$cast},\n";
        }
        $castsBlock = $casts ? "    protected \$casts = [\n{$casts}    ];\n" : '';
        $stub = "<?php\nnamespace App\\Models\\Generated;\nuse Illuminate\\Database\\Eloquent\\Model;\nclass {$modelName} extends Model\n{\n    protected \$table = '{$tableName}';\n    protected \$fillable = [{$fillable}];\n{$castsBlock}}\n";
        file_put_contents("{$dir}/{$modelName}.php", $stub);
    }

    // ── Export ─────────────────────────────────────────────────────────────────

    private function createExport(string $modelName, $fields): void
    {
        $dir = app_path('Exports/Generated');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $headings = $fields->map(fn($f) => "'" . ($f->label ?: Str::headline($f->column_name ?: Str::snake($f->field_name))) . "'")->implode(', ');
        $cols     = $fields->map(fn($f) => "'" . ($f->column_name ?: Str::snake($f->field_name)) . "'")->implode(', ');
        $stub = "<?php\nnamespace App\\Exports\\Generated;\nuse App\\Models\\Generated\\{$modelName};\nuse Maatwebsite\\Excel\\Concerns\\FromCollection;\nuse Maatwebsite\\Excel\\Concerns\\WithHeadings;\nuse Maatwebsite\\Excel\\Concerns\\WithMapping;\nuse Maatwebsite\\Excel\\Concerns\\ShouldAutoSize;\nclass {$modelName}Export implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize\n{\n    public function collection()\n    {\n        return {$modelName}::all();\n    }\n    public function headings(): array\n    {\n        return [{$headings}];\n    }\n    public function map(\$row): array\n    {\n        return array_map(fn(\$col) => \$row->{\$col} ?? '', [{$cols}]);\n    }\n}\n";
        file_put_contents("{$dir}/{$modelName}Export.php", $stub);
    }

    // ── Controller ─────────────────────────────────────────────────────────────

    private function createController(string $modelName, string $routeBase, string $routeSlug, string $viewFolder, $fields): void
    {
        $dir = app_path('Http/Controllers/Generated');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $varName   = Str::camel($modelName);
        $varPlural = Str::camel(Str::plural($modelName));
        $validateLines = '';
        foreach ($fields as $f) {
            $col   = $f->column_name ?: Str::snake($f->field_name);
            $rules = $f->is_required ? ["'required'"] : ["'nullable'"];
            $validateLines .= "            '{$col}' => [{$this->fieldValidationRules($f, $rules)}],\n";
        }
        $stub = "<?php\nnamespace App\\Http\\Controllers\\Generated;\nuse App\\Http\\Controllers\\Controller;\nuse App\\Models\\Generated\\{$modelName};\nuse App\\Exports\\Generated\\{$modelName}Export;\nuse Maatwebsite\\Excel\\Facades\\Excel;\nuse Illuminate\\Http\\Request;\nclass {$modelName}Controller extends Controller\n{\n    public function index(Request \$request)\n    {\n        \$search = \$request->input('search');\n        \${$varPlural} = {$modelName}::when(\$search, fn(\$q) => \$q->where(array_key_first((new {$modelName})->getFillable() ? array_flip((new {$modelName})->getFillable()) : []), 'like', \"%{\$search}%\"))->latest()->paginate(15)->withQueryString();\n        return view('{$viewFolder}.index', compact('{$varPlural}', 'search'));\n    }\n    public function export()\n    {\n        return Excel::download(new {$modelName}Export, '{$routeSlug}.xlsx');\n    }\n    public function create() { return view('{$viewFolder}.create'); }\n    public function store(Request \$request)\n    {\n        \$data = \$request->validate([\n{$validateLines}        ]);\n        {$modelName}::create(\$data);\n        return redirect()->route('{$routeBase}.index')->with('success', 'Record created.');\n    }\n    public function show({$modelName} \${$varName}) { return view('{$viewFolder}.show', compact('{$varName}')); }\n    public function edit({$modelName} \${$varName}) { return view('{$viewFolder}.edit', compact('{$varName}')); }\n    public function update(Request \$request, {$modelName} \${$varName})\n    {\n        \$data = \$request->validate([\n{$validateLines}        ]);\n        \${$varName}->update(\$data);\n        return redirect()->route('{$routeBase}.index')->with('success', 'Record updated.');\n    }\n    public function destroy({$modelName} \${$varName})\n    {\n        \${$varName}->delete();\n        return redirect()->route('{$routeBase}.index')->with('success', 'Record deleted.');\n    }\n}\n";
        file_put_contents("{$dir}/{$modelName}Controller.php", $stub);
    }

    private function fieldValidationRules($field, array $base): string
    {
        $rules   = $base;
        $rules[] = match($field->field_type) {
            'email'              => "'email'",
            'url'                => "'url'",
            'number'             => "'integer'",
            'decimal', 'currency'=> "'numeric'",
            'toggle', 'checkbox' => "'boolean'",
            'date', 'datetime'   => "'date'",
            'rating'             => "'integer', 'min:1', 'max:5'",
            default              => "'string'",
        };
        if ($field->column_length) $rules[] = "'max:{$field->column_length}'";
        return implode(', ', $rules);
    }

    // ── Views ──────────────────────────────────────────────────────────────────

    private function createViews(string $modelName, string $routeBase, string $viewFolder, $fields): void
    {
        $dir = resource_path("views/{$viewFolder}");
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $varName   = Str::camel($modelName);
        $varPlural = Str::camel(Str::plural($modelName));
        $title     = Str::headline($modelName);
        $thCols = $tdCols = '';
        foreach ($fields as $f) {
            $col    = $f->column_name ?: Str::snake($f->field_name);
            $label  = $f->label ?: Str::headline($col);
            $thCols .= "                <th class=\"px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider\">{$label}</th>\n";
            $tdCols .= "                <td class=\"px-6 py-4 text-stone-700\">{{ \${$varName}->{$col} ?? '—' }}</td>\n";
        }
        file_put_contents("{$dir}/index.blade.php",  $this->indexView($title, $routeBase, $varPlural, $varName, $thCols, $tdCols));
        file_put_contents("{$dir}/create.blade.php", $this->formView($title, $routeBase, $varName, $fields, false));
        file_put_contents("{$dir}/edit.blade.php",   $this->formView($title, $routeBase, $varName, $fields, true));
        file_put_contents("{$dir}/show.blade.php",   $this->showView($title, $routeBase, $varName, $fields));
    }

    private function indexView(string $title, string $routeBase, string $varPlural, string $varName, string $thCols, string $tdCols): string
    {
        return "@extends('layouts.app')\n@section('content')\n<div class=\"bg-white border border-stone-200 rounded-2xl overflow-hidden\">\n    <div class=\"px-6 py-5 border-b border-stone-100 flex items-center justify-between gap-4\">\n        <div>\n            <h3 class=\"text-sm font-semibold text-stone-800\">{$title}</h3>\n            <p class=\"text-xs text-stone-400 mt-0.5\">{{ \${$varPlural}->total() }} {{ Str::plural('record', \${$varPlural}->total()) }}</p>\n        </div>\n        <div class=\"flex items-center gap-3\">\n            <form method=\"GET\" action=\"{{ route('{$routeBase}.index') }}\">\n                <div class=\"flex items-center gap-2 border border-stone-300 rounded-xl px-3 py-2 focus-within:border-red-700 focus-within:ring-2 focus-within:ring-red-700/10 transition bg-white\">\n                    <svg class=\"w-4 h-4 text-stone-400 shrink-0\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z\"/></svg>\n                    <input type=\"text\" name=\"search\" value=\"{{ \$search ?? '' }}\" placeholder=\"Search…\" autocomplete=\"off\" class=\"text-sm outline-none border-none p-0 bg-transparent text-stone-700 placeholder-stone-400 w-40\" oninput=\"clearTimeout(window._st); window._st = setTimeout(() => this.form.submit(), 400)\">\n                    @if(!empty(\$search))\n                    <a href=\"{{ route('{$routeBase}.index') }}\" class=\"text-stone-400 hover:text-stone-600 transition shrink-0\"><svg class=\"w-3.5 h-3.5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M6 18L18 6M6 6l12 12\"/></svg></a>\n                    @endif\n                </div>\n            </form>\n            <a href=\"{{ route('{$routeBase}.create') }}\" class=\"inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-800 hover:bg-red-700 text-white text-sm font-medium transition-colors shadow-sm whitespace-nowrap\">\n                <svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 4v16m8-8H4\"/></svg>\n                Add New\n            </a>\n            <a href=\"{{ route('{$routeBase}.export') }}\" class=\"inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-green-700 hover:bg-green-600 text-white text-sm font-medium transition-colors shadow-sm whitespace-nowrap\">\n                <svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4\"/></svg>\n                Export\n            </a>\n        </div>\n    </div>\n    @if(session('success'))\n    <div class=\"mx-6 mt-4 px-4 py-2.5 bg-green-50 border border-green-200 text-green-700 text-xs rounded-lg\">{{ session('success') }}</div>\n    @endif\n    @if(\${$varPlural}->isEmpty())\n    <div class=\"flex flex-col items-center justify-center py-20 text-center\">\n        <div class=\"w-14 h-14 rounded-2xl bg-stone-100 flex items-center justify-center mb-4\"><svg class=\"w-7 h-7 text-stone-400\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"1.5\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"/></svg></div>\n        <p class=\"text-sm font-medium text-stone-600\">No records yet</p>\n        <p class=\"text-xs text-stone-400 mt-1\">Click \"Add New\" to get started.</p>\n    </div>\n    @else\n    <table class=\"w-full text-sm\">\n        <thead>\n            <tr class=\"border-b border-stone-100 bg-stone-50 text-left\">\n                <th class=\"px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider w-12\">#</th>\n{$thCols}                <th class=\"px-6 py-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right\">Actions</th>\n            </tr>\n        </thead>\n        <tbody class=\"divide-y divide-stone-100\">\n            @foreach(\${$varPlural} as \$index => \${$varName})\n            <tr class=\"hover:bg-stone-50 transition-colors\">\n                <td class=\"px-6 py-4 text-stone-400\">{{ \${$varPlural}->firstItem() + \$index }}</td>\n{$tdCols}                <td class=\"px-6 py-4 text-right\">\n                    <div class=\"inline-flex items-center gap-2\">\n                        <a href=\"{{ route('{$routeBase}.show', \${$varName}) }}\" class=\"inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors\"><svg class=\"w-3.5 h-3.5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 12a3 3 0 11-6 0 3 3 0 016 0z\"/><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z\"/></svg>View</a>\n                        <a href=\"{{ route('{$routeBase}.edit', \${$varName}) }}\" class=\"inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors\"><svg class=\"w-3.5 h-3.5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z\"/></svg>Edit</a>\n                        <form method=\"POST\" action=\"{{ route('{$routeBase}.destroy', \${$varName}) }}\" onsubmit=\"return confirm('Delete this record?')\">\n                            @csrf @method('DELETE')\n                            <button type=\"submit\" class=\"inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors\"><svg class=\"w-3.5 h-3.5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\"/></svg>Delete</button>\n                        </form>\n                    </div>\n                </td>\n            </tr>\n            @endforeach\n        </tbody>\n    </table>\n    @if(\${$varPlural}->hasPages())\n    <div class=\"px-6 py-4 border-t border-stone-100 flex items-center justify-between gap-4\">\n        <p class=\"text-xs text-stone-400\">Showing {{ \${$varPlural}->firstItem() }}–{{ \${$varPlural}->lastItem() }} of {{ \${$varPlural}->total() }} results</p>\n        <div class=\"flex items-center gap-1\">\n            @if(\${$varPlural}->onFirstPage())<span class=\"inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-300 cursor-not-allowed\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 19l-7-7 7-7\"/></svg></span>@else<a href=\"{{ \${$varPlural}->previousPageUrl() }}\" class=\"inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-500 hover:bg-stone-100 transition-colors\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 19l-7-7 7-7\"/></svg></a>@endif\n            @foreach(\${$varPlural}->getUrlRange(1, \${$varPlural}->lastPage()) as \$pg => \$url)<a href=\"{{ \$url }}\" class=\"inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-medium transition-colors {{ \$pg == \${$varPlural}->currentPage() ? 'bg-red-800 text-white' : 'text-stone-600 hover:bg-stone-100' }}\">{{ \$pg }}</a>@endforeach\n            @if(\${$varPlural}->hasMorePages())<a href=\"{{ \${$varPlural}->nextPageUrl() }}\" class=\"inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-500 hover:bg-stone-100 transition-colors\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 5l7 7-7 7\"/></svg></a>@else<span class=\"inline-flex items-center justify-center w-8 h-8 rounded-lg text-stone-300 cursor-not-allowed\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 5l7 7-7 7\"/></svg></span>@endif\n        </div>\n    </div>\n    @endif\n    @endif\n</div>\n@endsection\n";
    }

    private function formView(string $title, string $routeBase, string $varName, $fields, bool $isEdit): string
    {
        $action  = $isEdit ? "route('{$routeBase}.update', \${$varName})" : "route('{$routeBase}.store')";
        $method  = $isEdit ? "@method('PUT')" : '';
        $heading = $isEdit ? "Edit {$title}" : "New {$title}";
        $subtext = $isEdit ? "Update the record details." : "Fill in the details below.";
        $btnText = $isEdit ? "Update Record" : "Create Record";
        $inputs  = '';
        foreach ($fields as $f) {
            $col         = $f->column_name ?: Str::snake($f->field_name);
            $label       = $f->label ?: Str::headline($col);
            $placeholder = $f->placeholder ?: $label;
            $oldVal      = $isEdit ? "\${$varName}->{$col}" : "old('{$col}')";
            $inputs     .= $this->formInput($f, $col, $label, $placeholder, $oldVal);
        }
        $ic = "w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10";
        return "@extends('layouts.app')\n@section('content')\n<div class=\"bg-white border border-stone-200 rounded-2xl overflow-hidden\">\n    <div class=\"px-6 py-5 border-b border-stone-100 flex items-center justify-between\">\n        <div>\n            <h3 class=\"text-sm font-semibold text-stone-800\">{$heading}</h3>\n            <p class=\"text-xs text-stone-400 mt-0.5\">{$subtext}</p>\n        </div>\n        <a href=\"{{ route('{$routeBase}.index') }}\" class=\"inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors\"><svg class=\"w-3.5 h-3.5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 19l-7-7 7-7\"/></svg>Back</a>\n    </div>\n    <form method=\"POST\" action=\"{{ {$action} }}\" enctype=\"multipart/form-data\">\n        @csrf {$method}\n        <div class=\"p-6\">\n            @if(\$errors->any())\n            <div class=\"mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl\">Please fix the errors below.</div>\n            @endif\n            <div class=\"grid grid-cols-1 sm:grid-cols-2 gap-5\">\n{$inputs}            </div>\n        </div>\n        <div class=\"px-6 py-4 bg-stone-50 border-t border-stone-100 flex items-center justify-end gap-3\">\n            <a href=\"{{ route('{$routeBase}.index') }}\" class=\"px-4 py-2.5 rounded-xl text-sm font-medium text-stone-600 bg-white border border-stone-300 hover:bg-stone-50 transition-colors\">Cancel</a>\n            <button type=\"submit\" class=\"inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-800 hover:bg-red-700 text-white text-sm font-medium transition-colors shadow-sm\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"/></svg>{$btnText}</button>\n        </div>\n    </form>\n</div>\n@endsection\n";
    }

    private function formInput($field, string $col, string $label, string $placeholder, string $oldVal): string
    {
        $req = $field->is_required ? ' <span class="text-red-500">*</span>' : '';
        $ic  = "w-full px-3.5 py-2.5 text-sm border rounded-xl outline-none transition border-stone-300 focus:border-red-700 focus:ring-2 focus:ring-red-700/10 @error('{$col}') border-red-400 bg-red-50 @enderror";
        $err = "                    @error('{$col}')<p class=\"mt-1.5 text-xs text-red-600\">{{ \$message }}</p>@enderror\n";
        $base = "                <div>\n                    <label class=\"block text-sm font-medium text-stone-700 mb-1.5\">{$label}{$req}</label>\n";
        $input = match($field->field_type) {
            'content', 'json' =>
                "                    <textarea name=\"{$col}\" rows=\"4\" placeholder=\"{$placeholder}\" class=\"{$ic} resize-none\">{{ {$oldVal} }}</textarea>\n",
            'checkbox', 'toggle' =>
                "                    <div class=\"flex items-center gap-2 mt-1\"><input type=\"checkbox\" name=\"{$col}\" value=\"1\" id=\"{$col}\" {{ {$oldVal} ? 'checked' : '' }} class=\"w-4 h-4 rounded border-stone-300 text-red-700 focus:ring-red-700\"><label for=\"{$col}\" class=\"text-sm text-stone-600\">{$label}</label></div>\n",
            'select' =>
                "                    <select name=\"{$col}\" class=\"{$ic}\"><option value=\"\">-- Select --</option></select>\n",
            'color' =>
                "                    <input type=\"color\" name=\"{$col}\" value=\"{{ {$oldVal} ?? '#000000' }}\" class=\"h-10 w-20 rounded-xl border border-stone-300 cursor-pointer\">\n",
            'image', 'file' =>
                "                    <input type=\"file\" name=\"{$col}\" class=\"w-full text-sm text-stone-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-red-800 file:text-white file:text-xs file:font-medium\">\n",
            default =>
                "                    <input type=\"{$this->htmlInputType($field->field_type)}\" name=\"{$col}\" value=\"{{ {$oldVal} }}\" placeholder=\"{$placeholder}\" class=\"{$ic}\">\n",
        };
        return $base . $input . $err . "                </div>\n";
    }

    private function htmlInputType(string $type): string
    {
        return match($type) {
            'number', 'rating', 'currency', 'decimal' => 'number',
            'email' => 'email', 'phone' => 'tel', 'url' => 'url',
            'password' => 'password', 'date' => 'date',
            'datetime' => 'datetime-local', 'time' => 'time',
            default => 'text',
        };
    }

    private function showView(string $title, string $routeBase, string $varName, $fields): string
    {
        $inputs = '';
        foreach ($fields as $f) {
            $col   = $f->column_name ?: Str::snake($f->field_name);
            $label = $f->label ?: Str::headline($col);
            $inputs .= "            <div>\n                <label class=\"block text-sm font-medium text-stone-700 mb-1.5\">{$label}</label>\n                <input type=\"text\" disabled value=\"{{ \${$varName}->{$col} ?? '—' }}\" class=\"w-full px-3.5 py-2.5 text-sm border rounded-xl border-stone-200 bg-stone-50 text-stone-600 cursor-not-allowed\">\n            </div>\n";
        }
        return "@extends('layouts.app')\n@section('content')\n<div class=\"bg-white border border-stone-200 rounded-2xl overflow-hidden\">\n    <div class=\"px-6 py-5 border-b border-stone-100 flex items-center justify-between\">\n        <div>\n            <h3 class=\"text-sm font-semibold text-stone-800\">{$title} — Detail</h3>\n            <p class=\"text-xs text-stone-400 mt-0.5\">Record #{{ \${$varName}->id }}</p>\n        </div>\n        <a href=\"{{ route('{$routeBase}.index') }}\" class=\"inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-stone-100 text-stone-600 hover:bg-stone-200 transition-colors\"><svg class=\"w-3.5 h-3.5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15 19l-7-7 7-7\"/></svg>Back</a>\n    </div>\n    <div class=\"p-6\">\n        <div class=\"grid grid-cols-1 sm:grid-cols-2 gap-5\">\n{$inputs}        </div>\n    </div>\n    <div class=\"px-6 py-4 bg-stone-50 border-t border-stone-100 flex items-center justify-end\">\n        <a href=\"{{ route('{$routeBase}.edit', \${$varName}) }}\" class=\"inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-800 hover:bg-red-700 text-white text-sm font-medium transition-colors shadow-sm\"><svg class=\"w-4 h-4\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z\"/></svg>Edit</a>\n    </div>\n</div>\n@endsection\n";
    }

    // ── Routes ─────────────────────────────────────────────────────────────────

    private function appendRoutes(string $modelName, string $routeSlug): void
    {
        $routesFile   = base_path('routes/web.php');
        $content      = file_get_contents($routesFile);
        $useStatement = "use App\\Http\\Controllers\\Generated\\{$modelName}Controller;";
        if (!str_contains($content, $useStatement)) {
            $content = str_replace(
                "use Illuminate\\Support\\Facades\\Route;",
                "use Illuminate\\Support\\Facades\\Route;\n{$useStatement}",
                $content
            );
        }
        $resourceLine = "        Route::resource('{$routeSlug}', {$modelName}Controller::class);\n        Route::get('{$routeSlug}/export', [{$modelName}Controller::class, 'export'])->name('{$routeSlug}.export');";
        if (!str_contains($content, $resourceLine)) {
            if (str_contains($content, "Route::prefix('generated')")) {
                $content = preg_replace(
                    "/(Route::prefix\('generated'\)[^\{]*\{[^\}]*)(}\);)/s",
                    "$1    {$resourceLine}\n    $2",
                    $content
                );
            } else {
                $group = "\n    // Generated CRUD routes\n    Route::prefix('generated')->name('generated.')->group(function () {\n        // {$modelName}\n        {$resourceLine}\n    });\n";
                $content = str_replace(
                    "    // ── Generated CRUD routes (auto-appended by GeneratorController) ──────────\n});",
                    "    // ── Generated CRUD routes (auto-appended by GeneratorController) ──────────\n{$group}});",
                    $content
                );
            }
        }
        file_put_contents($routesFile, $content);
    }
}
