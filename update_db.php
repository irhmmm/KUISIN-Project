<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Memperbarui tabel rooms...\n";
if (!Schema::hasColumn('rooms', 'mode')) {
    Schema::table('rooms', function (Blueprint $table) {
        $table->string('mode')->default('standard');
        $table->integer('num_teams')->nullable();
        $table->string('status')->default('waiting');
        $table->timestamp('started_at')->nullable();
    });
    echo "Kolom pada tabel rooms berhasil ditambahkan!\n";
} else {
    echo "Kolom pada tabel rooms sudah ada.\n";
}

echo "Memperbarui tabel student_sessions...\n";
if (!Schema::hasColumn('student_sessions', 'team_name')) {
    Schema::table('student_sessions', function (Blueprint $table) {
        $table->string('team_name')->nullable();
    });
    echo "Kolom team_name berhasil ditambahkan!\n";
} else {
    echo "Kolom team_name sudah ada.\n";
}

echo "Sukses!";
