<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='login/login.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];

$database = new Database();
$pdo = $database->getConnection(); // Dapatkan koneksi PDO
// Ambil data profil perusahaan dari database menggunakan PDO
$sql = "SELECT * FROM profil_perusahaan WHERE id_user = :id_user";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmt->execute();
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

$sql2 = "SELECT id, bidang FROM bidang_perusahaan WHERE id_user = :id_user";
$stmt2 = $pdo->prepare($sql2);
$stmt2->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmt2->execute();
$bidanglist = $stmt2->fetchAll(PDO::FETCH_ASSOC); // Ambil semua nilai kolom 'bidang'

$sql3 = "SELECT id, periode_laporan, nilai_investasi_mesin, modal_kerja, investasi_tanpa_tanah_bangunan, status, menggunakan_maklon, menyediakan_maklon FROM data_umum WHERE id_user = :id_user";
$stmt3 = $pdo->prepare($sql3);
$stmt3->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmt3->execute();
$data_umum = $stmt3->fetch(PDO::FETCH_ASSOC);

$sql4 = "SELECT id, nama_penanda_tangan_laporan, jabatan, nama_perusahaan_induk FROM data_khusus WHERE id_user = :id_user";
$stmt4 = $pdo->prepare($sql4);
$stmt4->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmt4->execute();
$data_khusus = $stmt4->fetch(PDO::FETCH_ASSOC);

$sql5 = "SELECT id, pemerintah_pusat, pemerintah_daerah, swasta_nasional, asing, negara_asal, nilai_investasi_tanah, nilai_investasi_bangunan FROM investasi WHERE id_user = :id_user";
$stmt5 = $pdo->prepare($sql5);
$stmt5->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmt5->execute();
$investasi = $stmt5->fetch(PDO::FETCH_ASSOC);

$sql6 = "SELECT id, laki_laki_pro_tetap, perempuan_pro_tetap, laki_laki_pro_tidak_tetap, perempuan_pro_tidak_tetap, laki_laki_lainnya, perempuan_lainnya, sd, smp, sma, d1_d2_d3, s1_d4, s2, s3 FROM pekerja WHERE id_user = :id_user";
$stmt6 = $pdo->prepare($sql6);
$stmt6->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmt6->execute();
$pekerja = $stmt6->fetch(PDO::FETCH_ASSOC);
?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Profil Perusahaan</h1>
    <?php if ($profil): ?>
        <!-- INFORMASI PROFIL PERUSAHAAN -->
        <div class="card shadow mb-3">
            <a href="#collapseCardProfil" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardProfil">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Profil Perusahaan</h6>
            </a>
            <div class="collapse show" id="collapseCardProfil">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Nama Perusahaan</th>
                            <td><?php echo htmlspecialchars($profil['nama_perusahaan']); ?></td>
                        </tr>
                        <tr>
                            <th>Alamat Kantor</th>
                            <td><?php echo htmlspecialchars($profil['alamat_kantor']); ?></td>
                        </tr>
                        <tr>
                            <th>Alamat Pabrik</th>
                            <td><?php echo htmlspecialchars($profil['alamat_pabrik']); ?></td>
                        </tr>
                        <tr>
                            <th>Bidang Perusahaan</th>
                            <td>
                                <!-- Tombol tambah diletakkan di luar ul agar valid -->
                                <a href="?page=tambah_bidang" class="btn btn-primary btn-icon-split btn-sm mb-3">
                                    <span class="icon text-white-50">
                                        <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                                    </span>
                                    <span class="text">Tambah Bidang Perusahaan</span>
                                </a>

                                <!-- List bidang -->
                                <ul class="mb-0 ps-3">
                                    <?php foreach ($bidanglist as $row): ?>
                                        <li class="d-flex justify-content-between align-items-center my-2">
                                            <span>• <?= htmlspecialchars($row['bidang']); ?></span>
                                            <span>
                                                <a href="?page=edit_bidang&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-icon-split btn-sm me-1">
                                                    <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Edit</span>
                                                </a>
                                                <a href="?page=hapus_bidang&id=<?= htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    <span class="icon text-white-50"><i class="fa fa-trash" style="vertical-align: middle; margin-top: 5px;"></i></span>
                                                    <span class="text">Hapus</span>
                                                </a>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <th>Nomor Telepon</th>
                            <td><?php echo htmlspecialchars($profil['no_telpon']); ?></td>
                        </tr>
                        <tr>
                            <th>Nomor Fax</th> <!-- diperbaiki -->
                            <td><?php echo htmlspecialchars($profil['no_fax']); ?></td>
                        </tr>
                        <tr>
                            <th>Jenis Lokasi Pabrik</th>
                            <td><?php echo htmlspecialchars($profil['jenis_lokasi_pabrik']); ?></td>
                        </tr>
                        <tr>
                            <th>Jenis Kuisioner</th>
                            <td><?php echo htmlspecialchars($profil['jenis_kuisioner']); ?></td>
                        </tr>
                    </table>
                    <a href="?page=update_profil&id_user=<?php echo $_SESSION['id_user']; ?>" class="btn btn-warning">Update Profil</a>
                    <a href="?page=delete_profil&id_user=<?= $_SESSION['id_user']; ?>"
                        onclick="return confirmHapus(event)"
                        class="btn btn-danger">Hapus Profil
                    </a>
                </div>
            </div>
        </div>

        <!-- DATA UMUM -->
        <div class="card shadow mb-3">
            <a href="#collapseCardUmum" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardUmum">
                <h6 class="m-0 font-weight-bold text-primary">Data Umum</h6>
            </a>
            <div class="collapse" id="collapseCardUmum">
                <div class="card-body">
                    <a href="?page=tambah_data_umum" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                        </span>
                        <span class="text">Tambah Data</span>
                    </a>
                    <table class="table table-bordered my-2">
                        <tr>
                            <th>Periode Laporan</th>
                            <td><?php echo htmlspecialchars($data_umum['nama_penanda_tangan'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Nilai Investasi Mesin</th>
                            <td><?php echo htmlspecialchars($data_umum['nilai_investasi_mesin'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Nilai Investasi Lainnya</th>
                            <td><?php echo htmlspecialchars($data_umum['nilai_investasi_lainnya'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Modal Kerja</th>
                            <td><?php echo htmlspecialchars($data_umum['modal_kerja'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Investasi Tanpa Tanah dan Bangunan</th>
                            <td><?php echo htmlspecialchars($data_umum['investasi_tanpa_tanah_bangunan'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><?php echo htmlspecialchars($data_umum['status'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Menggunakan Maklon</th>
                            <td><?php echo htmlspecialchars($data_umum['menggunakan_maklon'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Menyediakan Maklon</th>
                            <td><?php echo htmlspecialchars($data_umum['menyediakan_maklon'] ?? '-'); ?></td>
                        </tr>
                    </table>
                    <a href="?page=update_data_umum&id=<?= htmlspecialchars($data_umum['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                        <span class="text">Edit</span>
                    </a>
                    <a href="?page=delete_data_umum&id=<?= htmlspecialchars($data_umum['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                        <span class="icon text-white-50"><i class="fa fa-trash" style="vertical-align: middle; margin-top: 5px;"></i></span>
                        <span class="text">Hapus</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- DATA KHUSUS -->
        <div class="card shadow mb-3">
            <a href="#collapseCardKhusus" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardKhusus">
                <h6 class="m-0 font-weight-bold text-primary">Data Khusus</h6>
            </a>
            <div class="collapse" id="collapseCardKhusus">
                <div class="card-body">
                    <a href="?page=tambah_data_khusus" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                        </span>
                        <span class="text">Tambah Data</span>
                    </a>
                    <table class="table table-bordered my-2">
                        <tr>
                            <th>Nama Penanda Tangan Laporan</th>
                            <td><?php echo htmlspecialchars($data_khusus['nama_penanda_tangan_laporan'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td><?php echo htmlspecialchars($data_khusus['jabatan'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th>Nama Perusahaan Induk</th>
                            <td><?php echo htmlspecialchars($data_khusus['nama_perusahaan_induk'] ?? '-'); ?></td>
                        </tr>
                    </table>
                    <a href="?page=update_data_khusus&id=<?= htmlspecialchars($data_khusus['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                        <span class="text">Edit</span>
                    </a>
                    <a href="?page=delete_data_khusus&id=<?= htmlspecialchars($data_khusus['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                        <span class="icon text-white-50"><i class="fa fa-trash" style="vertical-align: middle; margin-top: 5px;"></i></span>
                        <span class="text">Hapus</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- INVESTASI -->
        <div class="card shadow mb-3">
            <a href="#collapseCardInvestasi" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardInvestasi">
                <h6 class="m-0 font-weight-bold text-primary">Investasi</h6>
            </a>
            <div class="collapse" id="collapseCardInvestasi">
                <div class="card-body">
                    <a href="?page=tambah_investasi" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                        </span>
                        <span class="text">Tambah Data</span>
                    </a>
                    <table class="table table-bordered my-2">
                        <tbody>
                            <tr class="table-secondary">
                                <th colspan="2" class="text-center">Presentasi Kepemilikan</th>
                            </tr>
                            <tr>
                                <th>Pemerintah Pusat</th>
                                <td><?php echo htmlspecialchars($investasi['pemerintah_pusat'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>Pemerintah Daerah</th>
                                <td><?php echo htmlspecialchars($investasi['pemerintah_daerah'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>Swasta Nasional</th>
                                <td><?php echo htmlspecialchars($investasi['swasta_nasional'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>Asing</th>
                                <td><?php echo htmlspecialchars($investasi['asing'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>Negara Asal</th>
                                <td><?php echo htmlspecialchars($investasi['negara_asal'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>Nilai Investasi Tanah</th>
                                <td><?php echo htmlspecialchars($investasi['nilai_investasi_tanah'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <th>Nilai Investasi Bangunan</th>
                                <td><?php echo htmlspecialchars($investasi['nilai_investasi_bangunan'] ?? '-'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="?page=update_investasi&id=<?= htmlspecialchars($investasi['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                        <span class="text">Edit</span>
                    </a>
                    <a href="?page=delete_investasi&id=<?= htmlspecialchars($investasi['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                        <span class="icon text-white-50"><i class="fa fa-trash" style="vertical-align: middle; margin-top: 5px;"></i></span>
                        <span class="text">Hapus</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- PEKERJA PER HARI -->
        <div class="card shadow mb-3">
            <a href="#collapseCardPekerja" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardPekerja">
                <h6 class="m-0 font-weight-bold text-primary">Pekerja per Hari</h6>
            </a>
            <div class="collapse" id="collapseCardPekerja">
                <div class="card-body">
                    <a href="?page=tambah_pekerja" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus" style="vertical-align: middle; margin-top: 5px;"></i>
                        </span>
                        <span class="text">Tambah Data</span>
                    </a>
                    <table class="table table-bordered my-2">
                        <tbody>
                            <tr class="table-secondary">
                                <th colspan="2" class="text-center">Produksi Tetap</th>
                            </tr>
                            <tr>
                                <th>Laki-Laki</th>
                                <td><?php echo htmlspecialchars($pekerja['laki_laki_pro_tetap'] ?? '-'); ?> orang</td>
                            </tr>
                            <tr>
                                <th>Perempuan</th>
                                <td><?php echo htmlspecialchars($pekerja['perempuan_pro_tetap'] ?? '-'); ?> orang</td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr class="table-secondary">
                                <th colspan="2" class="text-center">Produksi Tidak Tetap</th>
                            </tr>
                            <tr>
                                <th>Laki-Laki</th>
                                <td><?php echo htmlspecialchars($pekerja['laki_laki_pro_tidak_tetap'] ?? '-'); ?> orang</td>
                            </tr>
                            <tr>
                                <th>Perempuan</th>
                                <td><?php echo htmlspecialchars($pekerja['perempuan_pro_tidak_tetap'] ?? '-'); ?> orang</td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr class="table-secondary">
                                <th colspan="2" class="text-center">Lainnya Tidak Tetap</th>
                            </tr>
                            <tr>
                                <th>Laki Laki</th>
                                <td><?php echo htmlspecialchars($pekerja['laki_laki_lainnya'] ?? '-'); ?> orang</td>
                            </tr>
                            <tr>
                                <th>Perempuan</th>
                                <td><?php echo htmlspecialchars($pekerja['perempuan_lainnya'] ?? '-'); ?> orang</td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr class="table-secondary">
                                <th colspan="2" class="text-center">Berdasarkan Tingkat Pendidikan</th>
                            </tr>
                            <tr>
                                <th>SD</th>
                                <td><?php echo htmlspecialchars($pekerja['sd'] ?? '-'); ?> orang</td>
                            </tr>
                            <tr>
                                <th>SMP</th>
                                <td><?php echo htmlspecialchars($pekerja['smp'] ?? '-'); ?> orang</td>
                            </tr>
                            <tr>
                                <th>SMA</th>
                                <td><?php echo htmlspecialchars($pekerja['sma'] ?? '-'); ?> orang</td>
                            </tr>
                            <tr>
                                <th>D1 - D3</th>
                                <td><?php echo htmlspecialchars($pekerja['d1_d2_d3'] ?? '-'); ?> orang</td>
                            </tr>
                            <tr>
                                <th>S1/D4</th>
                                <td><?php echo htmlspecialchars($pekerja['s1_d4'] ?? '-'); ?> orang</td>
                            </tr>
                            <tr>
                                <th>S2</th>
                                <td><?php echo htmlspecialchars($pekerja['s2'] ?? '-'); ?> orang</td>
                            </tr>
                            <tr>
                                <th>S3</th>
                                <td><?php echo htmlspecialchars($pekerja['s3'] ?? '-'); ?> orang</td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="?page=update_pekerja&id=<?= htmlspecialchars($pekerja['id']); ?>" class="btn btn-warning btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fa fa-pencil-alt" style="vertical-align: middle; margin-top: 5px;"></i></span>
                        <span class="text">Edit</span>
                    </a>
                    <a href="?page=delete_pekerja&id=<?= htmlspecialchars($pekerja['id']); ?>" class="btn btn-danger btn-icon-split btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">
                        <span class="icon text-white-50"><i class="fa fa-trash" style="vertical-align: middle; margin-top: 5px;"></i></span>
                        <span class="text">Hapus</span>
                    </a>
                </div>
            </div>
        </div>


    <?php else: ?>
        <p>Profil perusahaan belum diisi.</p>
        <a href="?page=tambah_profil&id_user=<?php echo $_SESSION['id_user']; ?>" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-info-circle"></i>
            </span>
            <span class="text">Isi Profil</span>
        </a>
    <?php endif; ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmHapus(event) {
        event.preventDefault(); // Mencegah link langsung terbuka

        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Profil Anda akan dihapus secara permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = event.target.href; // Melanjutkan ke halaman hapus_profil.php jika dikonfirmasi
            }
        });

        return false; // Menghentikan eksekusi default onclick
    }
</script>