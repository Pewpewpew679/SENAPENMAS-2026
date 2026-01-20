<!DOCTYPE html>
<html lang="id">
<head>
  <title>Dashboard Tugas Akhir</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/login.css">
</head>

<?php
include("includes/config.php");
ob_start();
session_start();

if (isset($_POST["login"])) {
    $useremail = $_POST["user"];
    $userpass  = $_POST["pass"];
    $sql_login = mysqli_query($conn,"SELECT * FROM admin 
         WHERE admin_USER = '$useremail' AND admin_PASS = '$userpass'");

    if (mysqli_num_rows($sql_login) > 0) {
        $row_admin = mysqli_fetch_array($sql_login);
        $_SESSION['useremail'] = $row_admin['admin_USER'];
        header("Location:dashboard.php");
        exit;
    }
}
?>
<body>

<div class="container">
  <!-- kolom kiri -->
  <div class="kolomkiri col-md-4 text-center">
    <h2>TUGAS AKHIR</h2>
    <p>My Profile</p>
    <b><p>Ferdinand Justin</b><br>NIM: 825240125<br>Kelas: SI C'24</p>
  </div>

  <!-- kolom kanan -->
  <div class="kolomkanan col-md-8">
    <div class="login-box">
      <h5 class="text-center mb-4">LOGIN</h5>
      <form method="POST">
        <div class="mb-3">
          <input type="email" class="form-control" placeholder="Enter Email" name="user" required>
        </div>
        <div class="mb-3">
          <input type="password" class="form-control" placeholder="Enter Password" name="pass" required>
        </div>
        <input type="submit" class="btn btn-login" value="Login" name="login">
      </form>
    </div>
  </div>
</div>

</body>
<?php
mysqli_close($conn);
ob_end_flush();
?>
</html>
