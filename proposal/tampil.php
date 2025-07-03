<?php

$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];
$database = new Database();

$pdo = $database->getConnection(); // Dapatkan koneksi PDO

// Pencarian keyword
$keyword = '';
if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
}

$query = "SELECT * FROM proposal WHERE 1=1";
$params = [];
if ($role != 'admin' && $role != 'superadmin') {
    $query .= " AND id_user = :id_user";
    $params[':id_user'] = $id_user;
}

// Filter keyword pencarian
if (!empty($keyword)) {
    $query .= " AND nama_perusahaan LIKE :keyword";
    $params[':keyword'] = '%' . $keyword . '%';
}

// Filter
$tahun = $_GET['tahun'] ?? '';
if (!empty($tahun)) {
    $query .= " AND tahun = :tahun";
    $params[':tahun'] = $tahun;
}

$status = $_GET['status'] ?? '';
if (!empty($status)) {
    $query .= " AND status = :status";
    $params[':status'] = $status;
}

// Ambil daftar untuk dropdown filter
$tahunStmt = $conn->query("SELECT DISTINCT tahun FROM proposal ORDER BY tahun");
$tahunList = $tahunStmt->fetchAll(PDO::FETCH_COLUMN);

$statusStmt = $conn->query("SELECT DISTINCT status FROM proposal ORDER BY status");
$statusList = $statusStmt->fetchAll(PDO::FETCH_COLUMN);

// Eksekusi Query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$proposal = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process approval/rejection
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['terima_id'])) {
        $id = $_POST['terima_id'];
        $updateQuery = "UPDATE proposal SET status = 'diterima' WHERE id = :id";
    } elseif (isset($_POST['tolak_laporan'])) {
        $id = $_POST['id'];
        $keterangan = $_POST['keterangan'];
        $updateQuery = "UPDATE proposal SET status = 'dikembalikan', keterangan = :keterangan WHERE id = :id";
    }

    if (isset($updateQuery)) {
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
        if (isset($keterangan)) {
            $updateStmt->bindParam(':keterangan', $keterangan, PDO::PARAM_STR);
        }
        $updateStmt->execute();
        echo "<meta http-equiv='refresh' content='0; url=?page=proposal_tampil'>";
        exit;
    }
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Proposal</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Proposal Kegiatan</h6>
        </div>
        <div class="card-body">
            <!-- Fitur Search -->
            <?php if ($role != 'umum'): ?>
                <div class="mb-4">
                    <form method="get" action="" class="row gx-2 gy-2 align-items-center">
                        <input type="hidden" name="page" value="proposal_tampil">

                        <!-- Search -->
                        <div class="col-auto flex-grow-1">
                            <div class="input-group">
                                <input type="text" name="keyword" class="form-control bg-light border-1 small" placeholder="Cari nama perusahaan..." aria-label="Search" aria-describedby="button-search">
                                <button class="btn btn-primary" type="submit" id="button-search">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                                <a href="?page=proposal_tampil" class="btn btn-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-sync-alt fa-sm"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Filter -->
                        <div class="col-auto">
                            <div class="input-group">
                                <select name="tahun" class="form-select" style="min-width: 230px;">
                                    <option value="">-- Pilih Tahun --</option>
                                    <?php foreach ($tahunList as $tahun): ?>
                                        <option value="<?= htmlspecialchars($tahun) ?>" <?= (isset($_GET['tahun']) && $_GET['tahun'] == $tahun) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($tahun) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="status" class="form-select" style="min-width: 170px;">
                                    <option value="">-- Pilih Status --</option>
                                    <?php foreach ($statusList as $status): ?>
                                        <option value="<?= htmlspecialchars($status) ?>" <?= (isset($_GET['status']) && $_GET['status'] == $status) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($status) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter fa-sm"></i>
                                </button>
                                <a href="?page=proposal_tampil" class="btn btn-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-sync-alt fa-sm"></i>
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            <?php endif; ?>

            <!-- Tombol Tambah & Ekspor -->
            <div class="mb-3">
                <?php if ($role == 'umum'): ?>
                    <a href="?page=tambah_proposal" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                        </span>
                        <span class="text">Tambah Data</span>
                    </a>
                <?php endif; ?>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="min-width: 1800px; white-space: nowrap;">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 5%;" onclick="sortTable(0)">No. <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(1)">Nama Perusahaan (Asosiasi)<i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2)">Tahun Usulan Kegiatan <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3)">Upload Berkas <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">status <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5)">Keterangan <i class="fa fa-sort"></i></th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($proposal) > 0): ?>
                            <?php
                            $groupedData = [];
                            foreach ($proposal as $row) {
                                if (isset($row['id_user'])) {
                                    $groupedData[$row['id_user']][] = $row;
                                } else {
                                    echo "id_user tidak ditemukan untuk baris: " . json_encode($row);
                                }
                            }

                            foreach ($groupedData as $id_user => $rows):
                                $no = 1;
                                $nama_perusahaan = htmlspecialchars($rows[0]['nama_perusahaan']);
                                echo "<tr><td colspan='16' class='fw-bold bg-light'>NAMA PERUSAHAAN = ($nama_perusahaan)</td></tr>";
                                foreach ($rows as $row):
                            ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['nama_perusahaan']); ?></td>
                                        <td><?= htmlspecialchars($row['tahun']); ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($row['upload'])) : ?>
                                                <a href="<?= htmlspecialchars($row['upload']); ?>" target="_blank" class="btn btn-sm btn-dark">
                                                    <i class="fas fa-file-alt"></i> Lihat
                                                </a>
                                            <?php else : ?>
                                                <span class="text-danger">Tidak ada file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            switch ($row['status']) {
                                                case 'diajukan':
                                                    echo '<i class="fas fa-clock" style="color: yellow;"></i> Diajukan';
                                                    break;
                                                case 'diterima':
                                                    echo '<i class="fas fa-check" style="color: green;"></i> Diterima';
                                                    break;
                                                case 'dikembalikan':
                                                    echo '<i class="fas fa-times" style="color: red;"></i> Dikembalikan';
                                                    break;
                                                default:
                                                    echo '<span class="text-muted">Status tidak diketahui</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['keterangan']); ?></td>

                                        <td class="text-center">
                                            <?php if (($role == 'admin' || $role == 'superadmin') && $row['status'] == 'diajukan'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="terima_id" value="<?= $row['id']; ?>">
                                                    <button type="submit" class="btn btn-success btn-icon-split btn-sm">
                                                        <span class="icon text-white-50"><i class="fa fa-check" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                        <span class="text">Terima</span>
                                                    </button>
                                                </form>
                                                <a href="#" data-toggle="modal" data-target="#modalTolak<?= $row['id']; ?>" class="btn btn-danger btn-icon-split btn-sm">
                                                    <span class="icon text-white-50"><i class="fa fa-undo" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Kembalikan</span>
                                                </a>

                                            <?php endif; ?>

                                            <?php if (($row['status'] == 'diterima' || $row['status'] == 'dikembalikan') && $role == 'superadmin'): ?>
                                                <a href="?page=update_proposal&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                                    <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Edit</span>
                                                </a>
                                                <a href="?page=delete_proposal&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <span class="icon text-white-50"><i class="fa fa-trash" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Hapus</span>
                                                </a>
                                                <?php if ($row['status'] == 'diterima'): ?>
                                                    <a href="#" class="btn btn-primary btn-icon-split btn-sm" data-toggle="modal" data-target="#modalTolak<?= $row['id']; ?>">
                                                        <span class="icon text-white-50"><i class="fa fa-undo" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                        <span class="text">Kembalikan</span>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if ($role == 'umum' && $row['status'] == 'dikembalikan'): ?>
                                                <a href="?page=update_proposal&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                                    <span class="icon text-white-50"><i class="fa fa-pencil-alt"></i></span>
                                                    <span class="text">Edit</span>
                                                </a>
                                                <a href="?page=delete_proposal&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <span class="icon text-white-50"><i class="fa fa-trash"></i></span>
                                                    <span class="text">Hapus</span>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <!-- Modal Tolak -->
                                    <div class="modal fade" id="modalTolak<?= $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalTolakLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalTolakLabel">Kembalikan Laporan</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                        <div class="form-group">
                                                            <label for="keterangan<?= $row['id']; ?>">Keterangan</label>
                                                            <textarea class="form-control" id="keterangan<?= $row['id']; ?>" name="keterangan" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" name="tolak_laporan" class="btn btn-danger">Kembalikan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
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
                            <th>Nama Perusahaan (Asosiasi)</th>
                            <th>Tahun Usulan Kegiatan</th>
                            <th>Upload Berkas</th>
                            <th>status</th>
                            <th>Keterangan</th>
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