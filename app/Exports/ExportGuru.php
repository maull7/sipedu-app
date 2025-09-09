<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportGuru implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $gurus;

    public function __construct($gurus)
    {
        $this->gurus = $gurus;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->gurus;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA',
            'NIP/NRP',
            'EMAIL',
            'JENIS KELAMIN',
            'ALAMAT',
        ];
    }

    public function map($guru): array
    {
        static $no = 1;

        return [
            $no++,
            $guru->nama_guru ?? '-',
            $guru->nip ?? '-',
            $guru->email ?? '-',

            $guru->jenis_kelamin,
            $guru->alamat_guru ?? '-',

        ];
    }

    public function styles(Worksheet $sheet)
{
    $lastRow = $sheet->getHighestRow();
    $lastColumn = $sheet->getHighestColumn(); // kolom terakhir yang terisi (misal "F")

    return [
        // Header styling
        1 => [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32'] // Green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ],

        // Border untuk semua sel sesuai data
        "A1:{$lastColumn}{$lastRow}" => [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ],

        // Center align untuk NO (kolom A) dan Jenis Kelamin (kolom E)
        'A:A' => [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ],
        'E:E' => [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]
    ];
}


    public function columnWidths(): array
    {
        return [
            'A' => 5,   // NO
            'B' => 25,  // NAMA
            'C' => 18,  // NIP
            'D' => 25,  // EMAIL
            'E' => 15,  // NO HP
            'F' => 15,  // JENIS KELAMIN
        ];
    }
}


