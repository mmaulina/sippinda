<?php
try {
    $role = $_SESSION['role'];
    $database = new Database();

    $pdo = $database->getConnection(); // Dapatkan koneksi PDO

    $query2 = "SELECT * FROM pekerja WHERE 1=1"; // supaya WHERE nya fleksibel
    $params2 = [];
    // Eksekusi Query
    $stmt2 = $pdo->prepare($query2);
    $stmt2->execute($params2);
    $pekerja = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Data Pekerja</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pekerja Per Hari</h6>
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
                <?php if ($role == 'umum'): ?>
                <a href="?page=tambah_pekerja" class="btn btn-primary btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                    </span>
                    <span class="text">Tambah Data</span>
                </a>
                <?php endif; ?>
                <a href="?page=excel_pekerja" class="btn btn-success btn-icon-split btn-sm">
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
                            <th rowspan="2" class="align-middle" style="width: 5%;" onclick="sortTable(0)">No. <i class="fa fa-sort"></i></th>
                            <th rowspan="2" class="align-middle" onclick="sortTable(1)">Nama Perusahaan <i class="fa fa-sort"></i></th>
                            <th colspan="2" class="align-middle">Produksi (Tetap)</th>
                            <th colspan="2" class="align-middle">Produksi (Tidak Tetap)</th>
                            <th colspan="2" class="align-middle">Lainnya (Tidak Tetap)</th>
                            <th colspan="7" class="align-middle">Berdasarkan Tingkat Pendidikan</th>
                            <th rowspan="2" class="align-middle">Aksi</th>
                        </tr>
                        <tr>
                            <th class="align-middle" onclick="sortTable(2)">Laki-Laki <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(3)">Perempuan <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(4)">Laki-Laki <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(5)">Perempuan <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(6)">Laki-Laki <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(7)">Perempuan <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(8)">SD <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(9)">SMP <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(10)">SMA <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(11)">D1 sampai D3 <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(12)">S1/D4 <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(13)">S2 <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(14)">S3 <i class="fa fa-sort"></i></th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php if (count($pekerja) > 0): ?>
                            <?php $no = 1;
                            foreach ($pekerja as $row): ?>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_perusahaan']); ?></td>
                                <td><?= htmlspecialchars($row['laki_laki_pro_tetap']); ?> orang</td>
                                <td><?= htmlspecialchars($row['perempuan_pro_tetap']); ?> orang</td>
                                <td><?= htmlspecialchars($row['laki_laki_pro_tidak_tetap']); ?> orang</td>
                                <td><?= htmlspecialchars($row['perempuan_pro_tidak_tetap']); ?> orang</td>
                                <td><?= htmlspecialchars($row['laki_laki_lainnya']); ?> orang</td>
                                <td><?= htmlspecialchars($row['perempuan_lainnya']); ?> orang</td>
                                <td><?= htmlspecialchars($row['sd']); ?> orang</td>
                                <td><?= htmlspecialchars($row['smp']); ?> orang</td>
                                <td><?= htmlspecialchars($row['sma']); ?> orang</td>
                                <td><?= htmlspecialchars($row['d1_d2_d3']); ?> orang</td>
                                <td><?= htmlspecialchars($row['s1_d4']); ?> orang</td>
                                <td><?= htmlspecialchars($row['s2']); ?> orang</td>
                                <td><?= htmlspecialchars($row['s3']); ?> orang</td>
                                <td>
                                    <a href="?page=update_pekerja&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                        <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                        <span class="text">Edit</span>
                                    </a>
                                    <a href="?page=delete_pekerja&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                            <th class="align-middle" style="width: 5%;">No. </th>
                            <th class="align-middle">Nama Perusahaan</th>
                            <th class="align-middle">Laki-Laki</th>
                            <th class="align-middle">Perempuan</th>
                            <th class="align-middle">Laki-Laki</th>
                            <th class="align-middle">Perempuan</th>
                            <th class="align-middle">Laki-Laki</th>
                            <th class="align-middle">Perempuan</th>
                            <th class="align-middle">SD</th>
                            <th class="align-middle">SMP</th>
                            <th class="align-middle">SMA</th>
                            <th class="align-middle">D1 sampai D3</th>
                            <th class="align-middle">S1/D4</th>
                            <th class="align-middle">S2</th>
                            <th class="align-middle">S3</th>
                            <th class="align-middle">Aksi</th>
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