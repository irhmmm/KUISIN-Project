<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KUISIN - Edit Soal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen p-8 flex justify-center items-center">

    <div class="w-full max-w-2xl bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
        <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
            <h3 class="font-bold text-gray-800 text-xl flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Mode
            </h3>
            <a href="{{ route('teacher.library') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-800 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Batal
            </a>
        </div>

        <form action="{{ route('teacher.updateQuestion', $question->id) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Teks Pertanyaan</label>
                <textarea name="question_text" rows="3" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4a728a]">{{ $question->question_text }}</textarea>
            </div>

            <div class="space-y-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 uppercase">Pilihan & Kunci Jawaban</label>

                @php $labels = ['A', 'B', 'C', 'D']; @endphp
                @foreach($options as $index => $opt)
                <div class="flex items-center space-x-4 bg-white p-2 rounded-lg shadow-sm border border-gray-100">
                    <input type="radio" name="correct_answer" value="{{ $labels[$index] }}" {{ $opt->is_correct ? 'checked' : '' }} required class="w-5 h-5 text-[#4a728a] focus:ring-[#4a728a]">
                    <span class="font-bold text-gray-800 text-lg">{{ $labels[$index] }}.</span>
                    <input type="text" name="option_{{ strtolower($labels[$index]) }}" value="{{ $opt->option_text }}" required class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4a728a]">
                </div>
                @endforeach
            </div>

            <button type="submit" class="w-full bg-[#4a728a] hover:bg-[#38596e] text-white font-bold py-3.5 rounded-lg transition shadow-md">
                Simpan Perubahan
            </button>
        </form>
    </div>

</body>
</html>
