<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';

$conn = getDBConnection();

try {
    $stats = [
        'total_patients'        => (int)$conn->query("SELECT COUNT(*) FROM patients WHERE status='active'")->fetch_row()[0],
        'today_appointments'    => (int)$conn->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()")->fetch_row()[0],
        'pending_prescriptions' => (int)$conn->query("SELECT COUNT(*) FROM prescriptions WHERE status='pending'")->fetch_row()[0],
        'low_stock_medicines'   => (int)$conn->query("SELECT COUNT(*) FROM medicines WHERE stock_quantity <= reorder_level AND status != 'expired'")->fetch_row()[0],
        'pending_lab_orders'    => (int)$conn->query("SELECT COUNT(*) FROM lab_orders WHERE status NOT IN ('completed','cancelled')")->fetch_row()[0],
        'pending_bills'         => (int)$conn->query("SELECT COUNT(*) FROM bills WHERE status IN ('pending','partial')")->fetch_row()[0],
        'total_staff'           => (int)$conn->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetch_row()[0],
    ];
} catch (Exception $e) {
    $stats = [];
    error_log("Dashboard stats error: " . $e->getMessage());
}

$roleStats = [];
if ($role === 'admin') {
    $roleStats = ['total_patients','today_appointments','pending_prescriptions','low_stock_medicines','pending_lab_orders','pending_bills','total_staff'];
} elseif ($role === 'doctor') {
    $roleStats = ['total_patients','today_appointments','pending_prescriptions','pending_lab_orders'];
} elseif ($role === 'nurse') {
    $roleStats = ['total_patients','today_appointments','pending_lab_orders'];
} elseif ($role === 'traige') {
    $roleStats = ['total_patients','today_appointments','pending_lab_orders'];
} elseif ($role === 'receptionist') {
    $roleStats = ['total_patients','today_appointments','pending_bills'];
} elseif ($role === 'pharmacist') {
    $roleStats = ['pending_prescriptions','low_stock_medicines','pending_bills'];
} elseif ($role === 'lab_technician') {
    $roleStats = ['pending_lab_orders'];
} elseif ($role === 'department_head') {
    $roleStats = ['total_patients','today_appointments','pending_prescriptions','low_stock_medicines','pending_lab_orders','pending_bills','total_staff'];
} elseif ($role === 'finance') {
    $roleStats = ['pending_bills'];
}

$finalStats = [];
foreach ($roleStats as $key) {
    if (isset($stats[$key])) {
        $finalStats[$key] = $stats[$key];
    }
}

$cardDefs = [
    'total_patients'        => ['Active Patients',        'bi-people-fill',               'primary'],
    'today_appointments'    => ["Today's Appointments",   'bi-calendar-check-fill',        'success'],
    'pending_prescriptions' => ['Pending Prescriptions',  'bi-capsule-pill',               'warning'],
    'low_stock_medicines'   => ['Low Stock Medicines',    'bi-exclamation-triangle-fill',  'danger'],
    'pending_lab_orders'    => ['Pending Lab Orders',     'bi-eyedropper-fill',            'info'],
    'pending_bills'         => ['Pending Bills',          'bi-receipt-cutoff',             'secondary'],
    'total_staff'           => ['Active Staff',           'bi-person-badge-fill',          'dark'],
];

$recentAppts = null;
$recentPatients = null;
$myPrescriptions = null;
$myLabOrders = null;

try {
    $uid = $_SESSION['user_id'];
    if (function_exists('getRecentNotifications')) {
        $userNotifications = getRecentNotifications($uid, 5);
    }

    // Role-specific appointment queries
    if ($role === 'doctor') {
        $recentAppts = $conn->query("SELECT a.id, p.full_name AS patient, p.patient_id AS pid, a.appointment_time, a.status, a.reason FROM appointments a JOIN patients p ON p.id = a.patient_id WHERE a.appointment_date = CURDATE() AND a.doctor_id = $uid ORDER BY a.appointment_time LIMIT 5");
    } elseif ($role === 'traige') {
        $recentAppts = $conn->query("SELECT a.id, p.full_name AS patient, p.patient_id AS pid, u.full_name AS doctor, a.appointment_time, a.status, a.reason FROM appointments a JOIN patients p ON p.id = a.patient_id JOIN users u ON u.id = a.doctor_id WHERE a.appointment_date = CURDATE() ORDER BY a.appointment_time LIMIT 5");
    }

    if (in_array($role, ['admin','nurse','receptionist','department_head'])) {
        $recentAppts = $conn->query("SELECT a.id, p.full_name AS patient, p.patient_id AS pid, u.full_name AS doctor, a.appointment_time, a.status, a.reason FROM appointments a JOIN patients p ON p.id = a.patient_id JOIN users u ON u.id = a.doctor_id WHERE a.appointment_date = CURDATE() ORDER BY a.appointment_time LIMIT 5");
    }

    if (in_array($role, ['admin','receptionist','department_head','nurse','traige'])) {
        $recentPatients = $conn->query("SELECT patient_id, full_name, category, gender, created_at FROM patients ORDER BY created_at DESC LIMIT 5");
    }
} catch (Exception $e) {
    error_log("Dashboard query error: " . $e->getMessage());
}

try {
    $conn->close();
} catch (Exception $e) {
    error_log("Dashboard close error: " . $e->getMessage());
}
?>

<div class="mb-4">
    <h4 class="mb-1"><i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard</h4>
    <p class="text-muted mb-0">Welcome back, <strong><?= htmlspecialchars($full_name) ?></strong> &mdash; <?= date('l, F j, Y') ?></p>
</div>

<?php if (!empty($finalStats)): ?>
<div class="row g-4 mb-4">
    <?php foreach ($finalStats as $key => $value):
        [$label, $icon, $color] = $cardDefs[$key];
    ?>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-<?= $color ?> border-start border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1"><?= $label ?></p>
                        <h3 class="fw-bold text-<?= $color ?> mb-0"><?= $value ?></h3>
                    </div>
                    <div class="bg-<?= $color ?> bg-opacity-10 rounded-circle p-3">
                        <i class="bi <?= $icon ?> fs-3 text-<?= $color ?>"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($recentAppts || $recentPatients): ?>
<div class="row g-4 mb-4">
    <?php if ($recentAppts): ?>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0"><i class="bi bi-calendar-check text-primary me-2"></i>Today's Appointments</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Patient</th>
                                <?php if (in_array($role, ['admin','receptionist','nurse','department_head','traige'])): ?>
                                <th>Doctor</th>
                                <?php endif; ?>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentAppts->num_rows === 0): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No appointments today.</td></tr>
                            <?php else: while ($row = $recentAppts->fetch_assoc()): ?>
                                <?php
                                    $sc = ['scheduled'=>'primary','completed'=>'success','cancelled'=>'danger','no_show'=>'warning'];
                                ?>
                            <tr>
                                <td><?= date('h:i A', strtotime($row['appointment_time'])) ?></td>
                                <td><small class="text-muted d-block"><?= htmlspecialchars($row['pid']) ?></small><?= htmlspecialchars($row['patient']) ?></td>
                                <?php if (in_array($role, ['admin','receptionist','nurse','department_head','traige'])): ?>
                                <td><small><?= htmlspecialchars($row['doctor'] ?? '—') ?></small></td>
                                <?php endif; ?>
                                <td><small><?= htmlspecialchars($row['reason'] ?? '—') ?></small></td>
                                <td><span class="badge bg-<?= $sc[$row['status']] ?? 'secondary' ?>"><?= ucfirst($row['status']) ?></span></td>
                            </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($recentPatients): ?>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3">
                <h6 class="mb-0"><i class="bi bi-person-plus text-success me-2"></i>Recently Registered</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Patient</th><th>Gender</th><th>Category</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($recentPatients->num_rows === 0): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No patients yet.</td></tr>
                            <?php else: while ($row = $recentPatients->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><small><?= htmlspecialchars($row['gender']) ?></small></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['category']) ?></span></td>
                                <td><small class="text-muted"><?= date('d M', strtotime($row['created_at'])) ?></small></td>
                            </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($role === 'admin'): ?>
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-people-gear text-primary me-2"></i>System Overview</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Module</th><th>Status</th><th>Pending Items</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="bi bi-capsule me-2"></i>Prescriptions</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td><?= $stats['pending_prescriptions'] ?? 0 ?></td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-eyedropper me-2"></i>Lab Orders</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td><?= $stats['pending_lab_orders'] ?? 0 ?></td>
                            </tr>
                            <tr>
                                <td><i class="bi bi-receipt me-2"></i>Billing</td>
                                <td><span class="badge bg-<?= $stats['pending_bills'] > 0 ? 'warning' : 'success' ?>"><?= $stats['pending_bills'] > 0 ? 'Action Needed' : 'Clear' ?></span></td>
                                <td><?= $stats['pending_bills'] ?? 0 ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
