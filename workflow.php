<?php
session_start();
require_once 'includes/auth.php';
requireRole(['admin','department_head']);

$role      = $_SESSION['role'];
$full_name = $_SESSION['full_name'];

$pageTitle = 'Patient Workflow';
require_once 'includes/header.php';

$conn_live = false;
try {
    if (function_exists('getDBConnection')) {
        $conn_live = getDBConnection();
    }
} catch (Throwable $e) {
    $conn_live = false;
}

function workflowStat($conn_live, $sql) {
    if (!$conn_live) return 0;
    try {
        $q = $conn_live->query($sql);
        if ($q) return (int)$q->fetch_row()[0];
    } catch (Throwable $e) {
        // ignore for workflow page
    }
    return 0;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-diagram-3 text-primary me-2"></i>Patient Journey Workflow</h4>
        <small class="text-muted">Visual guide from patient registration to checkout</small>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row g-4">
            <!-- Step 1: Receptionist -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 bg-primary bg-opacity-10 h-100">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px">
                            <i class="bi bi-person-plus fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-primary mb-2">1. Reception</h6>
                        <p class="small text-muted mb-2">Register Patient & Schedule Appointment</p>
                        <span class="badge bg-primary">Receptionist</span>
                        <div class="mt-3 d-grid gap-2">
                            <a href="/EPU health/patients/register_patient.php" class="btn btn-sm btn-primary">Register Patient</a>
                            <a href="/EPU health/appointments/add_appointment.php" class="btn btn-sm btn-outline-primary">Book Appointment</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Nurse -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 bg-success bg-opacity-10 h-100">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px">
                            <i class="bi bi-heart-pulse fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-success mb-2">2. Triage & Vitals</h6>
                        <p class="small text-muted mb-2">Take Vital Signs & Prepare Patient Record</p>
                        <span class="badge bg-success">Nurse</span>
                        <div class="mt-3 d-grid gap-2">
                            <a href="/EPU health/patients/patients.php" class="btn btn-sm btn-success">View Patients</a>
                            <a href="/EPU health/medical_records/add_record.php" class="btn btn-sm btn-outline-success">Record Vitals</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Doctor -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 bg-info bg-opacity-10 h-100">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-info mb-2">3. Consultation</h6>
                        <p class="small text-muted mb-2">Diagnosis, Treatment & Prescription</p>
                        <span class="badge bg-info">Doctor</span>
                        <div class="mt-3 d-grid gap-2">
                            <a href="/EPU health/appointments/appointments.php" class="btn btn-sm btn-info">Appointments</a>
                            <a href="/EPU health/medical_records/records.php" class="btn btn-sm btn-outline-info">Medical Records</a>
                            <a href="/EPU health/prescriptions/add_prescription.php" class="btn btn-sm btn-outline-info">New Prescription</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branching from Doctor -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 bg-warning bg-opacity-10 h-100">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px">
                            <i class="bi bi-diagram-3 fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-warning mb-2">4. Referrals</h6>
                        <p class="small text-muted mb-2">Lab, Pharmacy, or Billing</p>
                        <span class="badge bg-warning">Multiple</span>
                        <div class="mt-3 d-grid gap-2">
                            <a href="/EPU health/lab/add_lab_order.php" class="btn btn-sm btn-warning">Lab Order</a>
                            <a href="/EPU health/billing/create_bill.php" class="btn btn-sm btn-outline-warning">Create Bill</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flow Arrows -->
        <div class="text-center my-4 d-none d-lg-block">
            <i class="bi bi-arrow-down text-primary fs-3"></i>
        </div>
        <div class="text-center my-4 d-lg-none">
            <i class="bi bi-arrow-down text-primary fs-3"></i>
        </div>

        <div class="row g-4">
            <!-- Lab -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 bg-info bg-opacity-10 h-100">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px">
                            <i class="bi bi-eyedropper fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-info mb-2">5. Laboratory</h6>
                        <p class="small text-muted mb-2">Perform Tests & Submit Results</p>
                        <span class="badge bg-info">Lab Technician</span>
                        <div class="mt-3 d-grid gap-2">
                            <a href="/EPU health/lab/lab_orders.php" class="btn btn-sm btn-info">Lab Orders</a>
                            <a href="/EPU health/lab/enter_results.php" class="btn btn-sm btn-outline-info">Enter Results</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctor Reviews -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 bg-primary bg-opacity-10 h-100">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-primary mb-2">6. Review Results</h6>
                        <p class="small text-muted mb-2">Doctor Reviews Lab Results</p>
                        <span class="badge bg-primary">Doctor</span>
                        <div class="mt-3 d-grid gap-2">
                            <a href="/EPU health/lab/lab_orders.php" class="btn btn-sm btn-primary">View Results</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pharmacy -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 bg-warning bg-opacity-10 h-100">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px">
                            <i class="bi bi-capsule fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-warning mb-2">7. Pharmacy</h6>
                        <p class="small text-muted mb-2">Receive Prescription & Dispense Medication</p>
                        <span class="badge bg-warning">Pharmacist</span>
                        <div class="mt-3 d-grid gap-2">
                            <a href="/EPU health/prescriptions/prescriptions.php" class="btn btn-sm btn-warning">Prescriptions</a>
                            <a href="/EPU health/pharmacy/medicines.php" class="btn btn-sm btn-outline-warning">Medicine Stock</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Finance -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card border-0 bg-success bg-opacity-10 h-100">
                    <div class="card-body text-center p-3">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:50px;height:50px">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-success mb-2">8. Billing & Payment</h6>
                        <p class="small text-muted mb-2">Generate Bill & Receive Payment</p>
                        <span class="badge bg-success">Finance / Cashier</span>
                        <div class="mt-3 d-grid gap-2">
                            <a href="/EPU health/billing/billing.php" class="btn btn-sm btn-success">Bills & Payments</a>
                            <a href="/EPU health/finance/dashboard.php" class="btn btn-sm btn-outline-success">Finance Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final Step -->
        <div class="text-center my-4">
            <div class="d-inline-flex align-items-center gap-2 bg-light px-4 py-2 rounded-pill">
                <i class="bi bi-check-circle text-success fs-5"></i>
                <span class="fw-semibold">Patient Checkout</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                        <i class="bi bi-people text-primary fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Total Patients</p>
                        <h4 class="fw-bold mb-0"><?= workflowStat($conn_live, "SELECT COUNT(*) FROM patients WHERE status='active'") ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 rounded-circle p-2">
                        <i class="bi bi-calendar-check text-success fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Today's Appointments</p>
                        <h4 class="fw-bold mb-0"><?= workflowStat($conn_live, "SELECT COUNT(*) FROM appointments WHERE appointment_date=CURDATE()") ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                        <i class="bi bi-capsule text-warning fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Pending Prescriptions</p>
                        <h4 class="fw-bold mb-0"><?= workflowStat($conn_live, "SELECT COUNT(*) FROM prescriptions WHERE status='pending'") ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 rounded-circle p-2">
                        <i class="bi bi-receipt text-info fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Pending Bills</p>
                        <h4 class="fw-bold mb-0"><?= workflowStat($conn_live, "SELECT COUNT(*) FROM bills WHERE status IN ('pending','partial')") ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if ($conn_live) {
    try { $conn_live->close(); } catch (Throwable $e) {} }
require_once 'includes/footer.php';
?>
