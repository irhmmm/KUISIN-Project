<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Live Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col">

    <header class="bg-[#2a6282] text-white px-6 py-4 flex justify-between items-center shadow-md">
        <div class="flex items-center">
            <a href="{{ route('teacher.dashboard') }}" class="mr-4 hover:bg-[#1f4a63] p-2 rounded-full transition block">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold flex items-center">
                    Live Results
                    @if($room && $room->is_active)
                        <span class="ml-3 bg-green-500 text-white px-3 py-1 text-sm font-extrabold rounded-lg tracking-widest border border-green-400 shadow-sm">KODE: {{ $room->room_name }}</span>
                        @if(isset($room->status) && $room->status === 'waiting')
                            <span class="ml-2 bg-yellow-400 text-yellow-900 px-3 py-1 text-xs font-bold rounded-lg uppercase shadow-sm">Lobby Terbuka</span>
                        @endif
                    @else
                        <span class="ml-3 bg-red-500 text-white px-3 py-1 text-sm font-extrabold rounded-lg tracking-widest border border-red-400 shadow-sm">UJIAN BERAKHIR</span>
                    @endif
                </h1>
                <p class="text-xs text-blue-200 mt-1">Pantau perkembangan mahasiswa secara Real-time</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('teacher.export') }}" class="bg-green-500 hover:bg-green-600 text-white flex items-center px-4 py-2 rounded-md font-bold text-sm shadow-sm transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>Export Excel
            </a>
            @if($room && $room->is_active)
                @if(isset($room->status) && $room->status === 'waiting')
                    <form action="{{ route('teacher.startExam') }}" method="POST" class="ml-4">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md font-bold text-sm transition shadow-sm flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Mulai Ujian
                        </button>
                    </form>
                @else
                    <form action="{{ route('teacher.endExam') }}" method="POST" class="ml-4" onsubmit="return confirm('Yakin ingin mengakhiri ujian ini? Mahasiswa akan langsung ter-submit paksa.');">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md font-bold text-sm transition shadow-sm">Akhiri Ujian</button>
                    </form>
                @endif
            @endif
        </div>
    </header>

    <main class="flex-grow p-6">
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-4 font-bold border border-green-200">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="p-4 font-semibold">Nama Mahasiswa</th>
                        <th class="p-4 font-semibold text-center border-l border-gray-200">Skor (%)</th>
                        @foreach($questions as $index => $q)
                            <th class="p-4 font-semibold text-center border-l border-gray-200">{{ $index + 1 }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $res)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="p-4 font-medium text-gray-800">
                            {{ $res['name'] }}
                            @if(isset($res['cheat_attempts']) && $res['cheat_attempts'] > 0)
                                <span class="ml-2 text-xs font-bold bg-red-100 text-red-600 px-2 py-1 rounded-full border border-red-200" title="Siswa mencoba keluar dari tab ujian">
                                    ⚠️ Curang
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-center font-bold text-gray-800 border-l border-gray-100">{{ $res['score'] }}%</td>
                        @foreach($questions as $q)
                            @if($res['answers_status'][$q->id] == 'correct')
                                <td class="p-4 border-l border-gray-100 bg-[#1eb182] text-white text-center">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </td>
                            @elseif($res['answers_status'][$q->id] == 'wrong')
                                <td class="p-4 border-l border-gray-100 bg-[#ef4444] text-white text-center">
                                    <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </td>
                            @else
                                <td class="p-4 border-l border-gray-100 text-center">
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 mx-auto"></div>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Halaman otomatis merefresh setiap 5 detik agar nilai live muncul
        setTimeout(function() { window.location.reload(); }, 5000);
    </script>
</body>
</html>
