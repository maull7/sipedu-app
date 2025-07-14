<?php

namespace App\Http\Controllers;

use App\Exports\TemplateMapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Imports\ImportMapel;
use Maatwebsite\Excel\Facades\Excel;

class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('master_pelajaran')->get();
        return view('master_mapel.index',compact('data'));
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
        DB::table('master_pelajaran')->insert([
            'nama_mapel' => $request->nama_mapel
        ]);
        return redirect()->back()->with('success','Berhasil Menambahkan Mata Pelajaran !!');
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
        $data = DB::table('master_pelajaran')->where('id_pelajaran',$id)->first();
        return view('master_mapel.edit',compact('data'));
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
         DB::table('master_pelajaran')->where('id_pelajaran',$id)->update([
            'nama_mapel' => $request->nama_mapel
        ]);
        return redirect()->route('master_mapel.index')->with('success','Berhasil Mengubah Mata Pelajaran !!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('master_pelajaran')->where('id_pelajaran',$id)->delete();
        return redirect()->back()->with('success','Berhasil Menghapus Mata Pelajaran');
    }
     public function exportTemplate(){
          return Excel::download(new TemplateMapel, 'template_mapel.xlsx');
    }

     public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ImportMapel, $request->file('file'));

        return redirect()->route('master_mapel.index')->with('success', 'Data Mata Pelajaran berhasil diimpor.');
    }
}
