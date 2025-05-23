<?php
session_start();

$valid_users = [
    'admin' => ['password' => 'admin123', 'role' => 'admin'],
    'staff' => ['password' => 'staff123', 'role' => 'staff'],
    'manager' => ['password' => 'manager123', 'role' => 'manager']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($valid_users[$username]) && $valid_users[$username]['password'] === $password) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $valid_users[$username]['role'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login PT.Sumber Teknologi</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      padding: 0;
      margin: 0;
      font-family: 'Poppins', sans-serif;
    }
    body {
      background: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      position: relative;
      overflow: hidden;
    }
    body::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 1000px; /* perbesar logo background */
      height: 1000px;
      background: url('logo_profil.png') no-repeat center center;
      background-size: contain;
      opacity: 0.06;
      transform: translate(-50%, -50%);
      z-index: 0;
    }
    .login-box {
      position: relative;
      z-index: 1;
      background-color: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }
    .login-box img {
      width: 100px; /* perbesar logo utama */
      margin-bottom: 1rem;
    }
    .login-box h2 {
      margin-bottom: 1rem;
      color: #4b2ea3;
    }
    .login-box input {
      width: 100%;
      padding: 0.75rem;
      margin: 0.5rem 0;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    .login-box button {
      width: 100%;
      padding: 0.75rem;
      background-color: #4b2ea3;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .login-box button:hover {
      background-color: #5d3edc;
    }
    .error {
      color: red;
      margin-bottom: 1rem;
    }
    .login-box small {
      display: block;
      margin-top: 1rem;
      color: #999;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <img src="logo_profil.png" alt="Logo Gudang">
  
    <?php if (!empty($error)): ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
    <small>© PT. Sumber Teknologi</small>
  </div>
</body>
</html>
