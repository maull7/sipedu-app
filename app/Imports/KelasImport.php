<?php

namespace App\Imports;

use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek jika data penting kosong, skip
        if (
            empty($row['nama_kelas']) ||
            empty($row['jurusan']) ||
            empty($row['tahun_ajaran'])
        ) {
            return null;
        }

        // Cari kelas berdasarkan nama
        $jurusan = DB::table('master_jurusan')
            ->where('nama_jurusan', $row['jurusan'])
            ->first();

        if (!$jurusan) {
            // Jika tidak ada kelas, skip
            Log::warning('Jurusan tidak ditemukan: ' . $row['jurusan']);
            return null;
        }
        
        // Cari kelas berdasarkan nama
        $tahun = DB::table('master_tahun')
            ->where('tahun_ajaran', $row['tahun_ajaran'])
            ->first();

        if (!$tahun) {
            // Jika tidak ada kelas, skip
            Log::warning('Tahun Ajaran tidak ditemukan: ' . $row['tahun_ajaran']);
            return null;
        }
        // Simpan ke tabel master_siswa
      DB::table('master_kelas')->insert([
            'nama_kelas'    => $row['nama_kelas'],
            'id_jurusan'  => $jurusan->id_jurusan,
            'id_tahun' => $tahun->id_tahun,
            'created_at' => now(),
            'updated_at' => now()
        ]);


        return null; // karena pakai query builder, tidak perlu return model
    }
}
