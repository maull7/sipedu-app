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

class TemplateSiswaExport implements FromArray, WithHeadings, WithMapping, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [
            [
                'nama_siswa' => 'Contoh Siswa',
                'alamat_siswa' => 'Jl. Contoh No. 123',
                'jenis_kelamin' => 'Laki-laki',
                'nip' => '1234567890',
                'nik' => '321234567890',
                'email' => 'contoh@email.com',
                'kelas' => 'NAMA KELAS',
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Alamat',
            'Jenis Kelamin',
            'NIP',
            'NIK',
            'Email',
            'Kelas',
        ];
    }

    public function map($siswa): array
    {
        return [
            $siswa['nama_siswa'],
            $siswa['alamat_siswa'],
            $siswa['jenis_kelamin'],
            $siswa['nip'],
            $siswa['nik'],
            $siswa['email'] ?? 'Belum ada email',
            $siswa['kelas'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 45,
            'C' => 15,
            'D' => 15,
            'E' => 20,
            'F' => 25,
            'G' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Hanya styling header (baris pertama)
                $event->sheet->getStyle('A1:G1')->applyFromArray([
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
