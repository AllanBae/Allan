<?php
$conn = new mysqli("localhost", "root", "", "db_ti6b_uas");
if ($conn->connect_error) die("Koneksi gagal: " . $conn->connect_error);

// Ambil pengembalian yang status_denda = 'terdenda' dan belum masuk denda
$pengembalian = $conn->query("SELECT no_pengembalian FROM pengembalian 
                              WHERE status_denda = 'terdenda' 
                              AND no_pengembalian NOT IN (SELECT no_pengembalian FROM denda)");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_pengembalian = $_POST['no_pengembalian'];
    $tarif = $_POST['tarif_denda'];
    $alasan = $_POST['alasan_denda'];
    $tgl = date('Y-m-d');
    $no_denda = "DN" . time();

    $conn->query("INSERT INTO denda (no_denda, tgl_denda, tarif_denda, alasan_denda, no_pengembalian) 
                  VALUES ('$no_denda', '$tgl', '$tarif', '$alasan', '$no_pengembalian')");

    // Update status denda
    $conn->query("UPDATE pengembalian SET status_denda='terdenda' WHERE no_pengembalian='$no_pengembalian'");

    echo "<script>alert('Denda ditambahkan');location.href='admin.php?page=perpus_utama&panggil=denda.php';</script>";
}
?>

<h3>Tambah Denda</h3>
<form method="POST">
    <label>Pilih No Pengembalian</label>
    <select name="no_pengembalian" class="form-control" required>
        <option value="">-- Pilih --</option>
        <?php while($r = $pengembalian->fetch_assoc()): ?>
        <option value="<?= $r['no_pengembalian'] ?>"><?= $r['no_pengembalian'] ?></option>
        <?php endwhile; ?>
    </select>
    <label>Tarif Denda</label>
    <input type="number" name="tarif_denda" required class="form-control">
    <label>Alasan Denda</label>
    <input type="text" name="alasan_denda" required class="form-control mt-1">
    <br>
    <button type="submit" class="btn btn-success">Simpan</button>
</form>
