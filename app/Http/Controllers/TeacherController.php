<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    // =============================================
    // 1. AUTH: LOGIN, LOGOUT, REGISTER, FORGOT
    // =============================================

    public function showLogin()
    {
        return redirect()->route('login');
    }

    public function login(Request $request)
    {
        return redirect()->route('login');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }

    public function showRegister()
    {
        if (session('teacher_id')) return redirect()->route('teacher.dashboard');
        return view('teacher_register');
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:5'
        ], [
            'email.unique'  => 'Email ini sudah terdaftar!',
            'password.min'  => 'Password minimal 5 karakter!'
        ]);

        DB::table('users')->insert([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'teacher'
        ]);

        return redirect()->route('teacher.login')->with('success', 'Akun berhasil dibuat! Silakan Sign In.');
    }

    public function showForgot()
    {
        return view('teacher_forgot');
    }

    public function processForgot(Request $request)
    {
        $user = DB::table('users')
                  ->where('email', $request->email)
                  ->where('role', 'teacher')
                  ->first();

        if ($user) {
            DB::table('users')->where('id', $user->id)->update(['password' => '12345']);
            return redirect()->route('teacher.login')->with('success', 'Password direset menjadi: 12345');
        }
        return back()->with('error', 'Email tidak ditemukan.');
    }

    // =============================================
    // 2. DASHBOARD
    // =============================================

    public function dashboard()
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        $teacherId = session('teacher_id');
        $room      = DB::table('rooms')->where('teacher_id', $teacherId)->first();
        $roomId    = $room ? $room->id : 0;

        $recentStudents = DB::table('student_sessions')
                            ->where('room_id', $roomId)
                            ->orderBy('joined_at', 'desc')
                            ->limit(10)
                            ->get();

        $totalQuestions = DB::table('questions')->where('teacher_id', $teacherId)->count();
        $totalStudents  = DB::table('student_sessions')->where('room_id', $roomId)->count();
        $quizzes = DB::table('quizzes')->where('teacher_id', $teacherId)->get();

        return view('teacher_dashboard', compact('room', 'recentStudents', 'totalQuestions', 'totalStudents', 'quizzes'));
    }

    // =============================================
    // 3. LIVE RESULTS
    // =============================================

    public function showLiveResults()
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        $teacherId = session('teacher_id');
        $room      = DB::table('rooms')->where('teacher_id', $teacherId)->first();
        $roomId    = $room ? $room->id : 0;
        $quizId    = $room ? $room->quiz_id : 0;
        $questions = DB::table('questions')->where('quiz_id', $quizId)->get();
        $sessions  = DB::table('student_sessions')->where('room_id', $roomId)->get();

        $results = [];
        foreach ($sessions as $session) {
            $answers        = DB::table('student_answers')->where('session_id', $session->id)->get();
            $correctCount   = $answers->where('is_correct', 1)->count();
            $totalQ         = $questions->count();
            $score          = $totalQ > 0 ? round(($correctCount / $totalQ) * 100) : 0;

            $studentData = [
                'name'           => $session->student_name,
                'score'          => $score,
                'cheat_attempts' => $session->cheat_attempts ?? 0,
                'answers_status' => [],
                'answered'       => $answers->count(),
                'total'          => $totalQ,
            ];

            foreach ($questions as $q) {
                $ans = $answers->where('question_id', $q->id)->first();
                $studentData['answers_status'][$q->id] = $ans
                    ? ($ans->is_correct ? 'correct' : 'wrong')
                    : 'empty';
            }
            $results[] = $studentData;
        }

        // Urutkan berdasarkan skor tertinggi
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return view('live_results', compact('room', 'questions', 'results'));
    }

    // =============================================
    // 4. BANK SOAL (LIBRARY): TAMPIL, SIMPAN, EDIT, HAPUS
    // =============================================

    public function library(Request $request)
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        $teacherId = session('teacher_id');
        
        // Get all quizzes for this teacher
        $quizzes = DB::table('quizzes')->where('teacher_id', $teacherId)->orderBy('id', 'desc')->get();
        
        $selectedQuizId = $request->query('quiz_id');
        if (!$selectedQuizId && $quizzes->count() > 0) {
            $selectedQuizId = $quizzes->first()->id;
        }

        $questions = collect();
        if ($selectedQuizId) {
            $questions = DB::table('questions')
                        ->where('quiz_id', $selectedQuizId)
                        ->orderBy('id', 'desc')
                        ->get();

            foreach ($questions as $q) {
                $q->options = DB::table('options')->where('question_id', $q->id)->get();
            }
        }

        return view('teacher_library', compact('quizzes', 'questions', 'selectedQuizId'));
    }

    public function storeQuiz(Request $request)
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        DB::table('quizzes')->insert([
            'teacher_id' => session('teacher_id'),
            'title'      => $request->title,
        ]);

        return redirect()->route('teacher.library')->with('success', 'Kuis berhasil ditambahkan!');
    }

    public function storeQuestion(Request $request)
    {
        if (!session('teacher_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $teacherId = session('teacher_id');

        // --- A. LOGIKA BULK IMPORT (PDF/JSON/CSV) ---
        if ($request->boolean('bulk_import') || $request->input('bulk_import') === true) {
            $bulkData = $request->input('bulk_data');

            if (is_string($bulkData)) {
                $bulkData = json_decode($bulkData, true);
            }

            if (!$bulkData || !is_array($bulkData)) {
                return response()->json(['success' => false, 'message' => 'Data tidak valid.']);
            }

            $imported = 0;
            foreach ($bulkData as $item) {
                if (empty($item['question']) || empty($item['A']) || empty($item['B']) ||
                    empty($item['C']) || empty($item['D']) || empty($item['answer'])) {
                    continue;
                }

                $answer = strtoupper(trim($item['answer']));
                if (!in_array($answer, ['A', 'B', 'C', 'D'])) continue;

                $questionId = DB::table('questions')->insertGetId([
                    'teacher_id'    => $teacherId,
                    'quiz_id'       => $request->input('quiz_id'),
                    'question_text' => trim($item['question']),
                    'question_type' => 'multiple_choice',
                ]);

                $opts = [
                    'A' => trim($item['A']),
                    'B' => trim($item['B']),
                    'C' => trim($item['C']),
                    'D' => trim($item['D']),
                ];

                foreach ($opts as $letter => $text) {
                    DB::table('options')->insert([
                        'question_id' => $questionId,
                        'option_text' => $text,
                        'is_correct'  => ($letter === $answer) ? 1 : 0,
                    ]);
                }

                $imported++;
            }

            return response()->json([
                'success' => true,
                'message' => $imported . ' soal berhasil diimport!'
            ]);
        }

        // --- B. LOGIKA SINGLE QUESTION (Form Manual) ---
        $request->validate([
            'quiz_id'        => 'required|exists:quizzes,id',
            'question_text'  => 'required',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        $questionId = DB::table('questions')->insertGetId([
            'teacher_id'    => $teacherId,
            'quiz_id'       => $request->quiz_id,
            'question_text' => $request->question_text,
            'question_type' => 'multiple_choice',
        ]);

        $options = [
            ['text' => $request->option_a, 'letter' => 'A'],
            ['text' => $request->option_b, 'letter' => 'B'],
            ['text' => $request->option_c, 'letter' => 'C'],
            ['text' => $request->option_d, 'letter' => 'D'],
        ];

        foreach ($options as $opt) {
            DB::table('options')->insert([
                'question_id' => $questionId,
                'option_text' => $opt['text'],
                'is_correct'  => ($request->correct_answer === $opt['letter']) ? 1 : 0,
            ]);
        }

        return redirect()->route('teacher.library')->with('success', 'Soal berhasil ditambahkan!');
    }

    public function editQuestion($id)
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        $question = DB::table('questions')->where('id', $id)->first();
        if (!$question) return redirect()->route('teacher.library');

        $options = DB::table('options')->where('question_id', $id)->orderBy('id')->get();

        return view('teacher_edit_question', compact('question', 'options'));
    }

    public function updateQuestion(Request $request, $id)
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        $request->validate([
            'question_text'  => 'required',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        DB::table('questions')->where('id', $id)->update([
            'question_text' => $request->question_text,
        ]);

        $optionTexts = [
            'A' => $request->option_a,
            'B' => $request->option_b,
            'C' => $request->option_c,
            'D' => $request->option_d,
        ];

        $existingOptions = DB::table('options')->where('question_id', $id)->orderBy('id')->get();
        $letters = ['A', 'B', 'C', 'D'];

        foreach ($existingOptions as $index => $opt) {
            $letter = $letters[$index];
            DB::table('options')->where('id', $opt->id)->update([
                'option_text' => $optionTexts[$letter],
                'is_correct'  => ($request->correct_answer === $letter) ? 1 : 0,
            ]);
        }

        return redirect()->route('teacher.library')->with('success', 'Soal berhasil diperbarui!');
    }

    public function deleteQuestion($id)
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        DB::table('options')->where('question_id', $id)->delete();
        DB::table('questions')->where('id', $id)->delete();

        return redirect()->route('teacher.library')->with('success', 'Soal berhasil dihapus!');
    }

    // =============================================
    // 5. QUIZ CONTROL (LUNCURKAN & AKHIRI)
    // =============================================

    public function launchQuiz(Request $request)
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'mode' => 'nullable|string|in:standard,space_race',
            'num_teams' => 'nullable|integer|min:2|max:8'
        ]);

        $teacherId = session('teacher_id');
        $timeLimit = max(1, (int) $request->time_limit);
        $quizId = $request->quiz_id;

        // Simpan batas waktu untuk room milik dosen ini
        Cache::put('room_duration_' . $teacherId, $timeLimit, now()->addHours(24));

        $room = DB::table('rooms')->where('teacher_id', $teacherId)->first();

        if (!$room) {
            $roomId = DB::table('rooms')->insertGetId([
                'teacher_id' => $teacherId,
                'quiz_id'    => $quizId,
                'is_active'  => 1,
                'room_name'  => 'TEMP',
                'mode'       => $request->mode ?? 'standard',
                'num_teams'  => $request->mode == 'space_race' ? ($request->num_teams ?? 2) : null,
                'status'     => 'waiting',
                'started_at' => null
            ]);
        } else {
            $roomId = $room->id;
        }

        // Hapus data peserta dari sesi ujian yang lama
        $sessionIds = DB::table('student_sessions')->where('room_id', $roomId)->pluck('id');
        if ($sessionIds->isNotEmpty()) {
            DB::table('student_answers')->whereIn('session_id', $sessionIds)->delete();
            DB::table('student_sessions')->where('room_id', $roomId)->delete();
        }

        // Buat kode Room baru dan aktifkan
        $newRoomCode = strtoupper(Str::random(6));
        DB::table('rooms')->where('id', $roomId)->update([
            'is_active'  => 1,
            'quiz_id'    => $quizId,
            'room_name'  => $newRoomCode,
            'mode'       => $request->mode ?? 'standard',
            'num_teams'  => $request->mode == 'space_race' ? ($request->num_teams ?? 2) : null,
            'status'     => 'waiting',
            'started_at' => null
        ]);

        if ($request->mode == 'space_race') {
            return redirect()->route('teacher.spacerace');
        }

        return redirect()->route('teacher.results');
    }

    public function startExam()
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');
        
        $teacherId = session('teacher_id');
        DB::table('rooms')->where('teacher_id', $teacherId)->update([
            'status' => 'started',
            'started_at' => now()
        ]);
        
        return back()->with('success', 'Ujian telah dimulai! Waktu mulai berjalan.');
    }

    public function endExam()
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        // Kunci Room Secara Paksa (menghentikan ujian mahasiswa)
        DB::table('rooms')->where('teacher_id', session('teacher_id'))->update(['is_active' => 0]);

        return redirect()->route('teacher.results')->with('success', 'Ujian telah diakhiri! Jawaban semua mahasiswa berhasil dikumpulkan otomatis.');
    }

    public function launchExitTicket(Request $request)
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        $teacherId = session('teacher_id');

        // Cek apakah kuis Exit Ticket sudah ada, jika belum buat baru
        $exitQuiz = DB::table('quizzes')->where('teacher_id', $teacherId)->where('title', 'Exit Ticket Cepat')->first();
        
        if (!$exitQuiz) {
            $quizId = DB::table('quizzes')->insertGetId([
                'teacher_id' => $teacherId,
                'title' => 'Exit Ticket Cepat',
            ]);

            // Soal 1
            $q1 = DB::table('questions')->insertGetId([
                'teacher_id' => $teacherId,
                'quiz_id' => $quizId,
                'question_text' => 'Bagaimana tingkat pemahaman Anda terhadap materi hari ini?',
                'question_type' => 'multiple_choice'
            ]);
            DB::table('options')->insert([
                ['question_id' => $q1, 'option_text' => 'Sangat Paham', 'is_correct' => 1],
                ['question_id' => $q1, 'option_text' => 'Cukup Paham', 'is_correct' => 1],
                ['question_id' => $q1, 'option_text' => 'Kurang Paham', 'is_correct' => 0],
                ['question_id' => $q1, 'option_text' => 'Sama Sekali Tidak Paham', 'is_correct' => 0],
            ]);

            // Soal 2
            $q2 = DB::table('questions')->insertGetId([
                'teacher_id' => $teacherId,
                'quiz_id' => $quizId,
                'question_text' => 'Bagian mana dari pelajaran hari ini yang menurut Anda paling menantang?',
                'question_type' => 'multiple_choice'
            ]);
            DB::table('options')->insert([
                ['question_id' => $q2, 'option_text' => 'Semuanya mudah dimengerti', 'is_correct' => 1],
                ['question_id' => $q2, 'option_text' => 'Bagian teori/konsep', 'is_correct' => 0],
                ['question_id' => $q2, 'option_text' => 'Bagian praktik/latihan soal', 'is_correct' => 0],
                ['question_id' => $q2, 'option_text' => 'Kecepatan penyampaian materi terlalu cepat', 'is_correct' => 0],
            ]);
        } else {
            $quizId = $exitQuiz->id;
        }

        // Launch room with this quiz
        $timeLimit = 5; // Exit ticket usually very short
        Cache::put('room_duration_' . $teacherId, $timeLimit, now()->addHours(24));

        $room = DB::table('rooms')->where('teacher_id', $teacherId)->first();

        if (!$room) {
            $roomId = DB::table('rooms')->insertGetId([
                'teacher_id' => $teacherId,
                'quiz_id'    => $quizId,
                'is_active'  => 1,
                'room_name'  => 'TEMP',
                'mode'       => 'exit_ticket',
                'status'     => 'started', // Exit ticket langsung mulai saja, tidak perlu lobby agar cepat
                'started_at' => now()
            ]);
        } else {
            $roomId = $room->id;
        }

        $sessionIds = DB::table('student_sessions')->where('room_id', $roomId)->pluck('id');
        if ($sessionIds->isNotEmpty()) {
            DB::table('student_answers')->whereIn('session_id', $sessionIds)->delete();
            DB::table('student_sessions')->where('room_id', $roomId)->delete();
        }

        $newRoomCode = strtoupper(Str::random(6));
        DB::table('rooms')->where('id', $roomId)->update([
            'is_active'  => 1,
            'quiz_id'    => $quizId,
            'room_name'  => $newRoomCode,
            'mode'       => 'exit_ticket',
            'status'     => 'started',
            'started_at' => now()
        ]);

        return redirect()->route('teacher.results')->with('success', 'Exit Ticket berhasil diluncurkan! Mahasiswa kini bisa bergabung.');
    }

    // =============================================
    // 5.5 SPACE RACE LIVE
    // =============================================

    public function spaceRaceLive()
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        $teacherId = session('teacher_id');
        $room = DB::table('rooms')->where('teacher_id', $teacherId)->first();

        if (!$room || $room->mode !== 'space_race') {
            return redirect()->route('teacher.dashboard')->with('error', 'Tidak ada Space Race yang aktif.');
        }

        $questionsCount = DB::table('questions')->where('quiz_id', $room->quiz_id)->count();

        return view('space_race', compact('room', 'questionsCount'));
    }

    public function getSpaceRaceData()
    {
        if (!session('teacher_id')) return response()->json(['error' => 'Unauthorized'], 401);

        $teacherId = session('teacher_id');
        $room = DB::table('rooms')->where('teacher_id', $teacherId)->first();

        if (!$room) return response()->json(['error' => 'No room'], 404);

        $sessions = DB::table('student_sessions')->where('room_id', $room->id)->get();
        $sessionIds = $sessions->pluck('id');
        
        $questionsCount = DB::table('questions')->where('quiz_id', $room->quiz_id)->count();
        $totalQuestions = $questionsCount > 0 ? $questionsCount : 1;
        
        $answers = DB::table('student_answers')
            ->whereIn('session_id', $sessionIds)
            ->where('is_correct', 1)
            ->get();

        $teamScores = [];
        $teamMembersCount = [];

        foreach ($sessions as $session) {
            $team = $session->team_name;
            if (!$team) continue;
            
            if (!isset($teamScores[$team])) {
                $teamScores[$team] = 0;
                $teamMembersCount[$team] = 0;
            }
            $teamMembersCount[$team]++;
            
            $correctAnswers = $answers->where('session_id', $session->id)->count();
            $teamScores[$team] += $correctAnswers;
        }

        $teams = [];
        foreach ($teamScores as $team => $score) {
            // Persentase kemajuan tim = (Total benar tim) / (Total Soal * Jumlah Anggota Tim) * 100
            $maxPossibleScore = $totalQuestions * $teamMembersCount[$team];
            $progress = $maxPossibleScore > 0 ? ($score / $maxPossibleScore) * 100 : 0;
            
            $teams[] = [
                'name' => $team,
                'score' => $score,
                'progress' => min(100, $progress),
                'members' => $teamMembersCount[$team]
            ];
        }

        // Urutkan berdasarkan progress
        usort($teams, fn($a, $b) => $b['progress'] <=> $a['progress']);

        return response()->json([
            'status' => $room->status,
            'teams' => $teams,
            'joined_count' => $sessions->count()
        ]);
    }

    // =============================================
    // 6. EXPORT CSV
    // =============================================

    public function exportResults()
    {
        if (!session('teacher_id')) return redirect()->route('teacher.login');

        $teacherId = session('teacher_id');
        $room      = DB::table('rooms')->where('teacher_id', $teacherId)->first();
        $roomId    = $room ? $room->id : 0;
        $quizId    = $room ? $room->quiz_id : 0;
        $roomCode  = $room ? $room->room_name : 'KUIS';
        $questions = DB::table('questions')->where('quiz_id', $quizId)->get();
        $sessions  = DB::table('student_sessions')->where('room_id', $roomId)->get();

        $csvData   = [];
        $header    = ['Nama Mahasiswa', 'Skor (%)'];
        foreach ($questions as $i => $q) {
            $header[] = 'Soal ' . ($i + 1);
        }
        $csvData[] = $header;

        foreach ($sessions as $session) {
            $answers       = DB::table('student_answers')->where('session_id', $session->id)->get();
            $correctCount  = $answers->where('is_correct', 1)->count();
            $totalQ        = $questions->count();
            $score         = $totalQ > 0 ? round(($correctCount / $totalQ) * 100) : 0;

            $row = [$session->student_name, $score];
            foreach ($questions as $q) {
                $ans   = $answers->where('question_id', $q->id)->first();
                $row[] = $ans ? ($ans->is_correct ? 'Benar' : 'Salah') : 'Kosong';
            }
            $csvData[] = $row;
        }

        $filename = 'Hasil_Kuis_' . $roomCode . '_' . date('d-m-Y_H-i') . '.csv';
        $headers  = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =============================================
    // 7. GOOGLE OAUTH LOGIN
    // =============================================

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Bypass verifikasi SSL khusus untuk Localhost menggunakan Guzzle
            $googleUser = Socialite::driver('google')
                            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                            ->user();

            $existingUser = DB::table('users')->where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // Update Google ID jika belum ada
                DB::table('users')->where('id', $existingUser->id)->update([
                    'google_id' => $googleUser->getId()
                ]);
                session(['teacher_id' => $existingUser->id, 'teacher_name' => $existingUser->name]);
            } else {
                // Buat akun dosen baru secara otomatis
                $newId = DB::table('users')->insertGetId([
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'password'  => 'LOGIN_VIA_GOOGLE',
                    'role'      => 'teacher',
                    'google_id' => $googleUser->getId(),
                ]);
                session(['teacher_id' => $newId, 'teacher_name' => $googleUser->getName()]);
            }

            return redirect()->route('teacher.dashboard');

        } catch (\Throwable $e) {
            // Jika gagal, hentikan program dan tampilkan error ke layar
            dd('ERROR GOOGLE LOGIN: ' . $e->getMessage());
        }
    }
}
