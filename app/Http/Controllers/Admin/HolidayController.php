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
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->location_id, function ($query, $locationId) {
                $query->where('location_id', $locationId);
            })
            ->when($request->year, function ($query, $year) {
                $query->whereYear('date', $year);
            })
            ->when($request->is_active !== null, function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            });

        $holidays = $query->latest()->get();
        $locations = Location::all();

        return Inertia::render('Admin/Holidays/Index', [
            'holidays' => $holidays,
            'locations' => $locations,
            'filters' => $request->only(['type', 'location_id', 'year', 'is_active'])
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
            'type' => 'required|in:public,company,location',
            'location_id' => 'required_if:type,location|exists:locations,id',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean',
            'recurrence_type' => 'required_if:is_recurring,true|in:fixed,easter,custom',
            'recurrence_day' => 'required_if:recurrence_type,fixed|integer|min:1|max:31',
            'recurrence_month' => 'required_if:recurrence_type,fixed|integer|min:1|max:12',
            'easter_offset' => 'required_if:recurrence_type,easter|integer',
            'custom_rule' => 'required_if:recurrence_type,custom|array',
            'date' => 'required_unless:is_recurring,true|date'
        ]);

        Holiday::create($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday created successfully.');
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
            'type' => 'required|in:public,company,location',
            'location_id' => 'required_if:type,location|exists:locations,id',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean',
            'recurrence_type' => 'required_if:is_recurring,true|in:fixed,easter,custom',
            'recurrence_day' => 'required_if:recurrence_type,fixed|integer|min:1|max:31',
            'recurrence_month' => 'required_if:recurrence_type,fixed|integer|min:1|max:12',
            'easter_offset' => 'required_if:recurrence_type,easter|integer',
            'custom_rule' => 'required_if:recurrence_type,custom|array',
            'date' => 'required_unless:is_recurring,true|date'
        ]);

        $holiday->update($validated);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday deleted successfully.');
    }

    public function toggleStatus(Holiday $holiday): RedirectResponse
    {
        $holiday->update(['is_active' => !$holiday->is_active]);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday status updated successfully.');
    }
} 