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
?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Profil Perusahaan</h1>
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Profil Perusahaan</h6>
        </div>
        <div class="card-body">
            <?php if ($profil): ?>
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
                    class="btn btn-danger">Hapus Profil</a>
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
    </div>
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