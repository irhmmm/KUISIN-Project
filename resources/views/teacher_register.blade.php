<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Register Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 h-screen flex justify-center items-center p-4">

    <div class="w-full max-w-md bg-white p-8 rounde     d-2xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Buat Akun Dosen</h2>
        <p class="text-gray-500 mb-6 text-sm">Mulai kelola kuis interaktif Anda sendiri secara gratis.</p>

        <!-- Tampilkan Error Validasi (jika email kembar/sandi kurang panjang) -->
        @if($errors->any())
            <div class="bg-red-50 text-red-600 text-sm font-medium px-4 py-3 rounded-lg border border-red-100 mb-4">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('teacher.register.submit') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Dr. Andi" value="{{ old('name') }}"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#4a728a]">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Email</label>
                <input type="email" name="email" required placeholder="dosen@kampus.ac.id" value="{{ old('email') }}"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#4a728a]">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required placeholder="Minimal 5 karakter"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#4a728a]">
            </div>

            <button type="submit" class="w-full bg-[#4a728a] hover:bg-[#38596e] text-white font-bold py-3.5 rounded-lg transition shadow-md mt-2">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            Sudah punya akun? <a href="{{ route('teacher.login') }}" class="text-[#4a728a] font-bold hover:underline">Masuk di sini</a>
        </div>
    </div>

</body>
</html>
