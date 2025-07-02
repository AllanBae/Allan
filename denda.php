<?php
$conn = new mysqli("localhost", "root", "", "db_ti6b_uas");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

$denda = $conn->query("SELECT d.*, a.nm_anggota FROM denda d 
    LEFT JOIN pengembalian p ON d.no_pengembalian = p.no_pengembalian
    LEFT JOIN peminjaman pm ON p.no_peminjaman = pm.no_peminjaman
    LEFT JOIN anggota a ON pm.id_anggota = a.id_anggota");
?>

<h3>Data Denda</h3>
<a href="admin.php?page=perpus_utama&panggil=tambah_denda.php" class="btn btn-primary mb-2">+ Tambah Denda</a>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>No</th>
      <th>No Denda</th>
      <th>Nama Anggota</th>
      <th>Tarif</th>
      <th>Alasan</th>
      <th>Tanggal Denda</th>
    </tr>
  </thead>
  <tbody>
    <?php $no=1; while($row = $denda->fetch_assoc()): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= $row['no_denda'] ?></td>
      <td><?= $row['nm_anggota'] ?></td>
      <td><?= $row['tarif_denda'] ?></td>
      <td><?= $row['alasan_denda'] ?></td>
      <td><?= $row['tgl_denda'] ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
