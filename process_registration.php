<?php
require_once 'config/oracle_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    $student_id = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $department = trim($_POST['department']);

    if ($event_id > 0 && $student_id > 0) {
        
        // 1. Check if Student exists in USERS table
        $check_sql = "SELECT COUNT(*) AS CNT FROM USERS WHERE student_id = :sid";
        $check_stmt = oci_parse($conn, $check_sql);
        oci_bind_by_name($check_stmt, ':sid', $student_id);
        oci_execute($check_stmt);
        $row = oci_fetch_array($check_stmt, OCI_ASSOC);
        oci_free_statement($check_stmt);

        // 2. If student does not exist, insert into USERS table
        if ($row['CNT'] == 0) {
            $ins_sql = "INSERT INTO USERS (student_id, first_name, last_name, email, department, year_of_study) 
                        VALUES (:sid, :fname, :lname, :email, :dept, 1)";
            $ins_stmt = oci_parse($conn, $ins_sql);
            oci_bind_by_name($ins_stmt, ':sid', $student_id);
            oci_bind_by_name($ins_stmt, ':fname', $first_name);
            oci_bind_by_name($ins_stmt, ':lname', $last_name);
            oci_bind_by_name($ins_stmt, ':email', $email);
            oci_bind_by_name($ins_stmt, ':dept', $department);
            
            oci_execute($ins_stmt, OCI_NO_AUTO_COMMIT);
            oci_free_statement($ins_stmt);
        }

        // 3. Execute PL/SQL Procedure for Event Registration
        $proc_sql = 'BEGIN register_student_for_event(:p_student_id, :p_event_id, :p_status, :p_reg_id); END;';
        $stmt = oci_parse($conn, $proc_sql);
        oci_bind_by_name($stmt, ':p_student_id', $student_id);
        oci_bind_by_name($stmt, ':p_event_id', $event_id);
        
        $status_msg = '';
        $reg_id = 0;
        
        oci_bind_by_name($stmt, ':p_status', $status_msg, 200);
        oci_bind_by_name($stmt, ':p_reg_id', $reg_id, 32);

        oci_execute($stmt, OCI_NO_AUTO_COMMIT);
        oci_free_statement($stmt);

        // 4. Commit changes permanently to Oracle DB
        oci_commit($conn);
        oci_close($conn);

        // Redirect to My Registrations page
        header("Location: my_registrations.php?student_id=" . $student_id);
        exit;
    }
}

header("Location: index.php");
exit;
?>