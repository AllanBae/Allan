<?php
// Koneksi database
$conn = new mysqli("localhost", "root", "", "db_ti6b_uas");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Proses hapus jika ada parameter ?hapus=
if (isset($_GET['hapus'])) {
    $id = $conn->real_escape_string($_GET['hapus']);

    // Hapus dulu data dari tabel terkait (jika ada relasi ke tabel lain seperti 'bisa')
    $conn->query("DELETE FROM bisa WHERE no_pengembalian = '$id'");

    // Hapus dari tabel utama pengembalian
    $conn->query("DELETE FROM pengembalian WHERE no_pengembalian = '$id'");

    echo "<script>
        alert('Data pengembalian berhasil dihapus!');
        window.location.href='admin.php?page=perpus_utama&panggil=pengembalian.php';
    </script>";
    exit;
}

// Query pengembalian + anggota
$sql = "SELECT p.*, a.nm_anggota, a.id_anggota FROM pengembalian p
        LEFT JOIN peminjaman pm ON p.no_peminjaman = pm.no_peminjaman
        LEFT JOIN anggota a ON pm.id_anggota = a.id_anggota
        ORDER BY p.no_pengembalian DESC";
$result = $conn->query($sql);
?>

<h3 class="text-center mb-4">Data Pengembalian</h3>
<a href="admin.php?page=perpus_utama&panggil=tambah_pengembalian.php" class="btn btn-primary mb-3">Tambah Pengembalian</a>

<table class="table table-bordered table-striped">
  <thead class="table-dark text-center">
    <tr>
      <th>No</th>
      <th>No Pengembalian</th>
      <th>ID Anggota</th>
      <th>Nama Anggota</th>
      <th>Tanggal Pengembalian</th>
      <th>Status Denda</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; while($row = $result->fetch_assoc()): ?>
    <tr>
      <td class="text-center"><?= $no++ ?></td>
      <td class="text-center"><?= $row['no_pengembalian'] ?></td>
      <td class="text-center"><?= $row['id_anggota'] ?></td>
      <td><?= $row['nm_anggota'] ?></td>
      <td class="text-center"><?= $row['tgl_pengembalian'] ?></td>
      <td class="text-center text-<?= strtolower($row['status_denda']) == 'denda' ? 'danger' : 'success' ?>">
        <?= ucfirst($row['status_denda']) ?>
      </td>
      <td class="text-center">
        <a href="admin.php?page=perpus_utama&panggil=pengembalian.php&hapus=<?= $row['no_pengembalian'] ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>