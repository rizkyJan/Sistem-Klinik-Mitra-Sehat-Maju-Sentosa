<?php

return [
    /*
     * Periode Hear You mengikuti waktu Indonesia Barat agar pergantian
     * bulan tidak bergantung pada timezone server.
     */
    'timezone' => env('HEAR_YOU_TIMEZONE', 'Asia/Jakarta'),

    /*
     * Role yang wajib memenuhi Hear You setiap bulan.
     */

    'test_date' => env('HEAR_YOU_TEST_DATE'),
    'required_roles' => [
        'karyawan',
        'kabid',
    ],
];
