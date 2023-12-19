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

function get_all_subjects($public = true)
{
    global $conn;

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM subjects ";
    if ($public) {
        $query .= "WHERE visible = 1 ";
    }
    $query .= "ORDER BY position ASC";

    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Execute the query
        $stmt->execute();

        // Get the result set
        $subject_set = $stmt->get_result();

        // Confirm the query
        confirm_query($subject_set, $conn);

        // Close the statement
        $stmt->close();

        return $subject_set;
    } else {
        // Handle the case when the statement preparation fails
        die("Database query failed: " . $conn->error);
    }
}

function get_pages_for_subject($subject_id, $public = true)
{
    global $conn;

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM pages ";
    $query .= "WHERE subject_id = ? ";

    if ($public) {
        $query .= "AND visible = 1 ";
    }

    $query .= "ORDER BY position ASC";

    // Prepare the statement
    $stmt = $conn->prepare($query);

    // Bind the parameter
    $stmt->bind_param("i", $subject_id);

    // Execute the query
    $stmt->execute();

    // Get the result set
    $result_set = $stmt->get_result();

    // Confirm the query
    confirm_query($result_set, $conn);

    // Close the statement
    $stmt->close();

    return $result_set;
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

function get_default_page($subject_id)
{
    // Get all visible pages
    $page_set = get_pages_for_subject($subject_id, true);


    $first_page = $page_set->fetch_assoc();

    if ($first_page) {
        return $first_page;
    } else {
        return NULL;
    }
}


function find_selected_page()
{
    global $conn;
    global $sel_subject;
    global $sel_page;
    if (isset($_GET['subj'])) {
        $sel_subject = get_subject_by_id($_GET['subj'], $conn);
        $sel_page = get_default_page($sel_subject['id']);
    } elseif (isset($_GET['page'])) {
        $sel_subject = NULL;
        $sel_page = get_page_by_id($_GET['page'], $conn);
    } else {
        $sel_subject = NULL;
        $sel_page = NULL;
    }
}


function navigation($sel_subject, $sel_page, $conn, $public = false)
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

function public_navigation($sel_subject, $sel_page, $public = true)
{
    $output = "<ul class=\"subjects\">";

    // Fetch subjects using mysqli
    $subject_set = get_all_subjects($public);

    while ($subject = mysqli_fetch_assoc($subject_set)) {
        $output .= "<li";

        if (!is_null($sel_subject) && $subject["id"] == $sel_subject['id']) {
            $output .= " class=\"selected\"";
        }

        $output .= "><a href=\"index.php?subj=" . urlencode($subject["id"]) . "\">{$subject["menu_name"]}</a></li>";

        if (!is_null($sel_subject) && $subject["id"] == $sel_subject['id']) {
            // Fetch pages for the selected subject
            $page_set = get_pages_for_subject($subject["id"], $public);
            $output .= "<ul class=\"pages\">";

            while ($page = mysqli_fetch_assoc($page_set)) {
                $output .= "<li";

                if (!is_null($sel_page) && $page["id"] == $sel_page['id']) {
                    $output .= " class=\"selected\"";
                }

                $output .= "><a href=\"index.php?page=" . urlencode($page["id"]) . "\">{$page["menu_name"]}</a></li>";
            }

            $output .= "</ul>";
        }
    }

    $output .= "</ul>";

    return $output;
}
