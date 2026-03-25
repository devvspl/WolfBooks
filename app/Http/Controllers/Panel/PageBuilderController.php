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
        $page->delete();

        return redirect()->route('master.page-builder')
                         ->with('success', 'Page deleted successfully.');
    }
}
