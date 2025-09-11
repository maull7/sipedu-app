<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class ExportPenilaian implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected $laporan;
    protected $kategori;

    public function __construct($laporan, $kategori)
    {
        $this->laporan = $laporan;
        $this->kategori = $kategori;
    }

    public function collection(): Collection
    {
        $data = collect();

        foreach ($this->laporan as $row) {
            $nilai_akhir = $row['nilai_akhir'];
            $klasifikasi = $this->getKlasifikasi($nilai_akhir);

            $rowData = [
                $row['ranking'],
                $row['nip'],
                $row['nama_siswa'],
                $row['jk'],
                $row['kelas'],
                $row['mapel'],
                $row['progress'],
            ];

            // Tambahkan nilai per kategori
            foreach ($this->kategori as $kat) {
                $rowData[] = isset($row[$kat]) ? $row[$kat] : 0;
            }

            // Tambahkan perhitungan
            $rowData = array_merge($rowData, [
                $row['rata_rata_akademik'],
                $row['mental'],
                $row['x7'],
                $row['x3'],
                $row['total'],
                $row['nilai_akhir'],
                $klasifikasi
            ]);

            $data->push($rowData);
        }

        return $data;
    }

    public function headings(): array
    {
        $headers = [
            'Ranking',
            'NIP',
            'Nama Siswa',
            'Jenis Kelamin',
            'Kelas',
            'Mata Pelajaran',
            'Progress'
        ];

        // Tambahkan header kategori penilaian
        foreach ($this->kategori as $kat) {
            $headers[] = $kat;
        }

        // Tambahkan header perhitungan
        $headers = array_merge($headers, [
            'Rata-rata Akademik',
            'Mental',
            'X7 (Akademik x 7)',
            'X3 (Mental x 3)',
            'Total',
            'Nilai Akhir',
            'Klasifikasi'
        ]);

        return $headers;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Style untuk header
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style untuk semua data (termasuk border)
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Style untuk kolom ranking (center alignment)
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk kolom nilai (center alignment)
        $kategoriBanyak = count($this->kategori);
        $startNilaiColumn = chr(ord('G') + $kategoriBanyak); // G + jumlah kategori
        $endColumn = $highestColumn;
        $sheet->getStyle($startNilaiColumn . '2:' . $endColumn . $highestRow)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style untuk baris data (zebra striping)
        for ($row = 2; $row <= $highestRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA']
                    ]
                ]);
            }
        }

        // Style khusus untuk kolom klasifikasi
        $klasifikasiColumn = $highestColumn;
        for ($row = 2; $row <= $highestRow; $row++) {
            $klasifikasi = $sheet->getCell($klasifikasiColumn . $row)->getValue();
            $color = $this->getKlasifikasiColor($klasifikasi);

            $sheet->getStyle($klasifikasiColumn . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color]
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ]);
        }

        return [];
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 8,   // Ranking
            'B' => 15,  // NIP
            'C' => 25,  // Nama
            'D' => 12,  // JK
            'E' => 15,  // Kelas
            'F' => 20,  // Mapel
            'G' => 12,  // Progress
        ];

        // Width untuk kategori penilaian
        $currentColumn = 'H';
        foreach ($this->kategori as $kat) {
            $widths[$currentColumn] = 12;
            $currentColumn++;
        }

        // Width untuk perhitungan
        $columns = ['rata_rata_akademik', 'mental', 'x7', 'x3', 'total', 'nilai_akhir', 'klasifikasi'];
        foreach ($columns as $col) {
            $widths[$currentColumn] = $col == 'klasifikasi' ? 18 : 14;
            if ($currentColumn < 'Z') {
                $currentColumn++;
            }
        }

        return $widths;
    }

    private function getKlasifikasi($nilai_akhir): string
    {
        if ($nilai_akhir >= 86) {
            return 'Sangat Memuaskan';
        } elseif ($nilai_akhir >= 81) {
            return 'Memuaskan';
        } elseif ($nilai_akhir >= 75) {
            return 'Baik';
        } elseif ($nilai_akhir >= 65) {
            return 'Cukup';
        } else {
            return 'Kurang';
        }
    }

    private function getKlasifikasiColor($klasifikasi): string
    {
        switch ($klasifikasi) {
            case 'Sangat Memuaskan':
                return '28A745'; // Hijau tua
            case 'Memuaskan':
                return '20C997'; // Hijau muda
            case 'Baik':
                return '007BFF'; // Biru
            case 'Cukup':
                return 'FFC107'; // Kuning
            case 'Kurang':
                return 'DC3545'; // Merah
            default:
                return '6C757D'; // Abu-abu
        }
    }
}
