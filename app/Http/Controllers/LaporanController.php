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
            'master_penilaian.nilai'
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

    return view('laporan.index', compact('laporan', 'kategori', 'jurusanList', 'kelasList', 'mapelList'));
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
            'master_penilaian.nilai'
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

    // Sort dan kasih ranking
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
            'master_tahun.tahun_ajaran'
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
            'jurusan' => $row->nama_jurusan,
            'kelas' => $row->nama_kelas,
            'tahun' => $row->tahun_ajaran
        ];
    }

    $pdf = Pdf::loadView('laporan.pdf', ['laporan' => $laporan]);

    return $pdf->download('rekap-nilai.pdf');
}

}
