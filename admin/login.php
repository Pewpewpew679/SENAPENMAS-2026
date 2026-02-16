<?php
session_start();
require 'includes/config.php';

/* ===============================
   SECURITY CONFIG
=================================*/
$max_attempt = 5;
$lock_time   = 300; // 5 menit
$ip          = $_SERVER['REMOTE_ADDR'];
$now         = time();

/* ===============================
   CSRF TOKEN
=================================*/
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

/* ===============================
   FLASH MESSAGE
=================================*/
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

/* ===============================
   CREATE TABLE IF NOT EXISTS
=================================*/
$conn->query("CREATE TABLE IF NOT EXISTS login_attempts (
    ip_address VARCHAR(45) PRIMARY KEY,
    attempt_count INT DEFAULT 0,
    last_attempt INT
)");

/* ===============================
   HANDLE LOGIN
=================================*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Honeypot
    if (!empty($_POST['website'])) {
        exit("Bot detected.");
    }

    // CSRF
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        exit("Invalid request.");
    }

    // Rate limit check
    $stmt = $conn->prepare("SELECT attempt_count, last_attempt FROM login_attempts WHERE ip_address=?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['attempt_count'] >= $max_attempt && ($now - $row['last_attempt']) < $lock_time) {
            $_SESSION['message'] = "Terlalu banyak percobaan login. Coba lagi 5 menit.";
            header("Location: login.php");
            exit;
        }
    }

    /* ===== CAPTCHA VALIDATION ===== */
    if (
        !isset($_SESSION['captcha']) ||
        strtoupper(trim($_POST['captcha'])) !== $_SESSION['captcha']
    ) {

        unset($_SESSION['captcha']);

        $stmt = $conn->prepare("
            INSERT INTO login_attempts (ip_address, attempt_count, last_attempt)
            VALUES (?,1,?)
            ON DUPLICATE KEY UPDATE
            attempt_count = attempt_count + 1,
            last_attempt = ?
        ");
        $stmt->bind_param("sii", $ip, $now, $now);
        $stmt->execute();

        sleep(1);

        $_SESSION['message'] = "Captcha salah!";
        header("Location: login.php");
        exit;
    }

    /* ===== USER VALIDATION ===== */
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT admin_ID, admin_USER, admin_PASS FROM admin WHERE admin_USER=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['admin_PASS'])) {

            // Reset rate limit
            $stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address=?");
            $stmt->bind_param("s", $ip);
            $stmt->execute();

            session_regenerate_id(true);

            $_SESSION['admin_id']   = $admin['admin_ID'];
            $_SESSION['admin_user'] = $admin['admin_USER'];

            unset($_SESSION['captcha']);

            header("Location: dashboard.php");
            exit;
        }
    }

    // Username / Password salah
    $stmt = $conn->prepare("
        INSERT INTO login_attempts (ip_address, attempt_count, last_attempt)
        VALUES (?,1,?)
        ON DUPLICATE KEY UPDATE
        attempt_count = attempt_count + 1,
        last_attempt = ?
    ");
    $stmt->bind_param("sii", $ip, $now, $now);
    $stmt->execute();

    sleep(1);

    $_SESSION['message'] = "Username atau password salah!";
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ADMINISTRATOR</title>
    <style>
        body {
            font-family: Arial;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url("images/gedunguntar.jpg") no-repeat center center fixed;
            background-size: cover;
        }
        .box {
            background: white;
            padding: 40px;
            width: 350px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        h2 { text-align:center; }
        input {
            width: 93.5%;
            padding: 10px;
            margin-bottom: 15px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #667eea;
            border: none;
            color: white;
            cursor: pointer;
        }
        button:hover { background: #5563c1; }
        .error {
            color: red;
            text-align:center;
            margin-bottom: 10px;
        }
        img {
            margin-bottom: 10px;
            cursor:pointer;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>ADMINISTRATOR</h2>

    <?php if (!empty($message)) : ?>
        <div class="error"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
        <input type="text" name="website" style="display:none">

        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <img src="captcha.php"
             onclick="this.src='captcha.php?'+Math.random();"
             title="Klik untuk refresh captcha">

        <input type="text" name="captcha" placeholder="Masukkan captcha" required>

        <button type="submit">Login</button>
    </form>

    <div class="back-link">
        Kembali ke Web? 
        <a href="http://localhost/senapenmas/SENAPENMAS-2026/frontend/main.php">Klik di sini</a>
    </div>

</div>

</body>
</html>
