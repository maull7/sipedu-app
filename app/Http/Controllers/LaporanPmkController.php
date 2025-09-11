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
            'master_penilaian.progress'
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
        // Key yang unik untuk setiap kombinasi siswa + mapel + kategori
        $key = $row->id_siswa ;

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
                'kategori_penilaian' => $row->kategori_penilaian,
                // Nilai per progress - diinisialisasi sebagai null untuk deteksi duplikasi
                'Progress Test' => null,
                'Middle Test' => null,
                'Final Test' => null,
                '10%' => 0,
                '30%' => 0,
                '40%' => 0,
                'nilai_akademik' => 0
            ];
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

    // Hitung nilai akademik berdasarkan bobot
    // Progress Test 10%, Middle Test 30%, Final Test 40%
    foreach ($laporan as &$data) {
        // Pastikan nilai tidak null sebelum dikalkulasi
        $progressTest = $data['Progress Test'] ?? 0;
        $middleTest = $data['Middle Test'] ?? 0;
        $finalTest = $data['Final Test'] ?? 0;

        $nilaiProgressTest = $progressTest * 0.1;
        $nilaiMiddleTest = $middleTest * 0.3;
        $nilaiFinalTest = $finalTest * 0.4;

        $data['Progress Test'] = $progressTest; // Convert null ke 0 jika ada
        $data['Middle Test'] = $middleTest;
        $data['Final Test'] = $finalTest;

        $data['10%'] = round($nilaiProgressTest, 2);
        $data['30%'] = round($nilaiMiddleTest, 2);
        $data['40%'] = round($nilaiFinalTest, 2);

        $data['nilai_akademik'] = round($nilaiProgressTest + $nilaiMiddleTest + $nilaiFinalTest, 2);
    }

    $laporan = array_values($laporan);

    // Ambil list untuk dropdown filter
    $mapelList = DB::table('master_pelajaran')->get();
    $kategoriList = DB::table('master_kategori_penilaian')->get();

    $jurusanList = DB::table('master_jurusan')->get();
    $kelasList = DB::table('master_kelas')->get();
    $progress = ['Progress Test', 'Middle Test', 'Final Test'];


    return view('laporan.pmfk', compact('laporan', 'kategori', 'mapelList', 'kategoriList','jurusanList','kelasList','progress'));
}
}
