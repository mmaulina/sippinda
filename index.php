<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('location:login/login.php');
}
include "koneksi.php";
include "template/header.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SIPPINDA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome & Bootstrap -->
    <link rel="stylesheet" href="assets/fa/css/all.min.css">
    <link rel="stylesheet" href="assets/css/bootstraps.min.css">

    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Optional Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('d-none');
        }
    </script>
</head>
<body>

    <!-- Tombol Toggle (Hanya Muncul di Layar Kecil) -->
<button class="toggle-btn d-md-none" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>


    <div class="row">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <?php include "template/sidebar.php"; ?>
        </div>

        <!-- Konten Utama -->
        <div class="main-content">
            <?php
            $page = $_GET['page'] ?? 'home';

            switch ($page) {
                // Perusahaan
                case "profil_perusahaan":
                    include "perusahaan/tampil.php";
                    break;
                case "profil_admin":
                    include "perusahaan/tampil_admin.php";
                    break;
                case "tambah_profil":
                    include "perusahaan/tambah_profil.php";
                    break;
                case "update_profil":
                    include "perusahaan/update_profil.php";
                    break;
                case "update_profil_admin":
                    include "perusahaan/update_profil_admin.php";
                    break;
                case "delete_profil":
                    include "perusahaan/delete_profil.php";
                    break;
                case "delete_profil_admin":
                    include "perusahaan/delete_profil_admin.php";
                    break;
                case "excel_profil":
                    include "perusahaan/ekspor.php";
                    break;

                // Default (Home)
                default:
                    include "home.php";
                    break;
            }
            ?>
        </div>
    </div>

<?php include "template/footer.php"; ?>
</body>
</html>
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('show');
}
</script>
