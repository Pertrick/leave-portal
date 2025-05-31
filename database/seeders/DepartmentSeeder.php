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
            ],
            [
                'name' => 'Information Technology',
                'location_id' => $headOffice->id
            ],
            [
                'name' => 'Finance'
            ],
            [
                'name' => 'Marketing',
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
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
} 