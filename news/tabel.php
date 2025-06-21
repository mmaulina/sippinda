<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

// Pastikan hanya umum yang tidak bisa mengakses halaman ini
if (!isset($_SESSION['role']) || ($_SESSION['role'] == 'umum' && $_SESSION['role'] == 'kementerian')) {
    echo "<script>alert('Akses ditolak!'); window.location.href='index.php';</script>";
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Ambil semua konten
    $sql = "SELECT * FROM news ORDER BY tanggal DESC";
    $stmt = $conn->query($sql);
    $konten_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Konten</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Konten</h6>
            <a href="?page=perizinan_tampil" class="btn btn-primary btn-icon-split btn-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left" style="vertical-align: middle; margin-top: 5px;"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
        <div class="card-body">
            <!-- Fitur Search -->
            <div class="mb-3">
                <form class="d-none d-sm-inline-block form-inline mr-auto my-2 my-md-0 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-1 small" placeholder="Search for..."
                            aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tombol Tambah & Ekspor -->
            <div class="mb-3">
                <a href="?page=tambah_konten" class="btn btn-primary btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                    </span>
                    <span class="text">Tambah Data</span>
                </a>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="min-width: 1800px; white-space: nowrap;">
                    <thead>
                        <tr>
                            <th style="width: 5%;" onclick="sortTable(0)">No. <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1)">ID TITLE <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2)">Title <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3)">Jenis Konten <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Konten <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Caption <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Tanggal <i class="fa fa-sort"></i></th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th style="width: 5%;" onclick="sortTable(0)">No. <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1)">ID TITLE <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2)">Title <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3)">Jenis Konten <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Konten <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Caption <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Tanggal <i class="fa fa-sort"></i></th>
                            <th>Aksi</th>
                        </tr>
                        <tbody>
                            <?php if (count($konten_list) > 0): ?>
                                <?php $no = 1;
                                foreach ($konten_list as $row): ?>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['id_title']); ?></td>
                                    <td><?= htmlspecialchars($row['title']); ?></td>
                                    <td><?= htmlspecialchars($row['jenis_konten']); ?></td>
                                    <td>
                                        <?php
                                        if ($row['jenis_konten'] === 'gambar') : ?>
                                            <img src="<?php echo htmlspecialchars($row['konten']); ?>" alt="Gambar Konten" width="100">
                                        <?php elseif ($row['jenis_konten'] === 'link') : ?>
                                            <a href="<?php echo htmlspecialchars($row['konten']); ?>" target="_blank">
                                                🌐 Lihat Link
                                            </a>
                                        <?php elseif ($row['jenis_konten'] === 'file') : ?>
                                            <a href="<?php echo htmlspecialchars($row['konten']); ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-file-alt"></i> Lihat
                                            </a>
                                        <?php else : ?>
                                            <p class="text-muted">Tidak ada konten</p>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['caption']); ?></td>
                                    <td><?= htmlspecialchars($row['tanggal']); ?></td>
                                    <td>
                                        <a href="?page=update_konten&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                            <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                            <span class="text">Edit</span>
                                        </a>
                                        <a href="?page=delete_konten&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            <span class="icon text-white-50"><i class="fa fa-trash" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                            <span class="text">Hapus</span>
                                        </a>
                                    </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="14" class="text-center">Data tidak ditemukan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                </table>
            </div>
        </div>
    </div>

</div>