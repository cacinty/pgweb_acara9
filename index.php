<?php
// Koneksi ke MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "latihan";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data dari tabel 'penduduk'
$sql = "SELECT * FROM penduduk";
$result = $conn->query($sql);

// Array untuk menyimpan data marker dan menghitung bounds peta
$markers = [];
$latitude = [];
$longitude = [];
$jumlah_penduduk = [];
$luas = [];

if ($result && $result->num_rows > 0) {
    // Menyimpan data dalam array untuk digunakan di peta
    while ($row = $result->fetch_assoc()) {
        $markers[] = [
            "kecamatan" => $row["Kecamatan"],
            "longitude" => (float)$row["Longitude"],
            "latitude" => (float)$row["Latitude"],
            "jumlah_penduduk" => (float)$row["Jumlah_Penduduk"],
            "luas" => (float)$row["Luas"]
        ];
    }
} else {
    echo "Tidak ada data ditemukan.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peta Kecamatan</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        /* Gaya untuk peta */
        #map {
            width: 100%;
            height: 625px;
        }

        /* Gaya untuk tabel agar mengambang di bawah header */
        .floating-table {
            position: absolute;
            top: 100px; 
            right: 20px;
            width: 400px;
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            display: none; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #DDA0DD;
            color: white;
        }

        /* Gaya untuk header */
        header {
            background-color: #DDA0DD;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        /* Gaya untuk footer */
        footer {
            background-color: #DDA0DD;
            color: white;
            padding: 8px;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        /* Gaya untuk tombol toggle */
        .toggle-button {
            position: absolute;
            top: 70px; 
            right: 20px;
            background-color: #DDA0DD;
            color: white;
            border: none;
            padding: 8px 10px;
            cursor: pointer;
            border-radius: 8px;
            z-index: 1001;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        Peta Penduduk Kabupaten Sleman
    </header>

    <!-- Tombol Toggle -->
    <button class="toggle-button" onclick="toggleTable()"> Tabel</button>

    <!-- Tabel mengambang di bawah header -->
    <div class="floating-table" id="floatingTable">
        <table>
            <thead>
                <tr>
                    <th>Kecamatan</th>
                    <th>Longitude</th>
                    <th>Latitude</th>
                    <th>Jumlah Penduduk</th>
                    <th>Luas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($markers as $marker): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($marker['kecamatan']); ?></td>
                        <td><?php echo htmlspecialchars($marker['longitude']); ?></td>
                        <td><?php echo htmlspecialchars($marker['latitude']); ?></td>
                        <td><?php echo htmlspecialchars($marker['jumlah_penduduk']); ?></td>
                        <td><?php echo htmlspecialchars($marker['luas']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Peta -->
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Inisialisasi peta
        var map = L.map("map").setView([-7.773369, 110.374584], 11);

        // Tile layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // mengambil data marker dari variabel PHP $markers dan mengonversinya menjadi format JSON
        var markers = <?php echo json_encode($markers); ?>;

        // Melakukan iterasi melalui setiap marker dalam array markers,yang berisi koordinat latitude dan longitude.
        markers.forEach(function(marker) {
            var latLng = [marker.latitude, marker.longitude];
            L.marker(latLng)
                .addTo(map)
                .bindPopup("<b>" + marker.kecamatan + "</b>");
        });

        // Fungsi toggle untuk menampilkan/menyembunyikan tabel
        function toggleTable() {
            //Mengambil elemen tabel dengan ID floatingTable.
            var table = document.getElementById("floatingTable");
            if (table.style.display === "none") {
                table.style.display = "block";
            } else {
                table.style.display = "none";
            }
        }
    </script>

    <!-- Footer -->
    <footer>
        © 2024 Peta Lokasi Penduduk
    </footer>

</body>

</html>
