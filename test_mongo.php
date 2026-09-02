<?php
// Test MongoDB extension loading
echo "Testing MongoDB PHP Extension...\n";
echo "PHP Version: " . phpversion() . "\n";
echo "PHP CLI: " . PHP_SAPI . "\n";
echo "MongoDB Loaded: " . (extension_loaded('mongodb') ? 'YES' : 'NO') . "\n";

// Try to get extension info
$extensions = get_loaded_extensions();
echo "\nLoaded Extensions:\n";
foreach ($extensions as $ext) {
    if (stripos($ext, 'mongo') !== false) {
        echo "  - " . $ext . "\n";
    }
}

// Check for errors
if (!extension_loaded('mongodb')) {
    echo "\nMongoDB extension not loaded. Check these:\n";
    echo "1. php.ini location: " . php_ini_loaded_file() . "\n";
    echo "2. php.ini scan directory: " . ini_get('extension_dir') . "\n";
}
?>
