<?php

namespace App\Imports;

use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportTahun implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek jika data penting kosong, skip
        if (
            empty($row['tahun_ajaran'])
        ) {
            return null;
        }


        // Simpan ke tabel master_siswa
        DB::table('master_tahun')->insert([
            'tahun_ajaran'    => $row['tahun_ajaran']
        ]);


        return null; // karena pakai query builder, tidak perlu return model
    }
}
