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
        ->leftJoin('master_tahun', 'master_tahun.id_tahun', '=', 'master_kelas.id_tahun')
        ->select(
            'master_siswa.id_siswa',
            'master_siswa.nama_siswa',
            'master_siswa.nip',
            'master_siswa.jenis_kelamin',
            'master_kelas.nama_kelas',
            'master_jurusan.nama_jurusan',
            'master_pelajaran.nama_mapel',
            'master_kategori_penilaian.kategori_penilaian',
            'master_penilaian.nilai',
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
        $siswaId = $row->id_siswa;
        $mapel = $row->nama_mapel;

        // Buat key unik berdasarkan siswa dan mata pelajaran
        $key = $siswaId . '_' . $mapel;

        if (!isset($laporan[$key])) {
            $laporan[$key] = [
                'nama_siswa' => $row->nama_siswa,
                'nip' => $row->nip,
                'jk' => $row->jenis_kelamin,
                'kelas' => $row->nama_kelas,
                'jurusan' => $row->nama_jurusan,
                'mapel' => $row->nama_mapel,
                'tahun' => $row->tahun_ajaran,
                'progress' => $row->progress,
                'kepribadian' => $row->kepribadian,
                'intelek' => $row->intelek,
                'nilai_kategori' => [],
                'total_nilai' => 0,
                'count_kategori' => 0,
                'rata_rata' => 0
            ];
        }

        // Tambahkan nilai per kategori
        $nilai = str_replace(',', '.', $row->nilai);
        $nilai = floatval($nilai);

        $laporan[$key]['nilai_kategori'][$row->kategori_penilaian] = $nilai;
        $laporan[$key]['total_nilai'] += $nilai;
        $laporan[$key]['count_kategori'] += 1;
    }

    // Hitung rata-rata untuk setiap siswa per mata pelajaran
    foreach ($laporan as &$data) {
        if ($data['count_kategori'] > 0) {
            $data['rata_rata'] = round($data['total_nilai'] / $data['count_kategori'], 2);
        }

        // Hitung rata-rata kepribadian dan intelek jika ada
        if (!empty($data['kepribadian']) && !empty($data['intelek'])) {
            $data['rata_rata_kepribadian_intelek'] = round((floatval($data['kepribadian']) + floatval($data['intelek'])) / 2, 2);
        }
    }

    // Convert ke array biasa dan urutkan
    $laporan = array_values($laporan);

    // Urutkan berdasarkan nama siswa dan mata pelajaran
    usort($laporan, function($a, $b) {
        $cmp = strcmp($a['nama_siswa'], $b['nama_siswa']);
        if ($cmp == 0) {
            return strcmp($a['mapel'], $b['mapel']);
        }
        return $cmp;
    });


    $pdf = Pdf::loadView('laporan.pdf1', ['laporan' => $laporan]);

    return $pdf->download('lampiran1.pdf');
}

}
