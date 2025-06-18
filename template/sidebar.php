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

    <!-- ROLE UMUM -->
    <li class="nav-item">
        <a class="nav-link" href="?page=profil_perusahaan">
            <i class="fas fa-fw fa-building"></i>
            <span>Profil Perusahaan</span>
        </a>
    </li>

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


    <!-- DATA II -->
    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Data II
    </div>

    <li class="nav-item">
        <a class="nav-link" href="?page=perizinan_tampil">
            <i class="fas fa-fw fa-file"></i>
            <span>Perizinan</span>
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
        <a class="nav-link" href="?page=profil_perusahaan">
            <i class="fas fa-fw fa-cog"></i>
            <span>Profil Perusahaan</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="?page=profil_admin">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Data Perusahaan</span>
        </a>
    </li>

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
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>