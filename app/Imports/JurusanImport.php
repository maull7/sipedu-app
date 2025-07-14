<?php

namespace App\Imports;

use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JurusanImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek jika data penting kosong, skip
        if (
            empty($row['nama_jurusan'])
        ) {
            return null;
        }


        // Simpan ke tabel master_siswa
        DB::table('master_jurusan')->insert([
            'nama_jurusan'    => $row['nama_jurusan'],
            'created_at' => now(),
            'updated_at' => now()
        ]);


        return null; // karena pakai query builder, tidak perlu return model
    }
}
