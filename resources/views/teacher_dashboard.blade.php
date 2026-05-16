<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>
<body class="bg-gradient-to-br from-[#f8fafc] to-[#e2e8f0] min-h-screen flex overflow-hidden">

    <!-- Sidebar Kiri -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between hidden md:flex">
        <div>
            <!-- Logo -->
            <div class="p-6 flex items-center space-x-3">
                <div class="bg-[#4a728a] text-white p-1.5 rounded-md shadow-sm">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3z"/></svg>
                </div>
                <span class="font-bold text-xl tracking-wide text-gray-800">KUISIN</span>
            </div>

            <nav class="px-4 space-y-2 mt-6">
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center px-4 py-3 bg-gradient-to-r from-blue-50 to-blue-100/50 text-blue-700 rounded-xl font-semibold transition shadow-sm border border-blue-200/50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                <a href="{{ route('teacher.library') }}" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-gray-900 rounded-xl font-medium transition hover:shadow-sm">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Bank Soal
                </a>
                <a href="{{ route('teacher.results') }}" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-gray-900 rounded-xl font-medium transition hover:shadow-sm">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Live results
                </a>
                <div class="pt-6">
                    <a href="{{ route('teacher.logout') }}" onclick="return confirm('Yakin ingin keluar?')" class="flex items-center px-4 py-3 text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl font-bold transition border border-transparent hover:border-red-100 group">
                        <svg class="w-5 h-5 mr-3 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout Akun
                    </a>
                </div>
            </nav>
        </div>
        <!-- Profil Dosen Bawah (Dinamis) -->
        <a href="{{ route('profile.edit') }}" class="p-4 border-t border-gray-100 flex items-center bg-gray-50/50 hover:bg-gray-100 transition cursor-pointer group">
            <div class="w-10 h-10 bg-blue-100 text-blue-700 font-bold text-lg rounded-full mr-3 flex items-center justify-center uppercase shadow-inner group-hover:bg-blue-200 transition">
                {{ substr(session('teacher_name'), 0, 1) }}
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-gray-800 truncate w-32">{{ session('teacher_name') }}</p>
                <p class="text-[10px] text-gray-500 font-medium">Edit Profil</p>
            </div>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </a>
    </aside>

    <!-- Area Konten Utama -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">

        <!-- Header Atas -->
        <header class="bg-white/80 backdrop-blur-md border-b border-gray-200 h-20 flex items-center justify-between px-4 md:px-10 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] z-10 relative">
            <div>
                <h2 class="text-lg md:text-2xl font-bold text-gray-800 tracking-tight">Kuis Center</h2>
                <p class="text-xs md:text-sm text-gray-500 hidden md:block">Pilih mode kuis dan mulai sesi interaktif</p>
            </div>

            <div class="flex items-center gap-4">
                @if($room && $room->is_active)
                    <a href="{{ (isset($room->mode) && $room->mode === 'space_race') ? route('teacher.spacerace') : route('teacher.results') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl flex items-center text-sm font-semibold shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5">
                        <span class="mr-3 text-blue-100 text-xs tracking-widest uppercase">{{ (isset($room->status) && $room->status == 'waiting') ? 'LOBBY' : 'LIVE' }} ROOM</span>
                        <span class="text-lg tracking-widest">{{ $room->room_name }}</span>
                        <div class="w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse ml-3 shadow-[0_0_8px_rgba(74,222,128,0.8)]"></div>
                    </a>
                @else
                    <div class="bg-gray-100 text-gray-500 px-5 py-2.5 rounded-xl flex items-center text-sm font-semibold border border-gray-200">
                        <span class="mr-3 text-xs tracking-widest uppercase">STATUS</span>
                        <span class="text-base tracking-widest">OFFLINE</span>
                        <div class="w-2.5 h-2.5 rounded-full bg-red-400 ml-3"></div>
                    </div>
                @endif
            </div>
        </header>

        <!-- Konten Scrollable -->
        <div class="flex-1 overflow-y-auto p-4 md:p-10 pb-24 md:pb-10 flex flex-col gap-8">

            <!-- Baris Atas: Promo & Live Activity -->
            <div class="flex flex-col xl:flex-row gap-8">

                <!-- Banner Promo Mode -->
                <div class="flex-1 bg-gradient-to-r from-indigo-900 via-purple-900 to-indigo-900 rounded-3xl p-8 text-white shadow-xl shadow-purple-900/20 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-30"></div>
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-purple-500 rounded-full blur-[100px] opacity-50"></div>
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6 h-full">
                        <div class="flex-1">
                            <span class="bg-purple-500/30 text-purple-200 text-xs font-bold px-3 py-1 rounded-full border border-purple-400/30 mb-3 inline-block">MODE BARU</span>
                            <h3 class="text-3xl font-bold mb-2">Space Race! 🚀</h3>
                            <p class="text-indigo-100 text-sm leading-relaxed max-w-lg">Tingkatkan semangat kompetisi di kelas. Bagi mahasiswa ke dalam tim dan balapan menuju garis finish dengan menjawab pertanyaan dengan benar.</p>
                        </div>
                        <div class="flex-shrink-0 self-center md:self-end md:pb-2">
                            <button onclick="openLaunchModal('space_race')" class="bg-white text-indigo-900 hover:bg-indigo-50 px-6 py-3 rounded-xl font-bold shadow-[0_0_20px_rgba(255,255,255,0.3)] transition-all hover:scale-105 whitespace-nowrap">
                                Luncurkan Space Race
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Live Activity Feed -->
                <div class="w-full xl:w-80 bg-white border border-gray-200 rounded-3xl p-6 shadow-sm flex flex-col h-full max-h-[280px]">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-gray-800">Live Activity Feed</h3>
                        <div class="bg-green-100 text-green-700 px-3 py-1 rounded-full flex items-center text-xs font-bold shadow-sm transition-all hover:bg-green-200 cursor-default" title="{{ $totalStudents }} Mahasiswa Aktif">
                            <span class="relative flex h-2.5 w-2.5 mr-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                            </span>
                            {{ $totalStudents }} Online
                        </div>
                    </div>

                    <div class="space-y-4 overflow-y-auto pr-2">
                        @forelse($recentStudents as $student)
                            <div class="flex items-center justify-between border-b border-gray-50 pb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center text-xs uppercase">
                                        {{ substr($student->student_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $student->student_name }}</p>
                                        <p class="text-[10px] text-gray-400">Bergabung ke Room</p>
                                    </div>
                                </div>
                                <span class="text-[10px] text-gray-400">Baru saja</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-4">Belum ada mahasiswa yang masuk.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Grid Menu Mode Lainnya -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Kuis Standard -->
                <div onclick="openLaunchModal('standard')" class="group cursor-pointer bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300 relative overflow-hidden">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:-translate-y-2 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <h3 class="font-bold text-gray-800 text-xl mb-2">Kuis Standard</h3>
                    <p class="text-sm text-gray-500">Evaluasi individual dengan tampilan timer dan leaderboard.</p>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>

                <!-- Exit Ticket -->
                <div onclick="document.getElementById('form-exit-ticket').submit();" class="group cursor-pointer bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-green-200 transition-all duration-300 relative overflow-hidden">
                    <div class="w-16 h-16 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center mb-6 group-hover:-translate-y-2 group-hover:bg-green-500 group-hover:text-white transition-all duration-300 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </div>
                    <h3 class="font-bold text-gray-800 text-xl mb-2">Exit Ticket</h3>
                    <p class="text-sm text-gray-500">Cek pemahaman kelas secara cepat dengan 2 pertanyaan singkat di akhir jam.</p>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-green-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                </div>
                <form id="form-exit-ticket" action="{{ route('teacher.launchExitTicket') }}" method="POST" class="hidden">@csrf</form>
            </div>

        </div>
    </main>

    <!-- Modal Form Kuis -->
    <div id="launchModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity">
        <div class="bg-white p-8 rounded-3xl w-full max-w-md shadow-2xl scale-95 opacity-0 transition-all duration-300" id="launchModalContent">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-xl text-gray-800" id="modalTitle">Luncurkan Kuis</h3>
                <button onclick="closeLaunchModal()" class="text-gray-400 hover:text-gray-600 bg-gray-100 p-2 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('teacher.launch') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="mode" id="quizModeInput" value="standard">

                <div>
                    <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2 block">Pilih Kuis Bank Soal</label>
                    <div class="relative">
                        <select name="quiz_id" required class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 text-sm font-semibold text-gray-700 cursor-pointer transition appearance-none">
                            @if($quizzes->isEmpty())
                                <option value="">Belum ada kuis (Buat di Library)</option>
                            @else
                                @foreach($quizzes as $quiz)
                                    <option value="{{ $quiz->id }}">{{ $quiz->title }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="absolute right-3 top-3.5 pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2 block">Durasi</label>
                        <div class="relative">
                            <input type="number" name="time_limit" value="10" min="1" required class="w-full pl-4 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 text-sm font-bold text-gray-800 transition">
                            <span class="absolute right-4 top-3 text-xs font-bold text-gray-400">MIN</span>
                        </div>
                    </div>

                    <div id="teamInputContainer" class="hidden">
                        <label class="text-[11px] font-bold text-purple-600 uppercase tracking-wider mb-2 block">Jumlah Tim</label>
                        <div class="relative">
                            <input type="number" name="num_teams" value="4" min="2" max="8" class="w-full pl-4 pr-12 py-3 bg-purple-50 border border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500/50 text-sm font-bold text-purple-900 transition">
                            <span class="absolute right-4 top-3 text-xs font-bold text-purple-400">TIM</span>
                        </div>
                    </div>
                </div>

                <button type="submit" {{ $quizzes->isEmpty() ? 'disabled' : '' }} class="w-full bg-gray-900 hover:bg-black disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3.5 rounded-xl transition-all mt-6 text-sm shadow-[0_4px_14px_0_rgba(0,0,0,0.39)] hover:shadow-[0_6px_20px_rgba(0,0,0,0.23)] hover:-translate-y-0.5 active:scale-[0.98]">
                    Luncurkan ke Lobby
                </button>
            </form>
        </div>
    </div>

    <!-- Mobile Bottom Nav -->
    <div class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 flex justify-around items-center h-16 z-40 pb-safe">
        <a href="{{ route('teacher.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-bold mt-1">Dashboard</span>
        </a>
        <a href="{{ route('teacher.library') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-gray-900 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <span class="text-[10px] font-bold mt-1">Bank Soal</span>
        </a>
        <a href="{{ route('teacher.results') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-gray-900 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span class="text-[10px] font-bold mt-1">Live Results</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-gray-900 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[10px] font-bold mt-1">Profil</span>
        </a>
    </div>

    <script>
        function openLaunchModal(mode) {
            const modal = document.getElementById('launchModal');
            const content = document.getElementById('launchModalContent');
            const modeInput = document.getElementById('quizModeInput');
            const title = document.getElementById('modalTitle');
            const teamInput = document.getElementById('teamInputContainer');

            modeInput.value = mode;

            if(mode === 'space_race') {
                title.textContent = 'Setup Space Race 🚀';
                title.classList.replace('text-gray-800', 'text-purple-800');
                teamInput.classList.remove('hidden');
            } else {
                title.textContent = 'Setup Kuis Standard';
                title.classList.replace('text-purple-800', 'text-gray-800');
                teamInput.classList.add('hidden');
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Small delay for animation
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeLaunchModal() {
            const modal = document.getElementById('launchModal');
            const content = document.getElementById('launchModalContent');

            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Close on click outside
        document.getElementById('launchModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLaunchModal();
            }
        });
    </script>
</body>
</html>
