<<<<<<< HEAD:includes/config.php
<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "senapenmas";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("koneksi failed: " . $conn->connect_error);
}
=======
<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "tugasakhir";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("koneksi failed: " . $conn->connect_error);
}
>>>>>>> c6f886dee0e73641f53db3ac95e20784a6a7c95e:admin/includes/config.php
?>