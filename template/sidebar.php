<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='login/login.php';</script>";
    exit();
}

                            $id_user = $_SESSION['id_user'];
                            $role = $_SESSION['role'];
                            $database = new Database();
                            $conn = $database->getConnection();

                            if ($role == 'admin' || $role == 'superadmin') {
                            // Query untuk menghitung jumlah laporan_semester yang berstatus 'diajukan'
                            $queryperizinan = "SELECT COUNT(*) as total FROM perizinan WHERE verifikasi = 'diajukan'";
                            $stmtperizinan = $conn->prepare($queryperizinan);
                            $stmtperizinan->execute();
                            $resultperizinan = $stmtperizinan->fetch(PDO::FETCH_ASSOC);
                            $jumlahperizinanDiajukan = $resultperizinan['total'];
                        }

                        if ($role == 'admin' || $role == 'superadmin') {
                            // Query untuk menghitung jumlah laporan_semester yang berstatus 'diajukan'
                            $querysinas = "SELECT COUNT(*) as total FROM data_sinas WHERE status = 'diajukan'";
                            $stmtsinas = $conn->prepare($querysinas);
                            $stmtsinas->execute();
                            $resultsinas = $stmtsinas->fetch(PDO::FETCH_ASSOC);
                            $jumlahsinasDiajukan = $resultsinas['total'];
                        }

                        if ($role == 'admin' || $role == 'superadmin') {
                            // Query untuk menghitung jumlah laporan_semester yang berstatus 'diajukan'
                            $querypengguna = "SELECT COUNT(*) as total FROM users WHERE status = 'diajukan'";
                            $stmtpengguna = $conn->prepare($querypengguna);
                            $stmtpengguna->execute();
                            $resultpengguna = $stmtpengguna->fetch(PDO::FETCH_ASSOC);
                            $jumlahpenggunaDiajukan = $resultpengguna['total'];
                        }
?>
<head>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="?page=home">
        <div class="sidebar-brand-icon">
            <img src="assets/img/kemenperin.png" alt="Logo Kemenperin"
                style="height: 40px; width: auto; background-color: white; padding: 5px; border-radius: 20px;">
        </div>
        <div class="sidebar-brand-text mx-3">SIPPINDA</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="?page=home">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Home</span>
        </a>
    </li>

    <!-- DATA I -->
    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Data I
    </div>

    <?php if ($role == 'umum'): ?>
    <!-- ROLE UMUM -->
    <li class="nav-item">
        <a class="nav-link" href="?page=profil_perusahaan">
            <i class="fas fa-fw fa-building"></i>
            <span>Profil Perusahaan</span>
        </a>
    </li>
        <?php endif; ?>

        <?php if ($role == 'admin' || $role == 'superadmin'): ?>
    <!-- ROLE ADMIN & SUPERADMIN -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProfil"
            aria-expanded="false" aria-controls="collapseProfil">
            <i class="fas fa-fw fa-building"></i>
            <span>Profil Perusahaan</span>
        </a>
        <div id="collapseProfil" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Data Profil Perusahaan:</h6>
                <a class="collapse-item" href="?page=profil_admin">
                    <i class="fas fa-fw fa-industry mr-1"></i> Data Perusahaan (A)
                </a>
                <a class="collapse-item" href="?page=data_umum_tampil">
                    <i class="fas fa-fw fa-book mr-1"></i> Data Umum
                </a>
                <a class="collapse-item" href="?page=data_khusus_tampil">
                    <i class="fas fa-fw fa-folder mr-1"></i> Data Khusus
                </a>
                <a class="collapse-item" href="?page=investasi_tampil">
                    <i class="fas fa-fw fa-money-bill mr-1"></i> Investasi
                </a>
                <a class="collapse-item" href="?page=pekerja_tampil">
                    <i class="fas fa-fw fa-user-tie mr-1"></i> Pekerja Per Hari
                </a>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- DATA II -->
    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Data II
    </div>

    <li class="nav-item">
        <a class="nav-link" href="?page=perizinan_tampil">
            <i class="fas fa-fw fa-file-signature"></i>
            <span>Perizinan</span>
            <?php if ($role == 'admin' || $role == 'superadmin'): ?>
            <?php if ($jumlahperizinanDiajukan > 0) : ?>
            <span class="badge bg-danger ms-2"><?= $jumlahperizinanDiajukan; ?></span>
            <?php endif; ?>
            <?php endif; ?>
        </a>
    </li>

    <!-- DATA III -->
    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Data III
    </div>

    <li class="nav-item">
        <a class="nav-link" href="?page=data_siinas_tampil">
            <i class="fas fa-fw fa-upload"></i>
            <span>Data SIINas </span>
            <?php if ($role == 'admin' || $role == 'superadmin'): ?>
            <?php if ($jumlahsinasDiajukan > 0) : ?>
            <span class="badge bg-danger ms-2"><?= $jumlahsinasDiajukan; ?></span>
            <?php endif; ?>
            <?php endif; ?>
        </a>
    </li>

    <?php if ($role == 'admin' || $role == 'superadmin'): ?>
    <!-- DATA PENGGUNA -->
    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Data Pengguna
    </div>

    <li class="nav-item">
        <a class="nav-link" href="?page=pengguna_tampil">
            <i class="fas fa-fw fa-users"></i>
            <span>Pengguna</span>
            <?php if ($role == 'admin' || $role == 'superadmin'): ?>
            <?php if ($jumlahpenggunaDiajukan > 0) : ?>
            <span class="badge bg-danger ms-2"><?= $jumlahpenggunaDiajukan; ?></span>
            <?php endif; ?>
            <?php endif; ?>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <?php endif; ?>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>