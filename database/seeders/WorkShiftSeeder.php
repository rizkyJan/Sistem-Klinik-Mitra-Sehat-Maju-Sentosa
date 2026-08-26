<?php

namespace Database\Seeders;

use App\Models\WorkShift;
use Illuminate\Database\Seeder;

class WorkShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Shift Pagi',
            ],

            [
                'name' => 'Shift Siang',
            ],

            [
                'name' => 'Shift Malam',
            ],
        ];


        foreach ($shifts as $shift) {

            WorkShift::updateOrCreate(
                [
                    'name' => $shift['name'],
                ],
                [
                    'start_time' => null,
                    'end_time' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}
