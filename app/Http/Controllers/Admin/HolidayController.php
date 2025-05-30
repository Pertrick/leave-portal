<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Holiday;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class HolidayController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Holiday::query()
            ->with('location')
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->input('year'), function ($query, $year) {
                $query->whereYear('date', $year);
            })
            ->when($request->input('location_id'), function ($query, $locationId) {
                $query->where('location_id', $locationId);
            });

        $holidays = $query->orderBy('date')
            ->paginate(10)
            ->withQueryString();

        $locations = Location::all();
        $years = range(Carbon::now()->year - 1, Carbon::now()->year + 1);

        return Inertia::render('Admin/Holidays/Index', [
            'holidays' => $holidays,
            'locations' => $locations,
            'years' => $years,
            'filters' => $request->only(['search', 'year', 'location_id']),
        ]);
    }

    public function create(): Response
    {
        $locations = Location::all();
        
        return Inertia::render('Admin/Holidays/Create', [
            'locations' => $locations
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:public,company,special',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'boolean',
        ]);

        Holiday::create($validated);

        return redirect()->back()->with('success', 'Holiday created successfully.');
    }

    public function edit(Holiday $holiday): Response
    {
        $locations = Location::all();
        
        return Inertia::render('Admin/Holidays/Edit', [
            'holiday' => $holiday,
            'locations' => $locations
        ]);
    }

    public function update(Request $request, Holiday $holiday): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:public,company,special',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'boolean',
        ]);

        $holiday->update($validated);

        return redirect()->back()->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return redirect()->back()->with('success', 'Holiday deleted successfully.');
    }

    public function toggleStatus(Holiday $holiday): RedirectResponse
    {
        $holiday->update(['is_active' => !$holiday->is_active]);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday status updated successfully.');
    }

    public function bulkCreate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'holidays' => 'required|array',
            'holidays.*.name' => 'required|string|max:255',
            'holidays.*.date' => 'required|date',
            'holidays.*.type' => 'required|in:public,company,special',
            'holidays.*.location_id' => 'nullable|exists:locations,id',
            'holidays.*.is_active' => 'boolean',
        ]);

        Holiday::insert($validated['holidays']);

        return redirect()->back()->with('success', 'Holidays created successfully.');
    }
} 