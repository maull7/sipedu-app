<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ImportPenilaianFormatif implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    protected $siswaMap = [];
    protected $kategoriMap = [];

    public function __construct()
    {
        try { DB::connection()->disableQueryLog(); } catch (\Throwable $t) {}

        $this->siswaMap = [];
        $rows = DB::table('master_siswa')->select('id_siswa','nip')->get();
        foreach ($rows as $r) {
            $this->siswaMap[trim((string)$r->nip)] = $r->id_siswa;
        }

        $kategori = DB::table('master_kategori_penilaian')->select('id_kategori', 'kategori_penilaian')->get();
        foreach ($kategori as $k) {
            $this->kategoriMap[mb_strtolower(trim((string) $k->kategori_penilaian))] = $k->id_kategori;
        }
    }

    public function collection(Collection $rows)
    {
        $batch = [];
        $now = now();

        foreach ($rows as $row) {
            try {
                $nip = isset($row['nip_nrp']) ? trim((string) $row['nip_nrp']) : '';
                $kategoriNama = isset($row['kategori_nilai']) ? trim((string) $row['kategori_nilai']) : '';
                if ($nip === '' && $kategoriNama === '') {
                    continue;
                }

                $idSiswa = $this->siswaMap[$nip] ?? null;
                if (!$idSiswa) { continue; }

                $idKategori = $this->kategoriMap[mb_strtolower($kategoriNama)] ?? null;
                if (!$idKategori) { continue; }

                $batch[] = [
                    'id_siswa' => $idSiswa,
                    'id_kategori_penilaian' => $idKategori,
                    'nilai_formatif' => isset($row['nilai_formatif']) && $row['nilai_formatif'] !== '' ? (float) $row['nilai_formatif'] : null,
                    'nilai_kehadiran' => isset($row['nilai_kehadiran']) && $row['nilai_kehadiran'] !== '' ? (float) $row['nilai_kehadiran'] : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($batch) >= 500) {
                    DB::table('penilaian_formatif')->insert($batch);
                    $batch = [];
                }
            } catch (\Exception $e) {
                Log::error('Gagal import penilaian formatif: ' . $e->getMessage());
            }
        }

        if (!empty($batch)) {
            DB::table('penilaian_formatif')->insert($batch);
        }
    }
}
