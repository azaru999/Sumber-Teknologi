<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header('Location: login.php');
    exit();
}

$result = $conn->query("SELECT id_barang, nama_barang, kode_barang, kategori, lokasi, jumlah, harga FROM barang ORDER BY nama_barang ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Laporan Inventaris</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f0f2f5, #eaf0f8);
      color: #34495e;
    }

    header {
      display: flex;
      justify-content: center; /* ubah jadi center karena link logout dihapus */
      align-items: center;
      padding: 1rem 2rem;
      background: rgba(255,255,255,0.95);
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    header img.logo {
      height: 50px;
    }

    .container {
      max-width: 1100px;
      margin: 2rem auto;
      background: #fff;
      padding: 2.5rem 3rem;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }

    h2 {
      color: #4b2ea3;
      font-weight: 700;
      text-align: center;
      margin-bottom: 2rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
      font-size: 1rem;
    }

    table thead {
      background: #4b2ea3;
      color: #fff;
    }

    th, td {
      padding: 0.9rem 1rem;
      border: 1px solid #ddd;
      text-align: left;
    }

    tbody tr:nth-child(even) {
      background-color: #f9f9fc;
    }

    tbody tr:hover {
      background-color: #f1f4ff;
    }

    @media screen and (max-width: 768px) {
      .container {
        padding: 1.5rem;
      }

      table {
        font-size: 0.9rem;
      }

      th, td {
        padding: 0.6rem;
      }
    }
  </style>
</head>
<body>


<div class="container">
  <h2>Laporan Barang</h2>

  <?php if ($result->num_rows > 0): ?>
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
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$row['id_barang'] ?></td>
            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
            <td><?= htmlspecialchars($row['kode_barang']) ?></td>
            <td><?= htmlspecialchars($row['kategori']) ?></td>
            <td><?= htmlspecialchars($row['lokasi']) ?></td>
            <td><?= (int)$row['jumlah'] ?></td>
            <td><?= 'Rp ' . number_format($row['harga'], 0, ',', '.') ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="text-align:center; margin-top:2rem;">Belum ada data barang untuk ditampilkan.</p>
  <?php endif; ?>
</div>

</body>
</html>
