<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Head Office','type' => 'Head Office'],
            ['name' => 'Agbara', 'type' => 'Depot'],
            ['name' => 'Remote', 'type' => 'Remote'],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
} 