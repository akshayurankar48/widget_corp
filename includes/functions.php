<?php
require("constants.php");
// This is the place for simple functions

function confirm_query($result_set, $conn)
{
    if (!$result_set) {
        die("Database query failed: " . $conn->error);
    }
}

function redirect_to($location = NULL)
{
    if ($location != NULL) {
        header("Location: {$location}");
        exit;
    }
}

function get_all_subjects($conn)
{
    global $conn;
    $sql = "SELECT * 
    FROM subjects
    ORDER BY position ASC";
    $subject_set = $conn->query($sql);


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


    confirm_query($page_set, $conn);
    return $page_set;
}

function get_subject_by_id($subject_id, $conn)
{
    // Validate $subject_id
    if (empty($subject_id)) {
        return NULL;
    }
    global $conn;
    $query = "SELECT * ";
    $query .= "FROM subjects ";
    $query .= "WHERE id=" . $subject_id . " ";
    $query .= "LIMIT 1";

    $result_set = $conn->query($query);
    confirm_query($result_set, $conn);

    // REMEMBER: 
    // if no rows are returned, fetch_array will return false
    if ($subject = $result_set->fetch_assoc()) {
        return $subject;
    } else {
        return NULL;
    }
}

function get_page_by_id($page_id, $conn)
{
    // Validate $subject_id
    if (empty($page_id)) {
        return NULL;
    }
    global $conn;
    $query = "SELECT * ";
    $query .= "FROM pages ";
    $query .= "WHERE id=" . $page_id . " ";
    $query .= "LIMIT 1";

    $result_set = $conn->query($query);
    confirm_query($result_set, $conn);

    // REMEMBER: 
    // if no rows are returned, fetch_array will return false
    if ($page = $result_set->fetch_assoc()) {
        return $page;
    } else {
        return NULL;
    }
}

function find_selected_page()
{
    global $sel_subject, $sel_page, $conn;

    if (isset($_GET['subj'])) {
        $sel_subject = get_subject_by_id($_GET['subj'], $conn);
        $sel_page = NULL;
    } elseif (isset($_GET['page'])) {
        $sel_subject = NULL;
        $sel_page = get_page_by_id($_GET['page'], $conn);
    } else {
        $sel_subject = NULL;
        $sel_page = NULL;
    }
}

function navigation($sel_subject, $sel_page, $conn)
{
    $output = "<ul class=\"subjects\">";
    $subject_set = get_all_subjects($conn);

    while ($subject = $subject_set->fetch_assoc()) {
        $output .= "<li";

        if (!is_null($sel_subject) && $subject["id"] == $sel_subject['id']) {
            $output .= " class=\"selected\"";
        }

        $output .= "><a href=\"edit_subject.php?subj=" . urlencode($subject["id"]) .
            "\">{$subject["menu_name"]}</a>";

        $page_set = get_pages_for_subject($subject["id"], $conn);

        if ($page_set->num_rows > 0) {
            $output .= "<ul class=\"pages\">";

            while ($page = $page_set->fetch_assoc()) {
                $output .= "<li";

                if (!is_null($sel_page) && $page["id"] == $sel_page['id']) {
                    $output .= " class=\"selected\"";
                }

                $output .= "><a href=\"content.php?page=" . urlencode($page["id"]) .
                    "\">{$page["menu_name"]}</a></li>";
            }

            $output .= "</ul>";
        }

        $output .= "</li>";
    }

    $output .= "</ul>";

    return $output;
}
