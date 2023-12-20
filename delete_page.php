<?php require_once("includes/session.php"); ?>
<?php
require_once("includes/connection.php");
require_once("includes/functions.php");
?>
<?php confirm_logged_in(); ?>
<?php
// Make sure the page id sent is an integer
if (intval($_GET['page']) == 0) {
    redirect_to('content.php');
}

$id = mysqli_real_escape_string($conn, $_GET['page']);

// Make sure the page exists (not strictly necessary)
// It gives some extra security and allows the use of
// the page's subject_id for the redirect
if ($page = get_page_by_id($id, $conn)) {
    // LIMIT 1 isn't necessary but is a good fail-safe
    $query = "DELETE FROM pages WHERE id = {$page['id']} LIMIT 1";
    $result = $conn->query($query);

    if ($result && $conn->affected_rows == 1) {
        // Successfully deleted
        redirect_to("edit_subject.php?subj={$page['subject_id']}");
    } else {
        // Deletion failed
        echo "<p>Page deletion failed.</p>";
        echo "<p>" . $conn->error . "</p>";
        echo "<a href=\"content.php\">Return to Main Site</a>";
    }
} else {
    // Page didn't exist, deletion was not attempted
    redirect_to('content.php');
}

// Because this file didn't include footer.php, we need to add this manually
$conn->close();
