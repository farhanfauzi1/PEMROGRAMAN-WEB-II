<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTS PHP - Pendaftaran Mahasiswa</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 30px;
            background: #f4f4f4;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        input[type=text],
        input[type=number],
        input[type=date] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        .btn {
            background: blue;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        .btn:hover {
            background: darkblue;
        }

        .info {
            margin-top: 20px;
            padding: 10px;
            background: #eef;
        }

        .error {
            background: #ffe4e4;
            color: #8b0000;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>FORM PENDAFTARAN MAHASISWA BARU</h2>

    <form method="POST">

        <label>Tanggal Input</label>
        <input type="date" name="tgl_input" required>

        <label>Kode Pendaftaran</label>
        <input
            type="text"
            name="kode"
            maxlength="20"
            placeholder="Contoh : A2"
            required
        >

        <label>Nama Pendaftar</label>
        <input type="text" name="nama" maxlength="100" required>

        <label>Jenis Kelamin</label><br>
        <label>
            <input type="radio" name="jk" value="Laki-Laki" required>
            Laki-Laki
        </label>
        <label>
            <input type="radio" name="jk" value="Perempuan">
            Perempuan
        </label>
        <br><br>

        <label>Asal Sekolah</label>
        <input type="text" name="asal_sekolah" maxlength="150" required>

        <label>Pekerjaan Orang Tua</label>
        <input type="text" name="pekerjaan_ortu" maxlength="100" required>

        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir" maxlength="100" required>

        <label>Tanggal Lahir</label>
        <input type="date" name="tgl_lahir" required>

        <label>Matematika</label>
        <input
            type="number"
            name="mat"
            min="0"
            max="100"
            step="0.01"
            required
        >

        <label>Bahasa Inggris</label>
        <input
            type="number"
            name="bing"
            min="0"
            max="100"
            step="0.01"
            required
        >

        <label>Umum</label>
        <input
            type="number"
            name="umum"
            min="0"
            max="100"
            step="0.01"
            required
        >

        <button type="submit" name="simpan" class="btn">
            SIMPAN
        </button>

    </form>

    <?php
    // Mengamankan data saat ditampilkan ke HTML
    function aman($nilai)
    {
        return htmlspecialchars(
            (string) $nilai,
            ENT_QUOTES,
            'UTF-8'
        );
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $koneksi = null;
    $data_mahasiswa = [];
    $pesan = "";
    $error = "";

    // Koneksi dibuat di luar proses simpan,
    // supaya data lama bisa dibaca saat halaman dibuka.
    try {
        $koneksi = new mysqli(
            "localhost",
            "root",
            "",
            "db_pendaftaran"
        );

        $koneksi->set_charset("utf8mb4");
    } catch (mysqli_sql_exception $e) {
        error_log($e->getMessage());
        $error = "Koneksi gagal. Pastikan MySQL aktif dan database tersedia.";
    }

    // Proses menyimpan pendaftar baru
    if ($koneksi !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {

        $tgl_input = trim($_POST['tgl_input'] ?? '');
        $kode = strtoupper(trim($_POST['kode'] ?? ''));
        $nama = trim($_POST['nama'] ?? '');
        $jk = $_POST['jk'] ?? '';
        $asal_sekolah = trim($_POST['asal_sekolah'] ?? '');
        $pekerjaan_ortu = trim($_POST['pekerjaan_ortu'] ?? '');
        $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
        $tgl_lahir = trim($_POST['tgl_lahir'] ?? '');

        $mat = $_POST['mat'] ?? '';
        $bing = $_POST['bing'] ?? '';
        $umum = $_POST['umum'] ?? '';

        // Validasi kolom wajib
        foreach ([
            $tgl_input,
            $kode,
            $nama,
            $jk,
            $asal_sekolah,
            $pekerjaan_ortu,
            $tempat_lahir,
            $tgl_lahir
        ] as $isi) {
            if ($isi === '') {
                $error = "Semua kolom wajib diisi.";
                break;
            }
        }

        // Validasi jenis kelamin
        if (
            $error === "" &&
            !in_array($jk, ['Laki-Laki', 'Perempuan'], true)
        ) {
            $error = "Jenis kelamin tidak valid.";
        }

        // Validasi tanggal
        if ($error === "") {
            foreach ([$tgl_input, $tgl_lahir] as $tanggal) {
                $cek = DateTime::createFromFormat('!Y-m-d', $tanggal);

                if (!$cek || $cek->format('Y-m-d') !== $tanggal) {
                    $error = "Tanggal tidak valid.";
                    break;
                }
            }
        }

        // Validasi nilai sebelum dihitung
        if ($error === "") {
            foreach ([$mat, $bing, $umum] as $nilai) {
                if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
                    $error = "Nilai harus berupa angka antara 0 sampai 100.";
                    break;
                }
            }
        }

        if ($error === "") {
            $mat = (float) $mat;
            $bing = (float) $bing;
            $umum = (float) $umum;

            // Hitung rata-rata
            $rata = ($mat + $bing + $umum) / 3;

            // Menentukan keterangan
            if ($rata >= 70) {
                $keterangan = "Lulus";
            } elseif ($rata >= 60) {
                $keterangan = "Cadangan";
            } else {
                $keterangan = "Tidak Lulus";
            }

            // Menentukan tempat tes berdasarkan kode
            $kode_awal = substr($kode, 0, 1);

            if ($kode_awal === "A") {
                $tempat_tes = "Gedung A";
            } elseif ($kode_awal === "B") {
                $tempat_tes = "Gedung B";
            } elseif ($kode_awal === "V") {
                $tempat_tes = "Viktor";
            } else {
                $tempat_tes = "Tidak Diketahui";
            }

            try {
                $sql = "INSERT INTO mahasiswa (
                    tgl_input,
                    kode,
                    nama,
                    jk,
                    asal_sekolah,
                    pekerjaan_ortu,
                    tempat_lahir,
                    tgl_lahir,
                    mat,
                    bing,
                    umum,
                    rata,
                    keterangan,
                    tempat_tes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $koneksi->prepare($sql);

                $stmt->bind_param(
                    "ssssssssddddss",
                    $tgl_input,
                    $kode,
                    $nama,
                    $jk,
                    $asal_sekolah,
                    $pekerjaan_ortu,
                    $tempat_lahir,
                    $tgl_lahir,
                    $mat,
                    $bing,
                    $umum,
                    $rata,
                    $keterangan,
                    $tempat_tes
                );

                $stmt->execute();
                $stmt->close();

                $pesan = "Data pendaftaran berhasil disimpan ke database.";

            } catch (mysqli_sql_exception $e) {
                error_log($e->getMessage());
                $error = "Data gagal disimpan. Periksa isi form dan tabel database.";
            }
        }
    }

    // Mengambil seluruh data dari database,
    // bukan hanya data yang baru dimasukkan.
    $data_berhasil_dimuat = false;

    if ($koneksi !== null) {
        try {
            $hasil = $koneksi->query(
                "SELECT * FROM mahasiswa ORDER BY id DESC"
            );

            while ($data = $hasil->fetch_assoc()) {
                $data_mahasiswa[] = $data;
            }

            $hasil->free();
            $data_berhasil_dimuat = true;

        } catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            $error .= " Data gagal dimuat. Pastikan tabel mahasiswa tersedia.";
        }

        $koneksi->close();
    }

    // Menghitung rekap seluruh pendaftar
    $jumlah_pendaftar = count($data_mahasiswa);
    $jumlah_lulus = 0;
    $jumlah_cadangan = 0;
    $jumlah_tidak_lulus = 0;

    foreach ($data_mahasiswa as $data) {
        if ($data['keterangan'] === "Lulus") {
            $jumlah_lulus++;
        } elseif ($data['keterangan'] === "Cadangan") {
            $jumlah_cadangan++;
        } elseif ($data['keterangan'] === "Tidak Lulus") {
            $jumlah_tidak_lulus++;
        }
    }
    ?>

    <?php if ($pesan !== ""): ?>
        <div class="info">
            <?= aman($pesan) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="info error">
            <?= aman($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($data_berhasil_dimuat): ?>
        <div class="table-wrapper">
            <table>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Tempat, Tgl Lahir</th>
                    <th>Asal Sekolah</th>
                    <th>Pekerjaan Ortu</th>
                    <th>Tempat Tes</th>
                    <th>MAT</th>
                    <th>B. Inggris</th>
                    <th>Umum</th>
                    <th>Rata-rata</th>
                    <th>Keterangan</th>
                </tr>

                <?php if ($jumlah_pendaftar === 0): ?>
                    <tr>
                        <td colspan="12">
                            Belum ada data pendaftar.
                        </td>
                    </tr>
                <?php else: ?>

                    <?php foreach ($data_mahasiswa as $data): ?>
                        <tr>
                            <td><?= aman($data['kode']) ?></td>
                            <td><?= aman($data['nama']) ?></td>
                            <td><?= aman($data['jk']) ?></td>
                            <td>
                                <?= aman($data['tempat_lahir']) ?>,
                                <?= aman($data['tgl_lahir']) ?>
                            </td>
                            <td><?= aman($data['asal_sekolah']) ?></td>
                            <td><?= aman($data['pekerjaan_ortu']) ?></td>
                            <td><?= aman($data['tempat_tes']) ?></td>
                            <td><?= aman($data['mat']) ?></td>
                            <td><?= aman($data['bing']) ?></td>
                            <td><?= aman($data['umum']) ?></td>
                            <td>
                                <?= number_format((float) $data['rata'], 2) ?>
                            </td>
                            <td><?= aman($data['keterangan']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>
            </table>
        </div>

        <div class="info">
            <h3>Rekap Data:</h3>
            <p>Jumlah Pendaftar:
                <b><?= $jumlah_pendaftar ?></b>
            </p>
            <p>Jumlah Peserta Lulus:
                <b><?= $jumlah_lulus ?></b>
            </p>
            <p>Jumlah Peserta Cadangan:
                <b><?= $jumlah_cadangan ?></b>
            </p>
            <p>Jumlah Tidak Lulus:
                <b><?= $jumlah_tidak_lulus ?></b>
            </p>
        </div>
    <?php endif; ?>

    <div class="info">
        <h3>Keterangan:</h3>

        <p><b>Kode Tempat Tes:</b></p>
        <ul>
            <li>A = Gedung A</li>
            <li>B = Gedung B</li>
            <li>V = Viktor</li>
        </ul>

        <p><b>Hasil Seleksi:</b></p>
        <ul>
            <li>Rata-rata ≥ 70 = Lulus</li>
            <li>Rata-rata ≥ 60 dan &lt; 70 = Cadangan</li>
            <li>Rata-rata &lt; 60 = Tidak Lulus</li>
        </ul>
    </div>

</div>

</body>
</html>