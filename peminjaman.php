<?php

// Proses hapus jika ada parameter ?hapus
if (isset($_GET['hapus'])) {
    $idHapus = $conn->real_escape_string($_GET['hapus']);
    $conn->query("DELETE FROM peminjaman WHERE id_peminjaman = '$idHapus'");
    echo "<script>window.location.href='admin.php?page=perpus_utama&panggil=peminjaman.php';</script>";
}

// Ambil data peminjaman
$sql = "SELECT peminjaman.*, pengunjung.nama_pengunjung 
        FROM peminjaman 
        LEFT JOIN pengunjung ON peminjaman.id_pengunjung = pengunjung.id_pengunjung 
        ORDER BY id_peminjaman ASC";

$result = $conn->query($sql);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<h2 class="text-center">Data Peminjaman</h2>
<a href="admin.php?page=perpus_utama&panggil=tambah_peminjaman.php" class="btn btn-primary mb-3">Tambah Peminjaman</a>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>ID Pengunjung</th>
            <th>Nama Pengunjung</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['tgl_pinjam'] ?></td>
                <td><?= $row['tgl_kembali'] ?></td>
                <td><?= $row['id_pengunjung'] ?></td>
                <td><?= htmlspecialchars($row['nama_pengunjung']) ?></td>
                <td>
                    <a href="admin.php?page=perpus_utama&panggil=peminjaman.php&hapus=<?= $row['id_peminjaman'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
