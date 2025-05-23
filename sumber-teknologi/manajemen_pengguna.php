<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$error = '';
$success = '';

if (isset($_POST['tambah'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $allowed_roles = ['admin', 'staff', 'manager'];

    if ($username == '' || $password == '' || !in_array($role, $allowed_roles)) {
        $error = "Isi username, password, dan pilih role yang valid.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM pengguna WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Username sudah digunakan.";
        } else {
            $stmt->close();
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO pengguna (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            if ($stmt->execute()) {
                $success = "Pengguna berhasil ditambahkan.";
            } else {
                $error = "Gagal menambahkan pengguna: " . $conn->error;
            }
        }
        $stmt->close();
    }
}

$users_result = $conn->query("SELECT id, username, role FROM pengguna ORDER BY username ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manajemen Pengguna</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #f1f3f6, #e8efff);
      color: #2c3e50;
    }

    .container {
      max-width: 800px;
      margin: 3rem auto;
      background: #ffffff;
      padding: 2.5rem 2rem;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    h2 {
      text-align: center;
      color: #4b2ea3;
      margin-bottom: 2rem;
    }

    form {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      justify-content: space-between;
      margin-bottom: 2rem;
    }

    form label {
      flex: 1 1 220px;
      display: flex;
      flex-direction: column;
      font-weight: 600;
    }

    form input[type="text"],
    form input[type="password"],
    form select {
      padding: 0.6rem;
      border-radius: 10px;
      border: 1px solid #ccc;
      margin-top: 0.4rem;
      font-size: 1rem;
    }

    form button {
      background-color: #4b2ea3;
      color: #fff;
      border: none;
      padding: 0.7rem 1.5rem;
      border-radius: 12px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
      align-self: flex-end;
    }

    form button:hover {
      background-color: #3a2380;
    }

    .message {
      padding: 0.8rem 1.2rem;
      margin-bottom: 1.5rem;
      border-radius: 12px;
      font-weight: 600;
    }

    .error {
      background-color: #fdecea;
      color: #e74c3c;
      border-left: 4px solid #e74c3c;
    }

    .success {
      background-color: #e8f8f5;
      color: #16a085;
      border-left: 4px solid #16a085;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }

    table thead {
      background-color: #4b2ea3;
      color: white;
    }

    th, td {
      padding: 0.9rem 1rem;
      text-align: left;
      border: 1px solid #ddd;
    }

    tbody tr:nth-child(even) {
      background-color: #f9f9fb;
    }

    @media screen and (max-width: 768px) {
      form {
        flex-direction: column;
      }

      form label {
        flex: 1 1 100%;
      }
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Manajemen Pengguna</h2>

    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>
        Username
        <input type="text" name="username" required>
      </label>

      <label>
        Password
        <input type="password" name="password" required>
      </label>

      <label>
        Role
        <select name="role" required>
          <option value="">-- Pilih Role --</option>
          <option value="admin">Admin</option>
          <option value="staff">Staff</option>
          <option value="manager">Manager</option>
        </select>
      </label>

      <button type="submit" name="tambah">+ Tambah</button>
    </form>

    <?php if ($users_result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($user = $users_result->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$user['id'] ?></td>
              <td><?= htmlspecialchars($user['username']) ?></td>
              <td><?= htmlspecialchars($user['role']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Belum ada pengguna terdaftar.</p>
    <?php endif; ?>

  </div>

</body>
</html>
