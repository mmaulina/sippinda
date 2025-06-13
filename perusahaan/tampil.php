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
?>

<div class="container mt-5">
    <div class="card shadow" style="overflow-x: auto; max-height: calc(100vh - 150px); overflow-y: auto;">
        <div class="card-body">
            <h2> Informasi Profil Perusahaan </h2>

            <?php if ($profil): ?>
                <table class="table table-bordered">
                    <tr>
                        <th>Nama Perusahaan</th>
                        <td><?php echo htmlspecialchars($profil['nama_perusahaan']); ?></td>
                    </tr>
                    <tr>
                        <th>Kabupaten</th>
                        <td><?php echo htmlspecialchars($profil['kabupaten']); ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td><?php echo htmlspecialchars($profil['alamat']); ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Usaha</th>
                        <td><?php echo htmlspecialchars($profil['jenis_usaha']); ?></td>
                    </tr>
                    <tr>
                        <th>Nomor Telepon Kantor</th>
                        <td><?php echo htmlspecialchars($profil['no_telp_kantor']); ?></td>
                    </tr>
                    <tr>
                        <th>No Hp Pimpinan</th>
                        <td><?php echo htmlspecialchars($profil['no_hp_pimpinan']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($profil['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Tenaga Teknik</th>
                        <td><?php echo htmlspecialchars($profil['tenaga_teknik']); ?></td>
                    </tr>
                    <tr>
                    <tr>
                        <th>No Hp Tenaga Teknik</th>
                        <td><?php echo htmlspecialchars($profil['no_hp_teknik']); ?></td>
                    </tr>
                    <tr>
                        <th>Nama Admin</th>
                        <td><?php echo htmlspecialchars($profil['nama']); ?></td>
                    </tr>
                    <tr>
                        <th>Nomor HP</th>
                        <td><?php echo htmlspecialchars($profil['no_hp']); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                            // Menampilkan status dengan ikon dan warna
                            if ($profil['status'] == 'diajukan') {
                                echo '<i class="fas fa-clock" style="color: yellow;"></i> Diajukan';
                            } elseif ($profil['status'] == 'diterima') {
                                echo '<i class="fas fa-check" style="color: green;"></i> Diterima';
                            } elseif ($profil['status'] == 'ditolak') {
                                echo '<i class="fas fa-times" style="color: red;"></i> Ditolak';
                            } else {
                                echo '<span class="text-muted">Status tidak diketahui</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td><?php echo htmlspecialchars($profil['keterangan']); ?></td>
                    </tr>
                </table>
                <a href="?page=update_profil&id_user=<?php echo $_SESSION['id_user']; ?>" class="btn btn-warning">Update Profil</a>
                <a href="?page=delete_profil&id_user=<?= $_SESSION['id_user']; ?>"
                    onclick="return confirmHapus(event)"
                    class="btn btn-danger">
                    Hapus Profil
                </a>
            <?php else: ?>
                <p>Profil perusahaan belum diisi.</p>
                <a href="?page=tambah_profil&id_user=<?php echo $_SESSION['id_user']; ?>" class="btn btn-primary">Isi Profil</a>
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