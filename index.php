<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    // header('location:home.php');
    header('location:login/login.php');
}
include "koneksi.php";
include "template/header.php";
// include 'template/alert.php'; 
?>


<link rel="stylesheet" href="assets/fa/css/all.min.css">
<link rel="stylesheet" href="assets/css/bootstraps.min.css">
<script src="assets/js/bootstrap.min.js"></script>
<div class="container-fluid">
    <div class="row"> <!-- Mulai baris untuk sidebar dan konten -->

        <!-- Sidebar -->
        <?php include "template/sidebar.php"; ?>

        <!-- Konten utama -->
        <div class="col-md-9 col-lg-10 main-content"> <!-- Lebar konten agar sejajar dengan sidebar -->
            <?php
            $page = isset($_GET['page']) ? $_GET['page'] : 'home';
            switch ($page) {
                // perusahaan
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

                // data pembangkit dan data teknis pembangkit
                case "pembangkit":
                    include "pembangkit/tampil.php";
                    break;
                case "pembangkit_tambah":
                    include "pembangkit/tambah.php";
                    break;
                case "pembangkit_edit":
                    include "pembangkit/update.php";
                    break;
                case "pembangkit_hapus":
                    include "pembangkit/delete.php";
                    break;
                case "pembangkit_export":
                    include "pembangkit/export.php";
                    break;

                // laporan perbulan
                case "laporan_perbulan":
                    include "laporan_perbulan/tampil.php";
                    break;
                case "tambah_laporan_perbulan":
                    include "laporan_perbulan/tambah_laporan.php";
                    break;
                case "tambah_laporan_perbulan2":
                    include "laporan_perbulan/tambah_laporan2.php";
                    break;
                case "tambah_unit":
                    include "laporan_perbulan/tambah_unit.php";
                    break;
                case "edit_laporan_perbulan":
                    include "laporan_perbulan/update.php";
                    break;
                case "hapus_laporan_perbulan":
                    include "laporan_perbulan/hapus.php";
                    break;
                case "excel_laporan_bulanan":
                    include "laporan_perbulan/ekspor.php";
                    break;
                    

                // laporan persemester
                case "laporan_persemester":
                    include "laporan_persemester/tampil.php";
                    break;
                case "tambah_parameter":
                    include "laporan_persemester/tambah_parameter.php";
                    break;
                    case "edit_parameter":
                    include "laporan_persemester/edit_parameter.php";
                    break;
                    case "hapus_parameter":
                    include "laporan_persemester/hapus_parameter.php";
                    break;
                case "tambah_laporan_persemester":
                    include "laporan_persemester/tambah_laporan.php";
                    break;
                case "edit_laporan_persemester":
                    include "laporan_persemester/edit_laporan.php";
                    break;
                case "hapus_laporan_persemester":
                    include "laporan_persemester/hapus_laporan.php";
                    break;

                // pengguna
                case "pengguna":
                    include "pengguna/tampil.php";
                    break;
                case "pengguna_tambah_admin":
                    include "pengguna/tambah_admin.php";
                    break;
                case "pengguna_edit_admin":
                    include "pengguna/edit_admin.php";
                    break;
                case "edit_password":
                    include "pengguna/edit_password.php";
                    break;
                case "pengguna_hapus_admin":
                    include "pengguna/hapus_admin.php";
                    break;

                //news
                case "upload":
                    include "news/tambah.php";
                    break;
                case "tabel":
                    include "news/tabel.php";
                    break;
                case "edit_konten":
                    include "news/edit.php";
                    break;
                case "hapus_konten":
                    include "news/hapus.php";
                    break;

                //djih
                case "upload_djih":
                    include "djih/tambah.php";
                    break;
                case "tabel_djih":
                    include "djih/tabel.php";
                    break;
                case "edit_konten_djih":
                    include "djih/edit.php";
                    break;
                case "hapus_konten_djih":
                    include "djih/hapus.php";
                    break;
                case "tampil_konten_djih":
                    include "djih/tampil.php";
                    break;

                //notiifikasi
                case "notifikasi":
                    include "notif.php";
                    break;

                //kontak
                case "kontak":
                    include "kontak/tampil.php";
                    break;
                case "update_kontak":
                    include "kontak/edit_kontak.php";
                    break;


                // default saat login berhasil
                default:
                    include "home.php";
                    break;
            }
            ?>
        </div>

    </div> <!-- Tutup row -->
</div> <!-- Tutup container-fluid -->

<?php include "template/footer.php"; ?>