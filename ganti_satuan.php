<?php
// Daftar file yang ingin diubah
$files = ['laporan.php', 'manajemen_barang.php', 'transaksi_barang.php'];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $updated = str_ireplace('satuan', 'harga', $content); // Ganti semua "satuan" (case-insensitive)
        file_put_contents($file, $updated);
        echo "File $file telah diperbarui.\n";
    } else {
        echo "File $file tidak ditemukan.\n";
    }
}
?>
