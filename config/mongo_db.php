<?php
// CampusPulse MongoDB Safe Connection Handler
try {
    // Attempt to connect to local MongoDB if extension is available
    if (extension_loaded('mongodb')) {
        $mongo_manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    } else {
        $mongo_manager = null;
    }
} catch (Exception $e) {
    $mongo_manager = null;
}
?>