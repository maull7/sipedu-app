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
        // Hilangkan kolom nomor agar tidak rancu saat export
        return [
            'NAMA',
            'NIP NRP',
            'MATA PELAJARAN',
            'KATEGORI NILAI',
            'WAKTU PENILAIAN',
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
            'A' => 30,  // NAMA
            'B' => 18,  // NIP NRP
            'C' => 25,  // MATA PELAJARAN
            'D' => 20,  // KATEGORI NILAI
            'E' => 18,  // WAKTU PENILAIAN
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
        // Karena kolom NO dihapus, range header menyesuaikan dari A sampai H
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
        $sheet->freezePane('D2'); // Freeze kolom A-C (sampai MATA PELAJARAN)

        // Tambahkan sheet referensi siswa untuk dropdown nama dan lookup NRP
        [$endRowSiswa] = $this->buildReferensiSiswaSheet($sheet);

        // Tambahkan sheet referensi mapel untuk dropdown mata pelajaran
        [$endRowMapel] = $this->buildReferensiMapelSheet($sheet);

        // Tambahkan sheet referensi kategori untuk dropdown kategori nilai
        [$endRowKategori] = $this->buildReferensiKategoriSheet($sheet);

        // Tambahkan validasi data untuk kolom-kolom terkait
        $this->addDataValidation($sheet, $endRowSiswa, $endRowMapel, $endRowKategori);

        // Isikan formula VLOOKUP untuk kolom B (NIP NRP) mengikuti pilihan nama pada kolom A
        $this->fillNrpFormulas($sheet, $endRowSiswa);
    }

    /**
     * Tambahkan validasi data untuk kolom nilai (0-100)
     */
    private function addDataValidation($sheet, $endRowSiswa, $endRowMapel, $endRowKategori)
    {
        // Dropdown untuk kolom A (NAMA) dari sheet Referensi
        $this->addNamaDropdownValidation($sheet, 'A', $endRowSiswa);

        // Dropdown untuk kolom C (MATA PELAJARAN) dari sheet Mapel
        $this->addMapelDropdownValidation($sheet, 'C', $endRowMapel);

        // Validasi dropdown untuk kolom D (KATEGORI NILAI)
        $this->addKategoriDropdownValidation($sheet, 'D', $endRowKategori);

        // Validasi dropdown untuk kolom E (WAKTU PENILAIAN / PROGRESS)
        $this->addProgressDropdownValidation($sheet, 'E');

        // Validasi untuk kolom F (NILAI)
        $this->addNumericValidation($sheet, 'F', 'Nilai');

        // Validasi untuk kolom G (NILAI INTELEK)
        $this->addNumericValidation($sheet, 'G', 'Nilai Intelek');

        // Validasi untuk kolom H (NILAI PENGETAHUAN)
        $this->addNumericValidation($sheet, 'H', 'Nilai Pengetahuan');
    }

    private function addNamaDropdownValidation($sheet, $column, $endRow)
    {
        $range = $column . '2:' . $column . '1000';

        // Referensi ke range nama siswa pada sheet Referensi
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

    private function addMapelDropdownValidation($sheet, $column, $endRow)
    {
        $range = $column . '2:' . $column . '1000';

        // Referensi ke range nama mapel pada sheet Mapel
        $listRange = '=Mapel!$A$2:$A$' . $endRow;

        $validation = $sheet->getCell($column . '2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input tidak valid');
        $validation->setError('Silakan pilih mata pelajaran dari dropdown');
        $validation->setPromptTitle('Pilih Mata Pelajaran');
        $validation->setPrompt('Pilih salah satu mata pelajaran dari dropdown');
        $validation->setFormula1($listRange);

        $sheet->setDataValidation($range, $validation);
    }

    /**
     * Tambahkan dropdown validation untuk kategori nilai
     */
    private function addKategoriDropdownValidation($sheet, $column, $endRow)
    {
        $range = $column . '2:' . $column . '1000';

        // Gunakan sheet Kategori sebagai sumber dropdown
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

        // Apply validasi ke seluruh range kolom
        $sheet->setDataValidation($range, $validation);
    }

    /**
     * Tambahkan dropdown validation untuk progress
     */
    private function addProgressDropdownValidation($sheet, $column)
    {
        $range = $column . '2:' . $column . '1000';

        // Progress options
        $progressOptions = ['Progress Test', 'Middle Test', 'Final Test'];
        $dropdownList = '"' . implode(',', $progressOptions) . '"';

        $validation = $sheet->getCell($column . '2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input tidak valid');
        $validation->setError('Silakan pilih progress dari dropdown yang tersedia');
        $validation->setPromptTitle('Pilih Progress');
        $validation->setPrompt('Pilih salah satu progress: Test, Middle Test, atau Final Test');
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
                ->pluck('kategori_penilaian')
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

    /**
     * Buat sheet tersembunyi berisi daftar siswa (Nama, NIP/NRP)
     * Mengembalikan [endRow] yaitu baris terakhir data pada sheet Referensi
     */
    private function buildReferensiSiswaSheet($sheet)
    {
        // Ambil data siswa dari database
        $siswa = [];
        try {
            $siswa = DB::table('master_siswa')
                ->select('nama_siswa', 'nip')
                ->orderBy('nama_siswa')
                ->get();
        } catch (\Exception $e) {
            $siswa = collect();
        }

        $delegate = $sheet->getDelegate(); // Worksheet utama
        $spreadsheet = $delegate->getParent();

        // Buat sheet Referensi baru
        $refSheet = new Worksheet($spreadsheet, 'Referensi');
        $spreadsheet->addSheet($refSheet);

        // Header referensi
        $refSheet->setCellValue('A1', 'NAMA');
        $refSheet->setCellValue('B1', 'NIP_NRP');

        // Dedup by nama_siswa, pilih NIP yang tidak kosong jika ada
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

        // Sembunyikan sheet referensi
        $refSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        // Baris terakhir terisi
        $endRow = max(1, $row - 1);
        return [$endRow];
    }

    /**
     * Buat sheet tersembunyi berisi daftar mata pelajaran (Nama Mapel)
     * Mengembalikan [endRow] baris terakhir data pada sheet Mapel
     */
    private function buildReferensiMapelSheet($sheet)
    {
        $mapel = [];
        try {
            $mapel = DB::table('master_pelajaran')
                ->select('nama_mapel')
                ->orderBy('nama_mapel')
                ->get();
        } catch (\Exception $e) {
            $mapel = collect();
        }

        $delegate = $sheet->getDelegate();
        $spreadsheet = $delegate->getParent();

        $mapelSheet = new Worksheet($spreadsheet, 'Mapel');
        $spreadsheet->addSheet($mapelSheet);

        $mapelSheet->setCellValue('A1', 'MATA PELAJARAN');

        $row = 2;
        foreach ($mapel as $m) {
            $mapelSheet->setCellValue('A' . $row, $m->nama_mapel);
            $row++;
        }

        $mapelSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        $endRow = max(1, $row - 1);
        return [$endRow];
    }

    /**
     * Buat sheet tersembunyi berisi daftar kategori penilaian (Kategori Nilai)
     */
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

    /**
     * Isi formula VLOOKUP kolom B berdasarkan pilihan nama di kolom A
     */
    private function fillNrpFormulas($sheet, $endRow)
    {
        // Range referensi untuk VLOOKUP
        $lookupRange = 'Referensi!$A$2:$B$' . (int) $endRow;

        // Isi formula dari baris 2 sampai baris 1000
        for ($r = 2; $r <= 1000; $r++) {
            $formula = '=IFERROR(VLOOKUP(A' . $r . ', ' . $lookupRange . ', 2, FALSE), "")';
            $sheet->setCellValue('B' . $r, $formula);
        }
    }
}
