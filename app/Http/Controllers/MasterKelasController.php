<?php

namespace App\Http\Controllers;

use App\Exports\TemplateKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Imports\KelasImport;
use Maatwebsite\Excel\Facades\Excel;

class MasterKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('master_kelas')
            ->join('master_tahun', 'master_tahun.id_tahun', '=', 'master_kelas.id_tahun')
            ->join('master_jurusan', 'master_jurusan.id_jurusan', '=', 'master_kelas.id_jurusan')
            ->select('master_kelas.*', 'master_tahun.tahun_ajaran', 'master_jurusan.nama_jurusan')
            ->get();

        $jurusan = DB::table('master_jurusan')->get();
        $tahun = DB::table('master_tahun')->get();
        return view('master_kelas.index', compact('data', 'jurusan', 'tahun'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'nama_kelas' => 'required|string',
            'id_jurusan' => 'required',
            'id_tahun' => 'required'
        ]);
        $validate['created_at'] = now();
        $validate['updated_at'] = now();
        DB::table('master_kelas')->insert($validate);
        return redirect()->back()->with('success', 'Menambahkan Kelas Baru !!');
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
        $data = DB::table('master_kelas')->where('id_kelas', $id)->first();
        $jurusan = DB::table('master_jurusan')->get();
        $tahun = DB::table('master_tahun')->get();
        return view('master_kelas.edit', compact('data', 'jurusan', 'tahun'));
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
            'nama_kelas' => 'required|string',
            'id_jurusan' => 'required',
            'id_tahun' => 'required'
        ]);
        $validate['updated_at'] = now();
        DB::table('master_kelas')->where('id_kelas', $id)->update($validate);
        return redirect()->route('master_kelas.index')->with('success', 'Mengubah Kelas !!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('master_kelas')->where('id_kelas', $id)->delete();

        return redirect()->back()->with('success', 'Menghapus Kelas !!');
    }

    public function exportTemplate(){
          return Excel::download(new TemplateKelas, 'template_kelas.xlsx');
    }

     public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new KelasImport, $request->file('file'));

        return redirect()->route('master_kelas.index')->with('success', 'Data Kelas berhasil diimpor.');
    }
}
