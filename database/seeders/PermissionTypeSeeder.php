<?php

namespace Database\Seeders;

use App\Models\PermissionType;
use Illuminate\Database\Seeder;

class PermissionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [

            /*
            |--------------------------------------------------------------------------
            | CUTI TAHUNAN
            |--------------------------------------------------------------------------
            |
            | Hanya jenis ini yang membutuhkan masa kerja >= 12 bulan
            | dan menggunakan saldo leave_balances.
            |
            */

            [
                'name' => 'Cuti Tahunan',
                'code' => 'annual_leave',

                'policy_days' => null,

                'uses_annual_balance' => true,

                'excess_can_use_annual_leave' => false,

                'requires_doctor_letter' => false,

                'description' =>
                'Cuti tahunan menggunakan jatah cuti tahunan karyawan.',
            ],


            /*
            |--------------------------------------------------------------------------
            | IZIN SAKIT
            |--------------------------------------------------------------------------
            |
            | Semua karyawan berhak 1 hari.
            |
            */

            [
                'name' => 'Izin Sakit',
                'code' => 'sick',

                'policy_days' => 1,

                'uses_annual_balance' => false,

                'excess_can_use_annual_leave' => true,

                'requires_doctor_letter' => true,

                'description' =>
                'Izin sakit sebanyak 1 hari dengan surat dokter.',
            ],


            /*
            |--------------------------------------------------------------------------
            | IZIN MENIKAH
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Izin Menikah',
                'code' => 'marriage',

                'policy_days' => 3,

                'uses_annual_balance' => false,

                'excess_can_use_annual_leave' => true,

                'requires_doctor_letter' => false,

                'description' =>
                'Izin menikah sebanyak 3 hari.',
            ],


            /*
            |--------------------------------------------------------------------------
            | CUTI KEGUGURAN
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Cuti Keguguran',
                'code' => 'miscarriage',

                'policy_days' => 7,

                'uses_annual_balance' => false,

                'excess_can_use_annual_leave' => false,

                'requires_doctor_letter' => false,

                'description' =>
                'Cuti karena keguguran maksimal 7 hari.',
            ],


            /*
            |--------------------------------------------------------------------------
            | CUTI MELAHIRKAN
            |--------------------------------------------------------------------------
            |
            | Semua karyawan yang memenuhi kondisi cuti melahirkan
            | mendapatkan periode 2 bulan.
            |
            | Masa kerja hanya menentukan status gaji,
            | bukan boleh/tidaknya mengambil cuti.
            |
            */

            [
                'name' => 'Cuti Melahirkan',
                'code' => 'maternity',

                'policy_days' => null,

                'uses_annual_balance' => false,

                'excess_can_use_annual_leave' => false,

                'requires_doctor_letter' => false,

                'description' =>
                'Cuti melahirkan selama total 2 bulan.',
            ],


            /*
            |--------------------------------------------------------------------------
            | LAINNYA
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Izin Lainnya',
                'code' => 'other',

                'policy_days' => null,

                'uses_annual_balance' => false,

                'excess_can_use_annual_leave' => false,

                'requires_doctor_letter' => false,

                'description' =>
                'Perizinan lain sesuai keputusan pihak yang berwenang.',
            ],
        ];


        foreach ($types as $type) {

            PermissionType::updateOrCreate(
                [
                    'code' => $type['code'],
                ],
                $type
            );
        }
    }
}
