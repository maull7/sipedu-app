<?php

use App\Exports\RequestExport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KatergoriPenilaianController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotifController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\MasterGuruController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\MasterUserController;
use App\Http\Controllers\MasterJabatanController;
use App\Http\Controllers\MasterJurusanController;
use App\Http\Controllers\MasterKelasController;
use App\Http\Controllers\MasterPenggunaController;
use App\Http\Controllers\MasterSiswaController;
use App\Http\Controllers\MasterTahunAjaranController;
use App\Http\Controllers\PenilaianController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Route untuk login
Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/actionLogin', [LoginController::class, 'actionLogin'])->name('actionLogin');
//route untuk semua role
Route::group(['middleware' => 'auth'], function () {
    //user atau pengguna
    Route::resource('master_user', MasterUserController::class);
    //tahun ajaran
    Route::resource('master_tahun', MasterTahunAjaranController::class);
    Route::get('template-tahunajaran',[MasterTahunAjaranController::class,'exportTemplate'])->name('tahun.template');
    Route::post('import-tahun_ajaran',[MasterTahunAjaranController::class,'import'])->name('tahun.import');

    //jurusan
    Route::resource('master_jurusan', MasterJurusanController::class);
    Route::get('template-jurusan',[MasterJurusanController::class,'exportTemplate'])->name('jurusan.template');
    Route::post('import-jurusan',[MasterJurusanController::class,'import'])->name('jurusan.import');

    //kelas
    Route::resource('master_kelas', MasterKelasController::class);
    Route::get('template-kelas',[MasterKelasController::class,'exportTemplate'])->name('kelas.template');
    Route::post('import-kelas',[MasterKelasController::class,'import'])->name('kelas.import');

    //siswa
    Route::resource('master_siswa', MasterSiswaController::class);
    Route::get('/template-siswa',[MasterSiswaController::class,'exportTemplate'])->name('siswa.template');
    Route::post('/import-siswa',[MasterSiswaController::class,'import'])->name('siswa.import');

    //guru
    Route::resource('master_guru', MasterGuruController::class);
    Route::get('/template-guru',[MasterGuruController::class,'exportTemplate'])->name('guru.template');
    Route::post('/import-guru',[MasterGuruController::class,'import'])->name('guru.import');

    Route::resource('master_kategori', KatergoriPenilaianController::class);

    //mapel
    Route::resource('master_mapel',MapelController::class);
    Route::get('/template-mapel',[MapelController::class,'exportTemplate'])->name('mapel.template');
    Route::post('/import-mapel',[MapelController::class,'import'])->name('mapel.import');
    Route::resource('master_jabatan', MasterJabatanController::class);

    Route::resource('penilaian',PenilaianController::class);

    //laporan
    Route::get('laporan',[LaporanController::class,'index'])->name('laporan.index');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('/export-pdf',[LaporanController::class,'pdf'])->name('laporan.pdf');

    //pengguna
    Route::resource('master_pengguna', MasterPenggunaController::class);
    // Route::get('/', [HomeController::class, 'index']);
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/changePassword', [LoginController::class, 'changeView'])->name('change.view');
    Route::post('/change-password', [LoginController::class, 'changePassword'])->name('change.password');
    Route::get('/logout', [LoginController::class, 'actionLogout'])->name('actionLogout');
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/ajukan-pengajuan', [PengajuanController::class, 'create'])->name('pengajuan.create');

    //route ajuan
    Route::post('/tambah-pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::delete('/hapus-pengajuan/{id}', [PengajuanController::class, 'destroy'])->name('pengajuan.destroy');
    Route::get('/pengajuan/edit/{id}', [PengajuanController::class, 'edit'])->name('pengajuan.edit');
    Route::put('/update-pengajuan/{id}', [PengajuanController::class, 'update'])->name('pengajuan.update');
    Route::get('/verifikasi-pengajuan', [PengajuanController::class, 'verify'])->name('pengajuan.verify');

    //route acc reject
    Route::post('/approve/{id}', [PengajuanController::class, 'approve'])->name('approve');
    Route::post('/reject/{id}', [PengajuanController::class, 'reject'])->name('reject');
    Route::post('/approvals-reply/{id}', [PengajuanController::class, 'reply'])->name('approval.reply');
    Route::post('/comment-reply/{id}', [PengajuanController::class, 'replyComment'])->name('comment.reply');
    Route::get('/pengajuan-approve', [PengajuanController::class, 'success'])->name('success');
    Route::get('/pengajuan-reject', [PengajuanController::class, 'cancel'])->name('cancel');

    Route::get('/detail-pengajuan/{id}', [PengajuanController::class, 'detail'])->name('detail');

    //notif
    Route::get('/read', [NotifController::class, 'read'])->name('notif.read');
    


    Route::get('/laporan-approval-export', function () {
        $requests = DB::table('request')
            ->join('users', 'users.id', '=', 'request.user_id')
            ->select('request.*', 'users.name')
            ->when(request('start_date') && request('end_date'), function ($query) {
                $query->whereBetween('request.tanggal', [request('start_date'), request('end_date')]);
            })
            ->when(request('status'), function ($query) {
                $query->where('request.status', request('status'));
            })
            ->orderBy('request.tanggal', 'desc')
            ->get();

        return Excel::download(new RequestExport($requests), 'laporan_approval.xlsx');
    })->name('laporan_approval.export');
});
// //master untuk role admin mengandung master master
// Route::group(['middleware' => ['auth', 'admin.only']], function () {


    
// });
