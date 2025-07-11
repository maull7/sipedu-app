<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportPenilaian implements FromView
{
    protected $laporan;
    protected $kategori;

    public function __construct($laporan, $kategori)
    {
        $this->laporan = $laporan;
        $this->kategori = $kategori;
    }

    public function view(): View
    {
        return view('laporan.excel', [
            'laporan' => $this->laporan,
            'kategori' => $this->kategori
        ]);
    }
}
