<?php
require_once 'config/oracle_db.php';
require_once 'views/header.php';

$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$registrations = [];

if ($student_id > 0) {
    $query = "SELECT r.registration_id, r.registration_date, r.status AS reg_status,
                     e.event_id, e.title, e.event_date, e.ticket_price,
                     c.club_name, v.venue_name,
                     p.payment_status, p.amount_paid
              FROM campuspulse_user.REGISTRATIONS r
              JOIN campuspulse_user.EVENTS e ON r.event_id = e.event_id
              JOIN campuspulse_user.CLUBS_SOCIETIES c ON e.club_id = c.club_id
              JOIN campuspulse_user.VENUES v ON e.venue_id = v.venue_id
              LEFT JOIN campuspulse_user.PAYMENTS p ON r.registration_id = p.registration_id
              WHERE r.student_id = :sid
              ORDER BY r.registration_date DESC";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ":sid", $student_id);
    oci_execute($stmt);

    while ($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)) {
        $registrations[] = $row;
    }
    oci_free_statement($stmt);
}
?>

<div class="container my-5">
    <div class="row justify-content-center mb-4">
        <div class="col-md-6 text-center">
            <h2 class="fw-bold text-gold mb-3"><i class="fa-solid fa-user-check me-2"></i>My Event Registrations</h2>
            <form action="my_registrations.php" method="GET" class="d-flex gap-2">
                <input type="number" name="student_id" class="form-control bg-dark text-white border-secondary" 
                       placeholder="Enter Student ID (e.g., 101)" value="<?php echo $student_id > 0 ? $student_id : ''; ?>" required>
                <button type="submit" class="btn btn-gold text-nowrap">Find Registrations</button>
            </form>
        </div>
    </div>

    <?php if ($student_id > 0): ?>
        <?php if (!empty($registrations)): ?>
            <div class="row">
                <?php foreach ($registrations as $reg): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card card-custom h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge badge-gold"><?php echo htmlspecialchars($reg['CLUB_NAME']); ?></span>
                                <span class="badge bg-success"><?php echo htmlspecialchars($reg['REG_STATUS']); ?></span>
                            </div>
                            <h5 class="fw-bold text-white"><?php echo htmlspecialchars($reg['TITLE']); ?></h5>
                            
                            <ul class="list-unstyled small text-light mt-3 mb-3">
                                <li><i class="fa-regular fa-calendar me-2" style="color: var(--gold);"></i>Event Date: <?php echo date('M d, Y', strtotime($reg['EVENT_DATE'])); ?></li>
                                <li><i class="fa-solid fa-location-dot me-2" style="color: var(--gold);"></i>Venue: <?php echo htmlspecialchars($reg['VENUE_NAME']); ?></li>
                                <li><i class="fa-solid fa-receipt me-2" style="color: var(--gold);"></i>Reg ID: #<?php echo $reg['REGISTRATION_ID']; ?></li>
                                <li><i class="fa-solid fa-credit-card me-2" style="color: var(--gold);"></i>Payment Status: <?php echo $reg['PAYMENT_STATUS'] ? htmlspecialchars($reg['PAYMENT_STATUS']) : 'N/A'; ?></li>
                            </ul>

                            <div class="mt-auto pt-2 border-top border-secondary text-end">
                                <a href="event_details.php?id=<?php echo $reg['EVENT_ID']; ?>" class="btn btn-outline-gold btn-sm">View Event Page</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-dark text-center border-secondary mt-4">
                <p class="mb-0 text-secondary">No registrations found for Student ID: <strong><?php echo $student_id; ?></strong>.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php 
oci_close($conn);
require_once 'views/footer.php'; 
?>