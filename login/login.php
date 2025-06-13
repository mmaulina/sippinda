<?php
session_start();
include '../koneksi.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $db = new Database();
        $pdo = $db->getConnection();
        $sql = "SELECT * FROM users WHERE username = :username OR email = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($password == $user['password']) {
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: ../index.php");
                exit();
            } else {
                $error = "Username atau password salah!";
            }
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Harap isi username/email dan password!";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="../assets/img/kalsel.png" type="image/png">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css">

</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <div class="d-flex justify-content-between mb-3">
            <img src="../assets/img/kalsel.png" alt="Logo Kalsel" style="width: 50px;">
            <img src="../assets/img/LOGO KEMENTERIAN PERINDUSTRIAN.png" alt="Logo Kementerian" style="width: 80px;">
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