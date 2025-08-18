<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class PenilaianTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $kategoriPenilaian;

    public function __construct($kategoriPenilaian = null)
    {
        $this->kategoriPenilaian = $kategoriPenilaian;
    }

    /**
     * Return empty collection untuk template kosong
     */
    public function collection()
    {
        return collect([]);
    }

    /**
     * Generate headings statis
     */
    public function headings(): array
    {
        return [
            'NO',
            'NAMA',
            'NIP',
            'MATA PELAJARAN',
            'KATEGORI NILAI',
            'NILAI',
            'NILAI INTELEK',
            'NILAI PENGETAHUAN'
        ];
    }

    /**
     * Set lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // NO
            'B' => 30,  // NAMA
            'C' => 15,  // NIP
            'D' => 25,  // MATA PELAJARAN
            'E' => 20,  // KATEGORI NILAI
            'F' => 15,  // NILAI
            'G' => 18,  // NILAI INTELEK
            'H' => 20,  // NILAI PENGETAHUAN
        ];
    }

    /**
     * Style untuk header
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Event setelah sheet dibuat
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->setupWorksheet($event->sheet);
            },
        ];
    }

    /**
     * Setup worksheet dengan styling dan validasi
     */
    private function setupWorksheet($sheet)
    {
        $headerRange = 'A1:H1';

        // Border untuk header
        $sheet->getStyle($headerRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set tinggi header dan wrap text
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle($headerRange)->getAlignment()->setWrapText(true);

        // Auto filter dan freeze panes
        $sheet->setAutoFilter($headerRange);
        $sheet->freezePane('E2'); // Freeze sampai kolom mata pelajaran

        // Tambahkan validasi data untuk kolom nilai
        $this->addDataValidation($sheet);
    }

    /**
     * Tambahkan validasi data untuk kolom nilai (0-100)
     */
    private function addDataValidation($sheet)
    {
        // Validasi dropdown untuk kolom E (KATEGORI NILAI)
        $this->addKategoriDropdownValidation($sheet, 'E');

        // Validasi untuk kolom F (NILAI)
        $this->addNumericValidation($sheet, 'F', 'Nilai');

        // Validasi untuk kolom G (NILAI INTELEK)
        $this->addNumericValidation($sheet, 'G', 'Nilai Intelek');

        // Validasi untuk kolom H (NILAI PENGETAHUAN)
        $this->addNumericValidation($sheet, 'H', 'Nilai Pengetahuan');
    }

    /**
     * Tambahkan dropdown validation untuk kategori nilai
     */
    private function addKategoriDropdownValidation($sheet, $column)
    {
        $range = $column . '2:' . $column . '1000';

        // Ambil kategori penilaian dari database jika tidak di-pass via constructor
        if (empty($this->kategoriPenilaian)) {
            $this->kategoriPenilaian = $this->getKategoriPenilaianFromDb();
        }

        // Buat string untuk dropdown list
        $dropdownList = '"' . implode(',', $this->kategoriPenilaian) . '"';

        $validation = $sheet->getCell($column . '2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input tidak valid');
        $validation->setError('Silakan pilih kategori nilai dari dropdown yang tersedia');
        $validation->setPromptTitle('Pilih Kategori Nilai');
        $validation->setPrompt('Pilih salah satu kategori nilai dari dropdown');
        $validation->setFormula1($dropdownList);

        // Apply validasi ke seluruh range kolom
        $sheet->setDataValidation($range, $validation);
    }

    /**
     * Ambil kategori penilaian dari database
     */
    private function getKategoriPenilaianFromDb()
    {
        try {
            $kategoriList = \Illuminate\Support\Facades\DB::table('master_kategori_penilaian')
                ->pluck('nama_kategori')
                ->toArray();

            return !empty($kategoriList) ? $kategoriList : ['Tugas', 'UTS', 'UAS', 'Kehadiran'];
        } catch (\Exception $e) {
            // Fallback jika ada error
            \Illuminate\Support\Facades\Log::warning('Gagal mengambil kategori penilaian dari database: ' . $e->getMessage());
            return ['Tugas', 'UTS', 'UAS', 'Kehadiran'];
        }
    }

    /**
     * Tambahkan validasi numerik untuk kolom tertentu
     */
    private function addNumericValidation($sheet, $column, $label)
    {
        $range = $column . '2:' . $column . '1000';

        $validation = $sheet->getCell($column . '2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_DECIMAL);
        $validation->setOperator(DataValidation::OPERATOR_BETWEEN);
        $validation->setAllowBlank(true);
        $validation->setFormula1('0');
        $validation->setFormula2('100');
        $validation->setShowInputMessage(true);
        $validation->setPromptTitle('Input ' . $label);
        $validation->setPrompt('Masukkan nilai antara 0-100');
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Nilai Tidak Valid');
        $validation->setError($label . ' harus berupa angka antara 0-100');
        $validation->setErrorStyle(DataValidation::STYLE_STOP);

        // Apply validasi ke seluruh range kolom
        $sheet->setDataValidation($range, $validation);
    }
}
