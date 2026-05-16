<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KUISIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden">

    <!-- Sidebar Admin -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col justify-between hidden md:flex">
        <div>
            <!-- Logo -->
            <div class="p-6 flex items-center space-x-3 border-b border-gray-800">
                <div class="bg-white text-gray-900 p-1.5 rounded-lg shadow-sm">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3z"/></svg>
                </div>
                <span class="font-bold text-xl tracking-wide">KUISIN Admin</span>
            </div>

            <!-- Menu -->
            <nav class="p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl font-semibold transition">
                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.students') }}" class="flex items-center px-4 py-3 bg-gray-800 text-white rounded-xl font-semibold transition">
                    <svg class="w-5 h-5 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Data Mahasiswa
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-gray-800">
            <a href="{{ route('profile.edit') }}" class="flex items-center justify-center w-full px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-800 rounded-xl font-bold transition mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Edit Profil
            </a>
            <a href="{{ route('admin.logout') }}" onclick="return confirm('Keluar dari sistem Admin?')" class="flex items-center justify-center w-full px-4 py-3 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white rounded-xl font-bold transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 h-20 flex items-center justify-between px-8 shadow-sm z-10 relative">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Mahasiswa</h1>
                <p class="text-sm text-gray-500">Kelola data peserta CBT</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800">{{ session('admin_name') }}</p>
                    <p class="text-xs text-indigo-500 font-semibold">Administrator</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-lg shadow-inner">
                    {{ substr(session('admin_name', 'A'), 0, 1) }}
                </div>
            </div>
        </header>

        <!-- Scrollable Area -->
        <div class="flex-1 overflow-y-auto p-8 bg-gray-50">
            
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-semibold text-sm">{{ session('success') }}</span>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-fit">
                    <h2 class="font-bold text-gray-800 mb-4 text-lg">Impor Peserta Massal</h2>
                    <p class="text-xs text-gray-500 mb-4">Unggah file CSV dengan format kolom: <br><b class="text-gray-700">NIM, Nama Lengkap, Password</b></p>

                    <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center mb-4 bg-gray-50 hover:bg-gray-100 transition">
                            <input type="file" name="file_csv" accept=".csv" required class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        </div>
                        <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-gray-900/20 active:scale-95">
                            Mulai Impor Data
                        </button>
                    </form>
                </div>

                <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h2 class="font-bold text-lg text-gray-800">Daftar Akun Peserta</h2>
                        <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full">{{ count($students) }} Total</span>
                    </div>

                    <div class="overflow-x-auto max-h-[500px]">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50/50 text-gray-500 text-xs uppercase font-semibold border-b border-gray-100 sticky top-0">
                                <tr>
                                    <th class="px-6 py-4">NIM</th>
                                    <th class="px-6 py-4">Nama Peserta</th>
                                    <th class="px-6 py-4">Password Saat Ini</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($students as $s)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-700">{{ $s->nim }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $s->name }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $s->password }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('admin.students.reset', $s->id) }}" method="POST" onsubmit="return confirm('Reset password {{ $s->name }} menjadi 12345?');">
                                            @csrf
                                            <button type="submit" class="text-orange-500 hover:text-white hover:bg-orange-500 border border-orange-200 hover:border-orange-500 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                Reset Password
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada data peserta. Silakan impor CSV.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
