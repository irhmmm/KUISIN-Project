<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StudentController extends Controller
{
    // --- 1. HALAMAN LOGIN MAHASISWA ---
    public function showLogin()
    {
        return view('student_login');
    }

    // --- 2. PROSES JOIN ROOM ---
    public function joinRoom(Request $request)
    {
        $request->validate(['room_name' => 'required']);

        $room = DB::table('rooms')
                  ->where('room_name', $request->room_name)
                  ->where('is_active', 1)
                  ->first();

        if ($room) {
            session([
                'room_id' => $room->id,
                'room_name' => $room->room_name,
                'room_teacher_id' => $room->teacher_id
            ]);
            return redirect()->route('student.name');
        } else {
            return back()->with('error', 'Room tidak ditemukan atau belum diaktifkan.');
        }
    }

    // --- 3. HALAMAN INPUT NAMA ---
    public function showNameForm()
    {
        if (!session('room_id')) return redirect()->route('student.login');
        return view('student_name');
    }

    // --- 4. PROSES SIMPAN NAMA & ASSIGN TEAM ---
    public function submitName(Request $request)
    {
        $request->validate(['student_name' => 'required']);

        $roomId = session('room_id');
        $room = DB::table('rooms')->where('id', $roomId)->first();

        $teamName = null;
        if ($room && $room->mode === 'space_race') {
            $numTeams = $room->num_teams ?? 2;
            $teams = ['Merah', 'Biru', 'Hijau', 'Kuning', 'Ungu', 'Oranye', 'Pink', 'Cokelat'];
            
            // Ambil sesi yang sudah ada di room ini
            $existingSessions = DB::table('student_sessions')->where('room_id', $roomId)->get();
            
            // Hitung anggota tiap tim
            $teamCounts = [];
            for ($i = 0; $i < $numTeams; $i++) {
                $teamCounts[$teams[$i]] = 0;
            }
            
            foreach ($existingSessions as $s) {
                if ($s->team_name && isset($teamCounts[$s->team_name])) {
                    $teamCounts[$s->team_name]++;
                }
            }
            
            // Cari tim dengan anggota paling sedikit
            asort($teamCounts);
            $teamName = array_key_first($teamCounts);
        }

        $sessionId = DB::table('student_sessions')->insertGetId([
            'room_id' => $roomId,
            'student_name' => $request->student_name,
            'team_name' => $teamName,
            'joined_at' => now()
        ]);

        session(['session_id' => $sessionId, 'student_name' => $request->student_name, 'team_name' => $teamName]);
        return redirect()->route('student.quiz');
    }

    // --- 5. HALAMAN PENGERJAAN KUIS / LOBBY ---
    public function showQuiz()
    {
        $sessionId = session('session_id');
        if (!$sessionId) return redirect()->route('student.login');

        $sessionData = DB::table('student_sessions')->where('id', $sessionId)->first();
        if (!$sessionData) return redirect()->route('student.login');

        $room = DB::table('rooms')->where('id', session('room_id'))->first();
        
        // Pengecekan Lobby
        if ($room->status === 'waiting') {
            return view('student_lobby', compact('room', 'sessionData'));
        }

        // Pengecekan Timer (berdasarkan started_at jika ada, atau joined_at)
        $duration = (int) Cache::get('room_duration_' . session('room_teacher_id'), 5);
        $startTime = $room->started_at ? Carbon::parse($room->started_at) : Carbon::parse($sessionData->joined_at);
        $endTime = $startTime->copy()->addMinutes($duration);

        $remainingSeconds = now()->diffInSeconds($endTime, false);

        if ($remainingSeconds <= 0) {
            return redirect()->route('student.finished');
        }

        $questions = DB::table('questions')
                        ->where('quiz_id', $room->quiz_id)
                        ->orderBy('id')
                        ->get();

        $totalQuestions = $questions->count();

        foreach ($questions as $q) {
            $q->options = DB::table('options')->where('question_id', $q->id)->get();
            // Cek apakah mahasiswa sudah pernah menjawab soal ini sebelumnya
            $q->answered_id = DB::table('student_answers')
                                ->where('session_id', $sessionId)
                                ->where('question_id', $q->id)
                                ->value('option_id');
        }

        return view('student_quiz', compact('questions', 'totalQuestions', 'remainingSeconds', 'sessionData', 'room'));
    }

    // --- 6. PROSES SIMPAN JAWABAN (DIPERBARUI DENGAN AUTO-SAVE) ---
    public function submitQuiz(Request $request)
    {
        $sessionId = session('session_id');

        if (!$sessionId) {
            if ($request->ajax()) return response()->json(['error' => 'Sesi habis'], 401);
            return redirect()->route('student.login');
        }

        // Hapus jawaban lama untuk mencegah duplikasi data
        DB::table('student_answers')->where('session_id', $sessionId)->delete();

        $answers = $request->answers ?? [];

        foreach ($answers as $questionId => $optionId) {
            $isCorrect = DB::table('options')->where('id', $optionId)->value('is_correct');

            DB::table('student_answers')->insert([
                'session_id' => $sessionId,
                'question_id' => $questionId,
                'option_id' => $optionId,
                'is_correct' => $isCorrect ? 1 : 0
            ]);
        }

        // Jika request ini adalah "Auto-Save" dari ketukan JavaScript di latar belakang
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        // Jika ini adalah request pengumpulan final ("Selesai & Kumpulkan")
        return redirect()->route('student.finished');
    }

    // --- 7. HALAMAN SELESAI ---
    public function showFinished()
    {
        return view('student_finished');
    }

    // --- 8. RADAR PENGECEKAN STATUS ROOM ---
    public function checkRoomStatus()
    {
        $roomId = session('room_id');
        if (!$roomId) return response()->json(['is_active' => 0]);

        $room = DB::table('rooms')->where('id', $roomId)->first();
        return response()->json(['is_active' => $room ? $room->is_active : 0]);
    }

    // --- 9. POLLING LOBBY STATUS ---
    public function checkLobbyStatus()
    {
        $roomId = session('room_id');
        if (!$roomId) return response()->json(['status' => 'waiting']);

        $room = DB::table('rooms')->where('id', $roomId)->first();
        return response()->json([
            'status' => $room ? $room->status : 'waiting',
            'is_active' => $room ? $room->is_active : 0
        ]);
    }

    // --- 10. REPORT CHEATING ---
    public function reportCheat()
    {
        $sessionId = session('session_id');
        if ($sessionId) {
            DB::table('student_sessions')
                ->where('id', $sessionId)
                ->increment('cheat_attempts');
        }
        return response()->json(['success' => true]);
    }
}
