<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MasterSiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $siswa = DB::table('master_siswa')
            ->join('master_kelas', 'master_kelas.id_kelas', '=', 'master_siswa.id_kelas')
            ->join('master_jurusan', 'master_jurusan.id_jurusan', '=', 'master_kelas.id_jurusan')
            ->get();
        $kelas = DB::table('master_kelas')->get();
        return view('master_siswa.index', compact('siswa', 'kelas'));
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
            'nama_siswa' => 'required|string|max:255',
            'alamat_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required',
            'email' => 'required|string|max:255',
            'nip' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'id_kelas' => 'required'
        ]);
        $validate['sts'] = 1;
        $validate['created_at'] = now();
        $validate['updated_at'] = now();
        DB::table('master_siswa')->insert($validate);

        return redirect()->back()->with('success', 'Menambahkan Siswa Baru !!');
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
        $siswa = DB::table('master_siswa')->where('id_siswa', $id)->first();
        $kelas = DB::table('master_kelas')->get();
        return view('master_siswa.edit', compact('siswa', 'kelas'));
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
            'nama_siswa' => 'required|string|max:255',
            'alamat_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required',
            'email' => 'required|string|max:255',
            'nip' => 'required|string|max:255',
            'nik' => 'required|string|max:255',
            'id_kelas' => 'required'
        ]);
        $validate['sts'] = 1;
        $validate['created_at'] = now();
        $validate['updated_at'] = now();
        DB::table('master_siswa')->where('id_siswa', $id)->update($validate);
        return redirect()->route('master_siswa.index')->with('success', 'Mengubah Data Siswa !!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('master_siswa')->where('id_siswa', $id)->delete();

        return redirect()->back()->with('success', 'Menghapus Siswa !!');
    }
}
