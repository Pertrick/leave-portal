<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Location;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $headOffice = Location::where('name', 'Head Office')->first();
        
        $departments = [
            [
                'name' => 'Human Resources',
                'location_id' => $headOffice->id
            ],
            [
                'name' => 'Information Technology',
                'location_id' => $headOffice->id
            ],
            [
                'name' => 'Finance',
                'location_id' => $headOffice->id
            ],
            [
                'name' => 'Marketing',
                'location_id' => $headOffice->id
            ],
            [
                'name' => 'Operations',
                'location_id' => $headOffice->id
            ],
            [
                'name' => 'Sales',
                'location_id' => $headOffice->id
            ],
            [
                'name' => 'Customer Service',
                'location_id' => $headOffice->id
            ],
            [
                'name' => 'Research and Development',
                'location_id' => $headOffice->id
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
} 