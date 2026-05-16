<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Student Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-[#124b6d] to-[#3a82a0] h-screen flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-2xl p-10 w-[450px] text-center">
        <div class="bg-[#eef2f6] text-[#4a728a] w-16 h-16 rounded-2xl mx-auto flex items-center justify-center mb-6">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72l5 2.73 5-2.73v3.72z"/>
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-800 mb-2">Student Login</h2>
        <p class="text-gray-500 text-sm mb-8">Masukkan ID Room yang diberikan<br>oleh dosen Anda.</p>
        <form action="{{ route('student.join') }}" method="POST" class="text-left">
            @csrf

            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Room Name</label>
            <input type="text" name="room_name" placeholder="CONTOH: QWERTY" required
                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#4a728a] mb-4 bg-[#f8fafc] text-gray-700 font-medium">
            @if(session('error'))
                <div class="text-red-500 text-sm mb-4">{{ session('error') }}</div>
            @endif

            <button type="submit"
                class="w-full bg-[#4a728a] hover:bg-[#38596e] text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md">
                Join Room
            </button>
        </form>
    </div>

</body>
</html>
