<?php
require_once 'config/mongo_db.php';
require_once 'views/header.php';

$discussions = [];

if (isset($mongo_manager) && $mongo_manager !== null) {
    try {
        $query = new MongoDB\Driver\Query([]);
        $cursor = $mongo_manager->executeQuery('campuspulse_nosql.discussion_threads', $query);
        $discussions = $cursor->toArray();
    } catch (Exception $e) {
        // Handle mongo exception gracefully
    }
}
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-gold mb-1"><i class="fa-solid fa-comments me-2"></i>Campus Discussions & Q&A</h2>
            <p class="text-secondary mb-0">Join discussions and ask questions about upcoming events</p>
        </div>
    </div>

    <div class="row">
        <?php if (!empty($discussions)): ?>
            <?php foreach ($discussions as $thread): ?>
                <div class="col-12 mb-4">
                    <div class="card card-custom p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold text-white mb-0"><?php echo htmlspecialchars($thread->title); ?></h4>
                            <span class="badge badge-gold">Event ID: #<?php echo htmlspecialchars($thread->oracle_event_id); ?></span>
                        </div>
                        
                        <div class="forum-posts mt-3">
                            <?php if (!empty($thread->posts)): ?>
                                <?php foreach ($thread->posts as $post): ?>
                                    <div class="p-3 mb-2 rounded border-start border-3 border-warning" style="background-color: #1a1a1a;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-gold"><i class="fa-solid fa-user me-2"></i><?php echo htmlspecialchars($post->author); ?></strong>
                                        </div>
                                        <p class="text-light mb-0"><?php echo htmlspecialchars($post->content); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-secondary">No responses yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card card-custom p-4 text-center border-secondary">
                    <i class="fa-regular fa-comments fa-3x text-gold mb-3"></i>
                    <h5 class="text-white fw-bold">No Forum Discussions Yet</h5>
                    <p class="text-secondary mb-0">There are currently no active discussion topics available for campus events.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/footer.php'; ?>