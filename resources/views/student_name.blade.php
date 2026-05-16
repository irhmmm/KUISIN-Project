<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Masukkan Nama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gradient-to-br from-[#124b6d] to-[#3a82a0] h-screen flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-2xl p-10 w-[450px] text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Join Room: {{ session('room_name') }}</h2>
        <p class="text-gray-500 text-sm mb-8">Silakan masukkan nama lengkap Anda<br>untuk memulai kuis.</p>

        <form action="{{ route('student.submitName') }}" method="POST" class="text-left">
            @csrf

            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Mahasiswa</label>
            <input type="text" name="student_name" placeholder="Misal: Budi Pratama" required
                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4a728a] mb-6 bg-[#f8fafc] text-gray-700 font-medium">

            <button type="submit"
                class="w-full bg-[#4a728a] hover:bg-[#38596e] text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md">
                Masuk ke Kelas
            </button>
        </form>
    </div>

</body>
</html>
