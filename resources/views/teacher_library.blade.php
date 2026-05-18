<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KUISIN – Library Soal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Outfit', sans-serif; }
        @keyframes fadeUp   { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        @keyframes slideIn  { from { opacity:0; transform:translateX(16px); } to { opacity:1; transform:translateX(0); } }
        @keyframes spin     { to   { transform:rotate(360deg); } }
        .fade-up  { animation: fadeUp  .35s ease both; }
        .slide-in { animation: slideIn .3s ease both; }
        .spinner  { animation: spin .7s linear infinite; }

        /* drag-zone */
        .drop-zone { transition: border-color .2s, background .2s; }
        .drop-zone.dragover { border-color:#3b82f6 !important; background:#eff6ff !important; }

        /* tab */
        .tab-btn { transition: all .2s; border: 1.5px solid transparent; }
        .tab-btn.active { background:#1e293b; color:#fff; border-color:#1e293b; }
        .tab-btn:not(.active) { color:#64748b; border-color:#e2e8f0; }
        .tab-btn:not(.active):hover { border-color:#cbd5e1; color:#334155; }

        /* question card */
        .q-card { transition: box-shadow .2s, transform .2s; }
        .q-card:hover { box-shadow:0 10px 25px -5px rgba(0,0,0,0.05); transform:translateY(-2px); }

        /* progress bar */
        #importProgress { transition: width .4s ease; }

        /* toast */
        #toast { transition: all .35s cubic-bezier(.175,.885,.32,1.275); }

        /* scrollbar thin */
        .thin-scroll::-webkit-scrollbar { width:4px; }
        .thin-scroll::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:9px; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden">

{{-- ══════════════════ SIDEBAR ══════════════════ --}}
<aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between shrink-0 hidden md:flex z-20 shadow-sm relative">
    <div>
        <!-- Logo -->
        <div class="p-6 flex items-center space-x-3">
            <div class="bg-[#4a728a] text-white p-1.5 rounded-md shadow-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3z"/></svg>
            </div>
            <span class="font-bold text-xl tracking-wide text-gray-800">KUISIN</span>
        </div>

        <nav class="px-4 space-y-1 mt-4">
            <a href="{{ route('teacher.dashboard') }}" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            <a href="{{ route('teacher.library') }}" class="flex items-center px-4 py-3 bg-blue-50 text-blue-700 rounded-lg font-semibold transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Bank Soal
            </a>
            <a href="{{ route('teacher.results') }}" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Live results
            </a>
            <div class="pt-6">
                <a href="{{ route('teacher.logout') }}" onclick="return confirm('Yakin ingin keluar?')" class="flex items-center px-4 py-3 text-red-500 hover:bg-red-50 rounded-lg font-bold transition border border-red-100 shadow-sm">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout Akun
                </a>
            </div>
        </nav>
    </div>
    <div class="p-4 border-t border-gray-100 flex items-center bg-gray-50/50">
        <div class="w-10 h-10 bg-blue-100 text-blue-700 font-bold text-lg rounded-full mr-3 flex items-center justify-center uppercase shadow-inner">
            {{ substr(session('teacher_name','?'), 0, 1) }}
        </div>
        <div class="min-w-0">
            <p class="text-sm font-bold text-gray-800 truncate">{{ session('teacher_name') }}</p>
            <p class="text-[10px] text-gray-500 font-medium">Dosen Pengajar</p>
        </div>
    </div>
</aside>

{{-- ══════════════════ MAIN ══════════════════ --}}
<main class="flex-1 flex flex-col h-screen overflow-hidden">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 md:px-8 shrink-0 shadow-sm z-10 relative">
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-bold text-gray-800">Bank Soal</h1>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openQuizModal()" class="flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-blue-600 bg-blue-50 border border-blue-100 hover:bg-blue-100 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Kategori Baru
            </button>
            @if($selectedQuizId)
            <button onclick="openModal()" class="flex items-center gap-1.5 px-4 py-2 text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 rounded-xl transition shadow-md shadow-gray-900/20 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/></svg>
                Import Massal
            </button>
            @endif
        </div>
    </header>

    {{-- Body --}}
    <div class="flex-1 overflow-hidden flex flex-col md:flex-row bg-gray-50/50">

        {{-- ── Left: Daftar Quiz / Kategori ── --}}
        <div class="w-full md:w-80 shrink-0 border-r border-gray-200 bg-white overflow-y-auto thin-scroll flex flex-col shadow-[4px_0_24px_rgba(0,0,0,0.02)] z-0">
            <div class="p-6 border-b border-gray-100 bg-white sticky top-0 z-10">
                <h3 class="font-bold text-gray-800 text-sm mb-1">Kategori Kuis</h3>
                <p class="text-xs text-gray-500">Pilih kuis untuk melihat/edit soal</p>
            </div>
            
            <div class="p-4 space-y-2 flex-1">
                @forelse($quizzes as $quiz)
                <a href="{{ route('teacher.library', ['quiz_id' => $quiz->id]) }}" 
                   class="block p-4 rounded-2xl border {{ $selectedQuizId == $quiz->id ? 'bg-blue-50 border-blue-200 shadow-sm' : 'bg-white border-gray-100 hover:border-blue-100 hover:bg-gray-50' }} transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-colors {{ $selectedQuizId == $quiz->id ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-gray-100 text-gray-400 group-hover:bg-blue-100 group-hover:text-blue-500' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-sm truncate {{ $selectedQuizId == $quiz->id ? 'text-blue-900' : 'text-gray-700' }}">{{ $quiz->title }}</h4>
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center py-10 px-4">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">Belum ada Kategori</p>
                    <p class="text-xs text-gray-400 mt-1">Buat kategori kuis pertama Anda.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Right: Daftar Soal ── --}}
        <div class="flex-1 overflow-y-auto thin-scroll flex flex-col pb-24 md:pb-0">
            
            @if(!$selectedQuizId)
            <div class="flex-1 flex flex-col items-center justify-center p-10 text-center">
                <img src="https://illustrations.popsy.co/amber/keynote-presentation.svg" class="w-64 h-64 mb-4 opacity-80" alt="Select Quiz">
                <h3 class="text-xl font-bold text-gray-800">Pilih Kategori Kuis</h3>
                <p class="text-gray-500 mt-2 max-w-sm">Pilih salah satu kategori kuis di sebelah kiri untuk melihat dan menambahkan soal.</p>
            </div>
            @else
            
            <div class="p-8">
                @if(session('success'))
                <div class="fade-up bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 mb-6 shadow-sm">
                    <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="font-semibold text-sm">{{ session('success') }}</span>
                </div>
                @endif

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center font-bold">
                            {{ count($questions) }}
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Daftar Soal</h2>
                            <p class="text-xs text-gray-500">Kategori terpilih saat ini</p>
                        </div>
                    </div>
                    <div class="relative w-64 hidden md:block">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input id="searchQ" type="text" placeholder="Cari soal…" oninput="filterQ()"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-sm transition">
                    </div>
                </div>

                {{-- Form Tambah Soal Manual (Inline) --}}
                <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm mb-8 relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-gray-50 rounded-full opacity-50 pointer-events-none"></div>
                    <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Tambah Soal Manual
                    </h3>
                    <form action="{{ route('teacher.storeQuestion') }}" method="POST">
                        @csrf
                        <input type="hidden" name="quiz_id" value="{{ $selectedQuizId }}">
                        <textarea name="question_text" rows="2" required placeholder="Tulis pertanyaan di sini…"
                            class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl resize-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition mb-4 shadow-sm"></textarea>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                            @php $labels = ['A','B','C','D']; @endphp
                            @foreach($labels as $lbl)
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl p-2 pl-3 hover:border-blue-300 transition focus-within:border-blue-400 focus-within:bg-white">
                                <input type="radio" name="correct_answer" value="{{ $lbl }}" required class="w-4 h-4 shrink-0 text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-bold text-gray-500 w-4">{{ $lbl }}</span>
                                <input type="text" name="option_{{ strtolower($lbl) }}" required placeholder="Pilihan {{ $lbl }}"
                                    class="flex-1 bg-transparent px-2 py-1.5 text-sm border-none focus:outline-none focus:ring-0 text-gray-700">
                            </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition shadow-md shadow-gray-900/20 active:scale-95">
                                Simpan Soal
                            </button>
                        </div>
                    </form>
                </div>

                <div id="qList" class="space-y-4">
                    @forelse($questions as $i => $q)
                    <div class="q-card bg-white rounded-3xl border border-gray-200 p-6 fade-up relative" style="animation-delay:{{ $i*30 }}ms" data-q="{{ strtolower($q->question_text) }}">
                        <div class="flex items-start gap-4">
                            <span class="w-8 h-8 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">{{ $i+1 }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-semibold text-gray-800 leading-snug pr-20">{{ $q->question_text }}</p>
                                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @php $ol = ['A','B','C','D']; @endphp
                                    @foreach($q->options as $oi => $opt)
                                    <div class="flex items-center gap-2.5 text-xs px-3 py-2.5 rounded-xl border
                                        {{ $opt->is_correct ? 'bg-green-50 border-green-200 text-green-800 font-semibold shadow-sm' : 'text-gray-600 bg-gray-50 border-gray-100' }}">
                                        <div class="w-5 h-5 rounded flex items-center justify-center shrink-0 {{ $opt->is_correct ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-500' }} font-bold text-[10px]">{{ $ol[$oi] }}</div>
                                        <span class="truncate flex-1">{{ $opt->option_text }}</span>
                                        @if($opt->is_correct)
                                        <svg class="w-4 h-4 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="absolute top-6 right-6 flex gap-2">
                                <a href="{{ route('teacher.editQuestion', $q->id) }}" class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition border border-gray-200 hover:border-blue-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                                <form action="{{ route('teacher.deleteQuestion', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition border border-gray-200 hover:border-red-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50/50">
                        <div class="w-16 h-16 bg-white border border-gray-100 shadow-sm rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="font-bold text-gray-500">Belum ada soal</p>
                        <p class="text-sm text-gray-400 mt-1">Gunakan form di atas atau tombol Import Massal.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>
</main>

{{-- ══════════════════ MODAL BUAT KUIS BARU ══════════════════ --}}
<div id="modalQuiz" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden fade-up">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <h2 class="font-bold text-gray-800 text-lg">Buat Kategori Kuis Baru</h2>
            <button onclick="closeQuizModal()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
            <form action="{{ route('teacher.storeQuiz') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kategori</label>
                    <input type="text" name="title" required placeholder="Contoh: UTS Semester 1, Kuis Pemrograman..." 
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 text-sm font-medium transition">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-600/30 transition-all active:scale-95">
                    Simpan Kategori
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════ IMPORT MODAL ══════════════════ --}}
<div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-gray-900/40 backdrop-blur-sm">
    <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl overflow-hidden fade-up">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/></svg>
                </div>
                <h2 class="font-bold text-gray-800 text-lg">Import Soal Massal</h2>
            </div>
            <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-2 px-6 pt-5">
            <button class="tab-btn active text-sm font-bold px-4 py-2 rounded-xl" onclick="switchTab('csv',this)">CSV / Excel</button>
            <button class="tab-btn text-sm font-bold px-4 py-2 rounded-xl" onclick="switchTab('json',this)">JSON</button>
            <button class="tab-btn text-sm font-bold px-4 py-2 rounded-xl" onclick="switchTab('pdf',this)">PDF</button>
            <button class="tab-btn text-sm font-bold px-4 py-2 rounded-xl" onclick="switchTab('paste',this)">Paste Teks</button>
        </div>

        <div class="p-6 space-y-5">

            {{-- ── CSV Tab ── --}}
            <div id="panel-csv">
                <div id="dropCsv" class="drop-zone border-2 border-dashed border-gray-200 bg-gray-50 hover:bg-gray-100 transition-colors rounded-2xl p-10 text-center cursor-pointer"
                     onclick="document.getElementById('fileCsv').click()"
                     ondragover="doDragOver(event,'dropCsv')" ondragleave="doDragLeave('dropCsv')" ondrop="doDrop(event,'csv')">
                    <div class="w-12 h-12 bg-white shadow-sm border border-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <p class="text-base font-bold text-gray-700">Drag & drop file .csv di sini</p>
                    <p class="text-sm text-gray-400 mt-1">atau klik untuk pilih file dari komputer</p>
                    <p id="csvName" class="text-sm text-blue-600 font-bold mt-3 hidden bg-blue-50 inline-block px-3 py-1 rounded-lg"></p>
                </div>
                <input type="file" id="fileCsv" accept=".csv,.txt" class="hidden" onchange="onFileChange(this,'csv')">
                <div class="mt-4 flex justify-between items-center">
                    <p class="text-xs text-gray-500">Kolom: question, A, B, C, D, answer</p>
                    <button onclick="downloadTemplate()" class="text-xs font-bold text-blue-600 hover:text-blue-700 underline decoration-blue-200 underline-offset-2">Download Template</button>
                </div>
            </div>

            {{-- ── JSON Tab ── --}}
            <div id="panel-json" class="hidden">
                <textarea id="jsonInput" rows="8" placeholder="Tempel JSON array di sini…"
                    class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-2xl font-mono resize-none focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition"></textarea>
            </div>

            {{-- ── PDF Tab ── --}}
            <div id="panel-pdf" class="hidden">
                <div id="dropPdf" class="drop-zone border-2 border-dashed border-gray-200 bg-gray-50 hover:bg-gray-100 transition-colors rounded-2xl p-10 text-center cursor-pointer"
                     onclick="document.getElementById('filePdf').click()"
                     ondragover="doDragOver(event,'dropPdf')" ondragleave="doDragLeave('dropPdf')" ondrop="doDrop(event,'pdf')">
                    <div class="w-12 h-12 bg-white shadow-sm border border-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-base font-bold text-gray-700">Drag & drop file .pdf di sini</p>
                    <p class="text-sm text-gray-400 mt-1">Sistem akan mendeteksi soal secara otomatis</p>
                    <p id="pdfName" class="text-sm text-red-600 font-bold mt-3 hidden bg-red-50 inline-block px-3 py-1 rounded-lg"></p>
                </div>
                <input type="file" id="filePdf" accept=".pdf" class="hidden" onchange="onFileChange(this,'pdf')">
                <div class="mt-4 flex justify-between items-center">
                    <p class="text-xs text-gray-500">Gunakan format: "1. Pertanyaan ... A. Jawaban ... Jawaban: A"</p>
                    <button onclick="downloadPdfTemplate()" class="text-xs font-bold text-red-600 hover:text-red-700 underline decoration-red-200 underline-offset-2">Download Template PDF</button>
                </div>
            </div>

            {{-- ── Paste Tab ── --}}
            <div id="panel-paste" class="hidden">
                <textarea id="pasteInput" rows="8" placeholder="Format CSV per baris:&#10;pertanyaan,A,B,C,D,kunci&#10;&#10;Contoh:&#10;Apa itu HTML?,Bahasa Markah,Bahasa Program,Framework,Library,A"
                    class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-2xl font-mono resize-none focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 transition"></textarea>
            </div>

            {{-- Preview --}}
            <div id="previewWrap" class="hidden">
                <div class="flex items-center justify-between mb-3">
                    <span id="previewBadge" class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-lg">0 soal siap import</span>
                    <button onclick="clearPreview()" class="text-xs font-bold text-gray-400 hover:text-gray-700">Bersihkan</button>
                </div>
                <div id="previewList" class="space-y-2 max-h-48 overflow-y-auto thin-scroll pr-2"></div>
            </div>

            {{-- Import Progress --}}
            <div id="progressWrap" class="hidden bg-gray-50 p-4 rounded-2xl border border-gray-100">
                <div class="flex justify-between text-xs font-bold text-gray-600 mb-2">
                    <span>Memproses dan menyimpan soal…</span>
                    <span id="progressText" class="text-blue-600">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div id="importProgress" class="bg-blue-600 h-2.5 rounded-full" style="width:0%"></div>
                </div>
            </div>

            {{-- Error --}}
            <div id="errorWrap" class="hidden bg-red-50 border border-red-100 text-red-700 text-sm p-4 rounded-2xl">
                <p class="font-bold mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Terjadi Kesalahan
                </p>
                <p id="errorMsg" class="text-red-600"></p>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button onclick="doPreview()" class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition flex items-center gap-2">
                    Preview
                </button>
                <button id="btnImport" onclick="doImport()" disabled
                    class="flex-1 flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 hover:bg-gray-800 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl transition shadow-lg shadow-gray-900/20 active:scale-[0.98]">
                    <span id="btnImportLabel">Import ke Kategori Ini</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Bottom Nav -->
<div class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 flex justify-around items-center h-16 z-40 pb-safe">
    <a href="{{ route('teacher.dashboard') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-gray-900 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        <span class="text-[10px] font-bold mt-1">Dashboard</span>
    </a>
    <a href="{{ route('teacher.library') }}" class="flex flex-col items-center justify-center w-full h-full text-blue-600">
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

{{-- Toast --}}
<div id="toast" class="fixed bottom-6 right-6 hidden items-center gap-3 bg-gray-900 text-white text-sm font-semibold px-5 py-4 rounded-2xl shadow-2xl z-[60] max-w-sm">
    <div id="toastIcon"></div>
    <span id="toastMsg"></span>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
// ─── State ───────────────────────────────────────────
let activeTab  = 'csv';
let csvRaw     = '';
let pdfRaw     = '';
let parsedQ    = [];
const currentQuizId = '{{ $selectedQuizId }}';

// ─── Modal ───────────────────────────────────────────
function openQuizModal() { const m=document.getElementById('modalQuiz'); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeQuizModal() { const m=document.getElementById('modalQuiz'); m.classList.add('hidden'); m.classList.remove('flex'); }

function openModal()  { const m=document.getElementById('modal'); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeModal() { const m=document.getElementById('modal'); m.classList.add('hidden'); m.classList.remove('flex'); resetAll(); }

// ─── Tabs ────────────────────────────────────────────
function switchTab(tab, btn) {
    activeTab = tab;
    ['csv','json','pdf','paste'].forEach(t => {
        document.getElementById('panel-'+t).classList.add('hidden');
        document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('active'); });
    });
    document.getElementById('panel-'+tab).classList.remove('hidden');
    btn.classList.add('active');
    clearPreview();
}

// ─── Drag & Drop ─────────────────────────────────────
function doDragOver(e, zoneId) { e.preventDefault(); document.getElementById(zoneId).classList.add('dragover'); }
function doDragLeave(zoneId)   { document.getElementById(zoneId).classList.remove('dragover'); }
function doDrop(e, type) {
    e.preventDefault();
    const zoneId = type === 'pdf' ? 'dropPdf' : 'dropCsv';
    document.getElementById(zoneId).classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) readFile(file, type);
}
function onFileChange(input, type) { if (input.files[0]) readFile(input.files[0], type); }

function readFile(file, type) {
    const reader = new FileReader();
    if (type === 'pdf') {
        reader.onload = e => { pdfRaw = e.target.result; showFileName('pdfName', file.name, file.size); };
        reader.readAsArrayBuffer(file);
    } else {
        reader.onload = e => { csvRaw = e.target.result; showFileName('csvName', file.name, file.size); };
        reader.readAsText(file, 'UTF-8');
    }
}
function showFileName(id, name, size) {
    const el = document.getElementById(id);
    el.textContent = '✓ ' + name + ' (' + (size/1024).toFixed(1) + ' KB)';
    el.classList.remove('hidden');
}

// ─── Preview & Parse ──────────────────────────────────
async function doPreview() {
    clearPreview(); hideError();
    try {
        if      (activeTab === 'csv')   parsedQ = parseCSV(csvRaw);
        else if (activeTab === 'json')  parsedQ = parseJSON(document.getElementById('jsonInput').value);
        else if (activeTab === 'paste') parsedQ = parseCSV(document.getElementById('pasteInput').value);
        else if (activeTab === 'pdf')   parsedQ = await parsePDF(pdfRaw);

        if (!parsedQ.length) { showError('Tidak ada soal valid. Periksa format data.'); return; }
        renderPreview(parsedQ);
        document.getElementById('btnImport').disabled = false;
    } catch(err) {
        showError(err.message || 'Format tidak dapat dibaca.');
    }
}

// ─── CSV Parser ───────────────────────────────────────
function parseCSV(raw) {
    if (!raw.trim()) throw new Error('Data kosong.');
    const lines = raw.trim().split('\n');
    let start = 0;
    const first = lines[0].toLowerCase();
    if (first.includes('question') || first.includes('pertanyaan') || first.includes('soal')) start = 1;

    const result = [];
    for (let i = start; i < lines.length; i++) {
        const line = lines[i].trim(); if (!line) continue;
        const cols = splitCSVLine(line);
        if (cols.length < 6) continue;
        const q = { question:cols[0].trim(), A:cols[1].trim(), B:cols[2].trim(), C:cols[3].trim(), D:cols[4].trim(), answer:cols[5].trim().toUpperCase() };
        if (!q.question||!q.A||!q.B||!q.C||!q.D||!['A','B','C','D'].includes(q.answer)) continue;
        result.push(q);
    }
    return result;
}

function splitCSVLine(line) {
    const r = []; let cur = ''; let inQ = false;
    for (let c of line) {
        if (c === '"') { inQ = !inQ; }
        else if (c === ',' && !inQ) { r.push(cur); cur = ''; }
        else cur += c;
    }
    r.push(cur);
    return r;
}

// ─── JSON Parser ──────────────────────────────────────
function parseJSON(raw) {
    const raw2 = raw.trim().replace(/^```json|```$/g, '').trim();
    if (!raw2) throw new Error('Input kosong.');
    const data = JSON.parse(raw2);
    if (!Array.isArray(data)) throw new Error('JSON harus berupa array []');
    return data.filter(item => item.question && item.A && item.B && item.C && item.D && item.answer)
               .map(item => ({ ...item, answer: item.answer.toString().toUpperCase() }));
}

// ─── PDF Parser (PDF.js) ─────────────────────────────
async function parsePDF(arrayBuffer) {
    if (!arrayBuffer) throw new Error('Pilih file PDF terlebih dahulu.');
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    const bufferClone = arrayBuffer.slice(0);

    const pdf = await pdfjsLib.getDocument({ data: bufferClone }).promise;
    let fullText = '';

    for (let p = 1; p <= pdf.numPages; p++) {
        const page    = await pdf.getPage(p);
        const content = await page.getTextContent();

        // Rekonstruksi baris berdasarkan posisi Y dari setiap item teks
        // Ini mencegah semua teks pada halaman menjadi satu baris panjang
        let lastY = null;
        for (const item of content.items) {
            if (!item.str.trim()) continue;
            const y = item.transform ? Math.round(item.transform[5]) : null;
            if (lastY !== null && y !== null && Math.abs(y - lastY) > 3) {
                fullText += '\n';
            }
            fullText += item.str;
            if (y !== null) lastY = y;
        }
        fullText += '\n';
    }

    return extractQuestionsFromText(fullText);
}

function normalizePdfText(text) {
    // Sisipkan baris baru sebelum pola-pola kunci agar parser bisa membacanya
    // Menangani kasus di mana semua teks menyambung dalam satu baris
    return text
        // Sebelum nomor soal: "...A 2. Pertanyaan..." → "...A\n2. Pertanyaan..."
        .replace(/(\S)\s+(\d+[.)]\s)/g, '$1\n$2')
        // Sebelum pilihan: "...Indonesia A. Jakarta..." → "...Indonesia\nA. Jakarta..."
        .replace(/([^\n])\s+([A-Da-d][.)]\s)/g, '$1\n$2')
        // Sebelum "Jawaban:": "...Tesla Jawaban: B" → "...Tesla\nJawaban: B"
        .replace(/([^\n])\s+((?:[Jj]awaban|[Aa]nswer)\s*:)/g, '$1\n$2');
}

function extractQuestionsFromText(text) {
    // Normalisasi teks terlebih dahulu untuk menangani teks yang menyambung
    const normalized = normalizePdfText(text);
    const results = [];
    const lines = normalized.split(/\r?\n/).map(l => l.trim()).filter(Boolean);

    let i = 0;
    while (i < lines.length) {
        const qMatch = lines[i].match(/^(\d+)[.)]\s+(.+)/);
        if (!qMatch) { i++; continue; }

        let questionText = qMatch[2].trim();
        let opts = {};
        let currentAnswer = null;
        i++;

        while (i < lines.length) {
            // Berhenti jika menemukan soal berikutnya
            if (lines[i].match(/^(\d+)[.)]\s+/)) break;

            const oMatch = lines[i].match(/^([A-Da-d])[.)]\s+(.+)/);
            // Cocokkan "Jawaban: A" di mana saja di baris (bukan hanya di awal)
            const jMatch = lines[i].match(/(?:[Jj]awaban|[Aa]nswer)\s*:?\s*([A-Da-d])/);

            if (oMatch) {
                const letter = oMatch[1].toUpperCase();
                // Hapus huruf jawaban yang menempel di akhir teks pilihan sebelumnya
                // contoh: "MedanA" → "Medan"
                let optText = oMatch[2].trim();
                opts[letter] = optText;
            } else if (jMatch) {
                currentAnswer = jMatch[1].toUpperCase();
            } else {
                // Teks bebas yang bukan pilihan dan bukan jawaban: gabungkan ke pertanyaan jika belum ada pilihan A
                if (!opts['A']) questionText += ' ' + lines[i];
            }
            i++;
        }

        // Bersihkan teks pilihan terakhir yang mungkin ada huruf jawaban menempel
        // contoh: opts.D = "MedanA" padahal harusnya "Medan"
        if (currentAnswer && opts[currentAnswer]) {
            // Sudah bersih
        }

        if (opts.A && opts.B && opts.C && opts.D && currentAnswer) {
            results.push({
                question: questionText,
                A: opts.A,
                B: opts.B,
                C: opts.C,
                D: opts.D,
                answer: currentAnswer
            });
        }
    }
    return results;
}

// ─── Render Preview ───────────────────────────────────
function renderPreview(questions) {
    const wrap = document.getElementById('previewWrap');
    const list = document.getElementById('previewList');
    const badge = document.getElementById('previewBadge');
    wrap.classList.remove('hidden');
    badge.textContent = questions.length + ' soal siap import';
    list.innerHTML = '';
    questions.forEach((q, idx) => {
        const div = document.createElement('div');
        div.className = 'slide-in flex items-start gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 text-sm';
        div.style.animationDelay = (idx * 25) + 'ms';
        div.innerHTML = `
            <div class="w-5 h-5 bg-emerald-100 text-emerald-600 rounded flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-gray-800 truncate">${idx+1}. ${esc(q.question)}</p>
                <p class="text-gray-500 mt-1 text-xs">Kunci: <b class="text-blue-600">${q.answer}</b> — ${esc(q[q.answer])}</p>
            </div>`;
        list.appendChild(div);
    });
}

// ─── Import ───────────────────────────────────────────
async function doImport() {
    if (!parsedQ.length) return;
    document.getElementById('btnImport').disabled = true;
    document.getElementById('btnImportLabel').textContent = 'Mengimport…';
    document.getElementById('progressWrap').classList.remove('hidden');

    let prog = 0;
    const progBar = document.getElementById('importProgress');
    const progTxt = document.getElementById('progressText');
    const tick = setInterval(() => {
        prog = Math.min(prog + Math.random() * 15, 90);
        progBar.style.width = prog.toFixed(0) + '%';
        progTxt.textContent = prog.toFixed(0) + '%';
    }, 200);

    try {
        const resp = await fetch('{{ route("teacher.storeQuestion") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ bulk_import: true, bulk_data: parsedQ, quiz_id: currentQuizId })
        });

        clearInterval(tick);
        progBar.style.width = '100%'; progTxt.textContent = '100%';

        const data = await resp.json();
        if (data.success) {
            closeModal();
            toast('success', data.message || parsedQ.length + ' soal berhasil diimport!');
            setTimeout(() => location.reload(), 1600);
        } else {
            showError(data.message || 'Terjadi kesalahan saat import.');
            document.getElementById('btnImport').disabled = false;
            document.getElementById('btnImportLabel').textContent = 'Import ke Kategori Ini';
        }
    } catch(e) {
        clearInterval(tick);
        showError('Koneksi gagal: ' + e.message);
        document.getElementById('btnImport').disabled = false;
        document.getElementById('btnImportLabel').textContent = 'Import ke Kategori Ini';
    }
}

// ─── Template Download ────────────────────────────────
function downloadTemplate() {
    const h = 'question,A,B,C,D,answer\n';
    const rows = [
        '"Apa kepanjangan dari PWM?","Pulse Width Modulation","Power Width Module","Phase Watt Module","Passive Wave Motor","A"',
        '"Berapa tegangan GPIO ESP32?","3.3V","5V","12V","1.8V","A"',
        '"Protokol komunikasi I2C membutuhkan berapa kabel data?","1","2","3","4","B"',
    ].join('\n');
    const blob = new Blob([h + rows], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
    a.download = 'template_soal_kuisin.csv'; a.click();
    toast('success', 'Template CSV berhasil didownload!');
}

function downloadPdfTemplate() {
    const text = `1. Apa nama ibu kota Indonesia?
A. Jakarta
B. Bandung
C. Surabaya
D. Medan
Jawaban: A

2. Berapakah hasil dari 5 x 5?
A. 10
B. 20
C. 25
D. 30
Jawaban: C

3. Siapakah penemu bola lampu?
A. Albert Einstein
B. Thomas Alva Edison
C. Isaac Newton
D. Nikola Tesla
Jawaban: B

Catatan Penting:
- Pastikan nomor soal diikuti tanda titik (contoh: 1.)
- Pastikan setiap pilihan ganda diawali huruf besar dan titik (contoh: A.)
- Pastikan kunci jawaban ditulis jelas di bagian bawah setiap soal.
- Anda bisa menyalin teks ini atau mengetiknya di Microsoft Word, lalu pilih "Save As" -> "PDF".`;

    const blob = new Blob([text], { type: 'text/plain;charset=utf-8;' });
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
    a.download = 'Template_Soal_Untuk_PDF.txt'; a.click();
    toast('success', 'Template Format PDF berhasil didownload!');
}

// ─── Helpers ─────────────────────────────────────────
function clearPreview() {
    parsedQ = [];
    document.getElementById('previewWrap').classList.add('hidden');
    document.getElementById('previewList').innerHTML = '';
    document.getElementById('progressWrap').classList.add('hidden');
    document.getElementById('btnImport').disabled = true;
    document.getElementById('btnImportLabel').textContent = 'Import ke Kategori Ini';
    hideError();
}
function resetAll() {
    clearPreview();
    csvRaw = ''; pdfRaw = '';
    ['csvName','pdfName'].forEach(id => { const el=document.getElementById(id); if(el) el.classList.add('hidden'); });
    document.getElementById('jsonInput').value = '';
    document.getElementById('pasteInput').value = '';
}
function showError(msg) { document.getElementById('errorWrap').classList.remove('hidden'); document.getElementById('errorMsg').textContent = msg; }
function hideError()    { document.getElementById('errorWrap').classList.add('hidden'); }
function esc(str)       { const d=document.createElement('div'); d.textContent=str||''; return d.innerHTML; }

function toast(type, msg) {
    const t = document.getElementById('toast');
    const icon = type === 'success'
        ? `<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`
        : `<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;
    document.getElementById('toastIcon').innerHTML = icon;
    document.getElementById('toastMsg').textContent = msg;
    t.classList.remove('hidden'); t.classList.add('flex');
    setTimeout(() => { t.classList.add('hidden'); t.classList.remove('flex'); }, 3500);
}

// ─── Search ───────────────────────────────────────────
function filterQ() {
    const q = document.getElementById('searchQ').value.toLowerCase();
    const cards = document.querySelectorAll('[data-q]');
    cards.forEach(c => { const v = c.dataset.q.includes(q); c.style.display = v ? '' : 'none'; });
}
</script>
</body>
</html>
