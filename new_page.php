<?php
require_once("includes/connection.php");
require_once("includes/functions.php");

// Make sure the subject id sent is an integer
if (intval($_GET['subj']) == 0) {
    redirect_to('content.php');
}

include_once("includes/form_functions.php");

// START FORM PROCESSING
// Only execute the form processing if the form has been submitted
if (isset($_POST['submit'])) {
    // Initialize an array to hold our errors
    $errors = array();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $subject_id = isset($_GET['subj']) ? $_GET['subj'] : 0;
        var_dump('subj');
        $menu_name = $_POST['menu_name'];
        $position = $_POST['position'];
        $visible = $_POST['visible'];
        $content = $_POST['content'];

        $query = "INSERT INTO pages (subject_id, menu_name, position, visible, content) VALUES (?, ?, ?, ?, ?)";

        var_dump($query);

        $stmt = $conn->prepare($query);
        $stmt->bind_param("isisi", $subject_id, $menu_name, $position, $visible, $content);

        if ($stmt->execute()) {
            $message = "The page was successfully created.";
            var_dump($query);
            redirect_to("new_page.php");
        } else {
            // Display error message.
            echo "<p>Page creation failed.</p>";
            echo "<p>" . $stmt->error . "</p>";
        }
    }
    // END FORM PROCESSING
}

find_selected_page();
include("includes/header.php");
?>

<table id="structure">
    <tr>
        <td id="navigation">
            <?php echo navigation($sel_subject, $sel_page, $public = false); ?>
            <br />
            <a href="new_subject.php">+ Add a new subject</a>
        </td>
        <td id="page">
            <h2>Adding New Page</h2>
            <?php if (!empty($message)) {
                echo "<p class=\"message\">" . $message . "</p>";
            } ?>
            <?php if (!empty($errors)) {
                display_errors($errors);
            } ?>

            <form action="new_page.php?subj=<?php echo $sel_subject['id']; ?>" method="post">
                <?php $new_page = true; ?>
                <?php include "page_form.php" ?>
                <input type="submit" name="submit" value="Create Page" />
            </form>
            <br />
            <a href="edit_subject.php?subj=<?php echo $sel_subject['id']; ?>">Cancel</a><br />
        </td>
    </tr>
</table>

<?php include("includes/footer.php"); ?>