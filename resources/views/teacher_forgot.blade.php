<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Lupa Sandi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 h-screen flex justify-center items-center p-4">

    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Lupa Sandi?</h2>
        <p class="text-gray-500 mb-6 text-sm">Masukkan email yang terdaftar. Sistem akan me-reset sandi Anda ke sandi bawaan secara otomatis.</p>

        <form action="{{ route('teacher.forgot.submit') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                <input type="email" name="email" required placeholder="dosen@kuisin.com"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#4a728a] transition">
            </div>

            @if(session('error'))
                <div class="text-red-500 text-sm font-medium">{{ session('error') }}</div>
            @endif

            <button type="submit" class="w-full bg-[#4a728a] hover:bg-[#38596e] text-white font-bold py-3.5 rounded-lg transition shadow-md">
                Reset Sandi Saya
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            Kembali ke <a href="{{ route('teacher.login') }}" class="text-[#4a728a] font-bold hover:underline">Halaman Login</a>
        </div>
    </div>

</body>
</html>
