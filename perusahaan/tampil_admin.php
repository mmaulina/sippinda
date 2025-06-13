<?php
try {
    $role = $_SESSION['role'];
    $database = new Database();
    $pdo = $database->getConnection(); // Dapatkan koneksi PDO

    $query = "SELECT * FROM profil WHERE 1=1"; // supaya WHERE nya fleksibel
    $params = [];

    if (isset($_GET['id_profil'])) {
        $query .= " AND id_profil = :id_profil";
        $params[':id_profil'] = $_GET['id_profil'];
    }

    if (!empty($_GET['keyword'])) {
        $keyword = "%" . $_GET['keyword'] . "%";
        $query .= " AND nama_perusahaan LIKE :keyword"; //fitur cari berdasarkan nama_perusahaan
        $params[':keyword'] = $keyword;
    }

    $jenisUsaha = $_GET['jenis_usaha'] ?? '';
    if (!empty($jenisUsaha)) {
        $query .= " AND jenis_usaha = :jenis_usaha";
        $params[':jenis_usaha'] = $jenisUsaha;
    }

    $kabupaten = $_GET['kabupaten'] ?? '';
    if (!empty($kabupaten)) {
        $query .= " AND kabupaten = :kabupaten";
        $params[':kabupaten'] = $kabupaten;
    }

    // Ambil daftar jenis usaha dan kabupaten/kota untuk dropdown filter
    $jenisUsahaStmt = $pdo->query("SELECT DISTINCT jenis_usaha FROM profil ORDER BY jenis_usaha");
    $jenisUsahaList = $jenisUsahaStmt->fetchAll(PDO::FETCH_COLUMN);

    $kabupatenStmt = $pdo->query("SELECT DISTINCT kabupaten FROM profil ORDER BY kabupaten");
    $kabupatenList = $kabupatenStmt->fetchAll(PDO::FETCH_COLUMN);

    $query .= " ORDER BY FIELD(status, 'diajukan', 'dikembalikan', 'diterima')";

    // Eksekusi Query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Proses Persetujuan/Tolak
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['terima_id'])) {
            $id = $_POST['terima_id'];
            $updateQuery = "UPDATE profil SET status = 'diterima' WHERE id_profil = :id_profil";
        } elseif (isset($_POST['tolak_laporan'])) {
            $id = $_POST['id_profil'];
            $keterangan = $_POST['keterangan'];
            $updateQuery = "UPDATE profil SET status = 'dikembalikan', keterangan = :keterangan WHERE id_profil = :id_profil";
        }

        if (isset($updateQuery)) {
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':id_profil', $id, PDO::PARAM_INT);
            if (isset($keterangan)) {
                $updateStmt->bindParam(':keterangan', $keterangan, PDO::PARAM_STR);
            }
            $updateStmt->execute();
            echo "<meta http-equiv='refresh' content='0; url=?page=profil_admin'>";
            exit;
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="container mt-4">
    <h3 class="text-center mb-3"><i class="fas fa-bolt" style="color: #ffc107;"></i>Data Profil Perusahaan<i class="fas fa-bolt" style="color: #ffc107;"></i></h3>
    <hr>
    <div class="card shadow" style="overflow-x: auto; max-height: calc(100vh - 150px); overflow-y: auto;">
        <div class="card-body">
            <!-- Fitur pencarian dan filter -->
            <form method="GET" class="mb-2">
                <input type="hidden" name="page" value="profil_admin">
                <div class="input-group mb-2">
                    <input type="text" name="keyword" class="form-control" placeholder="Cari berdasarkan nama perusahaan" value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                    <button type="submit" class="btn btn-success">Cari</button>
                    <a href="?page=profil_admin" class="btn btn-secondary">Reset</a>
                </div>
                <div class="row mb-3 align-items-end">
                    <div class="col">
                        <label for="jenis_usaha" class="form-label">Jenis Usaha</label>
                        <select name="jenis_usaha" id="jenis_usaha" class="form-select">
                            <option value="">-- Pilih Jenis Usaha --</option>
                            <?php foreach ($jenisUsahaList as $jenis): ?>
                                <option value="<?= htmlspecialchars($jenis) ?>" <?= (isset($_GET['jenis_usaha']) && $_GET['jenis_usaha'] == $jenis) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($jenis) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col">
                        <label for="kabupaten" class="form-label">Kabupaten/Kota</label>
                        <select name="kabupaten" id="kabupaten" class="form-select">
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                            <?php foreach ($kabupatenList as $kab): ?>
                                <option value="<?= htmlspecialchars($kab) ?>" <?= (isset($_GET['kabupaten']) && $_GET['kabupaten'] == $kab) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kab) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col d-flex gap-2">
                        <button type="submit" class="btn btn-success w-100">Filter</button>
                        <a href="?page=profil_admin" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>

            <!-- FILTER -->


            <!-- Tombol Tambah & Export Spreadsheet -->
            <div class="mb-3">
                <?php if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'adminbulanan' && $_SESSION['role'] !== 'adminsemester') { ?> <!-- hanya admin yang tidak bisa mengakses ini -->
                    <a href="?page=tambah_profil" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Data
                    </a>
                <?php } ?>
                <a href="?page=excel_profil" class="btn btn-success">Ekspor ke Spreadsheet</a>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                <table class="table table-bordered" style="min-width: 1800px; white-space: nowrap;">
                    <thead class="table-dark text-center align-middle">
                        <tr>
                            <th rowspan="2" style="width: 5%;" onclick="sortTable(0)">No. <i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(1)">Nama Perusahaan <i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(2)">Kabupaten/Kota <i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(3)">Alamat <i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(4)">Jenis Usaha <i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(5)">Email Kantor<i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(6)">Nomor Telepon Kantor <i class="fa fa-sort"></i></th>
                            <?php if ($_SESSION['role'] == 'superadmin'): ?>
                            <th rowspan="2" onclick="sortTable(7)">No. Hp. Pimpinan<i class="fa fa-sort"></th>
                            <?php endif; ?>
                            <th rowspan="2" onclick="sortTable(8)">Tenaga Teknik <i class="fa fa-sort"></i></th>
                            <?php if ($_SESSION['role'] == 'superadmin'): ?>
                            <th rowspan="2" onclick="sortTable(9)">No Hp. Tenaga Teknik <i class="fa fa-sort"></i></th>
                            <?php endif; ?>
                            <th colspan="2">Kontak Person</th>
                            <th rowspan="2" onclick="sortTable(10)">Status <i class="fa fa-sort"></i></th>
                            <th rowspan="2" onclick="sortTable(11)">Keterangan</th>
                            <th rowspan="2">Aksi</th>
                        </tr>
                        <tr>
                            <th onclick="sortTable(12)">Nama <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(13)">No. HP <i class="fa fa-sort"></i></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (count($profiles) > 0): ?>
                            <?php $no = 1;
                            foreach ($profiles as $row): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_perusahaan']); ?></td>
                                    <td><?= htmlspecialchars($row['kabupaten']); ?></td>
                                    <td><?= htmlspecialchars($row['alamat']); ?></td>
                                    <td><?= htmlspecialchars($row['jenis_usaha']); ?></td>
                                    <td><?= htmlspecialchars($row['email']); ?></td>
                                    <td><?= htmlspecialchars($row['no_telp_kantor']); ?></td>
                                    <?php if ($_SESSION['role'] == 'superadmin'): ?>
                                    <td><?= htmlspecialchars($row['no_hp_pimpinan']); ?></td>
                                    <?php endif; ?>
                                    <td><?= htmlspecialchars($row['tenaga_teknik']); ?></td>
                                    <?php if ($_SESSION['role'] == 'superadmin'): ?>
                                    <td><?= htmlspecialchars($row['no_hp_teknik']); ?></td>
                                    <?php endif; ?>
                                    <td><?= htmlspecialchars($row['nama']); ?></td>
                                    <td><?= htmlspecialchars($row['no_hp']); ?></td>
                                    <td class="text-center">
                                        <?php
                                        // Menampilkan status dengan ikon dan warna
                                        if ($row['status'] == 'diajukan') {
                                            echo '<i class="fas fa-clock" style="color: yellow;"></i> Diajukan';
                                        } elseif ($row['status'] == 'diterima') {
                                            echo '<i class="fas fa-check" style="color: green;"></i> Diterima';
                                        } elseif ($row['status'] == 'dikembalikan') {
                                            echo '<i class="fas fa-times" style="color: red;"></i> dikembalikan';
                                        } else {
                                            echo '<span class="text-muted">Status tidak diketahui</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                    <td class="text-center">
                                        <?php if ($row['status'] == 'diajukan'): ?>
                                            <!-- Tombol Terima menggunakan POST -->
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="terima_id" value="<?php echo $row['id_profil']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">Terima</button>
                                            </form>
                                            <!-- Tombol Tolak dengan Modal -->
                                            <a href="" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalTolak<?php echo $row['id_profil']; ?>">Kembalikan</a>
                                        <?php endif; ?>

                                        <?php if (($row['status'] == 'diterima' || $row['status'] == 'dikembalikan')&& $role=='superadmin'): ?>
                                            <a href="?page=update_profil_admin&id_profil=<?= htmlspecialchars($row['id_profil']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="?page=delete_profil_admin&id_profil=<?= htmlspecialchars($row['id_profil']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <div class="modal fade" id="modalTolak<?php echo $row['id_profil']; ?>" tabindex="-1" role="dialog" aria-labelledby="modalTolakLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalTolakLabel">Kembalikan</h5>
                                            </div>
                                            <form action="" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id_profil" value="<?php echo $row['id_profil']; ?>">
                                                    <div class="form-group">
                                                        <label for="keterangan<?php echo $row['id_profil']; ?>">Keterangan di kembalikan</label>
                                                        <textarea class="form-control" id="keterangan<?php echo $row['id_profil']; ?>" name="keterangan" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" name="tolak_laporan" class="btn btn-danger">Tolak</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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