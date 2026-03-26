<?php
namespace App\Http\Controllers\Generated;
use App\Http\Controllers\Controller;
use App\Models\Generated\Air;
use App\Exports\Generated\AirExport;
use App\Models\ExportLog;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class AirController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $airs = Air::query()->with(['items'])->when($search, fn($q) => $q->where(array_key_first((new Air)->getFillable() ? array_flip((new Air)->getFillable()) : []), 'like', "%{$search}%"))->latest()->paginate(15)->withQueryString();
        $exportLogs = ExportLog::where('model', 'Air')->latest()->take(20)->get();
        return view('generated/airs.index', compact('airs', 'search', 'exportLogs'));
    }
    public function export()
    {
        $data = Air::orderBy('id')->get();
        $hash = md5($data->toJson());
        $existing = ExportLog::where('model', 'Air')->where('data_hash', $hash)->latest()->first();
        if ($existing && Storage::disk('public')->exists($existing->file_path)) {
            return Storage::disk('public')->download($existing->file_path, $existing->file_name);
        }
        $fileName = 'airs_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = 'exports/' . $fileName;
        Excel::store(new AirExport, $filePath, 'public');
        ExportLog::create(['model' => 'Air', 'file_name' => $fileName, 'file_path' => $filePath, 'row_count' => $data->count(), 'data_hash' => $hash, 'user_id' => Auth::id()]);
        return Storage::disk('public')->download($filePath, $fileName);
    }
    public function exportDownload(ExportLog $exportLog)
    {
        abort_if($exportLog->model !== 'Air', 403);
        abort_unless(Storage::disk('public')->exists($exportLog->file_path), 404);
        return Storage::disk('public')->download($exportLog->file_path, $exportLog->file_name);
    }
    public function create()
    {
        $dynamicData = [];
        $dynamicData['travel_class_options'] = \Illuminate\Support\Facades\DB::table('pages')->pluck('page_name', 'id');
        return view('generated/airs.create', $dynamicData);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'mode' => ['nullable', 'string'],
            'agent_name' => ['nullable', 'string'],
            'p_n_r_number' => ['nullable', 'string'],
            'date_of_booking' => ['nullable', 'date'],
            'journey_date' => ['nullable', 'date'],
            'air_line' => ['nullable', 'string'],
            'ticket_number' => ['nullable', 'string'],
            'journey_from' => ['nullable', 'string'],
            'journey_upto' => ['nullable', 'string'],
            'travel_class' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
        ]);
        $air = Air::create($data);
        if ($request->has('items')) {
            $air->items()->delete();
            $rows = collect($request->input('items'))->filter(function($row) {
                return !empty(array_filter($row, fn($v) => !is_null($v) && $v !== ''));
            });
            if ($rows->isNotEmpty()) $air->items()->createMany($rows->toArray());
        }
        return redirect()->route('generated.airs.index')->with('success', 'Record created.');
    }
    public function show(Air $air) { return view('generated/airs.show', compact('air')); }
    public function edit(Air $air)
    {
        $dynamicData = [];
        $dynamicData['travel_class_options'] = \Illuminate\Support\Facades\DB::table('pages')->pluck('page_name', 'id');
        return view('generated/airs.edit', array_merge(compact('air'), $dynamicData));
    }
    public function update(Request $request, Air $air)
    {
        $data = $request->validate([
            'mode' => ['nullable', 'string'],
            'agent_name' => ['nullable', 'string'],
            'p_n_r_number' => ['nullable', 'string'],
            'date_of_booking' => ['nullable', 'date'],
            'journey_date' => ['nullable', 'date'],
            'air_line' => ['nullable', 'string'],
            'ticket_number' => ['nullable', 'string'],
            'journey_from' => ['nullable', 'string'],
            'journey_upto' => ['nullable', 'string'],
            'travel_class' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
        ]);
        $air->update($data);
        if ($request->has('items')) {
            $air->items()->delete();
            $rows = collect($request->input('items'))->filter(function($row) {
                return !empty(array_filter($row, fn($v) => !is_null($v) && $v !== ''));
            });
            if ($rows->isNotEmpty()) $air->items()->createMany($rows->toArray());
        }
        return redirect()->route('generated.airs.index')->with('success', 'Record updated.');
    }
    public function destroy(Air $air)
    {
        $air->delete();
        return redirect()->route('generated.airs.index')->with('success', 'Record deleted.');
    }
}
