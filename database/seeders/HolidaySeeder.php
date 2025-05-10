<?php

namespace Database\Seeders;

use App\Models\Holiday;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $headOffice = Location::where('name', 'Head Office')->first();
        $currentYear = Carbon::now()->year;

        $holidays = [
            [
                'name' => 'New Year\'s Day',
                'date' => Carbon::create($currentYear, 1, 1),
                'location_id' => $headOffice->id,
                'description' => 'First day of the year',
                'is_recurring' => true
            ],
            [
                'name' => 'Good Friday',
                'date' => Carbon::create($currentYear, 4, 7),
                'location_id' => $headOffice->id,
                'description' => 'Christian holiday commemorating the crucifixion of Jesus',
                'is_recurring' => true
            ],
            [
                'name' => 'Easter Monday',
                'date' => Carbon::create($currentYear, 4, 10),
                'location_id' => $headOffice->id,
                'description' => 'Day after Easter Sunday',
                'is_recurring' => true
            ],
            [
                'name' => 'Labour Day',
                'date' => Carbon::create($currentYear, 5, 1),
                'location_id' => $headOffice->id,
                'description' => 'International Workers\' Day',
                'is_recurring' => true
            ],
            [
                'name' => 'Independence Day',
                'date' => Carbon::create($currentYear, 6, 12),
                'location_id' => $headOffice->id,
                'description' => 'National Independence Day',
                'is_recurring' => true
            ],
            [
                'name' => 'Christmas Day',
                'date' => Carbon::create($currentYear, 12, 25),
                'location_id' => $headOffice->id,
                'description' => 'Christian holiday celebrating the birth of Jesus',
                'is_recurring' => true
            ],
            [
                'name' => 'Boxing Day',
                'date' => Carbon::create($currentYear, 12, 26),
                'location_id' => $headOffice->id,
                'description' => 'Day after Christmas',
                'is_recurring' => true
            ]
        ];

        foreach ($holidays as $holiday) {
            Holiday::create($holiday);
        }
    }
} 