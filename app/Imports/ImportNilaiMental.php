<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ImportNilaiMental implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    protected $siswaMap = [];

    public function __construct()
    {
        try { DB::connection()->disableQueryLog(); } catch (\Throwable $t) {}
        $this->siswaMap = [];
        $rows = DB::table('master_siswa')->select('id_siswa','nip')->get();
        foreach ($rows as $r) {
            $this->siswaMap[trim((string)$r->nip)] = $r->id_siswa;
        }
    }

    public function collection(Collection $rows)
    {
        $batch = [];
        $now = now();

        foreach ($rows as $row) {
            try {
                $nip = isset($row['nip_nrp']) ? trim((string) $row['nip_nrp']) : '';
                $nilaiRaw = $row['nilai_mental'] ?? null;
                $nilaiStr = is_string($nilaiRaw) ? str_replace([','], ['.'], trim($nilaiRaw)) : $nilaiRaw;
                $hasNilai = (is_numeric($nilaiStr));

                // Wajib ada NIP dan Nilai untuk diproses
                if ($nip === '' || !$hasNilai) {
                    continue;
                }

                $idSiswa = $this->siswaMap[$nip] ?? null;
                if (!$idSiswa) { continue; }

                $batch[] = [
                    'id_siswa' => $idSiswa,
                    'nilai_mental' => (float) $nilaiStr,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($batch) >= 500) {
                    DB::table('nilai_mental')->insert($batch);
                    $batch = [];
                }
            } catch (\Exception $e) {
                Log::error('Gagal import nilai mental: ' . $e->getMessage());
            }
        }

        if (!empty($batch)) {
            DB::table('nilai_mental')->insert($batch);
        }
    }
}
