<?php require_once("includes/session.php"); ?>
<?php
require_once("includes/connection.php");
require_once("includes/functions.php");
?>
<?php confirm_logged_in(); ?>
<?php
if (intval($_GET['subj']) == 0) {
    redirect_to("content.php");
}

$id = mysqli_real_escape_string($conn, $_GET['subj']);

if ($subject = get_subject_by_id($id, $conn)) {
    $query = "DELETE FROM subjects WHERE id = {$id} LIMIT 1";
    $result = $conn->query($query);

    if ($conn->affected_rows == 1) {
        redirect_to("content.php");
    } else {
        // Deletion Failed
        echo "<p>Subject deletion failed.</p>";
        echo "<p>" . $conn->error . "</p>";
        echo "<a href=\"content.php\">Return to Main Page</a>";
    }
} else {
    // Subject didn't exist in the database
    redirect_to("content.php");
}

$conn->close();
