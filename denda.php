<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "db_ti6b_uas");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari tabel denda
$sql = "SELECT * FROM denda";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Denda</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px 12px; border: 1px solid #000; }
        th { background-color: #f2f2f2; }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .top-bar {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h2>Data Denda</h2>

    <div class="top-bar">
        <form action="admin.php" method="get" style="display: inline;">
            <input type="hidden" name="page" value="perpus_utama">
            <input type="hidden" name="panggil" value="tambah_denda.php">
            <button type="submit" class="btn btn-primary">Tambah Denda</button>
        </form>
    </div>

    <table>
        <tr>
            <th>No Denda</th>
            <th>Tanggal Denda</th>
            <th>Tarif Denda</th>
            <th>Alasan Denda</th>
            <th>No Pengembalian</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['no_denda']}</td>
                    <td>{$row['tgl_denda']}</td>
                    <td>{$row['tarif_denda']}</td>
                    <td>{$row['alasan_denda']}</td>
                    <td>{$row['no_pengembalian']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>Tidak ada data</td></tr>";
        }
        ?>
    </table>
</body>
</html>
