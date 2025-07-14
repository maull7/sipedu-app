<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ImportMapel implements ToModel, WithHeadingRow
{
    public function model(array $row)
{

 
    if (empty($row['nama_mapel'])) {
        Log::info('Baris dilewati karena nama_mapel kosong:', $row);
        return null;
    }

    Log::info('Data diinsert:', $row);

    DB::table('master_pelajaran')->insert([
        'nama_mapel' => $row['nama_mapel'],
    ]);

    return null;
}

}
