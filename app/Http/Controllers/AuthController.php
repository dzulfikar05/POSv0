<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');

            if (Auth::attempt($credentials)) {

                $levelId = Auth::user()->level_id;

                if ($levelId == 1) {
                    // admin
                    return response()->json([
                        'status' => true,
                        'message' => 'Login Berhasil',
                        'redirect' => url('/dashboard')
                    ]);
                } else {
                    // customer
                    return response()->json([
                        'status' => true,
                        'message' => 'Login Berhasil',
                        'redirect' => url('/')
                    ]);
                }
            }
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ]);
        }

        return redirect('login');
    }

    public function logout(Request $request)
    {
        $levelId = Auth::user()->level_id;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($levelId == 1) {
            // admin
            return redirect('login');
        } else {
            // customer
            return redirect('/');
        }
    }

    public function register()
    {
        return view('auth.register');
    }

    public function postRegister(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'username' => 'required|string|max:255|unique:m_user',
                    'password' => 'required|string|min:6',
                    'jk' => 'required',
                    'alamat' => 'required|string',
                    'wa' => 'required|integer',
                ]);

                $cusLevel = LevelModel::where('level_kode', 'CUS')->first();
                if (!$cusLevel) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Role CUS tidak ditemukan.',
                    ]);
                }

                $user = UserModel::create([
                    'level_id' => $cusLevel->level_id,
                    'username' => $request->username,
                    'nama' => $request->name,
                    'jk' => $request->jk,
                    'alamat' => $request->alamat,
                    'wa' => $request->wa,
                    'password' => $request->password,
                ]);

                if ($user) {
                    $credentials = $request->only('username', 'password');

                    if (Auth::attempt($credentials)) {
                        return response()->json([
                            'status' => true,
                            'message' => 'Registrasi Berhasil',
                            'redirect' => Auth::user()->level_id == 1 ? url('/dashboard') : url('/')
                        ]);
                    } else {
                        return response()->json([
                            'status' => true,
                            'message' => 'Registrasi Berhasil',
                            'redirect' => url('login'),
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Registrasi Gagal',
                    ]);
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                $errors = $e->validator->errors();

                if ($errors->has('username')) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Username sudah digunakan. Silakan pilih username lain.',
                        'msgField' => ['username' => $errors->get('username')]
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $errors
                ]);
            }
        }

        return redirect('register');
    }
}
