<?php
require_once 'config/oracle_db.php';
require_once 'views/header.php';

// 1. Fetch Total Metrics
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

// 2. Fetch Event Registration Breakdown
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
    <div class="mb-4 text-center">
        <h2 class="fw-bold text-gold"><i class="fa-solid fa-chart-line me-2"></i>Admin Reports & Analytics</h2>
        <p class="text-secondary">System-wide performance overview from Oracle Database</p>
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