<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "db_ti6b_uas");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses simpan data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $no_denda = $_POST['no_denda'];
    $tgl_denda = $_POST['tgl_denda'];
    $tarif_denda = $_POST['tarif_denda'];
    $alasan_denda = $_POST['alasan_denda'];
    $no_pengembalian = $_POST['no_pengembalian'];

    $sql = "INSERT INTO denda (no_denda, tgl_denda, tarif_denda, alasan_denda, no_pengembalian)
            VALUES ('$no_denda', '$tgl_denda', '$tarif_denda', '$alasan_denda', '$no_pengembalian')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Data berhasil ditambahkan!');
                window.location.href='admin.php?page=perpus_utama&panggil=tambah_denda.php';
              </script>";
        exit;
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}

// Ambil data pengembalian
$pengembalian = $conn->query("SELECT no_pengembalian, tgl_pengembalian FROM pengembalian");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Denda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fc;
        }
        .container {
            width: 500px;
            margin: 50px auto;
            background: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
        }
        h2 {
            text-align: center;
            color: #2c3e50;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            margin-top: 20px;
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bayar Denda Wak</h2>
        <form method="post">
            <label>No Denda</label>
            <input type="text" name="no_denda" required>

            <label>Tanggal Denda</label>
            <input type="date" name="tgl_denda" required>

            <label>Tarif Denda</label>
            <input type="number" name="tarif_denda" required>

            <label>Alasan Denda</label>
            <input type="text" name="alasan_denda" required>

            <label>Pilih No Pengembalian</label>
            <select name="no_pengembalian" required>
                <option value="">-- Pilih Pengembalian --</option>
                <?php while ($row = $pengembalian->fetch_assoc()): ?>
                    <option value="<?= $row['no_pengembalian']; ?>">
                        <?= $row['no_pengembalian']; ?> - <?= $row['tgl_pengembalian']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="submit" value="Simpan">
        </form>
    </div>
</body>
</html>
