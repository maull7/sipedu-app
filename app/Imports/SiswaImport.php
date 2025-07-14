<?php

namespace App\Imports;

use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek jika data penting kosong, skip
        if (
            empty($row['nama_siswa']) ||
            empty($row['alamat']) ||
            empty($row['jenis_kelamin']) ||
            empty($row['nip']) ||
            empty($row['nik']) ||
            empty($row['email']) ||
            empty($row['kelas'])
        ) {
            return null;
        }

        // Cari kelas berdasarkan nama
        $kelas = DB::table('master_kelas')
            ->where('nama_kelas', $row['kelas'])
            ->first();

        if (!$kelas) {
            // Jika tidak ada kelas, skip
            Log::warning('Kelas tidak ditemukan: ' . $row['kelas']);
            return null;
        }

        // Simpan ke tabel master_siswa
        $siswaId = DB::table('master_siswa')->insertGetId([
            'nama_siswa'    => $row['nama_siswa'],
            'alamat_siswa'  => $row['alamat'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'nip'           => $row['nip'],
            'nik'           => $row['nik'],
            'email'         => $row['email'],
            'id_kelas'      => $kelas->id_kelas,
            'sts'           => '1',
        ]);


        return null; // karena pakai query builder, tidak perlu return model
    }
}
