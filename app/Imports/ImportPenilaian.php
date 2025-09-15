<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ImportPenilaian implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    protected $siswaMap = [];
    protected $mapelMap = [];
    protected $kategoriMap = [];

    public function __construct()
    {
        // Preload mapping untuk menghindari query per baris
        try {
            DB::connection()->disableQueryLog();
        } catch (\Throwable $t) {
            // ignore
        }

        $this->siswaMap = [];
        $siswaRows = DB::table('master_siswa')->select('id_siswa','nip')->get();
        foreach ($siswaRows as $s) {
            $this->siswaMap[trim((string)$s->nip)] = $s->id_siswa;
        }

        // Mapel: key lower-case untuk toleransi perbedaan kapitalisasi
        $mapel = DB::table('master_pelajaran')->select('id_pelajaran', 'nama_mapel')->get();
        foreach ($mapel as $m) {
            $this->mapelMap[$this->toLower(trim((string) $m->nama_mapel))] = $m->id_pelajaran;
        }

        // Kategori: key lower-case
        $kategori = DB::table('master_kategori_penilaian')->select('id_kategori', 'kategori_penilaian')->get();
        foreach ($kategori as $k) {
            $this->kategoriMap[$this->toLower(trim((string) $k->kategori_penilaian))] = $k->id_kategori;
        }
    }

    public function collection(Collection $rows)
    {
        $batch = [];

        foreach ($rows as $row) {
            try {
                // Skip baris kosong minimal
                $nip = isset($row['nip_nrp']) ? trim((string) $row['nip_nrp']) : '';
                $mapelNama = isset($row['mata_pelajaran']) ? trim((string) $row['mata_pelajaran']) : '';
                $kategoriNama = isset($row['kategori_nilai']) ? trim((string) $row['kategori_nilai']) : '';
                if ($nip === '' && $mapelNama === '' && $kategoriNama === '') {
                    continue;
                }

                $idSiswa = $this->siswaMap[$nip] ?? null;
                if (!$idSiswa) {
                    // minimal logging agar tidak lambat
                    continue;
                }

                $idMapel = $this->mapelMap[$this->toLower($mapelNama)] ?? null;
                if (!$idMapel) {
                    continue;
                }

                $idKategori = $this->kategoriMap[$this->toLower($kategoriNama)] ?? null;
                if (!$idKategori) {
                    continue;
                }

                // Sanitisasi angka (dukung koma sebagai desimal)
                $nilaiStr = isset($row['nilai']) ? (is_string($row['nilai']) ? str_replace([','], ['.'], trim($row['nilai'])) : $row['nilai']) : null;
                $nilaiIntelekStr = isset($row['nilai_intelek']) ? (is_string($row['nilai_intelek']) ? str_replace([','], ['.'], trim($row['nilai_intelek'])) : $row['nilai_intelek']) : null;
                $nilaiPengetahuanStr = isset($row['nilai_pengetahuan']) ? (is_string($row['nilai_pengetahuan']) ? str_replace([','], ['.'], trim($row['nilai_pengetahuan'])) : $row['nilai_pengetahuan']) : null;

                $batch[] = [
                    'id_siswa'              => $idSiswa,
                    'id_pelajaran'          => $idMapel,
                    'id_kategori_penilaian' => $idKategori,
                    'nilai'                 => ($nilaiStr !== null && $nilaiStr !== '') ? (float) $nilaiStr : null,
                    'kepribadian'           => ($nilaiIntelekStr !== null && $nilaiIntelekStr !== '') ? (float) $nilaiIntelekStr : null,
                    'intelek'               => ($nilaiPengetahuanStr !== null && $nilaiPengetahuanStr !== '') ? (float) $nilaiPengetahuanStr : null,
                    'progress'              => isset($row['waktu_penilaian']) ? $row['waktu_penilaian'] : null,
                ];

                if (count($batch) >= 500) {
                    DB::table('master_penilaian')->insert($batch);
                    $batch = [];
                }
            } catch (\Exception $e) {
                Log::error('Gagal import penilaian: ' . $e->getMessage());
            }
        }

        if (!empty($batch)) {
            try {
                DB::table('master_penilaian')->insert($batch);
            } catch (\Exception $e) {
                Log::error('Gagal insert batch penilaian: ' . $e->getMessage());
                throw $e;
            }
        }
    }

    private function toLower($s)
    {
        return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
    }
}
