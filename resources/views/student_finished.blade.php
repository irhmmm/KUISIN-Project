<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN – Hasil Kuis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Outfit', sans-serif; }

        @keyframes fadeUp   { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        @keyframes scaleIn  { from { opacity:0; transform:scale(0.8); } to { opacity:1; transform:scale(1); } }
        @keyframes countUp  { from { opacity:0; } to { opacity:1; } }
        @keyframes confetti { 0%   { transform: translateY(-10px) rotate(0deg); opacity:1; }
                              100% { transform: translateY(100vh) rotate(720deg); opacity:0; } }

        .fade-up  { animation: fadeUp  .5s ease both; }
        .scale-in { animation: scaleIn .6s cubic-bezier(.175,.885,.32,1.275) both; }

        .score-ring {
            background: conic-gradient(
                var(--ring-color) calc(var(--pct) * 1%),
                #e2e8f0 0
            );
            border-radius: 50%;
        }

        .confetti-piece {
            position: fixed;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            animation: confetti 3s ease-in both;
            pointer-events: none;
        }

        .thin-scroll::-webkit-scrollbar { width:4px; }
        .thin-scroll::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:9px; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-[#0f3352] to-[#1a5276] flex items-start justify-center py-10 px-4">

    {{-- Confetti (untuk skor tinggi) --}}
    @if($score >= 70)
    <div id="confettiContainer"></div>
    @endif

    <div class="w-full max-w-2xl space-y-5">

        {{-- ── KARTU UTAMA: SKOR ── --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden fade-up">

            {{-- Header banner --}}
            <div class="
                @if($score >= 80) bg-gradient-to-r from-emerald-500 to-green-400
                @elseif($score >= 60) bg-gradient-to-r from-amber-400 to-yellow-300
                @else bg-gradient-to-r from-rose-500 to-red-400
                @endif
                px-8 pt-10 pb-16 text-white text-center relative overflow-hidden">

                {{-- decorative circles --}}
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full pointer-events-none"></div>

                <div class="relative">
                    @if($score >= 80)
                        <div class="text-5xl mb-2">🏆</div>
                        <h1 class="text-2xl font-extrabold">Luar Biasa, {{ $studentName }}!</h1>
                        <p class="text-white/80 mt-1 text-sm font-medium">Kamu berhasil menguasai materi dengan sangat baik!</p>
                    @elseif($score >= 60)
                        <div class="text-5xl mb-2">👍</div>
                        <h1 class="text-2xl font-extrabold">Bagus, {{ $studentName }}!</h1>
                        <p class="text-white/80 mt-1 text-sm font-medium">Masih ada ruang untuk terus berkembang!</p>
                    @else
                        <div class="text-5xl mb-2">📚</div>
                        <h1 class="text-2xl font-extrabold">Tetap Semangat, {{ $studentName }}!</h1>
                        <p class="text-white/80 mt-1 text-sm font-medium">Pelajari kembali materinya dan coba lagi!</p>
                    @endif
                </div>
            </div>

            {{-- Score Circle --}}
            <div class="flex justify-center -mt-12 mb-2 relative z-10">
                @php
                    $ringColor = $score >= 80 ? '#10b981' : ($score >= 60 ? '#f59e0b' : '#ef4444');
                @endphp
                <div class="scale-in" style="
                    --pct: {{ $score }};
                    --ring-color: {{ $ringColor }};
                    width: 112px; height: 112px;
                    background: conic-gradient({{ $ringColor }} calc({{ $score }} * 1%), #e2e8f0 0);
                    border-radius: 50%;
                    padding: 6px;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
                ">
                    <div class="w-full h-full bg-white rounded-full flex flex-col items-center justify-center">
                        <span id="scoreNum" class="text-3xl font-extrabold leading-none"
                              style="color:{{ $ringColor }}">0</span>
                        <span class="text-xs font-bold text-gray-400 mt-0.5">SKOR</span>
                    </div>
                </div>
            </div>

            {{-- Stats row --}}
            <div class="px-8 pb-8">
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-center">
                        <p class="text-2xl font-extrabold text-emerald-600">{{ $correct }}</p>
                        <p class="text-xs font-semibold text-emerald-500 mt-0.5">Benar</p>
                    </div>
                    <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 text-center">
                        <p class="text-2xl font-extrabold text-rose-500">{{ $total - $correct }}</p>
                        <p class="text-xs font-semibold text-rose-400 mt-0.5">Salah</p>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-center">
                        <p class="text-2xl font-extrabold text-slate-600">{{ $total }}</p>
                        <p class="text-xs font-semibold text-slate-400 mt-0.5">Total Soal</p>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mb-1 flex justify-between text-xs font-semibold text-gray-500">
                    <span>Kemajuan</span>
                    <span>{{ $correct }}/{{ $total }} benar</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div id="progressBar" class="h-3 rounded-full transition-all duration-1000"
                         style="width:0%; background:{{ $ringColor }}"></div>
                </div>
            </div>
        </div>

        {{-- ── REVIEW JAWABAN ── --}}
        @if($questions->count() > 0)
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden fade-up" style="animation-delay:.2s">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800 text-sm">Review Jawaban</h2>
                    <p class="text-xs text-gray-400">Lihat mana yang benar dan mana yang salah</p>
                </div>
            </div>

            <div class="p-4 space-y-3 max-h-[480px] overflow-y-auto thin-scroll">
                @foreach($questions as $i => $q)
                @php
                    $answered = !is_null($q->student_answer_id);
                    $correct_q = $q->is_correct == 1;
                    $statusColor = !$answered ? 'gray' : ($correct_q ? 'emerald' : 'rose');
                    $statusLabel = !$answered ? 'Tidak Dijawab' : ($correct_q ? 'Benar' : 'Salah');
                    $statusIcon  = !$answered
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>'
                        : ($correct_q
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>');
                @endphp
                <div class="rounded-2xl border p-4 
                    @if(!$answered) border-gray-100 bg-gray-50
                    @elseif($correct_q) border-emerald-100 bg-emerald-50/50
                    @else border-rose-100 bg-rose-50/30
                    @endif">

                    <div class="flex items-start gap-3">
                        {{-- Status badge --}}
                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5
                            @if(!$answered) bg-gray-200 text-gray-500
                            @elseif($correct_q) bg-emerald-500 text-white
                            @else bg-rose-500 text-white
                            @endif">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $statusIcon !!}
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <p class="text-xs font-bold text-gray-500">Soal {{ $i+1 }}</p>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                                    @if(!$answered) bg-gray-200 text-gray-600
                                    @elseif($correct_q) bg-emerald-100 text-emerald-700
                                    @else bg-rose-100 text-rose-700
                                    @endif">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-gray-800 leading-snug mb-3">{{ $q->question_text }}</p>

                            {{-- Opsi jawaban --}}
                            <div class="space-y-1.5">
                                @php $labels = ['A','B','C','D']; @endphp
                                @foreach($q->options as $oi => $opt)
                                @php
                                    $isStudentPick = ($q->student_answer_id == $opt->id);
                                    $isCorrectOpt  = $opt->is_correct;
                                @endphp
                                <div class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium
                                    @if($isCorrectOpt) bg-emerald-100 border border-emerald-200 text-emerald-800
                                    @elseif($isStudentPick && !$isCorrectOpt) bg-rose-100 border border-rose-200 text-rose-700 line-through
                                    @else bg-white border border-gray-100 text-gray-500
                                    @endif">
                                    <span class="w-5 h-5 rounded flex items-center justify-center shrink-0 font-bold text-[10px]
                                        @if($isCorrectOpt) bg-emerald-500 text-white
                                        @elseif($isStudentPick && !$isCorrectOpt) bg-rose-400 text-white
                                        @else bg-gray-100 text-gray-400
                                        @endif">{{ $labels[$oi] ?? '?' }}</span>
                                    <span class="flex-1">{{ $opt->option_text }}</span>
                                    @if($isCorrectOpt)
                                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @elseif($isStudentPick && !$isCorrectOpt)
                                        <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── FOOTER NOTE ── --}}
        <div class="text-center text-white/50 text-xs pb-6 fade-up" style="animation-delay:.4s">
            <p>Terima kasih sudah mengerjakan kuis di <span class="font-bold text-white/70">KUISIN</span>!</p>
        </div>

    </div>

<script>
    // Animasi count-up skor
    const target   = {{ $score }};
    const el       = document.getElementById('scoreNum');
    const bar      = document.getElementById('progressBar');
    const pct      = {{ $total > 0 ? round(($correct / $total) * 100) : 0 }};
    let   current  = 0;
    const step     = Math.ceil(target / 60);
    const interval = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = current;
        if (current >= target) clearInterval(interval);
    }, 20);

    // Progress bar animate
    setTimeout(() => {
        bar.style.width = pct + '%';
    }, 300);

    // Confetti untuk skor tinggi
    @if($score >= 70)
    const colors = ['#10b981','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#ec4899'];
    const container = document.getElementById('confettiContainer');
    for (let i = 0; i < 60; i++) {
        const p = document.createElement('div');
        p.className = 'confetti-piece';
        p.style.left     = Math.random() * 100 + 'vw';
        p.style.top      = -20 + 'px';
        p.style.background = colors[Math.floor(Math.random() * colors.length)];
        p.style.animationDelay    = (Math.random() * 2) + 's';
        p.style.animationDuration = (2 + Math.random() * 2) + 's';
        p.style.width  = (6 + Math.random() * 8) + 'px';
        p.style.height = (6 + Math.random() * 8) + 'px';
        container.appendChild(p);
    }
    @endif
</script>

</body>
</html>
