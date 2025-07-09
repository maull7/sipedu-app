<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KatergoriPenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('master_kategori_penilaian')->get();
        return view('master_kategori.index', compact('data'));
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
            'kategori_penilaian' => 'required|string|max:255'
        ]);
        DB::table('master_kategori_penilaian')->insert($validate);
        return redirect()->back()->with('success', 'Menambahkan Kategori Penilaian');
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
        $data = DB::table('master_kategori_penilaian')->where('id_kategori', $id)->first();
        return view('master_kategori.edit', compact('data'));
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
            'kategori_penilaian' => 'required|string|max:255'
        ]);
        DB::table('master_kategori_penilaian')->where('id_kategori', $id)->update($validate);
        return redirect()->route('master_kategori.index')->with('success', 'Mengubah Kategori Penilaian');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('master_kategori_penilaian')->where('id_kategori', $id)->delete();
        return redirect()->back()->with('success', 'Menghapus Kategori Penilaian');
    }
}
