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

    $bulan = date('n');
    $tahun = $_GET['tahun'] ?? date('Y');
    // Ambil tahun & triwulan dari GET atau default (berdasarkan bulan sekarang)
    if ($bulan >= 1 && $bulan <= 3) {
        $triwulan_default = 'Triwulan I';
    } elseif ($bulan >= 4 && $bulan <= 6) {
        $triwulan_default = 'Triwulan II';
    } elseif ($bulan >= 7 && $bulan <= 9) {
        $triwulan_default = 'Triwulan III';
    } else {
        $triwulan_default = 'Triwulan IV';
    }

    $triwulanList = ["Triwulan I", "Triwulan II", "Triwulan III", "Triwulan IV"];
    $triwulan = isset($_GET['triwulan']) && in_array($_GET['triwulan'], $triwulanList) ? $_GET['triwulan'] : $triwulan_default;

    // Ambil daftar tahun dari data_sinas
    $queryTahun = "SELECT DISTINCT SUBSTRING(triwulan, -4) AS tahun FROM data_sinas ORDER BY tahun DESC";
    $stmtTahun = $conn->query($queryTahun);
    $tahunList = $stmtTahun->fetchAll(PDO::FETCH_COLUMN);

    $triwulanList = ["Triwulan I", "Triwulan II", "Triwulan III", "Triwulan IV"];

    // Ambil semua perusahaan
    $queryPerusahaan = "SELECT id_user, nama_perusahaan FROM profil_perusahaan";
    $stmtPerusahaan = $conn->query($queryPerusahaan);
    $perusahaanList = $stmtPerusahaan->fetchAll(PDO::FETCH_ASSOC);
    $totalPerusahaan = count($perusahaanList);

    // Ambil id_user yang sudah upload
    $queryUpload = "SELECT DISTINCT id_user FROM data_sinas WHERE upload IS NOT NULL AND upload != '' AND triwulan = :tw";
    $stmtUpload = $conn->prepare($queryUpload);
    $stmtUpload->execute([':tw' => "$triwulan $tahun"]);

    $sudahUploadIds = $stmtUpload->fetchAll(PDO::FETCH_COLUMN);
    $sudahUpload = count($sudahUploadIds);

    // Hitung belum upload
    $belumUpload = $totalPerusahaan - $sudahUpload;
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
// Bagi data perusahaan
$sudahUploadList = array_filter($perusahaanList, fn($p) => in_array($p['id_user'], $sudahUploadIds));
$belumUploadList = array_filter($perusahaanList, fn($p) => !in_array($p['id_user'], $sudahUploadIds));

?>


<!-- Begin Page Content -->
<div class="container-fluid">

    <div class="text-center my-2">
        <h1 class="h3 text-gray-800 mb-3">WELCOME TO SIPPINDA</h1>
        <p class="text-muted mb-4">Sistem Informasi Pengawasan Pengendalian Industri Daerah yang Menjadi Kewenangan Provinsi</p>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Dashboard -->
        <div class="container-fluid">

            <!-- Filter Form -->
            <?php if ($role == 'admin' || $role == 'superadmin'): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Dashboard Upload Data SIINas</h6>
                    </div>
                    <div class="card-body">
                        <form method="get" class="row g-3">
                            <div class="col-md-5">
                                <label for="tahun" class="form-label">Pilih Tahun</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    <?php foreach ($tahunList as $th): ?>
                                        <option value="<?= $th ?>" <?= $th == $tahun ? 'selected' : '' ?>><?= $th ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label for="triwulan" class="form-label">Pilih Triwulan</label>
                                <select name="triwulan" id="triwulan" class="form-control">
                                    <?php foreach ($triwulanList as $tw): ?>
                                        <option value="<?= $tw ?>" <?= $tw == $triwulan ? 'selected' : '' ?>><?= $tw ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                        <!-- Cards Row -->
                        <div class="row">
                            <!-- Total Perusahaan -->
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2" data-bs-toggle="modal" data-bs-target="#modalTotalPerusahaan" style="cursor:pointer;">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Perusahaan Terdaftar</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPerusahaan ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sudah Upload -->
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100 py-2" data-bs-toggle="modal" data-bs-target="#modalSudahUpload" style="cursor:pointer;">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Perusahaan Sudah Upload</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $sudahUpload ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Belum Upload -->
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-danger shadow h-100 py-2" data-bs-toggle="modal" data-bs-target="#modalBelumUpload" style="cursor:pointer;">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Perusahaan Belum Upload</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $belumUpload ?></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>



    <!-- Content Row -->
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="container-fluid">
            <!-- Card Header -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">News</h6>
                    <?php if ($role == 'admin' || $role == 'superadmin'): ?>
                        <a href="?page=konten_tampil" class="btn btn-primary">
                            <i class="fas fa-fw fa-table fa-sm text-white-50"></i> Daftar Konten
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
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
                                                    <img src="<?php echo htmlspecialchars($konten['konten']); ?>"
                                                        class="img-fluid rounded"
                                                        style="width: 250px; height: auto; cursor: pointer;"
                                                        alt="Konten Gambar"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#imageModal"
                                                        data-img="<?php echo htmlspecialchars($konten['konten']); ?>">
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
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-body text-center">
                                    <img src="" id="modalImage" class="img-fluid rounded" alt="Preview Gambar">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function(event) {
        const img = event.relatedTarget;
        const src = img.getAttribute('data-img');
        const modalImg = imageModal.querySelector('#modalImage');
        modalImg.src = src;
    });
</script>

<!-- Modal: Total Perusahaan -->
<div class="modal fade" id="modalTotalPerusahaan" tabindex="-1" aria-labelledby="modalTotalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTotalLabel">Daftar Seluruh Perusahaan Terdaftar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-sm">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($perusahaanList as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($p['nama_perusahaan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Sudah Upload -->
<div class="modal fade" id="modalSudahUpload" tabindex="-1" aria-labelledby="modalSudahUploadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSudahUploadLabel">Perusahaan yang Sudah Upload</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-sm">
                    <thead class="table-success">
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($sudahUploadList as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($p['nama_perusahaan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Belum Upload -->
<div class="modal fade" id="modalBelumUpload" tabindex="-1" aria-labelledby="modalBelumUploadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBelumUploadLabel">Perusahaan yang Belum Upload</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-sm">
                    <thead class="table-danger">
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($belumUploadList as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($p['nama_perusahaan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>