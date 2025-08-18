<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenilaianTemplateExport;
use App\Imports\ImportPenilaian;

class PenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    $query = DB::table('master_penilaian')
        ->leftJoin('master_siswa', 'master_siswa.id_siswa', '=', 'master_penilaian.id_siswa')
        ->leftJoin('master_pelajaran', 'master_pelajaran.id_pelajaran', '=', 'master_penilaian.id_pelajaran')
        ->leftJoin('master_kategori_penilaian', 'master_kategori_penilaian.id_kategori', '=', 'master_penilaian.id_kategori_penilaian')
        ->leftJoin('master_kelas', 'master_kelas.id_kelas', '=', 'master_siswa.id_kelas')
        ->select(
            'master_penilaian.*',
            'master_siswa.nama_siswa',
            'master_kelas.nama_kelas',
            'master_pelajaran.nama_mapel',
            'master_kategori_penilaian.kategori_penilaian'
        );

            // ✅ Tambahkan filter jika ada request pelajaran
            if ($request->filled('pelajaran')) {
                $query->where('master_penilaian.id_pelajaran', $request->pelajaran);
            }

            $data = $query->get();

            // Untuk dropdown filter
            $mapel = DB::table('master_pelajaran')->get();

            return view('master_penilaian.index', compact('data', 'mapel'));
        }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $siswa = DB::table('master_siswa')->get();
        $mapel = DB::table('master_pelajaran')->get();
        $kategori = DB::table('master_kategori_penilaian')->get();

        return view('master_penilaian.create',compact('siswa','mapel','kategori'));


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'id_siswa' => 'required',
            'id_pelajaran' => 'required',
            'id_kategori_penilaian' => 'required',
            'nilai' => 'required',
            'kepribadian' => 'required',
            'intelek' => 'required'
        ]);

        DB::table('master_penilaian')->insert($validate);

        return redirect()->route('penilaian.index')->with('success','Berhasil melakukan Penilaian');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $penilaian = DB::table('master_penilaian')->where('id_penilaian',$id)->first();
        $siswa = DB::table('master_siswa')->get();
        $mapel = DB::table('master_pelajaran')->get();
        $kategori = DB::table('master_kategori_penilaian')->get();

        return view('master_penilaian.edit',compact('penilaian','siswa','mapel','kategori'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validate = $request->validate([
            'id_siswa' => 'required',
            'id_pelajaran' => 'required',
            'id_kategori_penilaian' => 'required',
            'nilai' => 'required',
             'kepribadian' => 'required',
            'intelek' => 'required'
        ]);

        DB::table('master_penilaian')->where('id_penilaian',$id)->update($validate);

        return redirect()->route('penilaian.index')->with('success','Berhasil Mengubah Penilaian');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('master_penilaian')->where('id_penilaian',$id)->delete();
        return redirect()->back()->with('success','Menghapus Nilai ');
    }

    public function exportTemplate()
    {
        // Ambil kategori penilaian dari database
        $kategoriPenilaian = DB::table('master_kategori_penilaian')
            ->pluck('kategori_penilaian')
            ->toArray();

        return Excel::download(new PenilaianTemplateExport($kategoriPenilaian), 'template_penilaian.xlsx');
    }
    public function importNilai(Request $request)
{
    // validasi input file
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);



    try {
        // jalankan proses import
        Excel::import(new ImportPenilaian, $request->file('file'));

        return redirect()->back()->with('success', 'Data penilaian berhasil diimport.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
    }
}
}
