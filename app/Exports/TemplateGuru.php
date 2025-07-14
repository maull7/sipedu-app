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

class TemplateGuru implements FromArray, WithHeadings, WithMapping, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [
            [
                'nama_guru' => 'Nama Guru',
                'alamat_guru' => 'JL contoh guru',
                'jenis_kelamin' => 'Laki-laki/Perempuan',
                'nip' => '1234567890',
                'email' => 'contoh@email.com',
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Guru',
            'Alamat Guru',
            'Jenis Kelamin',
            'NIP',
            'Email',
        ];
    }

    public function map($guru): array
    {
        return [
            $guru['nama_guru'],
            $guru['alamat_guru'],
            $guru['jenis_kelamin'],
            $guru['nip'],
            $guru['email'] ?? 'Belum ada email',
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
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Hanya styling header (baris pertama)
                $event->sheet->getStyle('A1:E1')->applyFromArray([
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
