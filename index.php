<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('location:login/login.php');
}
include "koneksi.php";
include "notif.php"
// include "template/sidebar.php";
?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include "template/sidebar.php"; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar (Header) -->
                <?php include "template/header.php"; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <?php
                    $page = $_GET['page'] ?? 'home';

                    switch ($page) {
                        // NEWS
                        case "konten_tampil":
                            include "news/tabel.php";
                            break;
                        case "tambah_konten":
                            include "news/tambah.php";
                            break;
                        case "update_konten":
                            include "news/edit.php";
                            break;
                        case "delete_konten":
                            include "news/hapus.php";
                            break;
                        
                        // JDIH
                        case "jdih_tampil":
                            include "jdih/tampil.php";
                            break;
                        case "jdih_tabel":
                            include "jdih/tabel.php";
                            break;
                        case "tambah_jdih":
                            include "jdih/tambah.php";
                            break;
                        case "update_jdih":
                            include "jdih/edit.php";
                            break;
                        case "delete_jdih":
                            include "jdih/hapus.php";

                        // PROFIL PERUSAHAAN
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
                        case "tambah_bidang":
                            include "perusahaan/tambah_bidang.php";
                            break;
                        case "edit_bidang":
                            include "perusahaan/edit_bidang.php";
                            break;
                        case "hapus_bidang":
                            include "perusahaan/hapus_bidang.php";
                            break;

                        // DATA UMUM
                        case "data_umum_tampil":
                            include "data_umum/tampil.php";
                            break;
                        case "tambah_data_umum":
                            include "data_umum/tambah.php";
                            break;
                        case "update_data_umum":
                            include "data_umum/edit.php";
                            break;
                        case "delete_data_umum":
                            include "data_umum/hapus.php";
                            break;

                        // DATA KHUSUS
                        case "data_khusus_tampil":
                            include "data_khusus/tampil.php";
                            break;
                        case "tambah_data_khusus":
                            include "data_khusus/tambah.php";
                            break;
                        case "update_data_khusus":
                            include "data_khusus/edit.php";
                            break;
                        case "delete_data_khusus":
                            include "data_khusus/hapus.php";
                            break;

                        // INVESTASI
                        case "investasi_tampil":
                            include "investasi/tampil.php";
                            break;
                        case "tambah_investasi":
                            include "investasi/tambah.php";
                            break;
                        case "update_investasi":
                            include "investasi/edit.php";
                            break;
                        case "delete_investasi":
                            include "investasi/hapus.php";
                            break;

                        // PEKERJA
                        case "pekerja_tampil":
                            include "pekerja/tampil.php";
                            break;
                        case "tambah_pekerja":
                            include "pekerja/tambah.php";
                            break;
                        case "update_pekerja":
                            include "pekerja/edit.php";
                            break;
                        case "delete_pekerja":
                            include "pekerja/hapus.php";
                            break;

                        // PERIZINAN
                        case "perizinan_tampil":
                            include "perizinan/tampil.php";
                            break;
                        case "tambah_perizinan":
                            include "perizinan/tambah.php";
                            break;
                        case "update_perizinan":
                            include "perizinan/edit.php";
                            break;
                        case "delete_perizinan":
                            include "perizinan/hapus.php";
                            break;

                        // DATA SINAS
                        case "data_siinas_tampil":
                            include "data_siinas/tampil.php";
                            break;
                        case "tambah_data_siinas":
                            include "data_siinas/tambah.php";
                            break;
                        case "update_data_siinas":
                            include "data_siinas/edit.php";
                            break;
                        case "delete_data_siinas":
                            include "data_siinas/hapus.php";
                            break;

                        // PROPOSAL KEGIATAN
                        case "proposal_tampil":
                            include "proposal/tampil.php";
                            break;
                        case "tambah_proposal":
                            include "proposal/tambah.php";
                            break;
                        case "update_proposal":
                            include "proposal/edit.php";
                            break;
                        case "delete_proposal":
                            include "proposal/hapus.php";
                            break;

                        // PENGGUNA
                        case "pengguna_tampil":
                            include "pengguna/tampil.php";
                            break;
                        case "tambah_pengguna":
                            include "pengguna/tambah.php";
                            break;
                        case "update_pengguna":
                            include "pengguna/edit.php";
                            break;
                        case "edit_password":
                            include "pengguna/edit_password.php";
                            break;
                        case "delete_pengguna":
                            include "pengguna/hapus.php";
                            break;

                        // Default (Home)
                        default:
                            include "home.php";
                            break;
                    }
                    ?>
                </div>
                <?php include "template/footer.php"; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="assets/js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="assets/js/demo/chart-area-demo.js"></script>
    <script src="assets/js/demo/chart-pie-demo.js"></script>

</body>

</html>