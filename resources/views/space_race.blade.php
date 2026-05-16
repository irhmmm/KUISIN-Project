<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Space Race Live!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800;900&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Outfit', sans-serif; } 
        .stars {
            background-image: radial-gradient(2px 2px at 20px 30px, #eee, rgba(0,0,0,0)),
            radial-gradient(2px 2px at 40px 70px, #fff, rgba(0,0,0,0)),
            radial-gradient(2px 2px at 50px 160px, #ddd, rgba(0,0,0,0)),
            radial-gradient(2px 2px at 90px 40px, #fff, rgba(0,0,0,0)),
            radial-gradient(2px 2px at 130px 80px, #fff, rgba(0,0,0,0));
            background-repeat: repeat;
            background-size: 200px 200px;
            animation: stars 4s linear infinite;
        }
        @keyframes stars {
            0% { background-position: 0 0; }
            100% { background-position: -200px 0; }
        }
    </style>
</head>
<body class="bg-[#0b0c10] text-white h-screen flex flex-col overflow-hidden relative">

    <div class="absolute inset-0 stars opacity-50 pointer-events-none"></div>

    <header class="bg-[#1f2833]/80 backdrop-blur-md px-8 py-5 flex justify-between items-center shadow-lg border-b border-[#45a29e]/30 relative z-10">
        <div class="flex items-center">
            <a href="{{ route('teacher.dashboard') }}" class="mr-4 text-[#66fcf1] hover:text-white p-2 transition block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#66fcf1] to-[#45a29e] tracking-wider uppercase">
                    Space Race
                </h1>
                <p class="text-sm text-gray-400 mt-1">Kode Room: <span class="text-white font-bold tracking-widest bg-white/10 px-2 py-0.5 rounded">{{ $room->room_name }}</span></p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div id="statusBadge" class="bg-yellow-500/20 border border-yellow-500 text-yellow-500 px-4 py-2 rounded-lg font-bold text-sm tracking-widest uppercase">
                Menunggu Peserta... (<span id="joinedCount">0</span>)
            </div>
            
            @if($room->status === 'waiting')
            <form action="{{ route('teacher.startExam') }}" method="POST">
                @csrf
                <button type="submit" class="bg-gradient-to-r from-green-400 to-emerald-600 hover:from-green-500 hover:to-emerald-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition shadow-[0_0_15px_rgba(52,211,153,0.5)] uppercase tracking-wider">
                    Mulai Balapan! 🚀
                </button>
            </form>
            @else
            <form action="{{ route('teacher.endExam') }}" method="POST" onsubmit="return confirm('Akhiri Space Race sekarang?');">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition shadow-sm uppercase tracking-wider">
                    Akhiri Balapan
                </button>
            </form>
            @endif
        </div>
    </header>

    <main class="flex-grow p-8 relative z-10 flex flex-col gap-6 overflow-y-auto" id="raceContainer">
        <!-- Roket akan di-render di sini via JS -->
    </main>

    <script>
        // Mape warna roket
        const teamColors = {
            'Merah': '#ef4444',
            'Biru': '#3b82f6',
            'Hijau': '#22c55e',
            'Kuning': '#eab308',
            'Ungu': '#a855f7',
            'Oranye': '#f97316',
            'Pink': '#ec4899',
            'Cokelat': '#84cc16'
        };

        function fetchSpaceRaceData() {
            fetch("{{ route('teacher.spacerace.data') }}")
                .then(res => res.json())
                .then(data => {
                    if(data.error) return;

                    document.getElementById('joinedCount').innerText = data.joined_count;
                    
                    if(data.status === 'started') {
                        const badge = document.getElementById('statusBadge');
                        badge.className = 'bg-green-500/20 border border-green-500 text-green-400 px-4 py-2 rounded-lg font-bold text-sm tracking-widest uppercase shadow-[0_0_10px_rgba(34,197,94,0.3)]';
                        badge.innerText = 'BALAPAN BERLANGSUNG!';
                    } else if (data.status === 'finished') {
                        const badge = document.getElementById('statusBadge');
                        badge.className = 'bg-gray-500/20 border border-gray-500 text-gray-400 px-4 py-2 rounded-lg font-bold text-sm tracking-widest uppercase';
                        badge.innerText = 'BALAPAN SELESAI';
                    }

                    const container = document.getElementById('raceContainer');
                    container.innerHTML = '';

                    data.teams.forEach((team, index) => {
                        const color = teamColors[team.name] || '#ffffff';
                        const progress = team.progress;
                        
                        const track = document.createElement('div');
                        track.className = 'w-full bg-[#1f2833]/50 rounded-2xl border border-white/5 p-4 relative flex items-center shadow-lg';
                        
                        // Garis start & finish
                        const finishLine = `<div class="absolute right-[5%] top-0 bottom-0 w-2 border-l-2 border-r-2 border-dashed border-white/20"></div>`;
                        
                        const rocket = `
                            <div class="relative w-full h-16 flex items-center pr-[10%]">
                                <div class="absolute w-full h-1 bg-gradient-to-r from-transparent to-${color.replace('#', '')} opacity-20 rounded-full left-0"></div>
                                <div class="absolute left-0 transition-all duration-1000 ease-out z-10 flex items-center" style="left: calc(${progress * 0.85}%);">
                                    <div class="mr-3 text-right">
                                        <div class="text-sm font-bold tracking-widest" style="color: ${color}">${team.name.toUpperCase()}</div>
                                        <div class="text-[10px] text-gray-400">${team.score} point (${team.members} orang)</div>
                                    </div>
                                    <div class="text-4xl filter drop-shadow-[0_0_15px_${color}] transform rotate-45">🚀</div>
                                </div>
                            </div>
                        `;

                        track.innerHTML = finishLine + rocket;
                        container.appendChild(track);
                    });
                });
        }

        setInterval(fetchSpaceRaceData, 2000);
        fetchSpaceRaceData();
    </script>
    <script>
        // Halaman otomatis merefresh setiap 5 detik agar nilai live muncul
        setTimeout(function() { window.location.reload(); }, 5000);
    </script>
</body>
</html>
