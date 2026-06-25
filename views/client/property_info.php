<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

date_default_timezone_set('Asia/Manila');

$clientUserId = (int)($_SESSION['user_id'] ?? 0);
$properties = [];
$selectedProperty = null;

function getAssessedRate(string $landType): float
{
    switch ($landType) {
        case 'Residential':
            return 0.06;
        case 'Agricultural':
            return 0.07;
        case 'Commercial':
            return 0.14;
        case 'Industrial':
            return 0.14;
        case 'Special':
            return 0.05;
        default:
            return 0.0;
    }
}

$sql = "SELECT property_id, lot_number, barangay, land_type, area_sqm, market_value, status, document_type, boundary_coordinates
        FROM tbl_properties
        WHERE owner_id = ? AND is_deleted = 0
        ORDER BY property_id DESC";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $clientUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
    $stmt->close();
}

$selectedProperty = $properties[0] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Property Information</title>
  <link rel="icon" type="image/png" href="../../assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <link rel="stylesheet" href="../../assets/css/layout.css">
  <link rel="stylesheet" href="../../assets/css/forms.css?v=1.0">
  <link rel="stylesheet" href="../../assets/css/property_info.css?v=1.0">
</head>
<body>
  <?php include_once '../../layouts/sidebar_client.php'; ?>
  <div class="main-content">
    <?php include_once '../../layouts/header_client.php'; ?>
    <div class="container mt-4 px-4">
      <h2 class="page-title mb-3">Property Information</h2>

      <div class="map-shell">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div>
            <h5 class="mb-1">Property Map</h5>
            <div class="text-muted">View and inspect your property boundaries on the map.</div>
          </div>
          <div class="map-actions">
            <button type="button" id="locateNextBtn" class="btn btn-success btn-sm">
              Locate Next
            </button>
            <span class="badge text-bg-success ms-2">
              <?php echo count($properties); ?> record(s)
            </span>
          </div>
        </div>

        <div class="map-meta">
          <?php if ($selectedProperty): ?>
            <div><strong>Selected:</strong> <span id="selectedPropertyTitle">Lot <?php echo htmlspecialchars($selectedProperty['lot_number'] ?? ''); ?></span></div>
          <?php else: ?>
            <div class="text-muted">No mapped properties available yet.</div>
          <?php endif; ?>
        </div>

        <div class="property-details-grid">
          <div>
            <span class="property-detail-label">Lot Number</span>
            <div class="property-detail-value" id="selectedLotNumber"><?php echo htmlspecialchars($selectedProperty['lot_number'] ?? 'N/A'); ?></div>
          </div>
          <div>
            <span class="property-detail-label">Barangay</span>
            <div class="property-detail-value" id="selectedBarangay"><?php echo htmlspecialchars($selectedProperty['barangay'] ?? 'N/A'); ?></div>
          </div>
          <div>
            <span class="property-detail-label">Land Type</span>
            <div class="property-detail-value" id="selectedLandType"><?php echo htmlspecialchars($selectedProperty['land_type'] ?? 'N/A'); ?></div>
          </div>
          <div>
            <span class="property-detail-label">Status</span>
            <div class="property-detail-value" id="selectedStatus"><?php echo htmlspecialchars($selectedProperty['status'] ?? 'N/A'); ?></div>
          </div>
        </div>

        <div class="mt-3" id="propertyMap"></div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const properties = <?php echo json_encode($properties); ?>;
    const propertyMap = L.map('propertyMap', {
      scrollWheelZoom: false
    }).setView([16.9754, 121.8107], 10);

    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxNativeZoom: 19,
      maxZoom: 24
    });

    const satelliteLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
      maxNativeZoom: 20,
      maxZoom: 24
    });

    satelliteLayer.addTo(propertyMap);

    L.control.layers({
      'Satellite View': satelliteLayer,
      'Street Map': osmLayer
    }, null, { collapsed: true }).addTo(propertyMap);

    const allLayers = L.featureGroup().addTo(propertyMap);
    const propertyEntries = [];
    let selectedLayer = null;
    let currentPropertyIndex = -1;

    function parseGeoJson(rawGeoJson) {
      if (!rawGeoJson || rawGeoJson === 'null') {
        return null;
      }

      try {
        return JSON.parse(rawGeoJson);
      } catch (error) {
        console.error('Invalid property geometry', error);
        return null;
      }
    }

    function updatePropertyDetails(property) {
      document.getElementById('selectedPropertyTitle').textContent = property ? `Lot ${property.lot_number || 'N/A'}` : 'No property selected';
      document.getElementById('selectedLotNumber').textContent = property?.lot_number || 'N/A';
      document.getElementById('selectedBarangay').textContent = property?.barangay || 'N/A';
      document.getElementById('selectedLandType').textContent = property?.land_type || 'N/A';
      document.getElementById('selectedStatus').textContent = property?.status || 'N/A';
    }

    function highlightLayer(layer) {
      if (selectedLayer && selectedLayer.setStyle) {
        selectedLayer.setStyle({
          color: '#52B028',
          weight: 3,
          fillColor: '#52B028',
          fillOpacity: 0.2
        });
      }

      selectedLayer = layer;

      if (selectedLayer && selectedLayer.setStyle) {
        selectedLayer.setStyle({
          color: '#0f7a2a',
          weight: 4,
          fillColor: '#52B028',
          fillOpacity: 0.28
        });
      }
    }

    function selectPropertyByIndex(index, shouldOpenPopup = false) {
      if (!propertyEntries.length) {
        return;
      }

      currentPropertyIndex = (index + propertyEntries.length) % propertyEntries.length;
      const entry = propertyEntries[currentPropertyIndex];

      updatePropertyDetails(entry.property);
      highlightLayer(entry.layer);

      if (entry.layer && entry.layer.getBounds) {
        propertyMap.fitBounds(entry.layer.getBounds(), {
          padding: [20, 20],
          maxZoom: 18
        });
      }

      if (shouldOpenPopup && entry.layer && entry.layer.getLayers) {
        const childLayer = entry.layer.getLayers()[0];
        if (childLayer && childLayer.openPopup) {
          childLayer.openPopup();
        }
      }
    }

    properties.forEach(function (property) {
      const geoJson = parseGeoJson(property.boundary_coordinates);
      if (!geoJson) {
        return;
      }

      const lotNumber = property.lot_number || 'Property';
      const barangay = property.barangay || '';
      const landType = property.land_type || '';
      const status = property.status || '';

      const layer = L.geoJSON(geoJson, {
        style: {
          color: '#52B028',
          weight: 3,
          fillColor: '#52B028',
          fillOpacity: 0.2
        },
        onEachFeature: function (feature, featureLayer) {
          featureLayer.bindPopup(`
            <div style="min-width:180px">
              <div><strong>${lotNumber}</strong></div>
              <div class="text-muted">${barangay}</div>
              <div>${landType}</div>
              <div>Status: ${status}</div>
            </div>
          `);
          featureLayer.bindTooltip(lotNumber, { sticky: true });
          featureLayer.on('click', function () {
            const propertyIndex = propertyEntries.findIndex(function (entry) {
              return entry.property.property_id === property.property_id;
            });

            if (propertyIndex !== -1) {
              selectPropertyByIndex(propertyIndex, false);
            }
          });
        }
      });

      layer.addTo(allLayers);
      propertyEntries.push({ property, layer });
    });

    if (allLayers.getLayers().length > 0) {
      selectPropertyByIndex(0, false);
    }

    document.getElementById('locateNextBtn')?.addEventListener('click', function () {
      if (!propertyEntries.length) {
        return;
      }

      selectPropertyByIndex(currentPropertyIndex + 1, true);
    });

    setTimeout(function () {
      propertyMap.invalidateSize();
      if (selectedLayer && selectedLayer.getBounds) {
        propertyMap.fitBounds(selectedLayer.getBounds(), {
          padding: [20, 20],
          maxZoom: 18
        });
      }
    }, 150);
  </script>
</body>
</html>
