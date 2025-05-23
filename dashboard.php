<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$role = strtolower($_SESSION['role'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Inventaris</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body, html {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
    }
    .dashboard {
      display: flex;
      height: 100vh;
    }
    .sidebar {
      width: 240px;
      background-color: #4b2ea3;
      color: #fff;
      padding: 1.5rem 1rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
      align-items: center;
    }
    .sidebar .logo {
      width: 100px; /* perbesar logo sidebar */
      height: auto;
      margin-bottom: 1rem;
      filter: drop-shadow(0 0 6px rgba(0,0,0,0.3));
    }
    .sidebar h2 {
      font-size: 1rem;
      text-align: center;
      margin-bottom: 1.2rem;
    }
    .sidebar .menu-title {
      font-size: 0.95rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
      width: 100%;
      text-align: left;
      padding-left: 10px;
      color: #dcd9f7;
      border-bottom: 1px solid rgba(255,255,255,0.2);
      padding-bottom: 5px;
    }
    .sidebar a.menu-link {
      color: #ffffff;
      text-decoration: none;
      font-weight: 600;
      padding: 0.6rem 1rem;
      border-radius: 8px;
      transition: background 0.3s ease;
      width: 100%;
      text-align: left;
      display: block;
    }
    .sidebar a.menu-link:hover {
      background-color: #5d3edc;
    }
    .content {
      flex-grow: 1;
      background-color: #f8f9fa;
      overflow: hidden;
    }
    iframe {
      border: none;
      width: 100%;
      height: 100%;
    }
    .logout {
      margin-top: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0.75rem 1rem;
      background: #e74c3c;
      color: #fff;
      border-radius: 10px;
      text-decoration: none;
      font-weight: bold;
      width: 100%;
      transition: background 0.3s ease;
      text-align: center;
      min-height: 45px;
    }
    .logout:hover {
      background: #c0392b;
    }
  </style>
</head>
<body>

<div class="dashboard">
  <div class="sidebar">
    <img src="logo_profil.png" alt="Logo Gudang" class="logo">
    <h2>PT. SUMBER TEKNOLOGI</h2>
    <div class="menu-title">Menu Utama</div>

    <?php if ($role === 'admin'): ?>
      <a href="#" onclick="loadPage('manajemen_barang.php')" class="menu-link">Manajemen Barang</a>
      <a href="#" onclick="loadPage('transaksi_barang.php')" class="menu-link">Transaksi Barang</a>
      <a href="#" onclick="loadPage('laporan.php')" class="menu-link">Laporan</a>
      <a href="#" onclick="loadPage('manajemen_pengguna.php')" class="menu-link">Manajemen Pengguna</a>
    <?php elseif ($role === 'staff'): ?>
      <a href="#" onclick="loadPage('manajemen_barang.php')" class="menu-link">Manajemen Barang</a>
      <a href="#" onclick="loadPage('transaksi_barang.php')" class="menu-link">Transaksi Barang</a>
      <a href="#" onclick="loadPage('laporan.php')" class="menu-link">Laporan</a>
    <?php elseif ($role === 'manager'): ?>
      <a href="#" onclick="loadPage('laporan.php')" class="menu-link">Laporan</a>
    <?php else: ?>
      <p style="color: #fff; padding: 10px;">Role tidak dikenali: <?= htmlspecialchars($role) ?></p>
    <?php endif; ?>

    <a href="logout.php" class="logout">Logout</a>
  </div>

  <div class="content">
    <iframe id="main-frame" src="<?php
      if ($role === 'admin') {
        echo 'manajemen_barang.php';
      } elseif ($role === 'staff') {
        echo 'manajemen_barang.php';
      } elseif ($role === 'manager') {
        echo 'laporan.php';
      } else {
        echo 'login.php';
      }
    ?>"></iframe>
  </div>
</div>

<script>
  function loadPage(page) {
    document.getElementById('main-frame').src = page;
  }
</script>

</body>
</html>
