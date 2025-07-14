<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\TemplateTahun;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Imports\ImportTahun;
use Maatwebsite\Excel\Facades\Excel;

class MasterTahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('master_tahun')->get();
        return view('master_tahun.index', compact('data'));
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
            'tahun_ajaran' => 'required|string'
        ]);
        DB::table('master_tahun')->insert($validate);
        return redirect()->back()->with('success', 'Menambahkan Tahun Ajaran');
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
        $data = DB::table('master_tahun')->where('id_tahun', $id)->first();
        return view('master_tahun.edit', compact('data'));
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
            'tahun_ajaran' => 'required|string'
        ]);
        DB::table('master_tahun')->where('id_tahun', $id)->update($validate);
        return redirect()->route('master_tahun.index')->with('success', 'Menambahkan Tahun Ajaran');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('master_tahun')->where('id_tahun', $id)->delete();
        return redirect()->back()->with('success', 'Menghapus Tahun Ajaran');
    }

    public function exportTemplate(){
          return Excel::download(new TemplateTahun, 'template_tahun_ajaran.xlsx');
    }

     public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ImportTahun, $request->file('file'));

        return redirect()->route('master_tahun.index')->with('success', 'Data Tahun Ajaran berhasil diimpor.');
    }
}
