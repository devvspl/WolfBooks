<?php
namespace App\Http\Controllers\Generated;
use App\Http\Controllers\Controller;
use App\Models\Generated\GlobalRegion;
use Illuminate\Http\Request;
class GlobalRegionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $globalRegions = GlobalRegion::when($search, fn($q) => $q->where(array_key_first((new GlobalRegion)->getFillable() ? array_flip((new GlobalRegion)->getFillable()) : []), 'like', "%{$search}%"))->latest()->paginate(15)->withQueryString();
        return view('generated/global-regions.index', compact('globalRegions', 'search'));
    }
    public function create() { return view('generated/global-regions.create'); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'global_region_name' => ['nullable', 'string'],
            'global_region_code' => ['nullable', 'string'],
            'new_option' => ['nullable', 'string'],
        ]);
        GlobalRegion::create($data);
        return redirect()->route('generated.global-regions.index')->with('success', 'Record created.');
    }
    public function show(GlobalRegion $globalRegion) { return view('generated/global-regions.show', compact('globalRegion')); }
    public function edit(GlobalRegion $globalRegion) { return view('generated/global-regions.edit', compact('globalRegion')); }
    public function update(Request $request, GlobalRegion $globalRegion)
    {
        $data = $request->validate([
            'global_region_name' => ['nullable', 'string'],
            'global_region_code' => ['nullable', 'string'],
            'new_option' => ['nullable', 'string'],
        ]);
        $globalRegion->update($data);
        return redirect()->route('generated.global-regions.index')->with('success', 'Record updated.');
    }
    public function destroy(GlobalRegion $globalRegion)
    {
        $globalRegion->delete();
        return redirect()->route('generated.global-regions.index')->with('success', 'Record deleted.');
    }
}
