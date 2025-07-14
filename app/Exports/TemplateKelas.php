<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TemplateKelas implements FromArray, WithHeadings, WithMapping, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [
            [
                'nama_kelas' => 'Nama Kelas',
                'jurusan' => 'Nama Jurusan',
                'tahun_ajaran' => 'Tahun Ajaran',
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Kelas',
            'jurusan',
            'tahun Ajaran'
        ];
    }

    public function map($kelas): array
    {
        return [
           $kelas['nama_kelas'],
           $kelas['jurusan'],
           $kelas['tahun_ajaran'],

        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 25,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Hanya styling header (baris pertama)
                $event->sheet->getStyle('A1:C1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '219ebc'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
