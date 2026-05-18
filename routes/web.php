<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

// =============================================
// UNIFIED LOGIN & PROFILE
// =============================================
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('teacher.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Profile
Route::get('/profile',  [AuthController::class, 'editProfile'])->name('profile.edit');
Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');


// =============================================
// STUDENT ROUTES
// =============================================
Route::get('/student-login',  [StudentController::class, 'showLogin'])->name('student.login');
Route::post('/join-room',     [StudentController::class, 'joinRoom'])->name('student.join');
Route::get('/student-name',   [StudentController::class, 'showNameForm'])->name('student.name');
Route::post('/submit-name',   [StudentController::class, 'submitName'])->name('student.submitName');
Route::get('/quiz-room',      [StudentController::class, 'showQuiz'])->name('student.quiz');
Route::post('/submit-answer', [StudentController::class, 'submitAnswer'])->name('student.submitAnswer');
Route::get('/quiz-finished',  [StudentController::class, 'showFinished'])->name('student.finished');
Route::get('/student/check-room', [StudentController::class, 'checkRoomStatus'])->name('student.checkRoom');
Route::get('/student/check-lobby', [StudentController::class, 'checkLobbyStatus'])->name('student.checkLobby');
Route::post('/student/submit', [StudentController::class, 'submitQuiz'])->name('student.submit');
Route::post('/student/cheat', [StudentController::class, 'reportCheat'])->name('student.cheat');

// =============================================
// TEACHER ROUTES
// =============================================
// Redirect old teacher login
Route::get('/teacher-login', function() { return redirect()->route('login'); })->name('teacher.login');
Route::post('/teacher-login', function() { return redirect()->route('login'); })->name('teacher.login.submit');
Route::get('/teacher-logout', [AuthController::class, 'logout'])->name('teacher.logout');

Route::get('/teacher-register',  [TeacherController::class, 'showRegister'])->name('teacher.register');
Route::post('/teacher-register', [TeacherController::class, 'processRegister'])->name('teacher.register.submit');
Route::get('/teacher-forgot-password',  [TeacherController::class, 'showForgot'])->name('teacher.forgot');
Route::post('/teacher-forgot-password', [TeacherController::class, 'processForgot'])->name('teacher.forgot.submit');

// Dashboard & Control (Protected)
Route::middleware(['role:teacher'])->group(function () {
    Route::get('/teacher-dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    Route::post('/launch-quiz',      [TeacherController::class, 'launchQuiz'])->name('teacher.launch');
    Route::post('/start-exam',       [TeacherController::class, 'startExam'])->name('teacher.startExam');
    Route::post('/end-exam',         [TeacherController::class, 'endExam'])->name('teacher.endExam');
    Route::get('/live-results',      [TeacherController::class, 'showLiveResults'])->name('teacher.results');
    Route::get('/space-race',        [TeacherController::class, 'spaceRaceLive'])->name('teacher.spacerace');
    Route::get('/api/space-race',    [TeacherController::class, 'getSpaceRaceData'])->name('teacher.spacerace.data');
    Route::get('/export-results',    [TeacherController::class, 'exportResults'])->name('teacher.export');

    // Library & Questions
    Route::get('/teacher-library',         [TeacherController::class, 'library'])->name('teacher.library');
    Route::post('/teacher-library/quiz',   [TeacherController::class, 'storeQuiz'])->name('teacher.storeQuiz');
    Route::post('/teacher-library/store',  [TeacherController::class, 'storeQuestion'])->name('teacher.storeQuestion');
    Route::get('/teacher-library/edit/{id}',     [TeacherController::class, 'editQuestion'])->name('teacher.editQuestion');
    Route::post('/teacher-library/update/{id}',  [TeacherController::class, 'updateQuestion'])->name('teacher.updateQuestion');
    Route::delete('/teacher-library/delete/{id}',[TeacherController::class, 'deleteQuestion'])->name('teacher.deleteQuestion');
    Route::delete('/teacher-library/quiz/{id}',  [TeacherController::class, 'deleteQuiz'])->name('teacher.deleteQuiz');

    // Exit Ticket
    Route::post('/launch-exit-ticket', [TeacherController::class, 'launchExitTicket'])->name('teacher.launchExitTicket');
});


// =============================================
// ADMIN ROUTES
// =============================================
// Redirect old admin login
Route::get('/admin-login', function() { return redirect()->route('login'); })->name('admin.login');
Route::post('/admin-login', function() { return redirect()->route('login'); })->name('admin.login.submit');
Route::get('/admin-logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin-dashboard',   [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::delete('/admin-teacher/{id}', [AdminController::class, 'deleteTeacher'])->name('admin.deleteTeacher');

    // Manajemen Peserta (Mahasiswa) dipindah ke Admin
    Route::get('/admin/students', [AdminController::class, 'manageStudents'])->name('admin.students');
    Route::post('/admin/students/import', [AdminController::class, 'importStudents'])->name('admin.students.import');
    Route::post('/admin/students/{id}/reset', [AdminController::class, 'resetStudentPassword'])->name('admin.students.reset');
});

