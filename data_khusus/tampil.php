<?php
try {
    $role = $_SESSION['role'];
    $database = new Database();

    $pdo = $database->getConnection(); // Dapatkan koneksi PDO

    // Pencarian keyword
    $keyword = '';
    if (isset($_GET['keyword'])) {
        $keyword = trim($_GET['keyword']);
    }

    $query = "SELECT * FROM data_khusus WHERE 1=1"; // supaya WHERE nya fleksibel
    $params = [];

    // Filter keyword pencarian
    if (!empty($keyword)) {
        $query .= " AND nama_perusahaan LIKE :keyword";
        $params[':keyword'] = '%' . $keyword . '%';
    }

    // Eksekusi Query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data_khusus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Data Khusus Perusahaan</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Khusus</h6>
        </div>
        <div class="card-body">
            <!-- Fitur Search -->
            <div class="mb-3">
                <form method="get" action="" class="d-none d-sm-inline-block form-inline mr-auto my-2 my-md-0 mw-100 navbar-search">
                    <input type="hidden" name="page" value="data_khusus_tampil">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control bg-light border-1 small" placeholder="Cari nama perusahaan..."
                            aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                            <a href="?page=data_khusus_tampil" class="btn btn-secondary">
                                <i class="fas fa-sync-alt fa-sm" style="vertical-align: middle; margin-top: 5px;"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tombol Tambah & Ekspor -->
            <div class="mb-3">
                <?php if ($role == 'umum'): ?>
                    <a href="?page=tambah_data_khusus" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                        </span>
                        <span class="text">Tambah Data</span>
                    </a>
                <?php endif; ?>
                <a href="?page=excel_profil" class="btn btn-success btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-download" style="vertical-align: middle; margin-top: 5px;"></i>
                    </span>
                    <span class="text">Export Excel</span>
                </a>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="min-width: 1800px; white-space: nowrap;">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 5%;" onclick="sortTable(0)">No. <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1)">Nama Perusahaan <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2)">Nama Penanda Tangan Laporan <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3)">jabatan <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Nama Perusahaan Induk <i class="fa fa-sort"></i></th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_khusus) > 0): ?>
                            <?php $no = 1;
                            foreach ($data_khusus as $row): ?>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_perusahaan']); ?></td>
                                <td><?= htmlspecialchars($row['nama_penanda_tangan_laporan']); ?></td>
                                <td><?= htmlspecialchars($row['jabatan']); ?></td>
                                <td><?= htmlspecialchars($row['nama_perusahaan_induk']); ?></td>
                                <td>
                                    <a href="?page=update_data_khusus&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                        <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                        <span class="text">Edit</span>
                                    </a>
                                    <a href="?page=delete_data_khusus&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                    <tfoot class="text-center">
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th>Nama Perusahaan</th>
                            <th>Nama Penanda Tangan Laporan</th>
                            <th>jabatan</th>
                            <th>Nama Perusahaan Induk</th>
                            <th>Aksi</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<!-- JAVASCRIPT FILTER -->
<script>
    function sortTable(columnIndex) {
        var table = document.querySelector("table tbody");
        var rows = Array.from(table.querySelectorAll("tr"));
        var isAscending = table.getAttribute("data-sort-order") === "asc";

        // Sort rows
        rows.sort((rowA, rowB) => {
            var cellA = rowA.children[columnIndex].textContent.trim().toLowerCase();
            var cellB = rowB.children[columnIndex].textContent.trim().toLowerCase();

            if (!isNaN(cellA) && !isNaN(cellB)) {
                return isAscending ? cellA - cellB : cellB - cellA;
            }
            return isAscending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });

        // Remove existing rows
        table.innerHTML = "";

        // Append sorted rows
        rows.forEach(row => table.appendChild(row));

        // Toggle sorting order
        table.setAttribute("data-sort-order", isAscending ? "desc" : "asc");

        // Update icon
        updateSortIcons(columnIndex, isAscending);
    }

    function updateSortIcons(columnIndex, isAscending) {
        var headers = document.querySelectorAll("thead th i");
        headers.forEach(icon => icon.className = "fa fa-sort"); // Reset semua ikon

        var selectedHeader = document.querySelector(`thead th:nth-child(${columnIndex + 1}) i`);
        if (selectedHeader) {
            selectedHeader.className = isAscending ? "fa fa-sort-up" : "fa fa-sort-down";
        }
    }
</script>