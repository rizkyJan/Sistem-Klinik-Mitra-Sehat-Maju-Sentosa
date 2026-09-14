<?php

return [
    /*
     * Semua perhitungan periode laporan bulanan mengikuti WIB.
     */
    'timezone' => env('MONTHLY_REPORT_TIMEZONE', 'Asia/Jakarta'),

    /*
     * Mulai tanggal ini pada setiap bulan, laporan bulan berjalan wajib
     * dikirim Kabid. Sebelum tanggal ini fitur operasional tidak dikunci.
     *
     * Contoh default 25:
     * - 1 s.d. 24 September: belum wajib, tidak ada lock.
     * - mulai 25 September: laporan September wajib.
     */
    'required_from_day' => (int) env('MONTHLY_REPORT_REQUIRED_FROM_DAY', 25),

    /*
     * Bulan pertama fitur diberlakukan. Ini mencegah sistem meminta laporan
     * bulan-bulan sebelum fitur resmi dipakai. Format: YYYY-MM.
     */
    'start_period' => env('MONTHLY_REPORT_START_PERIOD', '2026-09'),

    /* Maksimum ukuran file laporan dalam KB (10 MB). */
    'max_file_size_kb' => (int) env('MONTHLY_REPORT_MAX_FILE_SIZE_KB', 10240),

    'allowed_extensions' => [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'jpg',
        'jpeg',
        'png',
    ],
];
