<?php
namespace App\Http\Controllers\Generated;
use App\Http\Controllers\Controller;
use App\Models\Generated\GlobalRegion;
use App\Exports\Generated\GlobalRegionExport;
use App\Models\ExportLog;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
class GlobalRegionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $globalRegions = GlobalRegion::when($search, fn($q) => $q->where(array_key_first((new GlobalRegion)->getFillable() ? array_flip((new GlobalRegion)->getFillable()) : []), 'like', "%{$search}%"))->latest()->paginate(15)->withQueryString();
        $exportLogs = ExportLog::where('model', 'GlobalRegion')->latest()->take(20)->get();
        return view('generated/global-regions.index', compact('globalRegions', 'search', 'exportLogs'));
    }
    public function export()
    {
        $data = GlobalRegion::orderBy('id')->get();
        $hash = md5($data->toJson());
        $existing = ExportLog::where('model', 'GlobalRegion')->where('data_hash', $hash)->latest()->first();
        if ($existing && Storage::disk('public')->exists($existing->file_path)) {
            return Storage::disk('public')->download($existing->file_path, $existing->file_name);
        }
        $fileName = 'global-regions_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = 'exports/' . $fileName;
        Excel::store(new GlobalRegionExport, $filePath, 'public');
        ExportLog::create(['model' => 'GlobalRegion', 'file_name' => $fileName, 'file_path' => $filePath, 'row_count' => $data->count(), 'data_hash' => $hash, 'user_id' => Auth::id()]);
        return Storage::disk('public')->download($filePath, $fileName);
    }
    public function exportDownload(ExportLog $exportLog)
    {
        abort_if($exportLog->model !== 'GlobalRegion', 403);
        abort_unless(Storage::disk('public')->exists($exportLog->file_path), 404);
        return Storage::disk('public')->download($exportLog->file_path, $exportLog->file_name);
    }
    public function create() { return view('generated/global-regions.create'); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'global_region_name' => ['required', 'string', Rule::unique('gen_global_regions', 'global_region_name')],
            'global_region_code' => ['nullable', 'string'],
            'test' => ['nullable', 'string'],
        ]);
        GlobalRegion::create($data);
        return redirect()->route('generated.global-regions.index')->with('success', 'Record created.');
    }
    public function show(GlobalRegion $globalRegion) { return view('generated/global-regions.show', compact('globalRegion')); }
    public function edit(GlobalRegion $globalRegion) { return view('generated/global-regions.edit', compact('globalRegion')); }
    public function update(Request $request, GlobalRegion $globalRegion)
    {
        $data = $request->validate([
            'global_region_name' => ['required', 'string', Rule::unique('gen_global_regions', 'global_region_name')->ignore($globalRegion->id)],
            'global_region_code' => ['nullable', 'string'],
            'test' => ['nullable', 'string'],
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
