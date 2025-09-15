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

class NilaiMentalTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function collection()
    {
        return collect([]);
    }

    public function headings(): array
    {
        // Hilangkan nomor, sediakan nama + NRP
        return [
            'NAMA',
            'NIP NRP',
            'NILAI MENTAL',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // NAMA
            'B' => 18, // NIP NRP
            'C' => 18, // NILAI MENTAL
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
                    'startColor' => ['rgb' => '6C757D'] // gray
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
        // Setelah hapus NO, range header menjadi A1:C1
        $headerRange = 'A1:C1';

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

        // Buat sheet referensi siswa untuk dropdown nama dan pengisian NRP
        [$endRow] = $this->buildReferensiSiswaSheet($sheet);

        // Dropdown nama di kolom A
        $this->addNamaDropdownValidation($sheet, 'A', $endRow);

        // Auto-isi NRP pada kolom B berdasarkan nama
        $this->fillNrpFormulas($sheet, $endRow);

        // Numeric validation untuk C (Nilai Mental)
        $this->addNumericValidation($sheet, 'C', 'Nilai Mental');
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

    private function fillNrpFormulas($sheet, $endRow)
    {
        $lookupRange = 'Referensi!$A$2:$B$' . (int) $endRow;
        for ($r = 2; $r <= 1000; $r++) {
            $formula = '=IFERROR(VLOOKUP(A' . $r . ', ' . $lookupRange . ', 2, FALSE), "")';
            $sheet->setCellValue('B' . $r, $formula);
        }
    }
}
