<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Platform Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>
<body class="bg-gray-50 h-screen flex">

    <!-- Sisi Kiri (Gambar & Branding) -->
    <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-[#4a728a] to-[#2a4d63] flex-col justify-center items-center text-white p-12 relative overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl"></div>

        <div class="bg-white/10 p-4 rounded-2xl mb-8 backdrop-blur-md border border-white/20 shadow-xl">
            <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3z"/></svg>
        </div>
        <h1 class="text-4xl font-extrabold mb-4 tracking-tight">KUISIN Portal</h1>
        <p class="text-center text-blue-100/80 text-lg max-w-md font-medium leading-relaxed">
            Satu portal terpadu untuk Dosen dan Administrator. Kelola kelas, buat kuis interaktif, dan pantau perkembangan mahasiswa secara real-time.
        </p>
    </div>

    <!-- Sisi Kanan (Form Login) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8 sm:p-12">
        <div class="w-full max-w-md">
            <!-- Mobile Logo (only visible on mobile) -->
            <div class="flex lg:hidden justify-center mb-8">
                <div class="bg-[#4a728a] p-3 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3z"/></svg>
                </div>
            </div>

            <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang</h2>
            <p class="text-gray-500 mb-8 font-medium">Silakan masuk ke akun Dosen atau Admin Anda.</p>

            @if(session('success'))
                <div class="bg-green-50 text-green-700 text-sm font-bold px-4 py-3 rounded-xl border border-green-200 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- FORM LOGIN STANDAR -->
            <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email Akun</label>
                    <input type="email" name="email" required placeholder="email@kuisin.com"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#4a728a]/30 focus:border-[#4a728a] transition font-medium">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#4a728a]/30 focus:border-[#4a728a] transition font-medium">
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" class="w-4 h-4 text-[#4a728a] bg-gray-100 border-gray-300 rounded focus:ring-[#4a728a]">
                        <label for="remember" class="ml-2 text-sm font-semibold text-gray-600">Ingat Saya</label>
                    </div>
                    <!-- Link Lupa Sandi -->
                    <a href="{{ route('teacher.forgot') }}" class="text-sm text-[#4a728a] font-bold hover:text-blue-800 transition">Lupa password?</a>
                </div>

                @if(session('error'))
                    <div class="bg-red-50 text-red-600 text-sm font-bold px-4 py-3 rounded-xl border border-red-100 mt-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex gap-4 mt-6">
                    <button type="submit" class="flex-1 bg-gray-900 hover:bg-gray-800 text-white font-bold py-3.5 rounded-xl transition-all active:scale-[0.98] shadow-lg shadow-gray-900/20">
                        Masuk ke dashboard
                    </button>
                    <a href="{{ route('student.login') }}" class="flex-1 flex justify-center items-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold py-3.5 rounded-xl shadow-sm transition-all active:scale-[0.98]">
                        Masuk sebagai Siswa
                    </a>
                </div>
            </form>

            <div class="flex items-center my-6">
                <div class="flex-1 border-t border-gray-100"></div>
                <span class="px-4 text-xs text-gray-400 font-bold uppercase tracking-wider">Metode Lainnya (Dosen)</span>
                <div class="flex-1 border-t border-gray-100"></div>
            </div>

            <!-- TOMBOL LOGIN GOOGLE -->
            <a href="{{ route('teacher.google') }}" class="w-full bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-3.5 rounded-xl flex items-center justify-center transition-all shadow-sm active:scale-[0.98]">
                <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Sign in with Google
            </a>

            <!-- Link Buat Akun Baru -->
            <div class="mt-8 text-center text-sm font-medium text-gray-500">
                Belum punya akun Dosen?
                <a href="{{ route('teacher.register') }}" class="text-[#4a728a] font-bold hover:underline">Buat Akun Baru</a>
            </div>
        </div>
    </div>
</body>
</html>
