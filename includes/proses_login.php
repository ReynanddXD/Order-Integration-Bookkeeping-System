<?php
session_start();
include 'db.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// Cek apakah username & password cocok
$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 1) {
    $_SESSION['username'] = $username;
    header("Location: ../index.php");
    exit();
} else {
    echo "<script>
        alert('Login gagal. Username atau password salah.');
        window.location.href='../pages/login.php';
    </script>";
}
?>
