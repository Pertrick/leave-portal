<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function getHolidays(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        // $locationId = Auth::user()->location_id;

        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])
            ->where('is_active', true)
            ->where(function ($query){
                $query->WhereNull('location_id');
            })
            ->get(['name', 'date', 'type']);

        return response()->json($holidays);
    }

    public function getHolidaysInRange(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Holiday::query();

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        // Order by date
        $query->where('is_active', true)->orderBy('date');

        $holidays = $query->get(['name', 'date']);

        return response()->json($holidays);
    }
}
