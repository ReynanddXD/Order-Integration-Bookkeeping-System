<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Responsif</title>
  <link rel="stylesheet" href="../assets/css/style-login.css" />
</head>
<body>
  <div class="login-container">
    <h2>Selamat Datang</h2>
    <form action="../includes/proses_login.php" method="post">
      <div class="form-group">
        <label for="username">Nama Pengguna</label>
        <input type="text" id="username" name="username" placeholder="Masukkan username" required />
      </div>
      <div class="form-group">
        <label for="password">Kata Sandi</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required />
      </div>
      <button type="submit" class="login-button">Masuk</button>
    </form>
  </div>
</body>
</html>
