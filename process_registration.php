<?php
require_once 'config/oracle_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;

    if ($event_id <= 0 || $student_id <= 0) {
        header("Location: index.php");
        exit;
    }

    // Call PL/SQL Stored Procedure
    $sql = 'BEGIN campuspulse_user.register_student_for_event(:p_student_id, :p_event_id, :p_status, :p_reg_id); END;';
    
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ':p_student_id', $student_id);
    oci_bind_by_name($stmt, ':p_event_id', $event_id);
    
    $status_msg = '';
    $reg_id = 0;
    
    oci_bind_by_name($stmt, ':p_status', $status_msg, 200);
    oci_bind_by_name($stmt, ':p_reg_id', $reg_id, 32);

    $executed = oci_execute($stmt);

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: my_registrations.php?student_id=" . $student_id);
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>