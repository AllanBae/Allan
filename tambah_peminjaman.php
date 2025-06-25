<?php
// Simpan data jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];
    $id_anggota = $_POST['id_anggota'];

    $query = "INSERT INTO peminjaman (tgl_pinjam, tgl_kembali, id_anggota) 
              VALUES ('$tgl_pinjam', '$tgl_kembali', '$id_anggota')";

    if ($conn->query($query)) {
        echo "<script>alert('Data peminjaman berhasil ditambahkan');</script>";
        echo "<script>window.location.href='admin.php?page=perpus_utama&panggil=peminjaman.php';</script>";
    } else {
        echo "Gagal menyimpan data: " . $conn->error;
    }
}

// Ambil data anggota untuk dropdown
$anggota_result = $conn->query("SELECT id_anggota, nm_anggota FROM anggota ORDER BY nm_anggota ASC");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<h2 class="text-center mb-4">Tambah Data Peminjaman</h2>

<form method="POST" class="container" style="max-width: 600px;">
    <div class="mb-3">
        <label for="tgl_pinjam" class="form-label">Tanggal Pinjam</label>
        <input type="date" class="form-control" id="tgl_pinjam" name="tgl_pinjam" required>
    </div>
    <div class="mb-3">
        <label for="tgl_kembali" class="form-label">Tanggal Kembali</label>
        <input type="date" class="form-control" id="tgl_kembali" name="tgl_kembali" required>
    </div>
    <div class="mb-3">
        <label for="id_anggota" class="form-label">Pilih Nama Anggota</label>
        <select class="form-select" id="id_anggota" name="id_anggota" required>
            <option value="">-- Pilih Anggota --</option>
            <?php while ($row = $anggota_result->fetch_assoc()) : ?>
                <option value="<?= $row['id_anggota'] ?>"><?= htmlspecialchars($row['nm_anggota']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="admin.php?page=perpus_utama&panggil=peminjaman.php" class="btn btn-secondary">Batal</a>
</form>
