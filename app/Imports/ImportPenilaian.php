<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportPenilaian implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Skip baris kosong
                if (
                    empty($row['nip']) &&
                    empty($row['mata_pelajaran']) &&
                    empty($row['kategori_nilai'])
                ) {
                    continue;
                }

                // === Ambil id_siswa dari master_siswa (nip) ===

                $siswa = DB::table('master_siswa')
                    ->where('nip', $row['nip_nrp'])
                    ->first();

                if (!$siswa) {
                    Log::warning("Siswa dengan NIP {$row['nip']} tidak ditemukan, skip baris.");
                    continue;
                }

                // === Ambil id_pelajaran dari master_pelajaran (nama_pelajaran) ===
                $pelajaran = DB::table('master_pelajaran')
                    ->where('nama_mapel', $row['mata_pelajaran'])
                    ->first();

                if (!$pelajaran) {
                    Log::warning("Pelajaran {$row['mata_pelajaran']} tidak ditemukan, skip baris.");
                    continue;
                }

                // === Ambil id_kategori_penilaian dari master_kategori_penilaian (nama_kategori) ===
                $kategori = DB::table('master_kategori_penilaian')
                    ->where('kategori_penilaian', $row['kategori_nilai'])
                    ->first();

                if (!$kategori) {
                    Log::warning("Kategori {$row['kategori_nilai']} tidak ditemukan, skip baris.");
                    continue;
                }

                // === Insert ke tabel penilaian ===
                DB::table('master_penilaian')->insert([
                    'id_siswa'              => $siswa->id_siswa,
                    'id_pelajaran'          => $pelajaran->id_pelajaran,
                    'id_kategori_penilaian' => $kategori->id_kategori,
                    'nilai'                 => $row['nilai'] ?? null,
                    'kepribadian'           => $row['nilai_intelek'] ?? null,   // mapping ke kolom kepribadian
                    'intelek'               => $row['nilai_pengetahuan'] ?? null, // mapping ke kolom intelek
                    'progress'              => $row['waktu_penilaian'] ?? null,
                ]);
            } catch (\Exception $e) {
                Log::error("Gagal import penilaian: " . $e->getMessage());
            }
        }
    }
}
