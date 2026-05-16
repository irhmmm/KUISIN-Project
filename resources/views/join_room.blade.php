<?php
// join_room.php
session_start();

// Konfigurasi Database PDO Laragon
$host = 'localhost';
$dbname = 'kuisin_db';
$username = 'root';
$password = ''; // Default Laragon biasanya kosong

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_name = $_POST['room_name'];

    // Cek apakah room ada dan sedang aktif
    $stmt = $pdo->prepare("SELECT id FROM rooms WHERE room_name = ? AND is_active = 1");
    $stmt->execute([$room_name]);
    $room = $stmt->fetch();

    if ($room) {
        // Jika room valid, simpan banyak data ke session sekaligus
        session([
            'room_id' => $room->id,
            'room_name' => $room_name,
            'room_teacher_id' => $room->teacher_id
        ]);
        return redirect('enter_name.php');
    } else {
        return back()->with('error', 'Room tidak ditemukan atau belum diaktifkan oleh Dosen.');
    }
}
?>
