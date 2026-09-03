<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            /*
             * NIP / ID Pegawai baru.
             *
             * Kolom `nik` lama sengaja BELUM dihapus agar fitur lama tetap aman
             * selama pemindahan kode dilakukan bertahap.
             */
            $table->string('nip', 50)
                ->nullable()
                ->unique()
                ->after('nik');

            /*
             * NIK KTP resmi.
             * Pada form baru nanti divalidasi tepat 16 digit.
             */
            $table->string('nik_ktp', 16)
                ->nullable()
                ->unique()
                ->after('nip');

            $table->string('birth_place', 100)
                ->nullable()
                ->after('nik_ktp');

            $table->date('birth_date')
                ->nullable()
                ->after('birth_place');

            $table->text('ktp_address')
                ->nullable()
                ->after('birth_date');

            $table->text('domicile_address')
                ->nullable()
                ->after('ktp_address');

            $table->string('blood_type', 3)
                ->nullable()
                ->after('domicile_address');

            $table->string('religion', 30)
                ->nullable()
                ->after('blood_type');

            /*
             * SIP dibuat nullable karena tidak semua pegawai mempunyai SIP.
             */
            $table->string('sip_number', 100)
                ->nullable()
                ->after('religion');

            $table->date('sip_valid_from')
                ->nullable()
                ->after('sip_number');

            $table->date('sip_valid_until')
                ->nullable()
                ->after('sip_valid_from');

            /*
             * Pas foto formal disimpan sebagai path private.
             */
            $table->string('formal_photo_path')
                ->nullable()
                ->after('sip_valid_until');

            /*
             * Rekening pegawai.
             *
             * Bank tidak dipilih oleh pegawai. Seluruh rekening wajib
             * Bank Syariah Indonesia (BSI). Field dibuat nullable pada
             * migration supaya data user lama tetap bisa dimigrasikan.
             * Pada form registrasi Karyawan/Kabid nanti dibuat REQUIRED.
             */
            $table->string('bank_name', 100)
                ->default('Bank Syariah Indonesia (BSI)')
                ->after('formal_photo_path');

            $table->string('bank_account_number', 30)
                ->nullable()
                ->after('bank_name');

            $table->string('bank_account_name', 150)
                ->nullable()
                ->after('bank_account_number');
        });

        /*
         * Migrasi aman data lama:
         * nilai `nik` lama (contoh MSMS001) sebenarnya merupakan
         * NIP / ID Pegawai. Nilai tersebut otomatis disalin ke `nip`.
         */
        DB::table('users')
            ->whereNotNull('nik')
            ->whereNull('nip')
            ->update([
                'nip' => DB::raw('nik'),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nip']);
            $table->dropUnique(['nik_ktp']);

            $table->dropColumn([
                'nip',
                'nik_ktp',
                'birth_place',
                'birth_date',
                'ktp_address',
                'domicile_address',
                'blood_type',
                'religion',
                'sip_number',
                'sip_valid_from',
                'sip_valid_until',
                'formal_photo_path',
                'bank_name',
                'bank_account_number',
                'bank_account_name',
            ]);
        });
    }
};
