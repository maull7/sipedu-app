<?php

// 1. Controller untuk generate template Excel
class PenilaianController extends Controller
{
    public function exportTemplate()
    {
        // Ambil kategori penilaian dari database
        $kategoriPenilaian = DB::table('master_kategori_penilaian')
            ->orderBy('urutan') // Jika ada field urutan
            ->pluck('nama_kategori')
            ->toArray();

        return Excel::download(new PenilaianTemplateExport($kategoriPenilaian), 'template_penilaian.xlsx');
    }
}

// 2. Export Class menggunakan Laravel Excel
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

class PenilaianTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $kategoriPenilaian;

    public function __construct($kategoriPenilaian)
    {
        $this->kategoriPenilaian = $kategoriPenilaian;
    }

    /**
     * Return empty collection karena ini template kosong
     */
    public function collection()
    {
        return collect([]);
    }

    /**
     * Generate headings dinamis berdasarkan kategori penilaian
     */
    public function headings(): array
    {
        $headings = [
            'NO',
            'NAMA',
            'PANGKAT',
            'NRP'
        ];

        // Tambahkan kategori penilaian dari database
        foreach ($this->kategoriPenilaian as $kategori) {
            $headings[] = strtoupper($kategori);
        }

        // Tambahkan kolom perhitungan
        $headings[] = 'TOTAL';
        $headings[] = 'RATA-RATA';

        return $headings;
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        $columnWidths = [
            'A' => 5,   // NO
            'B' => 30,  // NAMA
            'C' => 10,  // PANGKAT
            'D' => 15,  // NRP
        ];

        // Set width untuk kolom kategori penilaian
        $startColumn = 'E';
        foreach ($this->kategoriPenilaian as $index => $kategori) {
            $column = chr(ord($startColumn) + $index);
            $columnWidths[$column] = 12;
        }

        // Set width untuk kolom total dan rata-rata
        $totalColumn = chr(ord($startColumn) + count($this->kategoriPenilaian));
        $rataColumn = chr(ord($totalColumn) + 1);

        $columnWidths[$totalColumn] = 12; // TOTAL
        $columnWidths[$rataColumn] = 12;  // RATA-RATA

        return $columnWidths;
    }

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        $lastColumn = chr(ord('E') + count($this->kategoriPenilaian) + 1);

        return [
            // Style untuk header
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
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
     * Register events untuk styling lebih detail
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastColumn = chr(ord('E') + count($this->kategoriPenilaian) + 1);
                $headerRange = 'A1:' . $lastColumn . '1';

                // Apply border to header
                $event->sheet->getStyle($headerRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Set row height untuk header
                $event->sheet->getRowDimension(1)->setRowHeight(25);

                // Auto filter
                $event->sheet->setAutoFilter($headerRange);

                // Freeze panes
                $event->sheet->freezePane('E2');

                // Add some sample rows untuk guidance (opsional)
                $this->addSampleRows($event->sheet, $lastColumn);
            },
        ];
    }

    /**
     * Add sample rows (opsional - untuk panduan pengisian)
     */
    private function addSampleRows($sheet, $lastColumn)
    {
        // Uncomment jika ingin menambah sample rows
        /*
        $sampleData = [
            [1, 'CONTOH NAMA', 'BRIPKA', '12345678', '', '', '', '', '', '', '=SUM(E2:' . chr(ord($lastColumn) - 1) . '2)', '=AVERAGE(E2:' . chr(ord($lastColumn) - 1) . '2)'],
        ];

        $row = 2;
        foreach ($sampleData as $data) {
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        */
    }
}

// 3. Route
Route::get('/export/template-penilaian', [PenilaianController::class, 'exportTemplate'])->name('export.template.penilaian');

// 4. View untuk download button
/*
<a href="{{ route('export.template.penilaian') }}" class="btn btn-success">
    <i class="fas fa-download"></i> Download Template Excel
</a>
*/

// 5. Migration untuk tabel master_kategori_penilaian (jika belum ada)
/*
Schema::create('master_kategori_penilaian', function (Blueprint $table) {
    $table->id();
    $table->string('nama_kategori');
    $table->integer('urutan')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
*/

// 6. Seeder untuk tabel master_kategori_penilaian
/*
DB::table('master_kategori_penilaian')->insert([
    ['nama_kategori' => 'MENDENGAR', 'urutan' => 1],
    ['nama_kategori' => 'MEMBACA', 'urutan' => 2],
    ['nama_kategori' => 'MENULIS', 'urutan' => 3],
    ['nama_kategori' => 'BERBICARA', 'urutan' => 4],
    ['nama_kategori' => 'TATA BAHASA', 'urutan' => 5],
]);
*/

// 7. Alternatif dengan Template Blade untuk HTML Table (jika ingin preview)
class PenilaianViewController extends Controller
{
    public function templateView()
    {
        $kategoriPenilaian = DB::table('master_kategori_penilaian')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->pluck('nama_kategori');

        return view('penilaian.template', compact('kategoriPenilaian'));
    }
}

// Template Blade (resources/views/penilaian/template.blade.php)
/*
<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>NO</th>
                <th>NAMA</th>
                <th>PANGKAT</th>
                <th>NRP</th>
                @foreach ($kategoriPenilaian as $kategori)
                    <th>{{ strtoupper($kategori) }}</th>
                @endforeach
                <th>TOTAL</th>
                <th>RATA-RATA</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data akan diisi saat import atau input manual -->
            <tr>
                <td colspan="{{ 4 + count($kategoriPenilaian) + 2 }}" class="text-center text-muted">
                    Data akan muncul di sini setelah import atau input manual
                </td>
            </tr>
        </tbody>
    </table>
</div>
*/
