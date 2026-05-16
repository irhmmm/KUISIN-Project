<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (session('admin_id')) return redirect()->route('admin.dashboard');
        return view('admin_login');
    }

    public function login(Request $request)
    {
        $user = DB::table('users')
                  ->where('email', $request->email)
                  ->where('password', $request->password)
                  ->where('role', 'admin')
                  ->first();

        if ($user) {
            session(['admin_id' => $user->id, 'admin_name' => $user->name]);
            return redirect()->route('admin.dashboard');
        }
        return back()->with('error', 'Email atau password salah, atau Anda bukan Admin!');
    }

    public function logout()
    {
        session()->forget(['admin_id', 'admin_name']);
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        if (!session('admin_id')) return redirect()->route('admin.login');

        $totalTeachers = DB::table('users')->where('role', 'teacher')->count();
        $totalRooms    = DB::table('rooms')->count();
        $activeRooms   = DB::table('rooms')->where('is_active', 1)->count();
        $totalStudents = DB::table('students')->count();

        $teachers = DB::table('users')->where('role', 'teacher')->orderBy('id', 'desc')->get();

        return view('admin_dashboard', compact('totalTeachers', 'totalRooms', 'activeRooms', 'totalStudents', 'teachers'));
    }

    public function deleteTeacher($id)
    {
        if (!session('admin_id')) return redirect()->route('admin.login');
        
        DB::table('users')->where('id', $id)->where('role', 'teacher')->delete();
        return back()->with('success', 'Dosen berhasil dihapus.');
    }

    // =============================================
    // MANAJEMEN MAHASISWA
    // =============================================

    public function manageStudents()
    {
        if (!session('admin_id')) return redirect()->route('login');

        $students = DB::table('students')->orderBy('id', 'desc')->get();
        return view('admin_students', compact('students'));
    }

    public function importStudents(Request $request)
    {
        if (!session('admin_id')) return redirect()->route('login');

        $request->validate([
            'file_csv' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getPathname(), "r");
        fgetcsv($handle);

        $imported = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) >= 3) {
                // Assuming students table structure requires nim, name, password
                DB::table('students')->updateOrInsert(
                    ['nim' => trim($data[0])],
                    ['name' => trim($data[1]), 'password' => trim($data[2]), 'teacher_id' => 0] // 0 means default or global
                );
                $imported++;
            }
        }
        fclose($handle);

        return redirect()->back()->with('success', "$imported data peserta berhasil diimpor ke sistem!");
    }

    public function resetStudentPassword($id)
    {
        if (!session('admin_id')) return redirect()->route('login');

        DB::table('students')->where('id', $id)->update(['password' => '12345']);
        return redirect()->back()->with('success', 'Berhasil! Password peserta telah direset menjadi: 12345');
    }
}
