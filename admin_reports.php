<?php
session_start();
require_once 'config/oracle_db.php';

// Define Admin Password
define('ADMIN_PASSWORD', 'admin123');

// Handle Password Submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_pass'])) {
    $input_pass = trim($_POST['admin_pass']);
    if ($input_pass === ADMIN_PASSWORD) {
        $_SESSION['is_admin_logged_in'] = true;
    } else {
        $error = 'Incorrect Password! Access Denied.';
    }
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['is_admin_logged_in']);
    header('Location: admin_reports.php');
    exit;
}

require_once 'views/header.php';

// If NOT logged in, display Password Login Form
if (!isset($_SESSION['is_admin_logged_in']) || $_SESSION['is_admin_logged_in'] !== true):
?>

<div class="container my-5 d-flex justify-content-center">
    <div class="card card-custom p-4 shadow-lg" style="max-width: 450px; width: 100%;">
        <div class="text-center mb-4">
            <i class="fa-solid fa-user-shield fa-3x text-gold mb-3"></i>
            <h3 class="fw-bold text-gold">Admin Authentication</h3>
            <p class="text-secondary small">Please enter the password to access Oracle system reports.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 text-center small" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="admin_reports.php" method="POST">
            <div class="mb-3">
                <label class="form-label text-light small">Admin Password</label>
                <input type="password" name="admin_pass" class="form-control bg-dark text-white border-secondary py-2" placeholder="Enter password" required autofocus>
            </div>
            <button type="submit" class="btn btn-gold w-100 py-2 font-weight-bold">Unlock Reports</button>
        </form>
    </div>
</div>

<?php 
require_once 'views/footer.php';
exit; 
endif; // End Auth Check

// --- IF AUTHENTICATED: FETCH REPORTS ---
$total_students = 0;
$total_events = 0;
$total_regs = 0;

$q1 = oci_parse($conn, "SELECT COUNT(*) AS CNT FROM USERS");
oci_execute($q1);
if ($r = oci_fetch_array($q1, OCI_ASSOC)) { $total_students = $r['CNT']; }
oci_free_statement($q1);

$q2 = oci_parse($conn, "SELECT COUNT(*) AS CNT FROM EVENTS");
oci_execute($q2);
if ($r = oci_fetch_array($q2, OCI_ASSOC)) { $total_events = $r['CNT']; }
oci_free_statement($q2);

$q3 = oci_parse($conn, "SELECT COUNT(*) AS CNT FROM REGISTRATIONS");
oci_execute($q3);
if ($r = oci_fetch_array($q3, OCI_ASSOC)) { $total_regs = $r['CNT']; }
oci_free_statement($q3);

// Fetch Event Registration Breakdown
$report_sql = "SELECT e.event_id, e.title, c.club_name, v.venue_name, e.ticket_price,
                      COUNT(r.registration_id) AS total_registrations
               FROM EVENTS e
               JOIN CLUBS_SOCIETIES c ON e.club_id = c.club_id
               JOIN VENUES v ON e.venue_id = v.venue_id
               LEFT JOIN REGISTRATIONS r ON e.event_id = r.event_id
               GROUP BY e.event_id, e.title, c.club_name, v.venue_name, e.ticket_price
               ORDER BY e.event_id ASC";

$stmt = oci_parse($conn, $report_sql);
oci_execute($stmt);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-gold mb-1"><i class="fa-solid fa-chart-line me-2"></i>Admin Reports & Analytics</h2>
            <p class="text-secondary mb-0">System-wide performance overview from Oracle Database</p>
        </div>
        <a href="admin_reports.php?action=logout" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i> Lock Reports</a>
    </div>

    <!-- Overview Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card card-custom p-3 text-center border-start border-4 border-warning">
                <i class="fa-solid fa-users fa-2x text-gold mb-2"></i>
                <h6 class="text-secondary mb-1">Total Registered Students</h6>
                <h3 class="fw-bold text-white mb-0"><?php echo $total_students; ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 text-center border-start border-4 border-warning">
                <i class="fa-solid fa-calendar-check fa-2x text-gold mb-2"></i>
                <h6 class="text-secondary mb-1">Active Campus Events</h6>
                <h3 class="fw-bold text-white mb-0"><?php echo $total_events; ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 text-center border-start border-4 border-warning">
                <i class="fa-solid fa-ticket fa-2x text-gold mb-2"></i>
                <h6 class="text-secondary mb-1">Total Event Registrations</h6>
                <h3 class="fw-bold text-white mb-0"><?php echo $total_regs; ?></h3>
            </div>
        </div>
    </div>

    <!-- Detailed Event Breakdown Table -->
    <div class="card card-custom p-4">
        <h4 class="fw-bold text-gold mb-3"><i class="fa-solid fa-list-check me-2"></i>Event Participation Breakdown</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-secondary mb-0">
                <thead>
                    <tr class="text-gold">
                        <th>Event ID</th>
                        <th>Event Title</th>
                        <th>Organizer / Club</th>
                        <th>Venue</th>
                        <th>Ticket Price</th>
                        <th class="text-center">Total Registrations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)): ?>
                        <tr>
                            <td><span class="badge bg-secondary">#<?php echo htmlspecialchars($row['EVENT_ID']); ?></span></td>
                            <td class="fw-bold text-white"><?php echo htmlspecialchars($row['TITLE']); ?></td>
                            <td><?php echo htmlspecialchars($row['CLUB_NAME']); ?></td>
                            <td><?php echo htmlspecialchars($row['VENUE_NAME']); ?></td>
                            <td>LKR <?php echo number_format($row['TICKET_PRICE'], 2); ?></td>
                            <td class="text-center">
                                <span class="badge badge-gold px-3 py-2 fs-6">
                                    <?php echo htmlspecialchars($row['TOTAL_REGISTRATIONS']); ?> Student(s)
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
oci_free_statement($stmt);
oci_close($conn);
require_once 'views/footer.php'; 
?>