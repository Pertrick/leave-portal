<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Head Office',
                'type' => 'office'
            ],
            [
                'name' => 'Branch Office 1',
                'type' => 'office'
            ],
            [
                'name' => 'Branch Office 2',
                'type' => 'office'
            ],
            [
                'name' => 'Remote',
                'type' => 'remote'
            ]
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
} 