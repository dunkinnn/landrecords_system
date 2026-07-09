<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

date_default_timezone_set('Asia/Manila');

$selectedFormType = trim($_GET['form_type'] ?? '');
$selectedYear = trim($_GET['year'] ?? '');
$searchTerm = trim($_GET['search'] ?? '');

/**
 * Same UNION-ready pattern as landmanagement.php. Only Building exists
 * today - append "UNION ALL SELECT ..." blocks with the same column
 * aliases as Land/Machinery/Other Improvements get their own tables.
 */
$sql = "SELECT
            p.id AS property_id,
            p.pin,
            p.arp_no,
            p.owner_name,
            p.barangay,
            b.id AS form_id,
            b.lot_number,
            b.effectivity_year,
            b.last_generated_at,
            'Building' AS form_type
        FROM tbl_properties p
        INNER JOIN tbl_faas_building b ON b.property_id = p.id
        WHERE 1=1";

$params = [];
$types = '';

if ($selectedFormType !== '' && $selectedFormType === 'Building/Improvements Sheet') {
    $sql .= " AND 'Building' = ?";
    $types .= 's';
    $params[] = 'Building';
}
if ($selectedYear !== '') {
    $sql .= " AND b.effectivity_year = ?";
    $types .= 's';
    $params[] = $selectedYear;
}
if ($searchTerm !== '') {
    $sql .= " AND (p.arp_no LIKE ? OR p.owner_name LIKE ? OR b.lot_number LIKE ?)";
    $types .= 'sss';
    $like = '%' . $searchTerm . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY p.updated_at DESC";

$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$formRecords = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Automated Form Generation</title>
  <link rel="icon" type="image/png" href="../../assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../assets/css/layout.css">
  <link rel="stylesheet" href="../../assets/css/forms.css?v=1.0">
</head>
<body>
  <?php include_once '../../layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <?php include_once '../../layouts/header.php'; ?>
    <div class="container mt-4 px-4">
      <h2 class="page-title mb-3">Automated Form Generation</h2>
      <div class="form-container">
        <!-- FILTERS + SEARCH -->
        <form class="form-controls" method="GET">
          <div class="controls-left">
            <div class="filter-group">
              <select class="form-select filter-input" name="form_type">
                <option value="">Filter by Form Type</option>
                <option value="Land Sheet" <?php echo $selectedFormType === 'Land Sheet' ? 'selected' : ''; ?>>Land Sheet</option>
                <option value="Building/Improvements Sheet" <?php echo $selectedFormType === 'Building/Improvements Sheet' ? 'selected' : ''; ?>>Building/Improvements Sheet</option>
                <option value="Machinery Sheet" <?php echo $selectedFormType === 'Machinery Sheet' ? 'selected' : ''; ?>>Machinery Sheet</option>
                <option value="Certification" <?php echo $selectedFormType === 'Certification' ? 'selected' : ''; ?>>Certification</option>
              </select>
              <select class="form-select filter-input" name="year">
                <option value="">Filter by Year</option>
                <option value="2026" <?php echo $selectedYear === '2026' ? 'selected' : ''; ?>>2026</option>
                <option value="2025" <?php echo $selectedYear === '2025' ? 'selected' : ''; ?>>2025</option>
                <option value="2024" <?php echo $selectedYear === '2024' ? 'selected' : ''; ?>>2024</option>
              </select>
            </div>
          </div>

          <div class="controls-right">
            <div class="search-group">
              <div class="input-group search-box">
                <span class="input-group-text">
                  <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control search-input" name="search"
                       value="<?php echo htmlspecialchars($searchTerm); ?>"
                       placeholder="Search ARP No., owner, or lot...">
              </div>
            </div>
          </div>
        </form>

        <!-- TABLE -->
        <div class="table-responsive mt-3">
          <table class="table table-striped form-table">
            <thead>
              <tr>
                <th>Form ID</th>
                <th>Form Name</th>
                <th>Description</th>
                <th>Last Generated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($formRecords)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-2">No generated forms found.</td>
              </tr>
              <?php else: foreach ($formRecords as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['arp_no']); ?></td>
                <td>FAAS Building &amp; Other Improvements</td>
                <td><?php echo htmlspecialchars($row['owner_name']); ?> &mdash; <?php echo htmlspecialchars($row['barangay']); ?></td>
                <td><?php echo $row['last_generated_at'] ? date('M j, Y g:i A', strtotime($row['last_generated_at'])) : '<span class="text-muted">Never</span>'; ?></td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    <a class="btn btn-outline-primary" href="../../api/generate_faas_building_pdf.php?id=<?php echo urlencode($row['form_id']); ?>" target="_blank">
                      FAAS Sheet
                    </a>
                    <a class="btn btn-outline-primary" href="../../api/generate_tax_declaration_pdf.php?id=<?php echo urlencode($row['form_id']); ?>" target="_blank">
                      Tax Dec.
                    </a>
                    <a class="btn btn-outline-primary" href="../../api/generate_notice_of_assessment_pdf.php?id=<?php echo urlencode($row['form_id']); ?>" target="_blank">
                      Notice
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>