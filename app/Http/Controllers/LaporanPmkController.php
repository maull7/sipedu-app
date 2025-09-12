<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LaporanPmkController extends Controller
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
        // ✅ Join dengan tabel nilai_formatif
        ->leftJoin('penilaian_formatif', function($join) {
            $join->on('penilaian_formatif.id_siswa', '=', 'master_siswa.id_siswa')
                 ->on('penilaian_formatif.id_kategori_penilaian', '=', 'master_penilaian.id_kategori_penilaian');
        })
        ->select(
            'master_siswa.id_siswa',
            'master_siswa.nama_siswa',
            'master_siswa.nip',
            'master_siswa.jenis_kelamin',
            'master_kelas.nama_kelas',
            'master_jurusan.nama_jurusan',
            'master_pelajaran.nama_mapel',
            'master_penilaian.id_pelajaran',
            'master_kategori_penilaian.id_kategori',
            'master_kategori_penilaian.kategori_penilaian',
            'master_penilaian.nilai',
            'master_penilaian.progress',
            // ✅ Tambahkan kolom dari nilai_formatif
            'penilaian_formatif.nilai_formatif',
            'penilaian_formatif.nilai_kehadiran'
        )
        ->orderBy('master_siswa.nama_siswa')
        ->orderBy('master_pelajaran.nama_mapel')
        ->orderBy('master_kategori_penilaian.kategori_penilaian')
        ->orderBy('master_penilaian.progress');

    // ✅ Filter per pelajaran dan kategori penilaian
    if ($request->filled('mapel')) {
        $query->where('master_penilaian.id_pelajaran', $request->mapel);
    }

    if($request->filled('kelas')){
        $query->where('master_siswa.id_kelas', $request->kelas);
    }

    if ($request->filled('kategori')) {
        $query->where('master_penilaian.id_kategori_penilaian', $request->kategori);
    }

    // ✅ Filter untuk memastikan progress tidak kosong
    $query->whereNotNull('master_penilaian.progress')
          ->where('master_penilaian.progress', '!=', '')
          ->whereIn('master_penilaian.progress', ['Progress Test', 'Middle Test', 'Final Test']);

    $nilaiData = $query->get();
    $laporan = [];

    foreach ($nilaiData as $row) {
        // Key yang unik untuk setiap kombinasi siswa + kategori
        $key = $row->id_siswa . '_' . $row->id_kategori;

        if (!isset($laporan[$key])) {
            $laporan[$key] = [
                'id_siswa' => $row->id_siswa,
                'nama_siswa' => $row->nama_siswa,
                'nip' => $row->nip,
                'jk' => $row->jenis_kelamin,
                'kelas' => $row->nama_kelas,
                'jurusan' => $row->nama_jurusan,
                'mapel' => $row->nama_mapel,
                'id_pelajaran' => $row->id_pelajaran,
                'id_kategori' => $row->id_kategori,
                'kategori_penilaian' => $row->kategori_penilaian,
                // Nilai per progress - diinisialisasi sebagai null untuk deteksi duplikasi
                'Progress Test' => null,
                'Middle Test' => null,
                'Final Test' => null,
                // ✅ Tambahkan nilai formatif dan kehadiran
                'nilai_formatif' => 0,
                'nilai_kehadiran' => 0,
                // Perhitungan persentase
                '10%' => 0,  // Progress Test
                '30%' => 0,  // Middle Test
                '40%' => 0,  // Final Test
                '10%_formatif' => 0,  // Nilai Formatif (misal 10%)
                '10%_kehadiran' => 0, // Nilai Kehadiran (misal 10%)
                'nilai_akademik' => 0
            ];

            // ✅ Set nilai formatif dan kehadiran (hanya sekali per siswa per kategori)
            $nilaiFormatif = str_replace(',', '.', $row->nilai_formatif ?? 0);
            $nilaiKehadiran = str_replace(',', '.', $row->nilai_kehadiran ?? 0);

            $laporan[$key]['nilai_formatif'] = floatval($nilaiFormatif);
            $laporan[$key]['nilai_kehadiran'] = floatval($nilaiKehadiran);
        }

        $nilai = str_replace(',', '.', $row->nilai);
        $nilai = floatval($nilai);

        // Cek jika nilai sudah ada untuk progress ini (deteksi duplikasi)
        if ($laporan[$key][$row->progress] === null) {
            // Jika belum ada, set nilai
            $laporan[$key][$row->progress] = $nilai;
        } else {
            // Jika sudah ada (duplikasi), pertahankan nilai yang pertama
            // Atau bisa juga diubah sesuai kebutuhan:
            // - Ambil nilai tertinggi: max($laporan[$key][$row->progress], $nilai)
            // - Ambil nilai terendah: min($laporan[$key][$row->progress], $nilai)
            // - Ambil rata-rata: ($laporan[$key][$row->progress] + $nilai) / 2

            // Saat ini: pertahankan nilai yang sudah ada (nilai pertama)
            continue; // Skip, gunakan nilai yang sudah ada
        }
    }

    // ✅ Hitung nilai akademik berdasarkan bobot yang baru
    // Progress Test 10%, Middle Test 30%, Final Test 40%, Formatif 10%, Kehadiran 10%
    foreach ($laporan as &$data) {
        // Pastikan nilai tidak null sebelum dikalkulasi
        $progressTest = $data['Progress Test'] ?? 0;
        $middleTest = $data['Middle Test'] ?? 0;
        $finalTest = $data['Final Test'] ?? 0;
        $nilaiFormatif = $data['nilai_formatif'] ?? 0;
        $nilaiKehadiran = $data['nilai_kehadiran'] ?? 0;

        // Hitung nilai berdasarkan bobot
        $nilaiProgressTest = $progressTest * 0.1;   // 10%
        $nilaiMiddleTest = $middleTest * 0.3;       // 30%
        $nilaiFinalTest = $finalTest * 0.4;         // 40%
        $nilaiFormatifBobot = $nilaiFormatif * 0.1; // 10%
        $nilaiKehadiranBobot = $nilaiKehadiran * 0.1; // 10%

        // Update data dengan nilai yang sudah dikonversi
        $data['Progress Test'] = $progressTest;
        $data['Middle Test'] = $middleTest;
        $data['Final Test'] = $finalTest;

        // Simpan hasil perhitungan bobot
        $data['10%'] = round($nilaiProgressTest, 2);
        $data['30%'] = round($nilaiMiddleTest, 2);
        $data['40%'] = round($nilaiFinalTest, 2);
        $data['10%_formatif'] = round($nilaiFormatifBobot, 2);
        $data['10%_kehadiran'] = round($nilaiKehadiranBobot, 2);

        // Hitung nilai akademik total (100%)
        $data['nilai_akademik'] = round(
            $nilaiProgressTest + $nilaiMiddleTest + $nilaiFinalTest +
            $nilaiFormatifBobot + $nilaiKehadiranBobot, 2
        );
    }

    $laporan = array_values($laporan);

    // Ambil list untuk dropdown filter
    $mapelList = DB::table('master_pelajaran')->get();
    $kategoriList = DB::table('master_kategori_penilaian')->get();
    $jurusanList = DB::table('master_jurusan')->get();
    $kelasList = DB::table('master_kelas')->get();
    $progress = ['Progress Test', 'Middle Test', 'Final Test'];
    return view('laporan.pmfk2', compact('laporan', 'kategori', 'mapelList', 'kategoriList','jurusanList','kelasList','progress'));
}

   public function nilaiX(Request $request){
    $query = DB::table('master_penilaian')
        ->join('master_siswa', 'master_siswa.id_siswa', '=', 'master_penilaian.id_siswa')
        ->join('master_kelas', 'master_kelas.id_kelas', '=', 'master_siswa.id_kelas')
        ->join('master_jurusan', 'master_jurusan.id_jurusan', '=', 'master_kelas.id_jurusan')
        ->join('master_pelajaran', 'master_pelajaran.id_pelajaran', '=', 'master_penilaian.id_pelajaran')
        ->join('master_kategori_penilaian', 'master_kategori_penilaian.id_kategori', '=', 'master_penilaian.id_kategori_penilaian')
        // ✅ Join dengan tabel penilaian_formatif
        ->leftJoin('penilaian_formatif', function($join) {
            $join->on('penilaian_formatif.id_siswa', '=', 'master_siswa.id_siswa')
                 ->on('penilaian_formatif.id_kategori_penilaian', '=', 'master_penilaian.id_kategori_penilaian');
        })
        // ✅ Join dengan tabel nilai_mental (per siswa saja)
        ->leftJoin('nilai_mental', 'nilai_mental.id_siswa', '=', 'master_siswa.id_siswa')
        ->select(
            'master_siswa.id_siswa',
            'master_siswa.nama_siswa',
            'master_siswa.nip',
            'master_siswa.jenis_kelamin',
            'master_kelas.nama_kelas',
            'master_jurusan.nama_jurusan',
            'master_pelajaran.nama_mapel',
            'master_penilaian.id_pelajaran',
            'master_kategori_penilaian.id_kategori',
            'master_kategori_penilaian.kategori_penilaian',
            'master_penilaian.nilai',
            'master_penilaian.progress',
            // ✅ Kolom dari penilaian_formatif
            'penilaian_formatif.nilai_formatif',
            'penilaian_formatif.nilai_kehadiran',
            // ✅ Kolom dari nilai_mental
            'nilai_mental.nilai_mental'
        )
        ->orderBy('master_siswa.nama_siswa')
        ->orderBy('master_pelajaran.nama_mapel')
        ->orderBy('master_kategori_penilaian.kategori_penilaian')
        ->orderBy('master_penilaian.progress');

    // ✅ Filter hanya per kelas dan mapel
    if($request->filled('kelas')){
        $query->where('master_siswa.id_kelas', $request->kelas);
    }

    if ($request->filled('mapel')) {
        $query->where('master_penilaian.id_pelajaran', $request->mapel);
    }

    // ✅ Filter untuk memastikan progress tidak kosong
    $query->whereNotNull('master_penilaian.progress')
          ->where('master_penilaian.progress', '!=', '')
          ->whereIn('master_penilaian.progress', ['Progress Test', 'Middle Test', 'Final Test']);

    $nilaiData = $query->get();
    $laporan = [];

    foreach ($nilaiData as $row) {
        // ✅ Key yang unik per siswa saja (bukan per kategori)
        $key = $row->id_siswa;

        if (!isset($laporan[$key])) {
            $laporan[$key] = [
                'id_siswa' => $row->id_siswa,
                'nama_siswa' => $row->nama_siswa,
                'nip' => $row->nip,
                'jk' => $row->jenis_kelamin,
                'kelas' => $row->nama_kelas,
                'jurusan' => $row->nama_jurusan,
                'mapel' => $row->nama_mapel,
                'id_pelajaran' => $row->id_pelajaran,

                // ✅ Array untuk menyimpan semua nilai per kategori dan progress
                'nilai_data' => [],

                // ✅ Nilai mental
                'nilai_mental' => 0,

                // ✅ Nilai akademik dan perhitungan
                'nilai_akademik' => 0,      // rata-rata
                'total_akademik' => 0,      // total semua nilai
                'x7' => 0,                  // nilai_akademik × 70%
                'x3' => 0,                  // nilai_mental × 30%
                'total' => 0,
                'nilai_final' => 0          // (x7 + x3) ÷ 10
            ];

            // ✅ Set nilai mental (hanya sekali per siswa)
            $nilaiMental = str_replace(',', '.', $row->nilai_mental ?? 0);
            $laporan[$key]['nilai_mental'] = floatval($nilaiMental);
        }

        // ✅ Simpan semua data nilai ke dalam array
        $nilai = str_replace(',', '.', $row->nilai);
        $nilai = floatval($nilai);

        $nilaiFormatif = str_replace(',', '.', $row->nilai_formatif ?? 0);
        $nilaiKehadiran = str_replace(',', '.', $row->nilai_kehadiran ?? 0);

        $dataKey = $row->id_kategori . '_' . $row->progress;

        // Simpan nilai progress test, middle test, final test
        if (!isset($laporan[$key]['nilai_data'][$dataKey])) {
            $laporan[$key]['nilai_data'][$dataKey] = [
                'kategori' => $row->kategori_penilaian,
                'progress' => $row->progress,
                'nilai' => $nilai,
                'nilai_formatif' => floatval($nilaiFormatif),
                'nilai_kehadiran' => floatval($nilaiKehadiran)
            ];
        }
    }

    // ✅ Hitung nilai akademik per siswa dari semua kategori
    foreach ($laporan as &$data) {
        $allNilai = [];

        // Kumpulkan semua nilai dari semua kategori dan progress
        foreach ($data['nilai_data'] as $nilaiItem) {
            $allNilai[] = $nilaiItem['nilai'];
            if ($nilaiItem['nilai_formatif'] > 0) {
                $allNilai[] = $nilaiItem['nilai_formatif'];
            }
            if ($nilaiItem['nilai_kehadiran'] > 0) {
                $allNilai[] = $nilaiItem['nilai_kehadiran'];
            }
        }

        if (!empty($allNilai)) {
            // ✅ Hitung Total Akademik (semua nilai dijumlahkan)
            $totalAkademik = array_sum($allNilai);
            $data['total_akademik'] = round($totalAkademik, 2);

            // ✅ Hitung Nilai Akademik (rata-rata dari semua nilai)
            $nilaiAkademik = $totalAkademik / count($allNilai);
            $data['nilai_akademik'] = round($nilaiAkademik, 2);

            // ✅ Hitung x7 (Nilai Akademik × 70%)
            $x7 = $nilaiAkademik * 0.7;
            $data['x7'] = round($x7, 2);

            // ✅ Hitung x3 (Nilai Mental × 30%)
            $x3 = $data['nilai_mental'] * 0.3;
            $data['x3'] = round($x3, 2);

            // ✅ Hitung Nilai Final ((x7 + x3) ÷ 10)
            $data['total'] = round($x7,2) + round($x3,2);
            $nilaiFinal = ($x7 + $x3) / 10;
            $data['nilai_final'] = round($nilaiFinal, 2);
        }
    }

    $laporan = array_values($laporan);

    // ✅ Ambil list untuk dropdown filter (hanya kelas dan mapel)
    $mapelList = DB::table('master_pelajaran')->get();
    $jurusanList = DB::table('master_jurusan')->get();
    $kelasList = DB::table('master_kelas')->get();

    return view('laporan.pmfk2', compact('laporan', 'mapelList', 'jurusanList', 'kelasList'));
}
public function rekap(Request $request){
    $query = DB::table('master_penilaian')
        ->join('master_siswa', 'master_siswa.id_siswa', '=', 'master_penilaian.id_siswa')
        ->join('master_kelas', 'master_kelas.id_kelas', '=', 'master_siswa.id_kelas')
        ->join('master_jurusan', 'master_jurusan.id_jurusan', '=', 'master_kelas.id_jurusan')
        ->join('master_pelajaran', 'master_pelajaran.id_pelajaran', '=', 'master_penilaian.id_pelajaran')
        ->join('master_kategori_penilaian', 'master_kategori_penilaian.id_kategori', '=', 'master_penilaian.id_kategori_penilaian')
        // ✅ Join dengan tabel penilaian_formatif
        ->leftJoin('penilaian_formatif', function($join) {
            $join->on('penilaian_formatif.id_siswa', '=', 'master_siswa.id_siswa')
                 ->on('penilaian_formatif.id_kategori_penilaian', '=', 'master_penilaian.id_kategori_penilaian');
        })
        // ✅ Join dengan tabel nilai_mental
        ->leftJoin('nilai_mental', 'nilai_mental.id_siswa', '=', 'master_siswa.id_siswa')
        ->select(
            'master_siswa.id_siswa',
            'master_siswa.nama_siswa',
            'master_siswa.nip',
            'master_kelas.nama_kelas',
            'master_jurusan.nama_jurusan',
            'master_pelajaran.nama_mapel',
            'master_penilaian.id_pelajaran',
            'master_kategori_penilaian.kategori_penilaian',
            'master_penilaian.nilai',
            'master_penilaian.progress',
            'penilaian_formatif.nilai_formatif',
            'penilaian_formatif.nilai_kehadiran',
            'nilai_mental.nilai_mental'
        )
        ->orderBy('master_siswa.nama_siswa');

    // ✅ Filter per kelas dan mapel
    if($request->filled('kelas')){
        $query->where('master_siswa.id_kelas', $request->kelas);
    }

    if ($request->filled('mapel')) {
        $query->where('master_penilaian.id_pelajaran', $request->mapel);
    }

    // ✅ Filter untuk kategori bahasa yang diinginkan
    $query->whereIn('master_kategori_penilaian.kategori_penilaian', [
        'MENDENGAR', 'MEMBACA', 'MENULIS', 'BERBICARA', 'TATA BAHASA'
    ]);

    // ✅ Filter progress
    $query->whereNotNull('master_penilaian.progress')
          ->where('master_penilaian.progress', '!=', '')
          ->whereIn('master_penilaian.progress', ['Progress Test', 'Middle Test', 'Final Test']);

    $nilaiData = $query->get();
    $laporan = [];

    foreach ($nilaiData as $row) {
        // ✅ Key per siswa
        $key = $row->id_siswa;

        if (!isset($laporan[$key])) {
            $laporan[$key] = [
                'nama_siswa' => $row->nama_siswa,
                'nip' => $row->nip,
                'kelas' => $row->nama_kelas,
                'jurusan' => $row->nama_jurusan,
                'mapel' => $row->nama_mapel,

                // ✅ Kategori penilaian bahasa
                'MENDENGAR' => 0,
                'MEMBACA' => 0,
                'MENULIS' => 0,
                'BERBICARA' => 0,
                'TATA BAHASA' => 0,

                // ✅ Nilai yang diminta
                'JUMLAH AKADEMIK' => 0,
                'NILAI RATA-RATA AKADEMIK' => 0,
                'X7' => 0,                          // Nilai Akademik × 70%
                'NILAI RATA-RATA MENTAL' => 0,
                'X3' => 0,                          // Nilai Mental × 30%
                'TOTAL' => 0,                       // X7 + X3
                'Nilai Akhir' => 0,                 // TOTAL ÷ 10
                'Ranking' => 0,
                'Klasifikasi' => '',

                // Helper array untuk perhitungan
                'nilai_per_kategori' => [],
                'nilai_mental_raw' => 0
            ];

            // Set nilai mental
            $nilaiMental = str_replace(',', '.', $row->nilai_mental ?? 0);
            $laporan[$key]['nilai_mental_raw'] = floatval($nilaiMental);
            $laporan[$key]['NILAI RATA-RATA MENTAL'] = floatval($nilaiMental);
        }

        // ✅ Kumpulkan semua nilai per kategori
        $kategori = strtoupper($row->kategori_penilaian);
        if (!isset($laporan[$key]['nilai_per_kategori'][$kategori])) {
            $laporan[$key]['nilai_per_kategori'][$kategori] = [];
        }

        // Tambahkan nilai progress test
        $nilai = str_replace(',', '.', $row->nilai);
        $nilai = floatval($nilai);
        if ($nilai > 0) {
            $laporan[$key]['nilai_per_kategori'][$kategori][] = $nilai;
        }

        // Tambahkan nilai formatif dan kehadiran jika ada
        $nilaiFormatif = str_replace(',', '.', $row->nilai_formatif ?? 0);
        $nilaiKehadiran = str_replace(',', '.', $row->nilai_kehadiran ?? 0);

        if (floatval($nilaiFormatif) > 0) {
            $laporan[$key]['nilai_per_kategori'][$kategori][] = floatval($nilaiFormatif);
        }
        if (floatval($nilaiKehadiran) > 0) {
            $laporan[$key]['nilai_per_kategori'][$kategori][] = floatval($nilaiKehadiran);
        }
    }

    // ✅ Hitung nilai per kategori dan nilai akhir
    foreach ($laporan as &$data) {
        $totalNilaiSemua = [];

        // Hitung rata-rata per kategori
        foreach (['MENDENGAR', 'MEMBACA', 'MENULIS', 'BERBICARA', 'TATA BAHASA'] as $kategori) {
            if (isset($data['nilai_per_kategori'][$kategori]) && !empty($data['nilai_per_kategori'][$kategori])) {
                $rataRata = array_sum($data['nilai_per_kategori'][$kategori]) / count($data['nilai_per_kategori'][$kategori]);
                $data[$kategori] = round($rataRata, 2);
                $totalNilaiSemua[] = $rataRata;
            }
        }

        if (!empty($nilaiKategoriSemua)) {
            // ✅ JUMLAH AKADEMIK (total semua kategori)
            $jumlahAkademik = array_sum($nilaiKategoriSemua);
            $data['JUMLAH AKADEMIK'] = round($jumlahAkademik, 2);

            // ✅ NILAI RATA-RATA AKADEMIK
            $nilaiRataRataAkademik = $jumlahAkademik / count($nilaiKategoriSemua);
            $data['NILAI RATA-RATA AKADEMIK'] = round($nilaiRataRataAkademik, 2);

            // ✅ X7 (Nilai Rata-rata Akademik × 70%)
            $x7 = $nilaiRataRataAkademik * 0.7;
            $data['X7'] = round($x7, 2);

            // ✅ X3 (Nilai Mental × 30%)
            $x3 = $data['nilai_mental_raw'] * 0.3;
            $data['X3'] = round($x3, 2);

            // ✅ TOTAL (X7 + X3)
            $total = $x7 + $x3;
            $data['TOTAL'] = round($total, 2);

            // ✅ Nilai Akhir (TOTAL ÷ 10)
            $nilaiAkhir = $total / 10;
            $data['Nilai Akhir'] = round($nilaiAkhir, 2);
        }

        // Hapus helper array
        unset($data['nilai_per_kategori'], $data['nilai_mental_raw']);
    }

    $laporan = array_values($laporan);

    // ✅ Sorting berdasarkan Nilai Akhir untuk ranking
    usort($laporan, function($a, $b) {
        return $b['Nilai Akhir'] <=> $a['Nilai Akhir'];
    });

    // ✅ Assign ranking dan klasifikasi
    foreach ($laporan as $index => &$data) {
        // Ranking berurutan dari 1
        $data['Ranking'] = $index + 1;

        // ✅ Klasifikasi berdasarkan Nilai Akhir
        $nilaiAkhir = $data['Nilai Akhir'];

        if ($nilaiAkhir >= 8.6) {
            $data['Klasifikasi'] = 'SANGAT MEMUASKAN';
        } elseif ($nilaiAkhir >= 8.1) {
            $data['Klasifikasi'] = 'MEMUASKAN';
        } elseif ($nilaiAkhir >= 7.5) {
            $data['Klasifikasi'] = 'BAIK';
        } elseif ($nilaiAkhir >= 6.5) {
            $data['Klasifikasi'] = 'CUKUP';
        } else {
            $data['Klasifikasi'] = 'KURANG';
        }
    }

    // Ambil list untuk dropdown filter
    $mapelList = DB::table('master_pelajaran')->get();
    $jurusanList = DB::table('master_jurusan')->get();
    $kelasList = DB::table('master_kelas')->get();


    return view('laporan.pmfk2', compact('laporan', 'mapelList', 'jurusanList', 'kelasList'));
}
}
