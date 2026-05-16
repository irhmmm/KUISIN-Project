<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Menunggu Dimulai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>
<body class="bg-[#f8fafc] h-screen flex flex-col items-center justify-center p-6">

    <div class="bg-white p-10 rounded-3xl shadow-xl w-full max-w-md text-center relative overflow-hidden border border-gray-100">
        @if(isset($room->mode) && $room->mode === 'space_race')
            <div class="absolute -right-16 -top-16 w-32 h-32 bg-purple-100 rounded-full blur-2xl"></div>
            <div class="absolute -left-16 -bottom-16 w-32 h-32 bg-indigo-100 rounded-full blur-2xl"></div>
        @else
            <div class="absolute -right-16 -top-16 w-32 h-32 bg-blue-100 rounded-full blur-2xl"></div>
        @endif

        <div class="relative z-10">
            @if(isset($room->mode) && $room->mode === 'space_race')
                <div class="text-6xl mb-6 animate-bounce">🚀</div>
                <h1 class="text-3xl font-black text-gray-800 tracking-tight mb-2">Space Race Lobby</h1>
                <p class="text-gray-500 font-medium mb-6">Bersiaplah, balapan akan segera dimulai!</p>
                
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200 mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">KAMU BERADA DI</p>
                    <h2 class="text-3xl font-black" style="color: 
                        {{ $sessionData->team_name == 'Merah' ? '#ef4444' : 
                          ($sessionData->team_name == 'Biru' ? '#3b82f6' : 
                          ($sessionData->team_name == 'Hijau' ? '#22c55e' : 
                          ($sessionData->team_name == 'Kuning' ? '#eab308' : '#a855f7'))) }}">
                        TIM {{ strtoupper($sessionData->team_name) }}
                    </h2>
                </div>
            @else
                <div class="text-6xl mb-6">⏳</div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Menunggu Dosen...</h1>
                <p class="text-gray-500 mb-8">Ujian akan otomatis dimulai saat dosen mengklik tombol mulai.</p>
            @endif

            <div class="flex flex-col items-center justify-center space-y-3">
                <div class="flex space-x-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                    <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                    <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.4s;"></div>
                </div>
                <span class="text-xs font-bold text-gray-400 tracking-widest uppercase">Jangan tutup halaman ini</span>
            </div>
        </div>
    </div>

    <script>
        function checkLobbyStatus() {
            fetch("{{ route('student.checkLobby') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'started') {
                        // Kuis sudah dimulai, reload halaman untuk masuk ke soal
                        window.location.reload();
                    } else if (data.is_active === 0) {
                        // Room ditutup sebelum dimulai
                        window.location.href = "{{ route('student.finished') }}";
                    }
                });
        }
        
        // Cek status setiap 2 detik
        setInterval(checkLobbyStatus, 2000);
    </script>
</body>
</html>
