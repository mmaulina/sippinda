<?php
try {
    $role = $_SESSION['role'];
    $database = new Database();

    $pdo = $database->getConnection(); // Dapatkan koneksi PDO

    $query2 = "SELECT * FROM investasi WHERE 1=1"; // supaya WHERE nya fleksibel
    $params2 = [];
    // Eksekusi Query
    $stmt2 = $pdo->prepare($query2);
    $stmt2->execute($params2);
    $investasi = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Data Investasi</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Investasi</h6>
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
                <a href="?page=tambah_investasi" class="btn btn-primary btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                    </span>
                    <span class="text">Tambah Data</span>
                </a>
                <?php endif; ?>
                <a href="?page=excel_investasi" class="btn btn-success btn-icon-split btn-sm">
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
                            <th colspan="7" class="align-middle">Persentasi Kepemilikan</th>
                            <th rowspan="2" class="align-middle">Aksi</th>
                        </tr>
                        <tr>
                            <th class="align-middle" onclick="sortTable(2)">Pemerintah Pusat <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(3)">Pemerintah Daerah <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(4)">Swasta Nasional <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(5)">Asing <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(6)">Negara Asal <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(7)">Nilai Investasi Tanah <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(8)">Nilai Investasi Bangunan <i class="fa fa-sort"></i></th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php if (count($investasi) > 0): ?>
                            <?php $no = 1;
                            foreach ($investasi as $row): ?>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_perusahaan']); ?></td>
                                <td><?= htmlspecialchars($row['pemerintah_pusat']); ?></td>
                                <td><?= htmlspecialchars($row['pemerintah_daerah']); ?></td>
                                <td><?= htmlspecialchars($row['swasta_nasional']); ?></td>
                                <td><?= htmlspecialchars($row['asing']); ?></td>
                                <td><?= htmlspecialchars($row['negara_asal']); ?></td>
                                <td><?= htmlspecialchars($row['nilai_investasi_tanah']); ?></td>
                                <td><?= htmlspecialchars($row['nilai_investasi_bangunan']); ?></td>
                                <td>
                                    <a href="?page=update_investasi&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                        <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                        <span class="text">Edit</span>
                                    </a>
                                    <a href="?page=delete_investasi&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                            <th class="align-middle">Pemerintah Pusat</th>
                            <th class="align-middle">Pemerintah Daerah</th>
                            <th class="align-middle">Swasta Nasional</th>
                            <th class="align-middle">Asing</th>
                            <th class="align-middle">Negara Asal</th>
                            <th class="align-middle">Nilai Investasi Tanah</th>
                            <th class="align-middle">Nilai Investasi Bangunan</th>
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