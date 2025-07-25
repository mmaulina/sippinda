<?php
try {
    $role = $_SESSION['role'];
    $database = new Database();

    $pdo = $database->getConnection(); // Dapatkan koneksi PDO

    $search = $_GET['search'] ?? ''; // Ambil keyword pencarian
    $query = "SELECT * FROM users WHERE 1=1";
    $params = [];

    // Jika role admin, sembunyikan data superadmin
    if ($role === 'admin') {
        $query .= " AND role != :exclude_role";
        $params['exclude_role'] = 'superadmin';
    }

    // Filter pencarian
    if (!empty($search)) {
        $query .= " AND (username LIKE :search OR email LIKE :search OR no_telp LIKE :search)";
        $params['search'] = "%$search%";
    }

    // Eksekusi Query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $pengguna = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Proses persetujuan (Verifikasi)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['terima_id'])) {
    $id = $_POST['terima_id'];

    $updateQuery = "UPDATE users SET status = 'diverifikasi' WHERE id_user = :id_user";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':id_user', $id);
    $updateStmt->execute();

    echo "<script>alert('Pengguna telah diverifikasi!'); window.location.href='?page=pengguna_tampil';</script>";
}

// Proses penolakan
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['tolak_id'])) {
    $id = $_POST['tolak_id'];

    $updateQuery = "UPDATE users SET status = 'ditolak' WHERE id_user = :id_user";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':id_user', $id);
    $updateStmt->execute();

    echo "<script>alert('Pengguna telah ditolak!'); window.location.href='?page=pengguna_tampil';</script>";
}

?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Data Pengguna</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pengguna</h6>
        </div>
        <div class="card-body">
            <!-- Fitur Search -->
            <div class="mb-3">
                <form method="GET" class="d-none d-sm-inline-block form-inline mr-auto my-2 my-md-0 mw-100 navbar-search">
                    <div class="input-group">
                        <input type="hidden" name="page" value="pengguna_tampil">
                        <input name="search" type="text" class="form-control bg-light border-1 small" placeholder="Search for..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                            aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                            <a href="?page=pengguna_tampil" class="btn btn-secondary">
                                <i class="fas fa-sync-alt fa-sm" style="vertical-align: middle; margin-top: 5px;"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tombol Tambah & Ekspor -->
            <div class="mb-3">
                <a href="?page=tambah_pengguna" class="btn btn-primary btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                    </span>
                    <span class="text">Tambah Data</span>
                </a>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="min-width: 1800px; white-space: nowrap;">
                    <thead class="text-center">
                        <tr>
                            <th class="align-middle" style="width: 5%;" onclick="sortTable(0)">No. <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(1)">Username <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(2)">Email <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(3)">No. Telp <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(4)">Role <i class="fa fa-sort"></i></th>
                            <th class="align-middle" onclick="sortTable(5)">Status <i class="fa fa-sort"></i></th>
                            <th class="align-middle">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($pengguna) > 0): ?>
                            <?php $no = 1;
                            foreach ($pengguna as $row): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['username']); ?></td>
                                    <td><?= htmlspecialchars($row['email']); ?></td>
                                    <td><?= htmlspecialchars($row['no_telp']); ?></td>
                                    <td><?= htmlspecialchars($row['role']); ?></td>
                                    <td><?= htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <?php if (($role == 'admin' || $role == 'superadmin') && $row['status'] == 'diajukan'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="terima_id" value="<?= $row['id_user']; ?>">
                                                    <button type="submit" class="btn btn-success btn-icon-split btn-sm">
                                                        <span class="icon text-white-50"><i class="fa fa-check" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                        <span class="text">Terima</span>
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="tolak_id" value="<?= $row['id_user']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-icon-split btn-sm">
                                                        <span class="icon text-white-50"><i class="fa fa-undo" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Kembalikan</span>
                                                    </button>
                                                </form>

                                            <?php endif; ?>

                                            <?php if ((($row['status'] == 'diverifikasi' || $row['status'] == 'ditolak')) && $role == 'superadmin'): ?>
                                                <a href="?page=update_pengguna&id_user=<?= htmlspecialchars($row['id_user']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                                    <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Edit</span>
                                                </a>
                                                <a href="?page=delete_pengguna&id_user=<?= htmlspecialchars($row['id_user']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <span class="icon text-white-50"><i class="fa fa-trash" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Hapus</span>
                                                </a>
                                            <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center">Data tidak ditemukan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                    <!-- FOOT TABEL -->
                    <tfoot class="text-center">
                        <tr>
                            <th>No.</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Role</th>
                            <th>Status</th>
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