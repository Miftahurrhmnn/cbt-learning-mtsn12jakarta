<?php
$f = fopen(__DIR__ . '/data_siswa_600.csv', 'w');
fputcsv($f, ['Nama Lengkap', 'NISN', 'Kelas', 'Email', 'Password']);
$classes = ['Kelas X-A', 'Kelas X-B', 'Kelas XI-A'];

for ($i = 1; $i <= 600; $i++) {
    $num = str_pad($i, 4, '0', STR_PAD_LEFT);
    $name = 'Siswa Test ' . $num;
    $nisn = '8800' . str_pad($i, 6, '0', STR_PAD_LEFT);
    $cls = $classes[($i - 1) % count($classes)];
    $email = 'siswa.test' . $num . '@cbt-simpel.test';
    $password = 'password123';
    fputcsv($f, [$name, $nisn, $cls, $email, $password]);
}
fclose($f);
echo "Berhasil membuat data_siswa_600.csv dengan 600 data siswa.\n";
