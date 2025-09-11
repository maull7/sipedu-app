<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $guruCount = DB::table('master_guru')->count();
        $siswaCount = DB::table('master_siswa')->count();
        $adminCount = DB::table('users')->where('role',0)->count();
        $kelasCount = DB::table('master_kelas')->count();
        $data = [
            'total_guru' => $guruCount,
            'total_siswa' => $siswaCount,
            'total_admin' => $adminCount,
            'total_kelas' => $kelasCount,
        ];

        return view('home',compact('data'));
    }
}
