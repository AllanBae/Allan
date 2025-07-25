<?php
// koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function generateNoPeminjaman($conn) {
        $result = $conn->query("SELECT MAX(CAST(SUBSTRING(no_peminjaman, 3) AS UNSIGNED)) AS max_num FROM peminjaman");
        $row = $result->fetch_assoc();
        $next = (int)$row['max_num'] + 1;
        return "PJ" . $next;

    }

    $tgl_pinjam = $_POST['tgl_pinjam'];
    $durasi = $_POST['durasi_kembali'];
    $id_anggota = $_POST['id_anggota'];
    $no_peminjaman = generateNoPeminjaman($conn);
    $tgl_kembali = date('Y-m-d', strtotime($tgl_pinjam . " +$durasi days"));

    $query = "INSERT INTO peminjaman (no_peminjaman, tgl_peminjaman, tgl_harus_kembali, id_anggota)
              VALUES ('$no_peminjaman', '$tgl_pinjam', '$tgl_kembali', '$id_anggota')";

    if ($conn->query($query)) {
        foreach ($_POST['id_buku'] as $i => $id_buku) {
            $jumlah = (int)$_POST['jumlah'][$i];

            $copy = $conn->query("SELECT no_copy_buku FROM copy_buku 
                                  WHERE id_buku = '$id_buku' AND status_buku = 'tersedia'
                                  LIMIT $jumlah");

            if ($copy->num_rows < $jumlah) {
                echo "<script>alert('Copy buku ID $id_buku tidak cukup tersedia'); window.history.back();</script>";
                exit;
            }

            while ($c = $copy->fetch_assoc()) {
                $no_copy = $c['no_copy_buku'];
                $conn->query("UPDATE copy_buku SET status_buku = 'dipinjam' WHERE no_copy_buku = '$no_copy'");
                $conn->query("INSERT INTO dapat (no_peminjaman, no_copy_buku, jml_pinjam) 
                              VALUES ('$no_peminjaman', '$no_copy', 1)");
            }
        }

        echo "<script>alert('Peminjaman berhasil disimpan!'); window.location.href='admin.php?page=perpus_utama&panggil=peminjaman.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan data: " . $conn->error . "');</script>";
    }
}

// Ambil data anggota dan buku
$anggota_result = $conn->query("SELECT id_anggota, nm_anggota FROM anggota ORDER BY nm_anggota ASC");
$buku_result = $conn->query("SELECT buku.id_buku, judul_buku,
    (SELECT COUNT(*) FROM copy_buku WHERE id_buku = buku.id_buku AND status_buku = 'tersedia') AS stok
FROM buku ORDER BY judul_buku ASC");

$bookData = [];
while ($b = $buku_result->fetch_assoc()) {
    $bookData[$b['id_buku']] = [
        'judul' => $b['judul_buku'],
        'stok' => (int)$b['stok']
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Peminjaman Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .judul-buku select {
            width: 100%;
        }

        select.judul-buku {
            width: 100% !important;
            max-width: 100%;
        }

    </style>
</head>
<body class="p-3">
<h3>Tambah Peminjaman Buku</h3>

<form method="POST" class="container">
<div class="mb-3" style="width: 200px;">
    <label class="form-label">Tanggal Peminjaman</label>
    <input type="date" name="tgl_pinjam" id="tgl_pinjam" class="form-control form-control-sm" required>
</div>

<div class="mb-3" style="width: 200px;">
    <label class="form-label">Durasi Pinjam (hari)</label>
    <input list="durasi_list" name="durasi_kembali" id="durasi_kembali" class="form-control form-control-sm" min="1" required placeholder="Masukkan durasi (hari)">
    <datalist id="durasi_list">
        <option value="3">3 Hari</option>
        <option value="7">1 Minggu</option>
        <option value="14">2 Minggu</option>
        <option value="30">1 Bulan</option>
    </datalist>
</div>

<div class="mb-3" style="width: 200px;">
    <label class="form-label">Tanggal Pengembalian</label>
    <input type="date" name="tgl_kembali" id="tgl_kembali" class="form-control form-control-sm" readonly>
</div>


    <div class="mb-3 w-auto">
        <label class="form-label">Nama Anggota</label>
        <select name="id_anggota" class="form-select" required>
            <option value="">-- Pilih Anggota --</option>
            <?php while ($a = $anggota_result->fetch_assoc()) : ?>
                <option value="<?= htmlspecialchars($a['id_anggota']) ?>"><?= htmlspecialchars($a['nm_anggota']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3 bg-light p-3 rounded">
    <table class="table table-bordered" id="tabel_buku">
        <thead>
            <tr class="table-secondary text-center">
                <th style="width: 40px;">No</th>
                <th style="width: 140px;">ID Buku</th>
                <th>Judul Buku</th>
                <th style="width: 110px;">Jumlah</th>
                <th style="width: 60px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>
                    <input type="text" name="id_buku[]" class="form-control form-control-sm id-buku" readonly required>
                </td>
                <td>
                    <select class="form-select form-select-sm judul-buku" required>
                        <option value="">PILIH</option>
                        <?php foreach ($bookData as $id => $data): ?>
                            <option value="<?= $id ?>"><?= $data['judul'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="number" name="jumlah[]" class="form-control form-control-sm jumlah-buku" min="1" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-hapus">-</button>
                </td>
            </tr>
        </tbody>
    </table>
        <button type="button" id="btn-tambah" class="btn btn-success btn-sm">Tambah Baris</button>
    </div>

    <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
    <a href="admin.php?page=perpus_utama&panggil=peminjaman.php" class="btn btn-secondary">Batal</a>
</form>

<script>
const bookData = <?= json_encode($bookData) ?>;
const tableBody = document.querySelector("#tabel_buku tbody");
const btnTambah = document.getElementById("btn-tambah");

// Fungsi untuk mendapatkan semua ID buku yang sudah dipilih
function getSelectedIds() {
    return Array.from(document.querySelectorAll('.judul-buku'))
        .map(select => select.value)
        .filter(val => val !== '');
}

// Fungsi untuk update semua opsi dropdown berdasarkan yang sudah dipilih
function updateDropdownOptions() {
    const selectedIds = getSelectedIds();

    document.querySelectorAll('.judul-buku').forEach(select => {
        const currentValue = select.value;
        select.querySelectorAll('option').forEach(opt => {
            if (opt.value === '') return;
            if (opt.value === currentValue) {
                opt.hidden = false;
            } else {
                opt.hidden = selectedIds.includes(opt.value);
            }
        });
    });
}

// Tambah baris baru
btnTambah.addEventListener("click", () => {
    const rowCount = tableBody.rows.length + 1;
    const row = tableBody.insertRow();
    row.innerHTML = `
        <td class="text-center">${rowCount}</td>
        <td>
            <input type="text" name="id_buku[]" class="form-control form-control-sm id-buku" readonly required>
        </td>
        <td>
            <select class="form-select form-select-sm judul-buku" required>
                <option value="">PILIH</option>
                ${Object.entries(bookData).map(([id, data]) => `<option value="${id}">${data.judul}</option>`).join('')}
            </select>
        </td>
        <td>
            <input type="number" name="jumlah[]" class="form-control form-control-sm jumlah-buku" min="1" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm btn-hapus">-</button>
        </td>
    `;

    updateDropdownOptions();
});

// Sinkronisasi judul ke ID dan kontrol stok
tableBody.addEventListener("change", (e) => {
    if (e.target.classList.contains("judul-buku")) {
        const row = e.target.closest("tr");
        const judulSelect = row.querySelector(".judul-buku");
        const idInput = row.querySelector(".id-buku");
        const jumlahInput = row.querySelector(".jumlah-buku");

        const id = judulSelect.value;
        idInput.value = id;

        const stok = bookData[id]?.stok || 0;
        jumlahInput.max = stok;
        jumlahInput.placeholder = stok > 0 ? "max: " + stok : "stok habis";
        jumlahInput.disabled = stok === 0;

        updateDropdownOptions();
    }
});

// Hapus baris
tableBody.addEventListener("click", (e) => {
    if (e.target.classList.contains("btn-hapus")) {
        e.target.closest("tr").remove();
        updateNomor();
        updateDropdownOptions();
    }
});

// Update nomor urut
function updateNomor() {
    [...tableBody.rows].forEach((row, i) => {
        row.cells[0].textContent = i + 1;
    });
}
</script>

<script>
document.getElementById('durasi_kembali').addEventListener('change', updateTanggalKembali);
document.getElementById('tgl_pinjam').addEventListener('change', updateTanggalKembali);

function updateTanggalKembali() {
    const tglPinjam = document.getElementById('tgl_pinjam').value;
    const durasi = parseInt(document.getElementById('durasi_kembali').value);
    const tglKembaliInput = document.getElementById('tgl_kembali');

    if (tglPinjam && durasi) {
        const tgl = new Date(tglPinjam);
        tgl.setDate(tgl.getDate() + durasi);
        tglKembaliInput.value = tgl.toISOString().split('T')[0]; // Format YYYY-MM-DD
    } else {
        tglKembaliInput.value = '';
    }
}
</script>


</body>
</html>