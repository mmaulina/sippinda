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

    $query = "SELECT * FROM profil_perusahaan WHERE 1=1"; // supaya WHERE nya fleksibel
    $params = [];

    // Filter keyword pencarian
    if (!empty($keyword)) {
        $query .= " AND nama_perusahaan LIKE :keyword";
        $params[':keyword'] = '%' . $keyword . '%';
    }

    // Eksekusi Query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $query2 = "SELECT * FROM bidang_perusahaan WHERE 1=1"; // supaya WHERE nya fleksibel
    $params2 = [];

    if (!empty($keyword)) {
        $query2 .= " AND nama_perusahaan LIKE :keyword";
        $params2[':keyword'] = '%' . $keyword . '%';
    }

    // Eksekusi Query
    $stmt2 = $pdo->prepare($query2);
    $stmt2->execute($params2);
    $bidang = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Data Profil Perusahaan</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Profil Perusahaan</h6>
        </div>
        <div class="card-body">
            <!-- Fitur Search -->
            <div class="mb-4">
                <form method="get" action="" class="row gx-2 gy-2 align-items-center">
                    <input type="hidden" name="page" value="profil_admin">

                    <!-- Search -->
                    <div class="col-auto" style="max-width: 400px; flex: 1;">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control bg-light border-1 small"
                                placeholder="Cari nama perusahaan..." aria-label="Search" aria-describedby="button-search">
                            <button class="btn btn-primary" type="submit" id="button-search">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                            <a href="?page=profil_admin" class="btn btn-secondary d-flex align-items-center justify-content-center">
                                <i class="fas fa-sync-alt fa-sm"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tombol Tambah & Ekspor -->
            <div class="mb-3">
                <!-- <a href="?page=tambah_profil" class="btn btn-primary btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                    </span>
                    <span class="text">Tambah Data</span>
                </a>
                <a href="?page=tambah_bidang" class="btn btn-primary btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                    </span>
                    <span class="text">Tambah Bidang Perusahaan</span>
                </a> -->
                <a href="?page=excel_profil" class="btn btn-success btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-download" style="vertical-align: middle; margin-top: 5px;"></i>
                    </span>
                    <span class="text">Export Excel</span>
                </a>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="min-width: 1800px; white-space: nowrap;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 5%;" onclick="sortTable(0)">No. <i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(1)">Nama Perusahaan <i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(2)">Alamat Kantor<i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(3)">Alamat Pabrik<i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(4)">No Telpon<i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(5)">No Fax<i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(6)">Jenis Lokasi Pabrik<i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(7)">Jenis Kuisioner<i class="fa fa-sort"></i></th>
                            <th rowspan="2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($profiles) > 0): ?>
                            <?php $no = 1;
                            foreach ($profiles as $row): ?>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_perusahaan']); ?></td>
                                <td><?= htmlspecialchars($row['alamat_kantor']); ?></td>
                                <td><?= htmlspecialchars($row['alamat_pabrik']); ?></td>
                                <td><?= htmlspecialchars($row['no_telpon']); ?></td>
                                <td><?= htmlspecialchars($row['no_fax']); ?></td>
                                <td><?= htmlspecialchars($row['jenis_lokasi_pabrik']); ?></td>
                                <td><?= htmlspecialchars($row['jenis_kuisioner']); ?></td>
                                <td>
                                    <a href="?page=update_profil_admin&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                        <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                        <span class="text">Edit</span>
                                    </a>
                                    <a href="?page=delete_profil_admin&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                    <tfoot>
                        <tr>
                            <th rowspan="2" style="width: 5%;">No. </th>
                            <th rowspan="2">Nama Perusahaan </th>
                            <th rowspan="2">Alamat Kantor</th>
                            <th rowspan="2">Alamat Pabrik</th>
                            <th rowspan="2">No Telpon</th>
                            <th rowspan="2">No Fax</th>
                            <th rowspan="2">Jenis Lokasi Pabrik</th>
                            <th rowspan="2">Jenis Kuisioner</th>
                            <th rowspan="2">Aksi</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- TABEL BIDANG PERUSAHAAN -->
            <h6 class="mt-4 font-weight-bold text-primary">Bidang Perusahaan</h6>
            <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="min-width: 1800px; white-space: nowrap;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 5%;" onclick="sortTable(0)">No. <i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(1)">Nama Perusahaan <i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(2)">Bidang Perusahaan<i class="fa fa-sort"></i></th>
                            <th rowspan="2">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($bidang) > 0): ?>
                            <?php $no = 1;
                            foreach ($bidang as $row): ?>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_perusahaan']); ?></td>
                                <td><?= htmlspecialchars($row['bidang']); ?></td>
                                <td>
                                    <a href="?page=edit_bidang&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                        <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                        <span class="text">Edit</span>
                                    </a>
                                    <a href="?page=hapus_bidang&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
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

                    <tfoot>
                        <tr>
                            <th rowspan="2" style="width: 5%;">No.</th>
                            <th rowspan="2">Nama Perusahaan</th>
                            <th rowspan="2">Bidang Perusahaan</th>
                            <th rowspan="2">Aksi</th>
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