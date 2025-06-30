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

$query = "SELECT COUNT(*) AS jumlah_baru FROM news WHERE id NOT IN 
          (SELECT konten_id FROM konten_dilihat WHERE id_user = :id_user)";
$stmt = $conn->prepare($query);
$stmt->execute(['id_user' => $id_user]);
$konten_baru = $stmt->fetch(PDO::FETCH_ASSOC)['jumlah_baru'];

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
            <?php if ($konten_baru > 0): ?>
                <span class="position-relative ml-2">
                    <i class="fas fa-bell text-light"></i>
                    <span class="badge badge-success badge-counter position-absolute" style="top: -5px; right: -8px;">
                        <?= $konten_baru; ?>
                    </span>
                </span>
            <?php endif; ?>
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
            <i class="fas fa-file-signature fa-fw mr-2"></i>
            <span class="d-inline-flex align-items-center">
                Perizinan
                <?php if (($role == 'admin' && $jumlahperizinanDiajukan > 0) || ($role == 'superadmin' && $jumlahperizinanDiajukan > 0)): ?>
                    <span class="position-relative ml-2">
                        <i class="fas fa-bell text-light"></i>
                        <span class="badge badge-success badge-counter position-absolute" style="top: -5px; right: -8px;">
                            <?= $jumlahperizinanDiajukan; ?>
                        </span>
                    </span>
                <?php endif; ?>
            </span>
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
            <i class="fas fa-upload fa-fw mr-2"></i>
            <span class="d-inline-flex align-items-center">
                Data SIINas
                <?php if (($role == 'admin' && $jumlahsinasDiajukan > 0) || ($role == 'superadmin' && $jumlahsinasDiajukan > 0)): ?>
                    <span class="position-relative ml-2">
                        <i class="fas fa-bell text-light"></i>
                        <span class="badge badge-success badge-counter position-absolute" style="top: -5px; right: -8px;">
                            <?= $jumlahsinasDiajukan; ?>
                        </span>
                    </span>
                <?php endif; ?>
            </span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Data Pendukung
    </div>

    <li class="nav-item">
        <a class="nav-link" href="?page=proposal_tampil">
            <i class="fas fa-upload fa-fw mr-2"></i>
            <span class="d-inline-flex align-items-center">
                Proposal Kegiatan
                <?php if (($role == 'admin' && $jumlahsinasDiajukan > 0) || ($role == 'superadmin' && $jumlahsinasDiajukan > 0)): ?>
                    <span class="position-relative ml-2">
                        <i class="fas fa-bell text-light"></i>
                        <span class="badge badge-success badge-counter position-absolute" style="top: -5px; right: -8px;">
                            <?= $jumlahsinasDiajukan; ?>
                        </span>
                    </span>
                <?php endif; ?>
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="?page=jdih_tampil">
            <i class="fas fa-upload fa-fw mr-2"></i>
            <span class="d-inline-flex align-items-center">
                jaringan Dokumentasi dan Informasi Hukum
                <span class="position-relative ml-2">
                    <i class="fas fa-bell text-light"></i>
                    <span class="badge badge-success badge-counter position-absolute" style="top: -5px; right: -8px;">
                        <?= $jumlahsinasDiajukan; ?>
                    </span>
                </span>
            </span>
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
                <i class="fas fa-users fa-fw mr-2"></i>
                <span class="d-inline-flex align-items-center">
                    Pengguna
                    <?php if (($role == 'admin' && $jumlahpenggunaDiajukan > 0) || ($role == 'superadmin' && $jumlahpenggunaDiajukan > 0)): ?>
                        <span class="position-relative ml-2">
                            <i class="fas fa-bell text-light"></i>
                            <span class="badge badge-success badge-counter position-absolute" style="top: -5px; right: -8px;">
                                <?= $jumlahpenggunaDiajukan; ?>
                            </span>
                        </span>
                    <?php endif; ?>
                </span>
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