<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageBuilderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pages = Page::where('user_id', Auth::id())
            ->when($search, fn($q) => $q->where('page_name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('panel.master.page-builder', compact('pages', 'search'));
    }

    public function fields(Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);
        return response()->json([
            'page'   => ['id' => $page->id, 'page_name' => $page->page_name],
            'fields' => $page->fields->map(fn($f) => [
                'id'         => $f->id,
                'field_name' => $f->field_name,
                'field_type' => $f->field_type,
            ])->values(),
        ]);
    }

    public function create()
    {
        return view('panel.master.page-builder-form', ['page' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_name' => [
                'required',
                'string',
                'max:255',
                'unique:pages,page_name,NULL,id,user_id,' . Auth::id()
            ]
        ], [
            'page_name.unique' => 'A page with this name already exists.'
        ]);

        Page::create([
            'user_id'   => Auth::id(),
            'page_name' => $request->page_name,
        ]);

        return redirect()->route('master.page-builder')
                         ->with('success', 'Page created successfully.');
    }

    public function edit(Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);
        return view('panel.master.page-builder-form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);

        $request->validate([
            'page_name' => [
                'required',
                'string',
                'max:255',
                'unique:pages,page_name,' . $page->id . ',id,user_id,' . Auth::id()
            ]
        ], [
            'page_name.unique' => 'A page with this name already exists.'
        ]);

        $page->update(['page_name' => $request->page_name]);

        return redirect()->route('master.page-builder')
                         ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);

        if ($page->is_generated) {
            $this->cleanupGenerated($page->page_name);
        }

        $page->delete();

        return redirect()->route('master.page-builder')
                         ->with('success', 'Page deleted successfully.');
    }

    private function cleanupGenerated(string $pageName): void
    {
        $modelName  = \Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular($pageName));
        $routeSlug  = \Illuminate\Support\Str::slug(\Illuminate\Support\Str::plural($pageName));
        $viewFolder = resource_path("views/generated/{$routeSlug}");

        // Delete model
        $this->deleteFileIfExists(app_path("Models/Generated/{$modelName}.php"));

        // Delete controller
        $this->deleteFileIfExists(app_path("Http/Controllers/Generated/{$modelName}Controller.php"));

        // Delete export
        $this->deleteFileIfExists(app_path("Exports/Generated/{$modelName}Export.php"));

        // Delete views folder
        if (is_dir($viewFolder)) {
            array_map('unlink', glob("{$viewFolder}/*.blade.php"));
            @rmdir($viewFolder);
        }

        // Remove routes from web.php
        $routesFile = base_path('routes/web.php');
        $content    = file_get_contents($routesFile);

        // Remove use statement line
        $content = preg_replace("/^use App\\\\Http\\\\Controllers\\\\Generated\\\\{$modelName}Controller;\r?\n/m", '', $content);

        // Remove export route line
        $content = preg_replace("/^[ \t]*Route::get\('{$routeSlug}\/export'[^\n]+\r?\n/m", '', $content);

        // Remove resource route line
        $content = preg_replace("/^[ \t]*Route::resource\('{$routeSlug}'[^\n]+\r?\n/m", '', $content);

        file_put_contents($routesFile, $content);
    }

    private function deleteFileIfExists(string $path): void
    {
        if (file_exists($path)) unlink($path);
    }
}
