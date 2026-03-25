<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageFieldController extends Controller
{
    public function index(Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);
        $fields = $page->fields;
        return view('panel.master.page-fields', compact('page', 'fields'));
    }

    public function store(Request $request, Page $page)
    {
        abort_if($page->user_id !== Auth::id(), 403);

        $request->validate([
            'field_name' => ['required', 'string', 'max:255'],
            'field_type' => ['required', 'in:title,content,number,decimal,email,phone,url,password,slug,date,datetime,time,select,radio,checkbox,toggle,color,rating,currency,image,file,json'],
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
        ]);

        return back()->with('success', 'Field added successfully.');
    }

    public function updateSettings(Request $request, Page $page, PageField $field)
    {
        abort_if($page->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'label'         => ['nullable', 'string', 'max:255'],
            'column_name'   => ['nullable', 'string', 'max:255'],
            'placeholder'   => ['nullable', 'string', 'max:255'],
            'default_value' => ['nullable', 'string', 'max:255'],
            'is_required'   => ['nullable', 'boolean'],
            'is_unique'     => ['nullable', 'boolean'],
            'is_nullable'   => ['nullable', 'boolean'],
            'column_length' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'description'   => ['nullable', 'string', 'max:500'],
        ]);

        $data['is_required'] = $request->boolean('is_required');
        $data['is_unique']   = $request->boolean('is_unique');
        $data['is_nullable'] = $request->boolean('is_nullable');

        $field->update($data);

        return back()->with('success', 'Field settings saved.');
    }

    public function destroy(Page $page, PageField $field)
    {
        abort_if($page->user_id !== Auth::id(), 403);
        $field->delete();
        return back()->with('success', 'Field removed.');
    }
}
