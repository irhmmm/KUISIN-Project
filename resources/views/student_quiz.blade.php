<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Quiz Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col relative pb-20">

    <header class="bg-white px-6 py-4 flex justify-between items-center shadow-sm z-10">
        <div class="font-bold text-gray-500 uppercase tracking-wider text-sm flex items-center gap-3">
            {{ session('room_name') }}
            @if(isset($room->mode) && $room->mode === 'space_race' && $sessionData->team_name)
                <span class="text-xs px-2 py-1 rounded text-white font-black" style="background-color:
                    {{ $sessionData->team_name == 'Merah' ? '#ef4444' :
                      ($sessionData->team_name == 'Biru' ? '#3b82f6' :
                      ($sessionData->team_name == 'Hijau' ? '#22c55e' :
                      ($sessionData->team_name == 'Kuning' ? '#eab308' : '#a855f7'))) }}">
                    TIM {{ strtoupper($sessionData->team_name) }} 🚀
                </span>
            @endif
        </div>
        <div class="flex items-center space-x-4">
            <div class="bg-red-50 text-red-600 text-sm font-bold px-3 py-1.5 rounded-full flex items-center border border-red-100 shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span id="countdownDisplay">00:00</span>
            </div>
            <div class="bg-blue-100 text-[#4a728a] text-xs font-bold px-3 py-1.5 rounded-full" id="soalIndikator">
                1 dari {{ $totalQuestions }}
            </div>
        </div>
    </header>

    <div class="w-full bg-gray-200 h-1.5">
        <div id="progressBar" class="bg-[#4a728a] h-1.5 rounded-r-full transition-all duration-500" style="width: 0%"></div>
    </div>

    <main class="flex-grow max-w-4xl mx-auto w-full p-6 mt-4">
        <form id="quizForm" action="{{ route('student.submit') }}" method="POST">
            @csrf

            @foreach($questions as $index => $q)
                <div class="question-card" id="question_{{ $index }}" style="display: {{ $index == 0 ? 'block' : 'none' }};">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <span class="text-[#4a728a]">Soal {{ $index + 1 }} dari {{ count($questions) }}:</span><br>
                        {{ $q->question_text }}
                    </h3>

                    <div class="space-y-3">
                        @foreach($q->options as $opt)
                        <label class="flex items-center p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt->id }}"
                                   class="w-4 h-4 text-[#4a728a] focus:ring-[#4a728a]"
                                   {{ $q->answered_id == $opt->id ? 'checked' : '' }}>
                            <span class="ml-3 text-sm text-gray-700">{{ $opt->option_text }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <button type="button" id="btn-prev" onclick="changeQuestion(-1)" class="hidden px-5 py-2.5 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition">
                    &laquo; Kembali
                </button>
                <div id="btn-prev-placeholder" class="px-5 py-2.5"></div>

                <div class="flex gap-2">
                    <button type="button" id="btn-skip" onclick="changeQuestion(1)" class="px-5 py-2.5 border border-gray-300 text-gray-500 font-bold rounded-xl hover:bg-gray-50 transition">
                        Lewati
                    </button>
                    <button type="button" id="btn-next" onclick="changeQuestion(1)" class="px-5 py-2.5 bg-[#4a728a] text-white font-bold rounded-xl hover:bg-[#38596e] transition shadow-md">
                        Selanjutnya &raquo;
                    </button>
                    <button type="submit" id="btn-submit" class="hidden px-5 py-2.5 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition shadow-md">
                        Selesai & Kumpulkan
                    </button>
                </div>
            </div>
        </form>
    </main>

<script>
    // --- 1. TIMER PEMBULATAN ---
    let timeRemaining = Math.floor({{ $remainingSeconds }});
    const display = document.getElementById('countdownDisplay');
    let isSubmitting = false;

    const timerInterval = setInterval(() => {
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            isSubmitting = true;
            alert("Waktu habis! Jawaban Anda akan dikirim otomatis.");
            window.onbeforeunload = null;
            document.getElementById('quizForm').submit();
        } else {
            let minutes = Math.floor(timeRemaining / 60);
            let seconds = timeRemaining % 60;
            display.textContent = minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
            timeRemaining--;
        }
    }, 1000);

    // --- 2. NAVIGASI PROGRESS ---
    let currentIndex = 0;
    const totalQuestions = {{ count($questions) }};

    function updateProgress() {
        let percent = ((currentIndex + 1) / totalQuestions) * 100;
        document.getElementById('progressBar').style.width = percent + '%';
        document.getElementById('soalIndikator').innerText = (currentIndex + 1) + ' dari ' + totalQuestions;
    }

    function changeQuestion(step) {
        document.getElementById('question_' + currentIndex).style.display = 'none';
        currentIndex += step;
        document.getElementById('question_' + currentIndex).style.display = 'block';
        updateProgress();

        document.getElementById('btn-prev').classList.toggle('hidden', currentIndex === 0);
        document.getElementById('btn-prev-placeholder').classList.toggle('hidden', currentIndex !== 0);

        const isLast = currentIndex === totalQuestions - 1;
        document.getElementById('btn-skip').classList.toggle('hidden', isLast);
        document.getElementById('btn-next').classList.toggle('hidden', isLast);
        document.getElementById('btn-submit').classList.toggle('hidden', !isLast);
    }
    updateProgress();

    // --- 3. AUTO-SAVE REAL-TIME (SOLUSI LIVE RESULTS) ---
    const quizForm = document.getElementById('quizForm');
    const radios = quizForm.querySelectorAll('input[type="radio"]');

    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            const formData = new FormData(quizForm);

            // Kirim jawaban secara diam-diam ke server setiap kali di-klik
            fetch("{{ route('student.submit') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest" // Memberitahu server ini adalah request rahasia (AJAX)
                }
            })
            .then(res => res.json())
            .then(data => console.log("Jawaban tersimpan live!"))
            .catch(err => console.error("Gagal simpan:", err));
        });
    });

    // --- 4. RADAR DOSEN ---
    setInterval(() => {
        fetch("{{ route('student.checkRoom') }}")
        .then(response => response.json())
        .then(data => {
            if (data.is_active == 0) {
                isSubmitting = true;
                window.onbeforeunload = null;
                document.getElementById('quizForm').submit();
            }
        })
        .catch(err => console.log('Radar offline'));
    }, 3000);

    @if(!isset($room->mode) || $room->mode === 'standard')
    // --- 5. ANTI CURANG ---
    // Mencegah klik kanan
    document.addEventListener('contextmenu', event => event.preventDefault());

    // Mencegah shortcut keyboard tertentu (F12, Ctrl+Shift+I, dll)
    document.addEventListener('keydown', function(e) {
        if(e.key === 'F12' || (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) || (e.ctrlKey && e.key === 'U')) {
            e.preventDefault();
        }
    });

    // Mencegah reload atau keluar dari halaman
    window.addEventListener('beforeunload', function (e) {
        if (isSubmitting) return;
        e.preventDefault();
        e.returnValue = 'Anda sedang mengerjakan kuis. Apakah Anda yakin ingin keluar?';
    });

    // Deteksi jika siswa pindah tab
    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "hidden" && !isSubmitting) {
            isSubmitting = true;
            fetch("{{ route('student.cheat') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                }
            });
            alert("Peringatan! Anda terdeteksi meninggalkan halaman ujian. Ujian Anda langsung diakhiri otomatis!");
            window.onbeforeunload = null;
            document.getElementById('quizForm').submit();
        }
    });

    // Deteksi blur (buka window baru atau pindah window)
    window.addEventListener("blur", () => {
        if (!isSubmitting) {
            isSubmitting = true;
            fetch("{{ route('student.cheat') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                }
            });
            alert("Peringatan! Anda terdeteksi keluar dari jendela ujian. Ujian Anda langsung diakhiri otomatis!");
            window.onbeforeunload = null;
            document.getElementById('quizForm').submit();
        }
    });

    // Izinkan submit tanpa peringatan
    document.getElementById('quizForm').addEventListener('submit', function() {
        isSubmitting = true;
            window.onbeforeunload = null;
    });
    @endif
</script>
</body>
</html>
