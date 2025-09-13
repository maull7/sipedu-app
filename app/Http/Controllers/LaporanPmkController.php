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

   public function nilaiX(Request $request)
{
    $kategori = DB::table('master_kategori_penilaian')->pluck('kategori_penilaian', 'id_kategori');

    $query = DB::table('master_penilaian')
        ->join('master_siswa', 'master_siswa.id_siswa', '=', 'master_penilaian.id_siswa')
        ->join('master_kelas', 'master_kelas.id_kelas', '=', 'master_siswa.id_kelas')
        ->join('master_jurusan', 'master_jurusan.id_jurusan', '=', 'master_kelas.id_jurusan')
        ->join('master_pelajaran', 'master_pelajaran.id_pelajaran', '=', 'master_penilaian.id_pelajaran')
        ->join('master_kategori_penilaian', 'master_kategori_penilaian.id_kategori', '=', 'master_penilaian.id_kategori_penilaian')
        // Join dengan tabel nilai_formatif
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
            'master_siswa.jenis_kelamin',
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
        ->orderBy('master_siswa.nama_siswa')
        ->orderBy('master_pelajaran.nama_mapel')
        ->orderBy('master_kategori_penilaian.kategori_penilaian')
        ->orderBy('master_penilaian.progress');

    // Filter per pelajaran dan kategori penilaian
    if ($request->filled('mapel')) {
        $query->where('master_penilaian.id_pelajaran', $request->mapel);
    }

    if($request->filled('kelas')){
        $query->where('master_siswa.id_kelas', $request->kelas);
    }

    if ($request->filled('kategori')) {
        $query->where('master_penilaian.id_kategori_penilaian', $request->kategori);
    }

    // Filter untuk memastikan progress tidak kosong
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
                'jk' => $row->jenis_kelamin,
                'kelas' => $row->nama_kelas,
                'jurusan' => $row->nama_jurusan,
                'mapel' => $row->nama_mapel,
                'id_pelajaran' => $row->id_pelajaran,
                'id_kategori' => $row->id_kategori,
                'kategori_penilaian' => $row->kategori_penilaian,
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

    // Step 2: Hitung nilai akademik per kategori
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

        // Nilai akademik per kategori
        $data['nilai_akademik_kategori'] = round(
            $nilaiProgressTest + $nilaiMiddleTest + $nilaiFinalTest +
            $nilaiFormatifBobot + $nilaiKehadiranBobot, 2
        );
    }

    // Step 3: Kelompokkan per siswa dan hitung rata-rata semua kategori
    $laporanPerSiswa = [];

    foreach ($nilaiPerKategori as $data) {
        $siswaId = $data['id_siswa'];

        if (!isset($laporanPerSiswa[$siswaId])) {
            $laporanPerSiswa[$siswaId] = [
                'id_siswa' => $data['id_siswa'],
                'nama_siswa' => $data['nama_siswa'],
                'nip' => $data['nip'],
                'jk' => $data['jk'],
                'kelas' => $data['kelas'],
                'jurusan' => $data['jurusan'],
                'nilai_mental' => $data['nilai_mental'],
                'mapel' => $data['mapel'],
                'nilai_akademik_per_kategori' => [],
                'total_nilai_akademik_kategori' => 0,
                'jumlah_kategori' => 0
            ];
        }

        // Kumpulkan nilai akademik per kategori
        $laporanPerSiswa[$siswaId]['nilai_akademik_per_kategori'][] = $data['nilai_akademik_kategori'];
        $laporanPerSiswa[$siswaId]['total_nilai_akademik_kategori'] += $data['nilai_akademik_kategori'];
        $laporanPerSiswa[$siswaId]['jumlah_kategori']++;
    }

    // Step 4: Hitung nilai akhir per siswa
    $laporan = [];

    foreach ($laporanPerSiswa as $siswaData) {
        // 1. Nilai akademik = rata-rata semua kategori
        $nilai_akademik = $siswaData['jumlah_kategori'] > 0
            ? round($siswaData['total_nilai_akademik_kategori'] / $siswaData['jumlah_kategori'], 2)
            : 0;

        // 2. Total nilai akademik kategori (untuk referensi)
        $total_nilai_akademik_kategori = round($siswaData['total_nilai_akademik_kategori'], 2);

        // 3. Nilai mental
        $nilai_mental = $siswaData['nilai_mental'];

        // 4. Nilai akademik * 7
        $x7 = round($nilai_akademik * 7, 2);

        // 5. Nilai mental * 3
        $x3 = round($nilai_mental * 3, 2);

        // 6. Total akhir = x7 + x3
        $total_akhir = round($x7 + $x3, 2);

        // 7. Nilai akhir = total akhir / 10
        $nilai_akhir = round($total_akhir / 10, 2);

        $laporan[] = [
            'id_siswa' => $siswaData['id_siswa'],
            'nama_siswa' => $siswaData['nama_siswa'],
            'nip' => $siswaData['nip'],
            'jk' => $siswaData['jk'],
            'kelas' => $siswaData['kelas'],
            'jurusan' => $siswaData['jurusan'],
            'nilai_akademik' => $nilai_akademik,
            'total_nilai_akademik_kategori' => $total_nilai_akademik_kategori,
            'nilai_mental' => $nilai_mental,
            'x7' => $x7,
            'x3' => $x3,
            'total_akhir' => $total_akhir,
            'nilai_akhir' => $nilai_akhir,
            'jumlah_kategori' => $siswaData['jumlah_kategori'],
            'mapel' => $siswaData['mapel']
        ];
    }

    // Ambil list untuk dropdown filter
    $mapelList = DB::table('master_pelajaran')->get();
    $kategoriList = DB::table('master_kategori_penilaian')->get();
    $jurusanList = DB::table('master_jurusan')->get();
    $kelasList = DB::table('master_kelas')->get();
    $progress = ['Progress Test', 'Middle Test', 'Final Test'];


    return view('laporan.pmfk2', compact('laporan', 'kategori', 'mapelList', 'kategoriList','jurusanList','kelasList','progress'));
}
public function rekap(Request $request){
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
        if ($nilaiAkhir >= 8.6) {
            $klasifikasi = 'SANGAT MEMUASKAN';
        } elseif ($nilaiAkhir >= 8.1) {
            $klasifikasi = 'MEMUASKAN';
        } elseif ($nilaiAkhir >= 7.5) {
            $klasifikasi = 'BAIK';
        } elseif ($nilaiAkhir >= 6.5) {
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
            'Klasifikasi' => $klasifikasi,
            'Ranking' => 0 // Will be set after sorting
        ];
    }

    // Sorting berdasarkan Nilai Akhir untuk ranking
    usort($laporan, function($a, $b) {
        return $b['Nilai Akhir'] <=> $a['Nilai Akhir'];
    });

    // Assign ranking
    foreach ($laporan as $index => &$data) {
        $data['Ranking'] = $index + 1;
    }

    // Ambil list untuk dropdown filter
    $mapelList = DB::table('master_pelajaran')->get();
    $jurusanList = DB::table('master_jurusan')->get();
    $kelasList = DB::table('master_kelas')->get();
    $kategoriTersedia = DB::table('master_kategori_penilaian')->get();

    return view('laporan.pmfk2', compact('laporan', 'mapelList', 'jurusanList', 'kelasList', 'kategoriTersedia'));
}
}
