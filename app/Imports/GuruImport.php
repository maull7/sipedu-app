<?php

namespace App\Imports;

use App\Models\User;
use Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek jika data penting kosong, skip
        if (
            empty($row['nama_guru']) ||
            empty($row['alamat_guru']) ||
            empty($row['jenis_kelamin']) ||
            empty($row['nip']) ||
            empty($row['email'])
        ) {
            return null;
        }

        // Simpan ke tabel master_guru
       DB::table('master_guru')->insertGetId([
            'nama_guru'    => $row['nama_guru'],
            'alamat_guru'  => $row['alamat_guru'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'nip'           => $row['nip'],
            'email'         => $row['email'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

         // Insert ke users
            DB::table('users')->insert([
                'name' => $row['nama_guru'],
                'email' => $row['email'],
                'nip' => $row['nip'],
                'password' => Hash::make('12345678'), // default password, bisa diubah
                'role' => 1, // sesuaikan dengan sistem kamu
                'created_at' => now(),
                'updated_at' => now(),
            ]);


        return null; // karena pakai query builder, tidak perlu return model
    }
}
