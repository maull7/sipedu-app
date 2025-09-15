<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
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

class PenilaianFormatifTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $kategoriPenilaian;

    public function __construct($kategoriPenilaian = null)
    {
        $this->kategoriPenilaian = $kategoriPenilaian;
    }

    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        // Hilangkan kolom NO dan sediakan nama + NRP
        return [
            'NAMA',
            'NIP NRP',
            'KATEGORI NILAI',
            'NILAI FORMATIF',
            'NILAI KEHADIRAN',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // NAMA
            'B' => 18, // NIP NRP
            'C' => 22, // KATEGORI
            'D' => 18, // NILAI FORMATIF
            'E' => 18, // NILAI KEHADIRAN
        ];
    }

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
                    'startColor' => ['rgb' => '28A745'] // green
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $this->setupWorksheet($event->sheet);
            },
        ];
    }

    private function setupWorksheet($sheet)
    {
        // Setelah hapus NO, kolom sekarang A-E
        $headerRange = 'A1:E1';

        $sheet->getStyle($headerRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle($headerRange)->getAlignment()->setWrapText(true);
        $sheet->setAutoFilter($headerRange);

        // Tambah sheet referensi siswa
        [$endRow] = $this->buildReferensiSiswaSheet($sheet);

        // Tambah sheet referensi kategori
        [$endRowKategori] = $this->buildReferensiKategoriSheet($sheet);

        // Validations
        $this->addNamaDropdownValidation($sheet, 'A', $endRow);
        $this->addKategoriDropdownValidationRef($sheet, 'C', $endRowKategori);
        $this->addNumericValidation($sheet, 'D', 'Nilai Formatif');
        $this->addNumericValidation($sheet, 'E', 'Nilai Kehadiran');

        // Auto isi NRP berdasarkan nama
        $this->fillNrpFormulas($sheet, $endRow);
    }

    private function addKategoriDropdownValidation($sheet, $column)
    {
        $range = $column . '2:' . $column . '1000';

        if (empty($this->kategoriPenilaian)) {
            $this->kategoriPenilaian = $this->getKategoriPenilaianFromDb();
        }

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

        $sheet->setDataValidation($range, $validation);
    }

    private function getKategoriPenilaianFromDb()
    {
        try {
            $kategoriList = \Illuminate\Support\Facades\DB::table('master_kategori_penilaian')
                ->pluck('kategori_penilaian')
                ->toArray();

            return !empty($kategoriList) ? $kategoriList : ['Formatif', 'Kehadiran'];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mengambil kategori penilaian dari database: ' . $e->getMessage());
            return ['Formatif', 'Kehadiran'];
        }
    }

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

        $sheet->setDataValidation($range, $validation);
    }

    private function addNamaDropdownValidation($sheet, $column, $endRow)
    {
        $range = $column . '2:' . $column . '1000';
        $listRange = '=Referensi!$A$2:$A$' . $endRow;

        $validation = $sheet->getCell($column . '2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input tidak valid');
        $validation->setError('Silakan pilih nama siswa dari dropdown');
        $validation->setPromptTitle('Pilih Nama Siswa');
        $validation->setPrompt('Pilih salah satu nama siswa dari dropdown');
        $validation->setFormula1($listRange);

        $sheet->setDataValidation($range, $validation);
    }

    private function buildReferensiSiswaSheet($sheet)
    {
        $siswa = [];
        try {
            $siswa = DB::table('master_siswa')
                ->select('nama_siswa', 'nip')
                ->orderBy('nama_siswa')
                ->get();
        } catch (\Exception $e) {
            $siswa = collect();
        }

        $delegate = $sheet->getDelegate();
        $spreadsheet = $delegate->getParent();

        $refSheet = new Worksheet($spreadsheet, 'Referensi');
        $spreadsheet->addSheet($refSheet);

        $refSheet->setCellValue('A1', 'NAMA');
        $refSheet->setCellValue('B1', 'NIP_NRP');

        // Dedup nama
        $uniqueByNama = [];
        foreach ($siswa as $rowData) {
            $name = trim((string) $rowData->nama_siswa);
            if ($name === '') continue;
            $nipVal = trim((string) $rowData->nip);
            if (!array_key_exists($name, $uniqueByNama)) {
                $uniqueByNama[$name] = $nipVal;
            } else {
                if ($uniqueByNama[$name] === '' && $nipVal !== '') {
                    $uniqueByNama[$name] = $nipVal;
                }
            }
        }

        $row = 2;
        foreach ($uniqueByNama as $name => $nipVal) {
            $refSheet->setCellValue('A' . $row, $name);
            $refSheet->setCellValue('B' . $row, $nipVal);
            $row++;
        }

        $refSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        $endRow = max(1, $row - 1);
        return [$endRow];
    }

    private function buildReferensiKategoriSheet($sheet)
    {
        $kategori = [];
        try {
            $kategori = DB::table('master_kategori_penilaian')
                ->select('kategori_penilaian')
                ->orderBy('kategori_penilaian')
                ->get();
        } catch (\Exception $e) {
            $kategori = collect();
        }

        $delegate = $sheet->getDelegate();
        $spreadsheet = $delegate->getParent();

        $katSheet = new Worksheet($spreadsheet, 'Kategori');
        $spreadsheet->addSheet($katSheet);

        $katSheet->setCellValue('A1', 'KATEGORI NILAI');

        $row = 2;
        foreach ($kategori as $k) {
            $katSheet->setCellValue('A' . $row, $k->kategori_penilaian);
            $row++;
        }

        $katSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        $endRow = max(1, $row - 1);
        return [$endRow];
    }

    private function addKategoriDropdownValidationRef($sheet, $column, $endRow)
    {
        $range = $column . '2:' . $column . '1000';
        $listRange = '=Kategori!$A$2:$A$' . $endRow;

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
        $validation->setFormula1($listRange);

        $sheet->setDataValidation($range, $validation);
    }

    private function fillNrpFormulas($sheet, $endRow)
    {
        $lookupRange = 'Referensi!$A$2:$B$' . (int) $endRow;
        for ($r = 2; $r <= 1000; $r++) {
            $formula = '=IFERROR(VLOOKUP(A' . $r . ', ' . $lookupRange . ', 2, FALSE), "")';
            $sheet->setCellValue('B' . $r, $formula);
        }
    }
}
