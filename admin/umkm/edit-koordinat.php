<?php
session_start();

// Cek login
if (empty($_SESSION["admin_id"])) {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/database.php";
require_once "../../includes/functions.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get UMKM data
$umkm = null;
$stmt = mysqli_prepare($conn, "SELECT id, name, owner, latitude, longitude FROM umkm WHERE id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $umkm = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$umkm) {
    die("UMKM tidak ditemukan");
}

// Save if POST
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
    $longitude = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
    
    // Validasi
    $lat_val = floatval($latitude);
    $lng_val = floatval($longitude);
    
    if (empty($latitude) || empty($longitude)) {
        $message = 'Latitude dan Longitude harus diisi';
        $message_type = 'danger';
    } elseif ($lat_val < -90 || $lat_val > 90) {
        $message = 'Latitude harus antara -90 dan 90';
        $message_type = 'danger';
    } elseif ($lng_val < -180 || $lng_val > 180) {
        $message = 'Longitude harus antara -180 dan 180';
        $message_type = 'danger';
    } else {
        // Update database
        $stmt = mysqli_prepare($conn, "UPDATE umkm SET latitude = ?, longitude = ? WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ddi", $lat_val, $lng_val, $id);
            if (mysqli_stmt_execute($stmt)) {
                $umkm['latitude'] = $lat_val;
                $umkm['longitude'] = $lng_val;
                $message = '✓ Koordinat berhasil disimpan!';
                $message_type = 'success';
            } else {
                $message = 'Error: ' . mysqli_error($conn);
                $message_type = 'danger';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Koordinat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { background: #f8f9fa; padding: 20px 0; }
        #map { width: 100%; height: 400px; border-radius: 8px; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .card-header { background: #667eea; color: white; }
    </style>
</head>
<body>

<div class="container" style="max-width: 1000px;">
    
    <div class="mb-4">
        <a href="index.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    
    <h3 class="mb-3 fw-bold">📍 Edit Koordinat</h3>
    <p class="text-muted"><?= clean($umkm['name']) ?> - <?= clean($umkm['owner'] ?? '-') ?></p>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Peta Interaktif</div>
                <div class="card-body p-0">
                    <div id="map"></div>
                </div>
                <div class="card-footer small text-muted">
                    Klik untuk set marker atau drag marker untuk geser
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Input Koordinat</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude" class="form-control" id="lat" value="<?= number_format((float)$umkm['latitude'], 8) ?>" required>
                            <small class="text-muted">-90 hingga 90</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude" class="form-control" id="lng" value="<?= number_format((float)$umkm['longitude'], 8) ?>" required>
                            <small class="text-muted">-180 hingga 180</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </form>
                    <div class="mt-3 p-2 bg-light rounded small">
                        <strong>Sekarang:</strong><br>
                        <code><?= number_format((float)$umkm['latitude'], 8) ?><br><?= number_format((float)$umkm['longitude'], 8) ?></code>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([<?= (float)$umkm['latitude'] ?>, <?= (float)$umkm['longitude'] ?>], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    
    let marker = L.marker([<?= (float)$umkm['latitude'] ?>, <?= (float)$umkm['longitude'] ?>], { draggable: true }).addTo(map);
    
    const lat = document.getElementById('lat');
    const lng = document.getElementById('lng');
    
    marker.on('dragend', () => {
        const pos = marker.getLatLng();
        lat.value = pos.lat.toFixed(8);
        lng.value = pos.lng.toFixed(8);
    });
    
    map.on('click', (e) => {
        marker.setLatLng(e.latlng);
        lat.value = e.latlng.lat.toFixed(8);
        lng.value = e.latlng.lng.toFixed(8);
    });
    
    lat.addEventListener('change', () => {
        const l = parseFloat(lat.value), g = parseFloat(lng.value);
        if (!isNaN(l) && !isNaN(g)) { marker.setLatLng([l, g]); map.setView([l, g], 15); }
    });
    
    lng.addEventListener('change', () => {
        const l = parseFloat(lat.value), g = parseFloat(lng.value);
        if (!isNaN(l) && !isNaN(g)) { marker.setLatLng([l, g]); map.setView([l, g], 15); }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
