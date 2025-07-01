<?php

$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];
$database = new Database();

$pdo = $database->getConnection(); // Dapatkan koneksi PDO

$query = "SELECT * FROM perizinan WHERE 1=1";
$params = [];
if ($role != 'admin' && $role != 'superadmin') {
    $query .= " AND id_user = :id_user";
    $params[':id_user'] = $id_user;
}

// Eksekusi Query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$perizinan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process approval/rejection
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['terima_id'])) {
        $id = $_POST['terima_id'];
        $updateQuery = "UPDATE perizinan SET verifikasi = 'diterima',  tgl_verif = NOW() WHERE id = :id";
    } elseif (isset($_POST['tolak_laporan'])) {
        $id = $_POST['id'];
        $keterangan = $_POST['keterangan'];
        $updateQuery = "UPDATE perizinan SET verifikasi = 'dikembalikan', keterangan = :keterangan, tgl_verif = NULL WHERE id = :id";
    }

    if (isset($updateQuery)) {
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
        if (isset($keterangan)) {
            $updateStmt->bindParam(':keterangan', $keterangan, PDO::PARAM_STR);
        }
        $updateStmt->execute();
        echo "<meta http-equiv='refresh' content='0; url=?page=perizinan_tampil'>";
        exit;
    }
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Perizinan Perusahaan</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Perizinan</h6>
        </div>
        <div class="card-body">
            <!-- Fitur Search -->
            <?php if ($role != 'umum'): ?>
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
            <?php endif; ?>

            <!-- Tombol Tambah & Ekspor -->
            <div class="mb-3">
                <?php if ($role == 'umum'): ?>
                <a href="?page=tambah_perizinan" class="btn btn-primary btn-icon-split btn-sm">
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
                            <th onclick="sortTable(2)">Jenis Laporan <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3)">No Izin <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Tanggal Dokumen <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Berkas <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Verifikasi <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Keterangan <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Tanggal di Verifikasi <i class="fa fa-sort"></i></th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($perizinan) > 0): ?>
                            <?php
                            $groupedData = [];
                            foreach ($perizinan as $row) {
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
                                        <td><?= htmlspecialchars($row['jenis_laporan']); ?></td>
                                        <td><?= htmlspecialchars($row['no_izin']); ?></td>
                                        <td><?= htmlspecialchars($row['tgl_dokumen']); ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($row['upload_berkas'])) : ?>
                                                <a href="<?= htmlspecialchars($row['upload_berkas']); ?>" target="_blank" class="btn btn-sm btn-dark">
                                                    <i class="fas fa-file-alt"></i> Lihat
                                                </a>
                                            <?php else : ?>
                                                <span class="text-danger">Tidak ada file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            switch ($row['verifikasi']) {
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
                                        <td><?= !empty($row['tgl_verif']) ? htmlspecialchars(date('d-m-Y', strtotime($row['tgl_verif']))) : '-' ?></td>
                                        <td class="text-center">
                                            <?php if (($role == 'admin' || $role == 'superadmin') && $row['verifikasi'] == 'diajukan'): ?>
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

                                            <?php if (($row['verifikasi'] == 'diterima' || $row['verifikasi'] == 'dikembalikan') && $role == 'superadmin'): ?>
                                                <a href="?page=update_perizinan&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                                    <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Edit</span>
                                                </a>
                                                <a href="?page=delete_perizinan&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <span class="icon text-white-50"><i class="fa fa-trash" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Hapus</span>
                                                </a>
                                                <?php if ($row['verifikasi'] == 'diterima'): ?>
                                                    <a href="#" class="btn btn-primary btn-icon-split btn-sm" data-toggle="modal" data-target="#modalTolak<?= $row['id']; ?>">
                                                        <span class="icon text-white-50"><i class="fa fa-undo" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                        <span class="text">Kembalikan</span>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if ($role == 'umum' && $row['verifikasi'] == 'dikembalikan'): ?>
                                                <a href="?page=update_perizinan&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                                    <span class="icon text-white-50"><i class="fa fa-pencil-alt"></i></span>
                                                    <span class="text">Edit</span>
                                                </a>
                                                <a href="?page=delete_perizinan&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                            <th>Jenis Laporan</th>
                            <th>No Izin</th>
                            <th>Tanggal Dokumen</th>
                            <th>Berkas</th>
                            <th>Verifikasi</th>
                            <th>Keterangan</th>
                            <th>Tanggal di Verifikasi</th>
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