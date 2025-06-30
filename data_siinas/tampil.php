<?php

$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];
$database = new Database();

$pdo = $database->getConnection(); // Dapatkan koneksi PDO

$query = "SELECT * FROM data_sinas WHERE 1=1";
$params = [];
if ($role != 'admin' && $role != 'superadmin') {
    $query .= " AND id_user = :id_user";
    $params[':id_user'] = $id_user;
}

// Eksekusi Query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data_sinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process approval/rejection
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['terima_id'])) {
        $id = $_POST['terima_id'];
        $updateQuery = "UPDATE data_sinas SET status = 'diterima' WHERE id = :id";
    } elseif (isset($_POST['tolak_laporan'])) {
        $id = $_POST['id'];
        $keterangan = $_POST['keterangan'];
        $updateQuery = "UPDATE data_sinas SET status = 'dikembalikan', keterangan = :keterangan WHERE id = :id";
    }

    if (isset($updateQuery)) {
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':id', $id, PDO::PARAM_INT);
        if (isset($keterangan)) {
            $updateStmt->bindParam(':keterangan', $keterangan, PDO::PARAM_STR);
        }
        $updateStmt->execute();
        echo "<meta http-equiv='refresh' content='0; url=?page=data_siinas_tampil'>";
        exit;
    }
}

// $chartData = [
//     "Triwulan I" => ["sudah" => 0, "belum" => 0, "perusahaan" => []],
//     "Triwulan II" => ["sudah" => 0, "belum" => 0, "perusahaan" => []],
//     "Triwulan III" => ["sudah" => 0, "belum" => 0, "perusahaan" => []],
//     "Triwulan IV" => ["sudah" => 0, "belum" => 0, "perusahaan" => []],
// ];

$chartDataByYear = [];
$chartDataGlobal = [
    "Triwulan I" => ["sudah" => 0, "belum" => 0, "perusahaan_sudah" => [], "perusahaan_belum" => []],
    "Triwulan II" => ["sudah" => 0, "belum" => 0, "perusahaan_sudah" => [], "perusahaan_belum" => []],
    "Triwulan III" => ["sudah" => 0, "belum" => 0, "perusahaan_sudah" => [], "perusahaan_belum" => []],
    "Triwulan IV" => ["sudah" => 0, "belum" => 0, "perusahaan_sudah" => [], "perusahaan_belum" => []],
];

// 1. Ambil semua perusahaan
$queryPerusahaan = "SELECT id_user, nama_perusahaan FROM profil_perusahaan";
$perusahaanStmt = $pdo->prepare($queryPerusahaan);
$perusahaanStmt->execute();
$perusahaanList = $perusahaanStmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Buat index data_sinas agar lebih cepat dicek
$dataMap = []; // Format: [id_user][tahun][Triwulan X] = true/false
$tahunList = [];

foreach ($data_sinas as $row) {
    preg_match('/(Triwulan\s+[IVX]+)\s+(\d{4})/', $row['triwulan'], $matches);
    $tw = $matches[1] ?? null;
    $tahun = $matches[2] ?? null;

    if (!$tw || !$tahun) continue;

    $upload = !empty($row['upload']);
    $dataMap[$row['id_user']][$tahun][$tw] = $upload;

    // Simpan tahun ke daftar
    $tahunList[$tahun] = true;
}

// Ambil semua tahun unik dari data
$tahunList = array_keys($tahunList);
sort($tahunList); // urutkan tahun

// 3. Loop setiap perusahaan dan cek tiap tahun dan triwulan
$chartDataByYear = [];
$chartDataGlobal = [
    "Triwulan I" => ["sudah" => 0, "belum" => 0, "perusahaan_sudah" => [], "perusahaan_belum" => []],
    "Triwulan II" => ["sudah" => 0, "belum" => 0, "perusahaan_sudah" => [], "perusahaan_belum" => []],
    "Triwulan III" => ["sudah" => 0, "belum" => 0, "perusahaan_sudah" => [], "perusahaan_belum" => []],
    "Triwulan IV" => ["sudah" => 0, "belum" => 0, "perusahaan_sudah" => [], "perusahaan_belum" => []],
];

// 1. Ambil perusahaan sudah kamu lakukan
// 2. Buat index data_sinas juga sudah oke

foreach ($perusahaanList as $perusahaan) {
    $id_user = $perusahaan['id_user'];
    $nama_perusahaan = $perusahaan['nama_perusahaan'];

    foreach ($tahunList as $tahun) {
        foreach (["Triwulan I", "Triwulan II", "Triwulan III", "Triwulan IV"] as $tw) {
            $sudahUpload = $dataMap[$id_user][$tahun][$tw] ?? false;

            if (!isset($chartDataByYear[$tahun][$tw])) {
                $chartDataByYear[$tahun][$tw] = [
                    'sudah' => 0,
                    'belum' => 0,
                    'perusahaan_sudah' => [],
                    'perusahaan_belum' => []
                ];
            }

            if ($sudahUpload) {
                $chartDataByYear[$tahun][$tw]['sudah']++;
                $chartDataByYear[$tahun][$tw]['perusahaan_sudah'][] = $nama_perusahaan;

                $chartDataGlobal[$tw]['sudah']++;
                $chartDataGlobal[$tw]['perusahaan_sudah'][] = $nama_perusahaan;
            } else {
                $chartDataByYear[$tahun][$tw]['belum']++;
                $chartDataByYear[$tahun][$tw]['perusahaan_belum'][] = $nama_perusahaan;

                $chartDataGlobal[$tw]['belum']++;
                $chartDataGlobal[$tw]['perusahaan_belum'][] = $nama_perusahaan;
            }
        }
    }
}

// Buat unik
foreach ($chartDataGlobal as $tw => &$data) {
    $data['perusahaan_sudah'] = array_unique($data['perusahaan_sudah']);
    $data['perusahaan_belum'] = array_unique($data['perusahaan_belum']);
}




// 4. Hitung total perusahaan unik per triwulan global
// foreach ($chartData as $tw => &$data) {
//     $data['jumlah_perusahaan'] = count(array_unique(array_merge(
//         $data['perusahaan_sudah'],
//         $data['perusahaan_belum']
//     )));
// }

// 5. Hitung total perusahaan unik per tahun dan triwulan
foreach ($chartDataByYear as $tahun => &$triwulans) {
    foreach ($triwulans as $tw => &$data) {
        $data['jumlah_perusahaan'] = count(array_unique(array_merge(
            $data['perusahaan_sudah'],
            $data['perusahaan_belum']
        )));
    }
}


?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Data SIINas</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Sistem Informasi Industri Nasional</h6>
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
                <a href="?page=tambah_data_siinas" class="btn btn-primary btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                    </span>
                    <span class="text">Tambah Data</span>
                </a>
                <a href="?page=excel_profil" class="btn btn-success btn-icon-split btn-sm">
                    <span class="icon text-white-50">
                        <i class="fas fa-download" style="vertical-align: middle; margin-top: 5px;"></i>
                    </span>
                    <span class="text">Export Excel</span>
                </a>
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-x: auto; overflow-y: auto;">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0" style="min-width: 1000; white-space: nowrap;">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 5%;" onclick="sortTable(0)">No. <i class="fa fa-sort"></i></th>
                            <th>Upload Berkas</th>
                            <th onclick="sortTable(1)">Triwulan <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(2)">Tahun <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(3)">Jenis Pelaporan <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(4)">Status <i class="fa fa-sort"></i></th>
                            <th onclick="sortTable(5)">Keterangan <i class="fa fa-sort"></i></th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($data_sinas) > 0): ?>
                            <?php
                            $groupedData = [];
                            foreach ($data_sinas as $row) {
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
                                        <td class="text-center">
                                            <?php if (!empty($row['upload'])) : ?>
                                                <a href="<?= htmlspecialchars($row['upload']); ?>" target="_blank" class="btn btn-sm btn-dark">
                                                    <i class="fas fa-file-alt"></i> Lihat
                                                </a>
                                            <?php else : ?>
                                                <span class="text-danger">Tidak ada file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['triwulan']); ?></td>
                                        <td><?= htmlspecialchars($row['tahun']); ?></td>
                                        <td><?= htmlspecialchars($row['jenis_pelaporan']); ?></td>
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
                                                <a href="?page=update_data_siinas&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                                    <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Edit</span>
                                                </a>
                                                <a href="?page=delete_data_siinas&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                                                <a href="?page=update_data_siinas&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                                                    <span class="icon text-white-50"><i class="fa fa-pencil-alt"></i></span>
                                                    <span class="text">Edit</span>
                                                </a>
                                                <a href="?page=delete_data_siinas&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                            <th>Upload Berkas</th>
                            <th>Triwulan</th>
                            <th>Tahun</th>
                            <th>Jenis Pelaporan</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php if ($role == 'superadmin'): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Grafik Upload SIINas</h6>
            </div>
            <div class="card-body">
                <?php
                $tahunList = array_keys($chartDataByYear);
                sort($tahunList);
                ?>
                <div class="form-group mb-2">
                    <label for="filterTahun">Filter Tahun:</label>
                    <select id="filterTahun" class="form-control form-control-sm" style="width: 200px; display: inline-block;">
                        <?php foreach ($tahunList as $th): ?>
                            <option value="<?= $th ?>" <?= $th == date('Y') ? 'selected' : '' ?>><?= $th ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="filterTriwulan">Filter Triwulan:</label>
                    <select id="filterTriwulan" class="form-control form-control-sm" style="width: 200px; display: inline-block;">
                        <option value="ALL">Semua Triwulan</option>
                        <option value="Triwulan I">Triwulan I</option>
                        <option value="Triwulan II">Triwulan II</option>
                        <option value="Triwulan III">Triwulan III</option>
                        <option value="Triwulan IV">Triwulan IV</option>
                    </select>
                </div>

                <!-- Chart -->
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="chartSudah"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="chartBelum"></canvas>
                    </div>
                </div>

                <!-- List Global atau Per Triwulan -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6 class="text-primary" id="labelSudah">Perusahaan Sudah Upload</h6>
                        <ul id="listSudah" class="list-group list-group-flush small"></ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-danger" id="labelBelum">Perusahaan Belum Upload</h6>
                        <ul id="listBelum" class="list-group list-group-flush small"></ul>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const chartDataByYear = <?= json_encode($chartDataByYear) ?>;
            const triwulanLabels = ["Triwulan I", "Triwulan II", "Triwulan III", "Triwulan IV"];

            const tahunSelect = document.getElementById("filterTahun");
            const triwulanSelect = document.getElementById("filterTriwulan");

            const ctxSudah = document.getElementById("chartSudah").getContext("2d");
            const ctxBelum = document.getElementById("chartBelum").getContext("2d");

            const chartSudah = new Chart(ctxSudah, {
                type: 'bar',
                data: {
                    labels: triwulanLabels,
                    datasets: [{
                        label: 'Sudah Upload',
                        data: [],
                        backgroundColor: '#4e73df'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            const chartBelum = new Chart(ctxBelum, {
                type: 'bar',
                data: {
                    labels: triwulanLabels,
                    datasets: [{
                        label: 'Belum Upload',
                        data: [],
                        backgroundColor: '#e74a3b'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            function updateCharts() {
                const tahun = tahunSelect.value;
                const triwulan = triwulanSelect.value;
                const data = chartDataByYear[tahun] || {};

                let sudah = [],
                    belum = [];
                let listSudah = [],
                    listBelum = [];

                if (triwulan === "ALL") {
                    triwulanLabels.forEach(tw => {
                        sudah.push(data[tw]?.sudah || 0);
                        belum.push(data[tw]?.belum || 0);

                        if (data[tw]?.perusahaan_sudah) listSudah.push(...data[tw].perusahaan_sudah);
                        if (data[tw]?.perusahaan_belum) listBelum.push(...data[tw].perusahaan_belum);
                    });
                } else {
                    sudah = triwulanLabels.map(tw => tw === triwulan ? (data[tw]?.sudah || 0) : 0);
                    belum = triwulanLabels.map(tw => tw === triwulan ? (data[tw]?.belum || 0) : 0);

                    listSudah = data[triwulan]?.perusahaan_sudah || [];
                    listBelum = data[triwulan]?.perusahaan_belum || [];
                }

                chartSudah.data.datasets[0].data = sudah;
                chartBelum.data.datasets[0].data = belum;
                chartSudah.update();
                chartBelum.update();

                const listSudahElem = document.getElementById("listSudah");
                const listBelumElem = document.getElementById("listBelum");
                const labelSudah = document.getElementById("labelSudah");
                const labelBelum = document.getElementById("labelBelum");

                listSudahElem.innerHTML = "";
                listBelumElem.innerHTML = "";

                // Ubah judul list sesuai mode
                if (triwulan === "ALL") {
                    labelSudah.textContent = "Perusahaan Sudah Upload (Semua Triwulan)";
                    labelBelum.textContent = "Perusahaan Belum Upload (Semua Triwulan)";
                } else {
                    labelSudah.textContent = `Perusahaan Sudah Upload (${triwulan})`;
                    labelBelum.textContent = `Perusahaan Belum Upload (${triwulan})`;
                }

                [...new Set(listSudah)].forEach(p => {
                    const li = document.createElement("li");
                    li.textContent = p;
                    li.className = "list-group-item";
                    listSudahElem.appendChild(li);
                });

                [...new Set(listBelum)].forEach(p => {
                    const li = document.createElement("li");
                    li.textContent = p;
                    li.className = "list-group-item";
                    listBelumElem.appendChild(li);
                });
            }

            updateCharts();
            tahunSelect.addEventListener("change", updateCharts);
            triwulanSelect.addEventListener("change", updateCharts);
        </script>
    <?php endif; ?>