<?php
try {
    // Check if MongoDB extension is enabled
    if (!extension_loaded('mongodb')) {
        throw new Exception("The MongoDB PHP extension is not loaded in php.ini.");
    }

    // Connect to local MongoDB instance
    $mongo_manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

    // Ping the admin database to verify connectivity
    $command = new MongoDB\Driver\Command(['ping' => 1]);
    $mongo_manager->executeCommand('admin', $command);

    // echo "<h3 style='color:green;'>Connected to MongoDB Successfully!</h3>";

} catch (Exception $e) {
    die("<h3 style='color:red;'>MongoDB Connection Failed: " . htmlspecialchars($e->getMessage()) . "</h3>");
}
?>