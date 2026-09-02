<?php
require_once 'config/oracle_db.php';
require_once 'views/header.php';

// Explicitly prefix tables with campuspulse_user schema to fix ORA-00942
$query = "SELECT e.event_id, e.title, e.description, e.event_date, e.ticket_price, e.max_capacity, 
                 c.club_name, v.venue_name, v.building 
          FROM campuspulse_user.EVENTS e
          JOIN campuspulse_user.CLUBS_SOCIETIES c ON e.club_id = c.club_id
          JOIN campuspulse_user.VENUES v ON e.venue_id = v.venue_id
          ORDER BY e.event_date ASC";

$stmt = oci_parse($conn, $query);

if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    echo "<div class='container my-4'><div class='alert alert-danger'>Query Error: " . htmlspecialchars($e['message']) . "</div></div>";
} else {
?>

<div class="hero-banner text-center">
    <div class="container">
        <h1 class="display-4 fw-bold"><i class="fa-solid fa-crown me-2"></i>Discover Campus Events</h1>
        <p class="lead text-light">Register for workshops, hackathons, and university club events effortlessly.</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <?php while ($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS)): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card card-custom h-100">
                    <div class="card-body">
                        <span class="badge badge-gold mb-2"><?php echo htmlspecialchars($row['CLUB_NAME']); ?></span>
                        <h5 class="card-title fw-bold text-white"><?php echo htmlspecialchars($row['TITLE']); ?></h5>
                        <p class="card-text text-secondary small"><?php echo htmlspecialchars($row['DESCRIPTION']); ?></p>
                        
                        <ul class="list-unstyled small text-light mt-3">
                            <li class="mb-1"><i class="fa-regular fa-calendar me-2" style="color: var(--gold);"></i><?php echo date('M d, Y', strtotime($row['EVENT_DATE'])); ?></li>
                            <li class="mb-1"><i class="fa-solid fa-location-dot me-2" style="color: var(--gold);"></i><?php echo htmlspecialchars($row['VENUE_NAME'] . " (" . $row['BUILDING'] . ")"); ?></li>
                            <li class="mb-1"><i class="fa-solid fa-tag me-2" style="color: var(--gold);"></i>LKR <?php echo number_format($row['TICKET_PRICE'], 2); ?></li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <a href="event_details.php?id=<?php echo $row['EVENT_ID']; ?>" class="btn btn-outline-gold w-100">View Details & Register</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php 
}
oci_free_statement($stmt);
oci_close($conn);
require_once 'views/footer.php'; 
?>