<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru - Input Soal CBT</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f6f9; }
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], textarea, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #3498db; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; }
        button:hover { background: #2980b9; }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Form Input Soal Baru (Guru)</h2>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('guru.simpan_soal') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label>Pilih Mata Pelajaran:</label>
            <select name="subject_id" required>
                <option value="">-- Pilih Mapel --</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Teks Pertanyaan Soal:</label>
            <textarea name="question_text" rows="4" placeholder="Tulis soal di sini..." required></textarea>
        </div>

        <div class="form-group"><label>Pilihan A:</label><input type="text" name="option_a" required></div>
        <div class="form-group"><label>Pilihan B:</label><input type="text" name="option_b" required></div>
        <div class="form-group"><label>Pilihan C:</label><input type="text" name="option_c" required></div>
        <div class="form-group"><label>Pilihan D:</label><input type="text" name="option_d" required></div>

        <div class="form-group">
            <label>Kunci Jawaban Benar:</label>
            <select name="correct_answer" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>

        <button type="submit">Simpan ke Bank Soal</button>
    </form>
</div>

</body>
</html>
