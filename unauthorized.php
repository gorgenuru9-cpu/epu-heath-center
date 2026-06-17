<?php
$pageTitle = 'Access Denied';
require_once 'includes/header.php';
?>
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height:50vh">
    <i class="bi bi-shield-exclamation text-danger" style="font-size:5rem"></i>
    <h3 class="mt-3">Access Denied</h3>
    <p class="text-muted">You do not have permission to access this page.</p>
    <a href="dashboard.php" class="btn btn-primary mt-2">
        <i class="bi bi-house me-1"></i>Back to Dashboard
    </a>
</div>
<?php require_once 'includes/footer.php'; ?>
