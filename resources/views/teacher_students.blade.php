<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Manajemen Peserta CBT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col">

    <header class="bg-[#2a6282] text-white px-6 py-4 flex justify-between items-center shadow-md">
        <div class="flex items-center">
            <a href="{{ route('teacher.dashboard') }}" class="mr-4 hover:bg-[#1f4a63] p-2 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-xl font-bold">Manajemen Peserta CBT</h1>
        </div>
    </header>

    <main class="p-8 max-w-6xl mx-auto w-full">
        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl font-bold mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 h-fit">
                <h2 class="font-bold text-gray-800 mb-4 text-lg">Impor Peserta Massal</h2>
                <p class="text-xs text-gray-500 mb-4">Unggah file CSV dengan format kolom: <br><b class="text-gray-700">NIM, Nama Lengkap, Password</b></p>

                <form action="{{ route('teacher.students.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center mb-4">
                        <input type="file" name="file_csv" accept=".csv" required class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <button type="submit" class="w-full bg-[#4a728a] hover:bg-[#38596e] text-white font-bold py-2.5 rounded-lg transition shadow-sm">
                        Mulai Impor Data
                    </button>
                </form>
            </div>

            <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Daftar Akun Peserta</h2>
                    <span class="bg-blue-100 text-[#4a728a] text-xs font-bold px-3 py-1 rounded-full">{{ count($students) }} Total</span>
                </div>

                <div class="overflow-x-auto max-h-[500px]">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-white sticky top-0 border-b border-gray-100">
                            <tr class="text-xs text-gray-500 uppercase tracking-wider">
                                <th class="p-4 font-semibold">NIM</th>
                                <th class="p-4 font-semibold">Nama Peserta</th>
                                <th class="p-4 font-semibold">Password Saat Ini</th>
                                <th class="p-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($students as $s)
                            <tr class="border-b border-gray-50 hover:bg-gray-50">
                                <td class="p-4 font-bold text-gray-700">{{ $s->nim }}</td>
                                <td class="p-4 font-medium text-gray-800">{{ $s->name }}</td>
                                <td class="p-4 font-mono text-xs text-gray-500">{{ $s->password }}</td>
                                <td class="p-4 text-center">
                                    <form action="{{ route('teacher.students.reset', $s->id) }}" method="POST" onsubmit="return confirm('Reset password {{ $s->name }} menjadi 12345?');">
                                        @csrf
                                        <button type="submit" class="bg-orange-100 text-orange-600 hover:bg-orange-200 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                            Reset Password
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-400 text-sm">Belum ada data peserta. Silakan impor CSV.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
