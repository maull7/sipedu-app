<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class MasterGuruController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gurus = DB::table('master_guru')->get();
        return view('master_guru.index', compact('gurus'));
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
            'nama_guru' => 'required|string|max:255',
            'alamat_guru' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'nip' => 'required|string|max:255',

            'email' => 'required|string|email|max:255|unique:users,email', // tambah validasi unik email
        ]);

        DB::beginTransaction();

        try {
            $validate['created_at'] = now();
            $validate['updated_at'] = now();

            // Insert ke master_guru
            DB::table('master_guru')->insert($validate);

            // Insert ke users
            DB::table('users')->insert([
                'name' => $validate['nama_guru'],
                'email' => $validate['email'],
                'nip' => $validate['nip'],
                'password' => Hash::make('12345678'), // default password, bisa diubah
                'role' => 1, // sesuaikan dengan sistem kamu
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Menambahkan Guru dan User berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
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
        $guru = DB::table('master_guru')->where('id_guru', $id)->first();
        return view('master_guru.edit', compact('guru'));
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
        $data = DB::table('master_guru')->where('id_guru', $id)->first();
        $validate = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'alamat_guru' => 'required|string|max:255',
            'jenis_kelamin' => 'required|string',
            'nip' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        DB::beginTransaction();

        try {
            $validate['updated_at'] = now();

            // Update master_guru
            DB::table('master_guru')->where('id_guru', $id)->update($validate);

            // Update users berdasarkan nip yang sama
            DB::table('users')->where('nip', $data->nip)->update([
                'name' => $validate['nama_guru'],
                'email' => $validate['email'],
                'nip' => $validate['nip'],
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('master_guru.index')->with('success', 'Berhasil mengubah data guru dan user!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('master_guru')->where('id_guru', $id)->delete();

        return redirect()->back()->with('success', 'Menghapus Guru !!');
    }
}
