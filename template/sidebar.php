<!-- sidebar.php -->
     <link rel="stylesheet" href="assets/css/style.css">
<div class="sidebar-container">
    <ul class="sidebar" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <li class="sidebar-brand d-flex align-items-center justify-content-center">
            <a href="index.html" class="sidebar-brand-text mx-3 text-decoration-none text-dark">
                <strong>SIPPINDA</strong>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item <?= (!isset($_GET['page'])) ? 'active' : ''; ?>">
            <a class="nav-link" href="?page">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">Menu</div>

        <!-- Nav Items -->
        <li class="nav-item <?= (@$_GET['page'] == 'profil_perusahaan') ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=profil_perusahaan">
                <i class="fas fa-industry"></i>
                <span>Profil Perusahaan</span>
            </a>
        </li>

        <li class="nav-item <?= (@$_GET['page'] == 'profil_admin') ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=profil_admin">
                <i class="fas fa-building"></i>
                <span>Data Perusahaan (Admin)</span>
            </a>
        </li>

    </ul>
</div>
