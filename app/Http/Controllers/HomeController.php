<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Guru;
use App\Models\Soal;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Materi;
use App\Models\Jurusan;
use App\Models\MasterGuru;
use App\Models\MasterQuiz;
use App\Models\MasterSoal;
use App\Models\MasterKelas;
use App\Models\MasterSiswa;
use App\Models\MasterJadwal;
use App\Models\MasterMateri;
use Illuminate\Http\Request;
use App\Models\MasterJurusan;
use App\Models\MasterKategori;
use Illuminate\Support\Facades\Auth;
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
