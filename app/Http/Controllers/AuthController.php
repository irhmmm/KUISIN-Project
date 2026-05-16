<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // =============================================
    // 1. UNIFIED LOGIN
    // =============================================
    
    public function showLogin()
    {
        if (session('teacher_id')) return redirect()->route('teacher.dashboard');
        if (session('admin_id')) return redirect()->route('admin.dashboard');
        
        return view('auth_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = DB::table('users')
                  ->where('email', $request->email)
                  ->first();

        if ($user) {
            // Cek hash password atau fallback ke plaintext untuk migrasi halus
            if (Hash::check($request->password, $user->password) || $request->password === $user->password) {
                
                // Jika masih plaintext (karena cocok persis), update ke versi hash agar aman
                if ($request->password === $user->password) {
                    DB::table('users')->where('id', $user->id)->update([
                        'password' => Hash::make($request->password)
                    ]);
                }

                if ($user->role === 'admin') {
                    session(['admin_id' => $user->id, 'admin_name' => $user->name, 'role' => 'admin']);
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'teacher') {
                    session(['teacher_id' => $user->id, 'teacher_name' => $user->name, 'role' => 'teacher']);
                    return redirect()->route('teacher.dashboard');
                }
            }
        }
        
        return back()->with('error', 'Email atau password salah!');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }

    // =============================================
    // 2. PROFILE EDIT
    // =============================================

    public function editProfile()
    {
        $userId = session('admin_id') ?? session('teacher_id');
        if (!$userId) return redirect()->route('login');

        $user = DB::table('users')->where('id', $userId)->first();
        return view('profile_edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $userId = session('admin_id') ?? session('teacher_id');
        if (!$userId) return redirect()->route('login');

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|min:5'
        ]);

        $data = ['name' => $request->name];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $userId)->update($data);

        // Update session name
        if (session('admin_id')) {
            session(['admin_name' => $request->name]);
        } else {
            session(['teacher_name' => $request->name]);
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // =============================================
    // 3. GOOGLE OAUTH LOGIN (Untuk Dosen)
    // =============================================

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $existingUser = DB::table('users')->where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // Update Google ID jika belum ada
                DB::table('users')->where('id', $existingUser->id)->update([
                    'google_id' => $googleUser->getId()
                ]);
                
                if ($existingUser->role === 'admin') {
                    session(['admin_id' => $existingUser->id, 'admin_name' => $existingUser->name, 'role' => 'admin']);
                    return redirect()->route('admin.dashboard');
                } else {
                    session(['teacher_id' => $existingUser->id, 'teacher_name' => $existingUser->name, 'role' => 'teacher']);
                    return redirect()->route('teacher.dashboard');
                }
            } else {
                // Buat akun dosen baru secara otomatis
                $newId = DB::table('users')->insertGetId([
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'password'  => 'LOGIN_VIA_GOOGLE',
                    'role'      => 'teacher',
                    'google_id' => $googleUser->getId(),
                ]);
                session(['teacher_id' => $newId, 'teacher_name' => $googleUser->getName(), 'role' => 'teacher']);
                return redirect()->route('teacher.dashboard');
            }

        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }
    }
}
