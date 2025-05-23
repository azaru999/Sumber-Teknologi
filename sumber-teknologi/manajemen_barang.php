<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

// Handle tambah barang
if (isset($_POST['tambah'])) {
    $nama_barang = trim($_POST['nama_barang']);
    $kode_barang = trim($_POST['kode_barang']);
    $kategori = trim($_POST['kategori']);
    $lokasi = trim($_POST['lokasi']);
    $jumlah = intval($_POST['jumlah']);
    $harga = floatval(str_replace(',', '', $_POST['harga']));

    if ($nama_barang === '' || $kode_barang === '' || $jumlah < 0 || !is_numeric($harga)) {
        $error = "Nama, kode, jumlah (>=0), dan harga barang harus diisi dengan benar.";
    } else {
        $stmt = $conn->prepare("INSERT INTO barang (nama_barang, kode_barang, kategori, lokasi, jumlah, harga) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdi", $nama_barang, $kode_barang, $kategori, $lokasi, $jumlah, $harga);
        if ($stmt->execute()) {
            $success = "Barang berhasil ditambahkan.";
        } else {
            $error = "Gagal menambahkan barang: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle edit barang
if (isset($_POST['edit'])) {
    $id_barang = intval($_POST['id_barang']);
    $nama_barang = trim($_POST['nama_barang']);
    $kode_barang = trim($_POST['kode_barang']);
    $kategori = trim($_POST['kategori']);
    $lokasi = trim($_POST['lokasi']);
    $jumlah = intval($_POST['jumlah']);
    $harga = floatval(str_replace(',', '', $_POST['harga']));

    if ($nama_barang === '' || $kode_barang === '' || $jumlah < 0 || !is_numeric($harga)) {
        $error = "Nama, kode, jumlah (>=0), dan harga barang harus diisi dengan benar.";
    } else {
        $stmt = $conn->prepare("UPDATE barang SET nama_barang=?, kode_barang=?, kategori=?, lokasi=?, jumlah=?, harga=? WHERE id_barang=?");
        $stmt->bind_param("ssssdis", $nama_barang, $kode_barang, $kategori, $lokasi, $jumlah, $harga, $id_barang);
        if ($stmt->execute()) {
            $success = "Barang berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui barang: " . $conn->error;
        }
        $stmt->close();
    }
}

// Ambil data untuk edit jika ada id_barang di query string
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id_barang = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang=?");
    $stmt->bind_param("i", $id_barang);
    $stmt->execute();
    $result_edit = $stmt->get_result();
    $edit_data = $result_edit->fetch_assoc();
    $stmt->close();
}

// Ambil semua data barang
$result = $conn->query("SELECT * FROM barang ORDER BY id_barang DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manajemen Barang</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0; padding: 2rem;
      font-family: 'Poppins', sans-serif;
      background: #f0f5ff;
      color: #34495e;
      min-height: 100vh;
    }
    .container {
      max-width: 1100px;
      margin: auto;
      background: #fff;
      padding: 2rem;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(75,46,163,0.1);
      user-select: none;
    }
    h2 {
      color: #4b2ea3;
      font-weight: 600;
      margin-bottom: 2rem;
      text-align: center;
      user-select: none;
    }
    form {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1rem 2rem;
      margin-bottom: 2rem;
    }
    label {
      display: flex;
      flex-direction: column;
      font-weight: 600;
      color: #4b2ea3;
      user-select: none;
    }
    input[type="text"],
    input[type="number"] {
      padding: 0.6rem;
      margin-top: 0.3rem;
      font-size: 1rem;
      border: 1px solid #dcdde1;
      border-radius: 10px;
      transition: border-color 0.3s ease;
    }
    input:focus {
      outline: none;
      border-color: #4b2ea3;
      box-shadow: 0 0 5px #4b2ea3;
    }
    button {
      grid-column: span 2;
      padding: 0.8rem 1.2rem;
      background-color: #4b2ea3;
      color: white;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      user-select: none;
    }
    button:hover {
      background-color: #3a1f7d;
    }
    a.cancel-btn {
      grid-column: span 2;
      display: block;
      text-align: center;
      padding: 0.8rem 1.2rem;
      background: #ccc;
      color: #333;
      border-radius: 10px;
      font-weight: 600;
      text-decoration: none;
      transition: background 0.3s ease;
      user-select: none;
    }
    a.cancel-btn:hover {
      background: #999;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      box-shadow: 0 8px 30px rgba(75,46,163,0.1);
      border-radius: 12px;
      overflow: hidden;
    }
    thead {
      background-color: #4b2ea3;
      color: white;
      font-weight: 600;
      user-select: none;
    }
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    tbody tr:nth-child(even) {
      background-color: #f0f5ff;
    }
    tbody tr:hover {
      background-color: #d3d0e5;
      cursor: default;
    }
    .edit-link {
      color: #4b2ea3;
      font-weight: 600;
      text-decoration: none;
      user-select: none;
    }
    .edit-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Manajemen Barang</h2>

    <?php if ($error): ?>
      <div style="background:#ffe6e6; color:#e74c3c; border:1px solid #e74c3c; padding:1rem; border-radius:8px; margin-bottom:1.5rem; font-weight:600; user-select:none;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php elseif ($success): ?>
      <div style="background:#e0f9f2; color:#2ecc71; border:1px solid #2ecc71; padding:1rem; border-radius:8px; margin-bottom:1.5rem; font-weight:600; user-select:none;">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="hidden" name="id_barang" value="<?= $edit_data ? (int)$edit_data['id_barang'] : '' ?>">

      <label>Nama Barang
        <input type="text" name="nama_barang" required value="<?= $edit_data ? htmlspecialchars($edit_data['nama_barang']) : '' ?>">
      </label>

      <label>Kode Barang
        <input type="text" name="kode_barang" required value="<?= $edit_data ? htmlspecialchars($edit_data['kode_barang']) : '' ?>">
      </label>

      <label>Kategori
        <input type="text" name="kategori" value="<?= $edit_data ? htmlspecialchars($edit_data['kategori']) : '' ?>">
      </label>

      <label>Lokasi
        <input type="text" name="lokasi" value="<?= $edit_data ? htmlspecialchars($edit_data['lokasi']) : '' ?>">
      </label>

      <label>Jumlah
        <input type="number" name="jumlah" min="0" required value="<?= $edit_data ? (int)$edit_data['jumlah'] : '0' ?>">
      </label>

      <label>Harga
        <input type="text" name="harga" required value="<?= $edit_data ? htmlspecialchars($edit_data['harga']) : '' ?>">
      </label>

      <?php if ($edit_data): ?>
        <button type="submit" name="edit">Simpan Perubahan</button>
        <a href="manajemen_barang.php" class="cancel-btn">Batal</a>
      <?php else: ?>
        <button type="submit" name="tambah">Tambah Barang</button>
      <?php endif; ?>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Barang</th>
          <th>Kode</th>
          <th>Kategori</th>
          <th>Lokasi</th>
          <th>Jumlah</th>
          <th>Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$row['id_barang'] ?></td>
              <td><?= htmlspecialchars($row['nama_barang']) ?></td>
              <td><?= htmlspecialchars($row['kode_barang']) ?></td>
              <td><?= htmlspecialchars($row['kategori']) ?></td>
              <td><?= htmlspecialchars($row['lokasi']) ?></td>
              <td><?= (int)$row['jumlah'] ?></td>
              <td>Rp <?= number_format((float)$row['harga'], 0, ',', '.') ?></td>
              <td><a class="edit-link" href="?edit_id=<?= (int)$row['id_barang'] ?>">Edit</a></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" style="text-align:center;">Belum ada data barang.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
