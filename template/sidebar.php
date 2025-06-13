<!-- sidebar.php -->
<link rel="stylesheet" href="assets/css/style.css">

<div class="sidebar" id="sidebar">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand d-flex align-items-center justify-content-center">
        <a href="" class="sidebar-brand-text text-decoration-none">
            <strong>SIPPINDA</strong>
        </a>
    </div>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav List -->
    <ul class="nav flex-column">
        <!-- Nav Item - Home -->
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

        <!-- Nav Item - Profil Perusahaan -->
        <li class="nav-item <?= (@$_GET['page'] == 'profil_perusahaan') ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=profil_perusahaan">
                <i class="fas fa-industry"></i>
                <span>Profil Perusahaan</span>
            </a>
        </li>

        <!-- Nav Item - Data Perusahaan (Admin) -->
        <li class="nav-item <?= (@$_GET['page'] == 'profil_admin') ? 'active' : ''; ?>">
            <a class="nav-link" href="?page=profil_admin">
                <i class="fas fa-building"></i>
                <span>Data Perusahaan (Admin)</span>
            </a>
        </li>
    </ul>
</div>
