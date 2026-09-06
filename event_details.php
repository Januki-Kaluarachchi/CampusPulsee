<?php
require_once 'config/oracle_db.php';
require_once 'config/mongo_db.php';
require_once 'views/header.php';

$event_id = isset($_GET['id']) ? intval($_GET['id']) : 1001;

// 1. Fetch Oracle Event Details
$oracle_query = "SELECT e.*, c.club_name, v.venue_name, v.building 
                FROM campuspulse_user.EVENTS e
                JOIN campuspulse_user.CLUBS_SOCIETIES c ON e.club_id = c.club_id
                JOIN campuspulse_user.VENUES v ON e.venue_id = v.venue_id
                WHERE e.event_id = :eid";

$stmt = oci_parse($conn, $oracle_query);
oci_bind_by_name($stmt, ":eid", $event_id);
oci_execute($stmt);
$event = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS);

if (!$event) {
    echo "<div class='container my-5'><div class='alert alert-danger'>Event not found!</div></div>";
    require_once 'views/footer.php';
    exit;
}

// 2. Fetch MongoDB Content (Agenda & Speakers) safely if connection exists
$mongo_content = null;
$discussions = [];

if (isset($mongo_manager) && $mongo_manager !== null) {
    try {
        $filter = ['oracle_event_id' => $event_id];
        $query = new MongoDB\Driver\Query($filter);
        
        $mongo_content_cursor = $mongo_manager->executeQuery('campuspulse_nosql.event_content', $query);
        $content_arr = $mongo_content_cursor->toArray();
        if (!empty($content_arr)) {
            $mongo_content = $content_arr[0];
        }

        $thread_cursor = $mongo_manager->executeQuery('campuspulse_nosql.discussion_threads', $query);
        $discussions = $thread_cursor->toArray();
    } catch (Exception $e) {
        // Suppress any mongo runtime exception
    }
}
?>

<div class="container my-5">
    <div class="row">
        <!-- Event Main Details -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 mb-4">
                <span class="badge badge-gold w-auto mb-2" style="max-width: 180px;"><?php echo htmlspecialchars($event['CLUB_NAME']); ?></span>
                <h2 class="fw-bold text-white"><?php echo htmlspecialchars($event['TITLE']); ?></h2>
                <p class="text-secondary"><?php echo htmlspecialchars($event['DESCRIPTION']); ?></p>

                <div class="row text-center border-top border-bottom border-secondary py-3 my-3">
                    <div class="col-4">
                        <i class="fa-regular fa-calendar fa-2x mb-2" style="color: var(--gold);"></i>
                        <h6 class="text-gold">Date</h6>
                        <small class="text-light"><?php echo date('M d, Y', strtotime($event['EVENT_DATE'])); ?></small>
                    </div>
                    <div class="col-4">
                        <i class="fa-solid fa-location-dot fa-2x mb-2" style="color: var(--gold);"></i>
                        <h6 class="text-gold">Location</h6>
                        <small class="text-light"><?php echo htmlspecialchars($event['VENUE_NAME']); ?></small>
                    </div>
                    <div class="col-4">
                        <i class="fa-solid fa-ticket fa-2x mb-2" style="color: var(--gold);"></i>
                        <h6 class="text-gold">Price</h6>
                        <small class="text-light">LKR <?php echo number_format($event['TICKET_PRICE'], 2); ?></small>
                    </div>
                </div>

                <!-- MongoDB Dynamic Agenda -->
                <?php if ($mongo_content && !empty($mongo_content->agendas)): ?>
                    <h4 class="mt-4 fw-bold text-gold"><i class="fa-solid fa-clock me-2"></i>Event Agenda</h4>
                    <ul class="list-group list-group-flush mb-4 rounded">
                        <?php foreach ($mongo_content->agendas as $item): ?>
                            <li class="list-group-item bg-dark text-light border-secondary d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-white"><?php echo htmlspecialchars($item->topic); ?></strong>
                                    <br><small class="text-secondary">Speaker: <?php echo htmlspecialchars($item->speaker); ?></small>
                                </div>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($item->time); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- MongoDB Discussion Forum -->
                <?php if (!empty($discussions)): ?>
                    <h4 class="mt-4 fw-bold text-gold"><i class="fa-solid fa-comments me-2"></i>Discussion Forum</h4>
                    <?php foreach ($discussions as $thread): ?>
                        <div class="border border-secondary rounded p-3 mb-3" style="background-color: #121212;">
                            <h6 class="fw-bold text-gold"><?php echo htmlspecialchars($thread->title); ?></h6>
                            <?php foreach ($thread->posts as $post): ?>
                                <div class="ms-3 my-2 p-2 rounded border-start border-3 border-warning" style="background-color: #1e1e1e;">
                                    <small class="fw-bold text-gold"><?php echo htmlspecialchars($post->author); ?>:</small>
                                    <p class="mb-0 small text-light"><?php echo htmlspecialchars($post->content); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar / Registration Form -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 sticky-top" style="top: 20px;">
                <h4 class="fw-bold text-gold mb-3 text-center">Reserve Your Spot</h4>
                
                <form action="process_registration.php" method="POST">
                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">

                    <div class="mb-3">
                        <label class="form-label text-light small">Student ID / Index No</label>
                        <input type="number" name="student_id" class="form-control bg-dark text-white border-secondary" placeholder="e.g., 106" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light small">First Name</label>
                        <input type="text" name="first_name" class="form-control bg-dark text-white border-secondary" placeholder="John" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light small">Last Name</label>
                        <input type="text" name="last_name" class="form-control bg-dark text-white border-secondary" placeholder="Doe">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light small">Email Address</label>
                        <input type="email" name="email" class="form-control bg-dark text-white border-secondary" placeholder="john@campus.lk" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light small">Department</label>
                        <input type="text" name="department" class="form-control bg-dark text-white border-secondary" placeholder="Software Engineering">
                    </div>

                    <button type="submit" class="btn btn-gold w-100 py-2 mt-2">Confirm Registration</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
oci_free_statement($stmt);
oci_close($conn);
require_once 'views/footer.php'; 
?>