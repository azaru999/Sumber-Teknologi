<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Ambil daftar barang untuk dropdown
$barang_result = $conn->query("SELECT id_barang, nama_barang, jumlah, harga FROM barang ORDER BY nama_barang ASC");

// Handle transaksi
if (isset($_POST['submit'])) {
    $id_barang = intval($_POST['id_barang']);
    $tipe = $_POST['tipe'];
    $jumlah = intval($_POST['jumlah']);
    $keterangan = trim($_POST['keterangan']);

    if ($id_barang <= 0 || $jumlah <= 0 || !in_array($tipe, ['masuk', 'keluar'])) {
        $error = "Data transaksi tidak valid.";
    } else {
        $stmt = $conn->prepare("SELECT jumlah FROM barang WHERE id_barang = ?");
        $stmt->bind_param("i", $id_barang);
        $stmt->execute();
        $res = $stmt->get_result();
        $barang = $res->fetch_assoc();
        $stmt->close();

        if (!$barang) {
            $error = "Barang tidak ditemukan.";
        } elseif ($tipe == 'keluar' && $barang['jumlah'] < $jumlah) {
            $error = "Stok barang tidak cukup untuk transaksi keluar.";
        } else {
            if ($tipe == 'masuk') {
                $stmt = $conn->prepare("UPDATE barang SET jumlah = jumlah + ? WHERE id_barang = ?");
            } else {
                $stmt = $conn->prepare("UPDATE barang SET jumlah = jumlah - ? WHERE id_barang = ?");
            }
            $stmt->bind_param("ii", $jumlah, $id_barang);

            if ($stmt->execute()) {
                $stmt->close();

                $stmt = $conn->prepare("INSERT INTO transaksi (id_barang, tipe, jumlah, keterangan, tanggal) VALUES (?, ?, ?, ?, NOW())");
                $stmt->bind_param("isis", $id_barang, $tipe, $jumlah, $keterangan);
                $stmt->execute();
                $stmt->close();

                $success = "Transaksi berhasil diproses.";
            } else {
                $error = "Gagal memperbarui stok barang: " . $conn->error;
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Transaksi Barang</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body, html {
      margin: 0; padding: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
      color: #34495e;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    header {
      display: flex;
      justify-content: center; /* center logo horizontally */
      align-items: center;
      padding: 1rem 2rem;
      background: rgba(255,255,255,0.9);
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    header img.logo {
      height: 50px;
    }

    .container {
      background: rgba(255,255,255,0.95);
      max-width: 700px;
      margin: 2rem auto;
      padding: 2.5rem 3rem;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(75,46,163,0.1);
      flex-grow: 1;
    }

    h2 {
      color: #4b2ea3;
      margin-bottom: 2rem;
      font-weight: 700;
      text-align: center;
    }

    form.form-grid {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 1rem 2rem;
      align-items: center;
    }

    .form-group {
      display: contents;
    }

    label {
      font-weight: 600;
      user-select: none;
    }

    select, input[type="number"], textarea {
      padding: 0.7rem 1rem;
      border-radius: 12px;
      border: 1.8px solid #b2bec3;
      font-size: 1.1rem;
      font-family: 'Poppins', sans-serif;
      transition: border-color 0.3s ease;
      width: 100%;
    }
    select:focus, input[type="number"]:focus, textarea:focus {
      outline: none;
      border-color: #6dd5fa;
      box-shadow: 0 0 8px rgba(109, 213, 250, 0.6);
    }
    textarea {
      resize: vertical;
      min-height: 60px;
    }

    .full {
      grid-column: 1 / -1;
    }

    .form-actions {
      grid-column: 1 / -1;
      text-align: center;
      margin-top: 1.5rem;
    }

    button {
      padding: 0.9rem 2rem;
      background: #4b2ea3;
      color: #fff;
      border: none;
      border-radius: 14px;
      font-weight: 700;
      font-size: 1.15rem;
      cursor: pointer;
      box-shadow: 0 6px 20px rgba(75,46,163,0.25);
      transition: background 0.3s ease;
    }
    button:hover {
      background: #3a2173;
    }

    .message {
      padding: 0.8rem 1.2rem;
      border-radius: 14px;
      margin-bottom: 1.5rem;
      font-weight: 600;
      font-size: 1rem;
    }
    .error {
      background: #fdecea;
      color: #e74c3c;
      border: 1.5px solid #e74c3c;
    }
    .success {
      background: #e8f8f5;
      color: #16a085;
      border: 1.5px solid #16a085;
    }

    @media (max-width: 640px) {
      form.form-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Transaksi Barang</h2>

  <?php if ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" action="" class="form-grid">
    <div class="form-group">
      <label for="id_barang">Pilih Barang:</label>
      <select name="id_barang" id="id_barang" required>
        <option value="">-- Pilih Barang --</option>
        <?php if ($barang_result): ?>
          <?php while ($row = $barang_result->fetch_assoc()): ?>
            <option value="<?= (int)$row['id_barang'] ?>">
              <?= htmlspecialchars($row['nama_barang']) ?> (Stok: <?= (int)$row['jumlah'] ?>, Harga: Rp <?= number_format($row['harga'], 0, ',', '.') ?>)
            </option>
          <?php endwhile; ?>
        <?php endif; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="tipe">Tipe Transaksi:</label>
      <select name="tipe" id="tipe" required>
        <option value="masuk">Masuk (Tambah Stok)</option>
        <option value="keluar">Keluar (Kurangi Stok)</option>
      </select>
    </div>

    <div class="form-group">
      <label for="jumlah">Jumlah:</label>
      <input type="number" id="jumlah" name="jumlah" min="1" required />
    </div>

    <div class="form-group full">
      <label for="keterangan">Keterangan:</label>
      <textarea id="keterangan" name="keterangan" rows="2"></textarea>
    </div>

    <div class="form-actions">
      <button type="submit" name="submit">Proses Transaksi</button>
    </div>
  </form>
</div>

</body>
</html>
