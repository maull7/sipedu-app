<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MasterJurusanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('master_jurusan')->get();
        return view('master_jurusan.index', compact('data'));
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
            'nama_jurusan' => 'required|string'
        ]);
        $validate['created_at'] = now();
        $validate['updated_at'] = now();

        DB::table('master_jurusan')->insert($validate);
        return redirect()->back()->with('success', 'Menambahkan Jurusan');
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
        $data = DB::table('master_jurusan')->where('id_jurusan', $id)->first();
        return view('master_jurusan.edit', compact('data'));
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
            'nama_jurusan' => 'required|string'
        ]);
        $validate['updated_at'] = now();

        DB::table('master_jurusan')->where('id_jurusan', $id)->update($validate);
        return redirect()->route('master_jurusan.index')->with('success', 'Mengubah Jurusan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('master_jurusan')->where('id_jurusan', $id)->delete();
        return redirect()->back()->with('success', 'Menghapus Jurusan');
    }
}
