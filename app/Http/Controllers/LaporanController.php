<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use App\Exports\ExportPenilaian;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
{
    $kategori = DB::table('master_kategori_penilaian')->pluck('kategori_penilaian', 'id_kategori');

    $query = DB::table('master_penilaian')
        ->join('master_siswa', 'master_siswa.id_siswa', '=', 'master_penilaian.id_siswa')
        ->join('master_kelas', 'master_kelas.id_kelas', '=', 'master_siswa.id_kelas')
        ->join('master_jurusan', 'master_jurusan.id_jurusan', '=', 'master_kelas.id_jurusan')
        ->join('master_pelajaran', 'master_pelajaran.id_pelajaran', '=', 'master_penilaian.id_pelajaran')
        ->join('master_kategori_penilaian', 'master_kategori_penilaian.id_kategori', '=', 'master_penilaian.id_kategori_penilaian')
        ->select(
            'master_siswa.id_siswa',
            'master_siswa.nama_siswa',
            'master_siswa.nip',
            'master_siswa.jenis_kelamin',
            'master_kelas.nama_kelas',
            'master_jurusan.nama_jurusan',
            'master_pelajaran.nama_mapel',
            'master_penilaian.id_pelajaran',
            'master_kategori_penilaian.kategori_penilaian',
            'master_penilaian.nilai',
            'master_penilaian.progress'
        );


    // ✅ Filter jika request diisi
    if ($request->filled('jurusan')) {
        $query->where('master_kelas.id_jurusan', $request->jurusan);
    }
    if ($request->filled('kelas')) {
        $query->where('master_siswa.id_kelas', $request->kelas);
    }
    if ($request->filled('mapel')) {
        $query->where('master_penilaian.id_pelajaran', $request->mapel);
    }
     if ($request->filled('progress')) {
        $query->where('master_penilaian.progress', $request->progress);
    }

    $nilaiData = $query->get();

    $laporan = [];

    foreach ($nilaiData as $row) {
    $id = $row->id_siswa;

    if (!isset($laporan[$id])) {
        $laporan[$id] = [
            'nama_siswa' => $row->nama_siswa,
            'nip' => $row->nip,
            'jk' => $row->jenis_kelamin,
            'kelas' => $row->nama_kelas,
            'mapel' => $row->nama_mapel,
            'progress' => $row->progress,
            'total' => 0,
            'count' => 0,
            'rata_rata' => 0,
        ];
    }

    $nilai = str_replace(',', '.', $row->nilai); // ubah koma ke titik
    $nilai = floatval($nilai); // ubah ke float (angka)

    $laporan[$id][$row->kategori_penilaian] = $nilai;
    $laporan[$id]['total'] += $nilai;
    $laporan[$id]['count'] += 1;
}


    foreach ($laporan as &$data) {
        $data['rata_rata'] = $data['count'] > 0 ? round($data['total'] / $data['count'], 2) : 0;
    }

    $laporan = array_values($laporan);

    // Ambil semua list jurusan, kelas, mapel buat dropdown
    $jurusanList = DB::table('master_jurusan')->get();
    $kelasList = DB::table('master_kelas')->get();
    $mapelList = DB::table('master_pelajaran')->get();


    $progress = ['Progress Test','Middle Test','Final Test'];
    return view('laporan.index', compact('laporan', 'kategori', 'jurusanList', 'kelasList', 'mapelList','progress'));
}

    public function exportExcel(Request $request)
{
    $kategori = DB::table('master_kategori_penilaian')->pluck('kategori_penilaian', 'id_kategori');

    $query = DB::table('master_penilaian')
        ->join('master_siswa', 'master_siswa.id_siswa', '=', 'master_penilaian.id_siswa')
        ->join('master_kelas', 'master_kelas.id_kelas', '=', 'master_siswa.id_kelas')
        ->join('master_jurusan', 'master_jurusan.id_jurusan', '=', 'master_kelas.id_jurusan')
        ->join('master_pelajaran', 'master_pelajaran.id_pelajaran', '=', 'master_penilaian.id_pelajaran')
        ->join('master_kategori_penilaian', 'master_kategori_penilaian.id_kategori', '=', 'master_penilaian.id_kategori_penilaian')
        ->select(
            'master_siswa.id_siswa',
            'master_siswa.nama_siswa',
            'master_siswa.nip',
            'master_siswa.jenis_kelamin',
            'master_kelas.nama_kelas',
            'master_jurusan.nama_jurusan',
            'master_pelajaran.nama_mapel',
            'master_penilaian.id_pelajaran',
            'master_kategori_penilaian.kategori_penilaian',
            'master_penilaian.nilai',
            'master_penilaian.progress',
            'master_penilaian.kepribadian'
        );

    if ($request->filled('jurusan')) {
        $query->where('master_kelas.id_jurusan', $request->jurusan);
    }
    if ($request->filled('kelas')) {
        $query->where('master_siswa.id_kelas', $request->kelas);
    }
    if ($request->filled('mapel')) {
        $query->where('master_penilaian.id_pelajaran', $request->mapel);
    }
    if ($request->filled('progress')) {
        $query->where('master_penilaian.progress', $request->progress);
    }

    $nilaiData = $query->get();

    $laporan = [];

    foreach ($nilaiData as $row) {
        $id = $row->id_siswa;

        if (!isset($laporan[$id])) {
            $laporan[$id] = [
                'nama_siswa' => $row->nama_siswa,
                'nip' => $row->nip,
                'jk' => $row->jenis_kelamin,
                'kelas' => $row->nama_kelas,
                'mapel' => $row->nama_mapel,
                'progress' => $row->progress,
                'total_akademik' => 0,
                'count_akademik' => 0,
                'rata_rata_akademik' => 0,
                'mental' => 0,
                'x7' => 0,
                'x3' => 0,
                'total' => 0,
                'count' => 0,
                'nilai_akhir' => 0,
            ];
        }

        $nilai = str_replace(',', '.', $row->nilai); // ubah koma ke titik
        $nilai = floatval($nilai); // ubah ke float (angka)

        $laporan[$id][$row->kategori_penilaian] = $nilai;

        // Hanya hitung akademik (bukan mental)
        if ($row->kategori_penilaian != 'Mental') {
            $laporan[$id]['total_akademik'] += $nilai;
            $laporan[$id]['count_akademik'] += 1;
        }

        // Ambil nilai mental dari kepribadian
        if (!empty($row->kepribadian)) {
            $kepribadian = str_replace(',', '.', $row->kepribadian);
            $laporan[$id]['mental'] = floatval($kepribadian);
        }

        // Total dan count untuk rata-rata keseluruhan
        $laporan[$id]['total'] += $nilai;
        $laporan[$id]['count'] += 1;
    }

    // Hitung rata-rata akademik dan nilai-nilai lainnya
    foreach ($laporan as $id => &$data) {
        // Hitung rata-rata akademik
        if ($data['count_akademik'] > 0) {
            $data['rata_rata_akademik'] = $data['total_akademik'] / $data['count_akademik'];
        }

        // Hitung x7 = rata rata akademik * 7
        $data['x7'] = $data['rata_rata_akademik'] * 7;

        // Hitung x3 = mental * 3
        $data['x3'] = $data['mental'] * 3;

        // Hitung total baru = x7 + x3 (untuk ranking)
        $data['total'] = $data['x7'] + $data['x3'];

        // Hitung nilai akhir = total / 10
        $data['nilai_akhir'] = $data['total'] / 10;

        // Bulatkan nilai untuk tampilan yang lebih rapi
        $data['rata_rata_akademik'] = round($data['rata_rata_akademik'], 2);
        $data['x7'] = round($data['x7'], 2);
        $data['x3'] = round($data['x3'], 2);
        $data['total'] = round($data['total'], 2);
        $data['nilai_akhir'] = round($data['nilai_akhir'], 2);

        // Hitung rata-rata keseluruhan untuk kompatibilitas
        $data['rata_rata'] = $data['count'] > 0 ? round($data['total'] / $data['count'], 2) : 0;
    }

    // Sort dan kasih ranking berdasarkan total baru
    usort($laporan, fn($a, $b) => $b['total'] <=> $a['total']);

    foreach ($laporan as $i => &$data) {
        $data['ranking'] = $i + 1;
    }


    return Excel::download(new ExportPenilaian($laporan, $kategori), 'laporan-penilaian.xlsx');
}
    public function pdf(Request $request)
{
    $query = DB::table('master_penilaian')
        ->join('master_siswa', 'master_siswa.id_siswa', '=', 'master_penilaian.id_siswa')
        ->join('master_kelas', 'master_kelas.id_kelas', '=', 'master_siswa.id_kelas')
        ->join('master_jurusan', 'master_jurusan.id_jurusan', '=', 'master_kelas.id_jurusan')
        ->join('master_pelajaran', 'master_pelajaran.id_pelajaran', '=', 'master_penilaian.id_pelajaran')
        ->leftJoin('master_tahun','master_tahun.id_tahun','=','master_kelas.id_tahun')
        ->select(
            'master_siswa.id_siswa',
            'master_siswa.nama_siswa',
            'master_siswa.nip',
            'master_siswa.jenis_kelamin',
            'master_kelas.nama_kelas',
            'master_jurusan.nama_jurusan',
            'master_pelajaran.nama_mapel',
            'master_penilaian.kepribadian',
            'master_penilaian.intelek',
            'master_tahun.tahun_ajaran',
            'master_penilaian.progress'
        );

    // Filter opsional
    if ($request->filled('jurusan')) {
        $query->where('master_kelas.id_jurusan', $request->jurusan);
    }
    if ($request->filled('kelas')) {
        $query->where('master_siswa.id_kelas', $request->kelas);
    }
    if ($request->filled('mapel')) {
        $query->where('master_penilaian.id_pelajaran', $request->mapel);
    }
    if ($request->filled('progress')) {
        $query->where('master_penilaian.progress', $request->progress);
    }

    $nilaiData = $query->get();

    $laporan = [];

    foreach ($nilaiData as $row) {
        $rata = number_format((floatval($row->kepribadian) + floatval($row->intelek)) / 2, 2);

        $laporan[] = [
            'nama_siswa'   => $row->nama_siswa,
            'nip'          => $row->nip,
            'jk'           => $row->jenis_kelamin,
            'kelas'        => $row->nama_kelas,
            'mapel'        => $row->nama_mapel,
            'kepribadian'  => $row->kepribadian,
            'intelek'      => $row->intelek,
            'rata_rata'    => $rata,
            'progress'     => $row->progress,
            'jurusan' => $row->nama_jurusan,
            'kelas' => $row->nama_kelas,
            'tahun' => $row->tahun_ajaran
        ];
    }

    $pdf = Pdf::loadView('laporan.pdf', ['laporan' => $laporan]);

    return $pdf->download('lampiran2.pdf');
}

public function pdfL1(Request $request)
{
    $query = DB::table('master_penilaian')
        ->join('master_siswa', 'master_siswa.id_siswa', '=', 'master_penilaian.id_siswa')
        ->join('master_kelas', 'master_kelas.id_kelas', '=', 'master_siswa.id_kelas')
        ->join('master_jurusan', 'master_jurusan.id_jurusan', '=', 'master_kelas.id_jurusan')
        ->join('master_pelajaran', 'master_pelajaran.id_pelajaran', '=', 'master_penilaian.id_pelajaran')
        ->join('master_kategori_penilaian', 'master_kategori_penilaian.id_kategori', '=', 'master_penilaian.id_kategori_penilaian')
        // Join dengan tabel penilaian_formatif
        ->leftJoin('penilaian_formatif', function($join) {
            $join->on('penilaian_formatif.id_siswa', '=', 'master_siswa.id_siswa')
                 ->on('penilaian_formatif.id_kategori_penilaian', '=', 'master_penilaian.id_kategori_penilaian');
        })
        // Join dengan tabel nilai_mental
        ->leftJoin('nilai_mental', 'nilai_mental.id_siswa', '=', 'master_siswa.id_siswa')
        ->select(
            'master_siswa.id_siswa',
            'master_siswa.nama_siswa',
            'master_siswa.nip',
            'master_kelas.nama_kelas',
            'master_jurusan.nama_jurusan',
            'master_pelajaran.nama_mapel',
            'master_penilaian.id_pelajaran',
            'master_kategori_penilaian.id_kategori',
            'master_kategori_penilaian.kategori_penilaian',
            'master_penilaian.nilai',
            'master_penilaian.progress',
            'penilaian_formatif.nilai_formatif',
            'penilaian_formatif.nilai_kehadiran',
            'nilai_mental.nilai_mental'
        )
        ->orderBy('master_siswa.nama_siswa');

    // Filter per kelas dan mapel
    if($request->filled('kelas')){
        $query->where('master_siswa.id_kelas', $request->kelas);
    }

    if ($request->filled('mapel')) {
        $query->where('master_penilaian.id_pelajaran', $request->mapel);
    }
    // Filter untuk kategori bahasa yang diinginkan
    $query->whereIn('master_kategori_penilaian.kategori_penilaian', [
        'Mendengar', 'Membaca', 'Menulis', 'Berbicara', 'Tatabahasa'
    ]);

    // Filter progress
    $query->whereNotNull('master_penilaian.progress')
          ->where('master_penilaian.progress', '!=', '')
          ->whereIn('master_penilaian.progress', ['Progress Test', 'Middle Test', 'Final Test']);



    $nilaiData = $query->get();

    // Step 1: Kelompokkan data per siswa dan kategori
    $nilaiPerKategori = [];

    foreach ($nilaiData as $row) {
        $key = $row->id_siswa . '_' . $row->id_kategori;

        if (!isset($nilaiPerKategori[$key])) {
            $nilaiPerKategori[$key] = [
                'id_siswa' => $row->id_siswa,
                'nama_siswa' => $row->nama_siswa,
                'nip' => $row->nip,
                'kelas' => $row->nama_kelas,
                'jurusan' => $row->nama_jurusan,
                'mapel' => $row->nama_mapel,
                'id_pelajaran' => $row->id_pelajaran,
                'id_kategori' => $row->id_kategori,
                'kategori_penilaian' => strtoupper($row->kategori_penilaian),
                'Progress Test' => null,
                'Middle Test' => null,
                'Final Test' => null,
                'nilai_formatif' => 0,
                'nilai_kehadiran' => 0,
                'nilai_mental' => floatval(str_replace(',', '.', $row->nilai_mental ?? 0))
            ];

            $nilaiFormatif = str_replace(',', '.', $row->nilai_formatif ?? 0);
            $nilaiKehadiran = str_replace(',', '.', $row->nilai_kehadiran ?? 0);

            $nilaiPerKategori[$key]['nilai_formatif'] = floatval($nilaiFormatif);
            $nilaiPerKategori[$key]['nilai_kehadiran'] = floatval($nilaiKehadiran);
        }

        $nilai = floatval(str_replace(',', '.', $row->nilai));

        if ($nilaiPerKategori[$key][$row->progress] === null) {
            $nilaiPerKategori[$key][$row->progress] = $nilai;
        }
    }

    // Step 2: Hitung nilai akademik per kategori (bobot seperti sebelumnya)
    foreach ($nilaiPerKategori as &$data) {
        $progressTest = $data['Progress Test'] ?? 0;
        $middleTest = $data['Middle Test'] ?? 0;
        $finalTest = $data['Final Test'] ?? 0;
        $nilaiFormatif = $data['nilai_formatif'] ?? 0;
        $nilaiKehadiran = $data['nilai_kehadiran'] ?? 0;

        // Hitung nilai berdasarkan bobot per kategori
        $nilaiProgressTest = $progressTest * 0.1;   // 10%
        $nilaiMiddleTest = $middleTest * 0.3;       // 30%
        $nilaiFinalTest = $finalTest * 0.4;         // 40%
        $nilaiFormatifBobot = $nilaiFormatif * 0.1; // 10%
        $nilaiKehadiranBobot = $nilaiKehadiran * 0.1; // 10%

        // Nilai akademik per kategori (rata-rata dari semua progress dengan bobot)
        $data['nilai_akademik_kategori'] = round(
            $nilaiProgressTest + $nilaiMiddleTest + $nilaiFinalTest +
            $nilaiFormatifBobot + $nilaiKehadiranBobot, 2
        );
    }

    // Step 3: Kelompokkan per siswa dan hitung per kategori bahasa
    $laporanPerSiswa = [];

    foreach ($nilaiPerKategori as $data) {
        $siswaId = $data['id_siswa'];

        if (!isset($laporanPerSiswa[$siswaId])) {
            $laporanPerSiswa[$siswaId] = [
                'id_siswa' => $data['id_siswa'],
                'nama_siswa' => $data['nama_siswa'],
                'nip' => $data['nip'],
                'kelas' => $data['kelas'],
                'jurusan' => $data['jurusan'],
                'mapel' => $data['mapel'],
                'nilai_mental' => $data['nilai_mental'],
                // Kategori bahasa
                'MENDENGAR' => 0,
                'MEMBACA' => 0,
                'MENULIS' => 0,
                'BERBICARA' => 0,
                'TATABAHASA' => 0,
                'kategori_count' => 0,
                'total_nilai_kategori' => 0
            ];
        }

        // Set nilai per kategori bahasa
        $kategori = $data['kategori_penilaian'];
        if (in_array($kategori, ['MENDENGAR', 'MEMBACA', 'MENULIS', 'BERBICARA', 'TATABAHASA'])) {
            $laporanPerSiswa[$siswaId][$kategori] = $data['nilai_akademik_kategori'];
            $laporanPerSiswa[$siswaId]['total_nilai_kategori'] += $data['nilai_akademik_kategori'];
            $laporanPerSiswa[$siswaId]['kategori_count']++;
        }
    }

    // Step 4: Hitung nilai akhir per siswa
    $laporan = [];

    foreach ($laporanPerSiswa as $siswaData) {
        // Hitung jumlah dan rata-rata akademik
        $jumlahAkademik = $siswaData['total_nilai_kategori'];
        $nilaiRataRataAkademik = $siswaData['kategori_count'] > 0
            ? round($jumlahAkademik / $siswaData['kategori_count'], 2)
            : 0;

        // Nilai mental
        $nilaiMental = $siswaData['nilai_mental'];

        // X7 (Nilai Rata-rata Akademik × 7)
        $x7 = round($nilaiRataRataAkademik * 7, 2);

        // X3 (Nilai Mental × 3)
        $x3 = round($nilaiMental * 3, 2);

        // Total (X7 + X3)
        $total = round($x7 + $x3, 2);

        // Nilai Akhir (Total ÷ 10)
        $nilaiAkhir = round($total / 10, 2);

        // Klasifikasi berdasarkan Nilai Akhir
        $klasifikasi = '';
        if ($nilaiAkhir >= 86) {
            $klasifikasi = 'SANGAT MEMUASKAN';
        } elseif ($nilaiAkhir >= 81) {
            $klasifikasi = 'MEMUASKAN';
        } elseif ($nilaiAkhir >= 75) {
            $klasifikasi = 'BAIK';
        } elseif ($nilaiAkhir >= 65) {
            $klasifikasi = 'CUKUP';
        } else {
            $klasifikasi = 'KURANG';
        }

        $laporan[] = [
            'id_siswa' => $siswaData['id_siswa'],
            'nama_siswa' => $siswaData['nama_siswa'],
            'nip' => $siswaData['nip'],
            'kelas' => $siswaData['kelas'],
            'jurusan' => $siswaData['jurusan'],
            'mapel' => $siswaData['mapel'],

            // Kategori penilaian bahasa (rata-rata dari semua progress per kategori)
            'MENDENGAR' => $siswaData['MENDENGAR'],
            'MEMBACA' => $siswaData['MEMBACA'],
            'MENULIS' => $siswaData['MENULIS'],
            'BERBICARA' => $siswaData['BERBICARA'],
            'TATA BAHASA' => $siswaData['TATABAHASA'],

            // Nilai yang diminta
            'JUMLAH AKADEMIK' => round($jumlahAkademik, 2),
            'NILAI RATA-RATA AKADEMIK' => $nilaiRataRataAkademik,
            'X7' => $x7,
            'NILAI RATA-RATA MENTAL' => $nilaiMental,
            'X3' => $x3,
            'TOTAL' => $total,
            'Nilai Akhir' => $nilaiAkhir,
            'Klasifikasi' => $klasifikasi
        ];
    }

    // PERBAIKAN: Sorting berdasarkan Nilai Akhir untuk ranking SETELAH filtering
    usort($laporan, function($a, $b) {
        // Jika nilai akhir sama, urutkan berdasarkan nama
        if ($a['Nilai Akhir'] == $b['Nilai Akhir']) {
            return strcmp($a['nama_siswa'], $b['nama_siswa']);
        }
        return $b['Nilai Akhir'] <=> $a['Nilai Akhir'];
    });

    // PERBAIKAN: Assign ranking berdasarkan data yang sudah difilter
    $currentRank = 1;
    $previousScore = null;
    $sameScoreCount = 0;

    foreach ($laporan as $index => &$data) {
        if ($previousScore !== null && $data['Nilai Akhir'] < $previousScore) {
            $currentRank += $sameScoreCount;
            $sameScoreCount = 1;
        } elseif ($previousScore !== null && $data['Nilai Akhir'] == $previousScore) {
            $sameScoreCount++;
        } else {
            $sameScoreCount = 1;
        }

        $data['Ranking'] = $currentRank;
        $previousScore = $data['Nilai Akhir'];
    }



    $pdf = Pdf::loadView('laporan.pdf1', ['laporan' => $laporan]);

    return $pdf->download('lampiran1.pdf');
}

}
