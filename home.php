<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

// Tandai semua konten baru sebagai dilihat
    $query = "INSERT IGNORE INTO konten_dilihat (id_user, konten_id) 
    SELECT :id_user, id FROM news";
    $stmt = $conn->prepare($query);
    $stmt->execute(['id_user' => $_SESSION['id_user']]);

    // Ambil semua konten
    $sql = "SELECT * FROM news ORDER BY tanggal DESC";
    $stmt = $conn->query($sql);
    $konten_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grouped_konten = [];
    foreach ($konten_list as $konten) {
        $grouped_konten[$konten['id_title']][] = $konten;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="text-center my-2">
        <h1 class="h3 text-gray-800 mb-3">WELCOME TO SIPPINDA</h1>
        <p class="text-muted mb-4">Sistem Informasi Pengawasan Pengendalian Industri Daerah yang Menjadi Kewenangan Provinsi</p>
    </div>


    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                News</div>
                                <a href="?page=konten_tampil" class ="btn btn-primary">Konten</a>
                                    <div class="timeline position-relative">
                                    <?php foreach ($grouped_konten as $id_title => $kontens) : ?>
                                        <div class="timeline-item d-flex flex-column align-items-center text-center position-relative">
                                            <div class="circle bg-dark rounded-circle position-absolute" style="width: 15px; height: 15px; left: -10px; top: 50%; transform: translateY(-50%);"></div>
                                            <div class="content w-75 ms-3">
                                                <h5 class="card-text mt-3"><?php echo htmlspecialchars($kontens[0]['title']); ?></h5>
                                                <div class="d-flex flex-column align-items-center mt-3">
                                                    <?php foreach ($kontens as $konten) : ?>
                                                        <div class="text-center">
                                                            <?php if ($konten['jenis_konten'] === 'gambar') : ?>
                                                                <img src="<?php echo htmlspecialchars($konten['konten']); ?>" class="img-fluid rounded" style="width: 150px; height: auto;" alt="Konten Gambar">
                                                            <?php elseif ($konten['jenis_konten'] === 'file') : ?>
                                                                <a href="<?php echo htmlspecialchars($konten['konten']); ?>" class="btn btn-secondary" style="width: 150px;">Download File</a>
                                                            <?php elseif ($konten['jenis_konten'] === 'link') : ?>
                                                                <a href="<?php echo htmlspecialchars($konten['konten']); ?>" target="_blank" class="btn btn-info" style="width: 150px;">Lihat Link</a>
                                                            <?php endif; ?>
                                                            <p class="mt-2 small"><?php echo nl2br(htmlspecialchars($konten['caption'])); ?></p>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <p class="card-text mt-3"><small class="text-muted">Diupload pada: <?php echo $kontens[0]['tanggal']; ?></small></p>
                                            </div>
                                            <div class="line position-absolute bg-dark" style="width: 2px; height: 100%; left: 0px; top: 0;"></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>