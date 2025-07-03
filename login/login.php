<?php
session_start();
include '../koneksi.php';

$error = '';

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    if (!isset($_SESSION['last_login_attempt'])) {
        $_SESSION['last_login_attempt'] = time();
    }

    $wait_time = 15 * 60; // 15 menit
    $elapsed = time() - $_SESSION['last_login_attempt'];

    // Cek apakah harus diblokir sementara
    if ($_SESSION['login_attempts'] >= 5 && $elapsed < $wait_time) {
        $error = "Terlalu banyak percobaan login. Coba lagi dalam " . ceil(($wait_time - $elapsed) / 60) . " menit.";
    } else {
        // Jika waktu blokir sudah lewat, reset attempts
        if ($elapsed >= $wait_time) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_login_attempt'] = time();
        }

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (!empty($username) && !empty($password)) {
            $db = new Database();
            $pdo = $db->getConnection();
            $sql = "SELECT * FROM users WHERE (username = :username OR email = :username)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Ambil kontak admin
            $sqlkontak = "SELECT email, no_telp FROM users WHERE id_user = 1";
            $stmtkontak = $pdo->prepare($sqlkontak);
            $stmtkontak->execute();
            $kontak = $stmtkontak->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user['status'] !== 'diverifikasi') {
                    if ($kontak && !empty($kontak['no_telp'])) {
                        $nomor_hp = preg_replace('/[^0-9]/', '', $kontak['no_telp']);
                        if (substr($nomor_hp, 0, 1) == "0") {
                            $nomor_hp = "62" . substr($nomor_hp, 1);
                        }
                        $wa_link = "https://wa.me/" . $nomor_hp;
                        $error = "Akun Anda belum diverifikasi. Silakan hubungi admin di <a href='$wa_link' target='_blank'>WhatsApp</a>.";
                    } else {
                        $error = "Nomor HP admin tidak ditemukan.";
                    }
                } else {
                    if (password_verify($password, $user['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['id_user'] = $user['id_user'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['login_attempts'] = 0;

                        header("Location: ../index.php");
                        exit();
                    } else {
                        $_SESSION['login_attempts'] += 1;
                        $_SESSION['last_login_attempt'] = time();
                        $error = "Username atau password salah!";
                        file_put_contents("log_login.txt", date('Y-m-d H:i:s') . " - Gagal login: $username\n", FILE_APPEND);
                    }
                }
            } else {
                $_SESSION['login_attempts'] += 1;
                $_SESSION['last_login_attempt'] = time();
                $error = "Username atau password salah!";
            }
        } else {
            $error = "Harap isi username/email dan password!";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="../assets/img/kemenperin.png" type="image/png">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css">

</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <div class="d-flex justify-content-between mb-3">
            <img src="../assets/img/kalsel.png" alt="Logo Kalsel" style="width: 50px;">
            <img src="../assets/img/kemenperin.png" alt="Logo Kementerian Perindustrian" style="width: 80px;">
        </div>
        <h3 class="text-center"><i class="me-1">Login SIPPINDA</h3>
        <hr>
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username atau Email</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username atau email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                    <span class="input-group-text" onclick="togglePassword()" style="cursor:pointer;">
                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn custom-btn w-100">Login</button>
        </form>

        <div class="text-center mt-3">
            <a href="daftar.php">Belum punya akun?</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            const toggleIcon = document.getElementById("toggleIcon");

            const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
            passwordField.setAttribute("type", type);

            toggleIcon.classList.toggle("bi-eye");
            toggleIcon.classList.toggle("bi-eye-slash");
        }
    </script>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>

    </script>


</body>

</html>