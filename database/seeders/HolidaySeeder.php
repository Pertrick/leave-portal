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
        $agbara = Location::where('name', 'Agbara')->first();
        $currentYear = Carbon::now()->year;

        // Fixed Date Holidays (same date every year)
        $fixedHolidays = [
            [
                'name' => 'New Year\'s Day',
                'type' => 'public',
                'description' => 'First day of the year',
                'is_recurring' => true,
                'recurrence_type' => 'fixed',
                'recurrence_day' => 1,
                'recurrence_month' => 1
            ],
            [
                'name' => 'Labour Day',
                'type' => 'public',
                'description' => 'International Workers\' Day',
                'is_recurring' => true,
                'recurrence_type' => 'fixed',
                'recurrence_day' => 1,
                'recurrence_month' => 5
            ],
            [
                'name' => 'Independence Day',
                'type' => 'public',
                'description' => 'National Independence Day',
                'is_recurring' => true,
                'recurrence_type' => 'fixed',
                'recurrence_day' => 1,
                'recurrence_month' => 10
            ],
            [
                'name' => 'Christmas Day',
                'type' => 'public',
                'description' => 'Christian holiday celebrating the birth of Jesus',
                'is_recurring' => true,
                'recurrence_type' => 'fixed',
                'recurrence_day' => 25,
                'recurrence_month' => 12
            ]
        ];

        // Easter-based Holidays
        $easterHolidays = [
            [
                'name' => 'Good Friday',
                'type' => 'public',
                'description' => 'Christian holiday commemorating the crucifixion of Jesus',
                'is_recurring' => true,
                'recurrence_type' => 'easter',
                'easter_offset' => -2 // 2 days before Easter Sunday
            ],
            [
                'name' => 'Easter Monday',
                'type' => 'public',
                'description' => 'Day after Easter Sunday',
                'is_recurring' => true,
                'recurrence_type' => 'easter',
                'easter_offset' => 1 // 1 day after Easter Sunday
            ]
        ];

        // Custom Rule Holidays
        $customHolidays = [
            [
                'name' => 'Democracy Day',
                'type' => 'public',
                'description' => 'National Democracy Day',
                'is_recurring' => true,
                'recurrence_type' => 'fixed',
                'recurrence_day' => 12,
                'recurrence_month' => 6
            ],
            [
                'name' => 'Company Foundation Day',
                'type' => 'company',
                'description' => 'Anniversary of company foundation',
                'is_recurring' => true,
                'recurrence_type' => 'fixed',
                'recurrence_day' => 15,
                'recurrence_month' => 3
            ]
        ];

        // Location-specific Holidays
        $locationHolidays = [
            [
                'name' => 'Head Office Maintenance Day',
                'type' => 'location',
                'location_id' => $headOffice->id,
                'description' => 'Annual maintenance day for Head Office',
                'is_recurring' => true,
                'recurrence_type' => 'custom',
                'custom_rule' => [
                    'type' => 'first',
                    'month' => 7,
                    'day' => 1 // First Monday of July
                ]
            ],
            [
                'name' => 'Agbara Community Day',
                'type' => 'location',
                'location_id' => $agbara->id,
                'description' => 'Annual community day for Agbara location',
                'is_recurring' => true,
                'recurrence_type' => 'custom',
                'custom_rule' => [
                    'type' => 'last',
                    'month' => 9,
                    'day' => 1 // Last Monday of September
                ]
            ]
        ];

        // Create fixed date holidays
        foreach ($fixedHolidays as $holiday) {
            foreach ([$headOffice, $agbara] as $location) {
                $date = Carbon::create($currentYear, $holiday['recurrence_month'], $holiday['recurrence_day']);
                Holiday::create(array_merge($holiday, [
                    'location_id' => $location->id,
                    'date' => $date
                ]));
            }
        }

        // Create Easter-based holidays
        foreach ($easterHolidays as $holiday) {
            foreach ([$headOffice, $agbara] as $location) {
                $date = (new Holiday($holiday))->getDateForYear($currentYear);
                Holiday::create(array_merge($holiday, [
                    'location_id' => $location->id,
                    'date' => $date
                ]));
            }
        }

        // Create custom rule holidays
        foreach ($customHolidays as $holiday) {
            foreach ([$headOffice, $agbara] as $location) {
                $date = (new Holiday($holiday))->getDateForYear($currentYear);
                Holiday::create(array_merge($holiday, [
                    'location_id' => $location->id,
                    'date' => $date
                ]));
            }
        }

        // Create location-specific holidays
        foreach ($locationHolidays as $holiday) {
            $date = (new Holiday($holiday))->getDateForYear($currentYear);
            Holiday::create(array_merge($holiday, ['date' => $date]));
        }
    }
} 