<?php
require("constants.php");
// This is the place for simple functions

function confirm_query($result_set, $conn)
{
    if (!$result_set) {
        die("Database query failed: " . $conn->error);
    }
}

function get_all_subjects($conn)
{
    global $conn;
    $sql = "SELECT * 
    FROM subjects
    ORDER BY position ASC";
    $subject_set = $conn->query($sql);

    // 3.  Check if the query was successful
    confirm_query($subject_set, $conn);
    return $subject_set;
}

function get_pages_for_subject($subject_id, $conn)
{
    global $conn;
    $sql = "SELECT * 
    FROM pages 
    WHERE subject_id = {$subject_id} 
    ORDER BY position ASC";
    $page_set = $conn->query($sql);

    // 3.  Check if the query was successful
    confirm_query($page_set, $conn);
    return $page_set;
}
