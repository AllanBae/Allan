<?php
// Koneksi database
$conn = new mysqli("localhost", "root", "", "db_ti6b_uas");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Query pengembalian + anggota + status
$sql = "SELECT p.*, a.nm_anggota, a.id_anggota FROM pengembalian p
        LEFT JOIN peminjaman pm ON p.no_peminjaman = pm.no_peminjaman
        LEFT JOIN anggota a ON pm.id_anggota = a.id_anggota
        ORDER BY p.no_pengembalian DESC";
$result = $conn->query($sql);
?>

<h3>Data Pengembalian</h3>
<a href="admin.php?page=perpus_utama&panggil=tambah_pengembalian.php" class="btn btn-primary mb-2">+ Tambah</a>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>No</th>
      <th>No Pengembalian</th>
      <th>ID Anggota</th>
      <th>Nama Anggota</th>
      <th>Tanggal Pengembalian</th>
      <th>Status Denda</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1; while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $row['no_pengembalian'] ?></td>
      <td><?= $row['id_anggota'] ?></td>
      <td><?= $row['nm_anggota'] ?></td>
      <td><?= $row['tgl_pengembalian'] ?></td>
      <td><?= ucfirst($row['status_denda']) ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
