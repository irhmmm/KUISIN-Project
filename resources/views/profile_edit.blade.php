<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Edit Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col overflow-hidden">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-8 shrink-0 shadow-sm z-10">
        <div class="flex items-center gap-3">
            @if($user->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
            @else
                <a href="{{ route('teacher.dashboard') }}" class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
            @endif
            <h1 class="text-xl font-bold text-gray-800">Edit Profil</h1>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-1 overflow-y-auto p-8 flex justify-center items-start pt-12">
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-8 border-b border-gray-100 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-50 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-purple-50 rounded-full blur-2xl"></div>
                
                <div class="w-20 h-20 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rounded-full mx-auto flex items-center justify-center text-2xl font-bold uppercase shadow-lg shadow-blue-500/30 relative z-10">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-800 relative z-10">{{ $user->name }}</h2>
                <p class="text-sm font-medium text-gray-500 relative z-10 capitalize">{{ $user->role }} Account</p>
            </div>

            <div class="p-8">
                @if(session('success'))
                    <div class="bg-green-50 text-green-700 text-sm font-bold px-4 py-3 rounded-xl border border-green-200 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 text-sm font-medium transition">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email (Tidak bisa diubah)</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm font-medium text-gray-500 cursor-not-allowed">
                    </div>

                    <div class="pt-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Ubah Password <span class="text-xs font-normal text-gray-400">(Biarkan kosong jika tidak ingin mengubah)</span></label>
                        <input type="password" name="password" placeholder="Password baru"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 text-sm font-medium transition">
                    </div>

                    <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-gray-900/20 active:scale-95 mt-4">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </main>

</body>
</html>
