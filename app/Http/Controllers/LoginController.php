<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use App\Models\MasterSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    //
    public function login()
    {
        if (Auth::check()) {
            return redirect('/');
        } else {
            return view('login');
        }
    }


    public function actionLogin(Request $request)
{
    // 1) Validasi input
    $credentials = $request->validate([
        'nip'      => ['required'],
        'password' => ['required'],
    ]);

    // 2) Attempt login
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate(); // penting untuk keamanan

        // 3) Cek first_login SETELAH berhasil login
        if ((int) Auth::user()->first_login === 0) {
            return redirect()->route('change.view');
        }

        // 4) Arahkan ke home kalau bukan login pertama
        return redirect()->intended('/home');
    }

    // Gagal login
    return back()
        ->withInput($request->only('nip'))
        ->withErrors(['nip' => 'NIP atau password salah.']);
}


    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
    public function actionLogout()
    {
        Auth::logout();
        Session::flush(); // Add session flush for complete logout
        return redirect('/');
    }

    public function changeView()
    {
        if (Auth::check()) {
            return view('change');
        } else {
            return view('login');
        }
    }
    public function changePassword(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal memperbarui password. Periksa kembali input Anda.');
        }

        // Jika validasi sukses
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->first_login = 1; // Set jadi 1 menandakan bukan login pertama
        $user->save();

        session()->flash('success', 'Password berhasil diperbarui.');

        // Redirect sesuai role
      return redirect('/home');
    }
}
