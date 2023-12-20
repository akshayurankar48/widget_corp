<?php require_once("includes/session.php"); ?>
<?php
require_once("includes/connection.php");
require_once("includes/functions.php");
?>
<?php confirm_logged_in(); ?>
<?php
// make sure the page id sent is an integer
if (intval($_GET['page']) == 0) {
    redirect_to('content.php');
}

// START FORM PROCESSING
// only execute the form processing if the form has been submitted
if (isset($_POST['submit'])) {
    // initialize an array to hold our errors
    $errors = array();

    // perform validations on the form data
    $id = isset($_GET['page']) ? mysqli_real_escape_string($conn, $_GET['page']) : 0;
    $menu_name = mysqli_real_escape_string($conn, $_POST['menu_name']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $visible = isset($_POST['visible']) ? 1 : 0;
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    if (empty($menu_name) || !is_numeric($position) || ($visible !== 0 && $visible !== 1)) {
        $errors[] = 'Invalid input. Please fill in all fields.';
    }

    // Database submission only proceeds if there were NO errors.
    if (empty($errors)) {
        $query = "UPDATE pages SET 
                    menu_name = '{$menu_name}',
                    position = {$position}, 
                    visible = {$visible},
                    content = '{$content}'
                WHERE id = {$id}";
        $result = $conn->query($query);
        // test to see if the update occurred
        if ($result && mysqli_affected_rows($conn) == 1) {
            // Success!
            $message = "The page was successfully updated.";
        } else {
            $message = "The page could not be updated.";
            $message .= "<br />" . $conn->error;
        }
    } else {
        if (count($errors) == 1) {
            var_dump($errors);
            $message = "There was 1 error in the form.";
        } else {
            $message = "There were " . count($errors) . " errors in the form.";
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
            <?php echo navigation($sel_subject, $sel_page, $conn); ?>
            <br />
            <a href="new_subject.php">+ Add a new subject</a>
        </td>
        <td id="page">
            <h2>Edit page: <?php echo $sel_page['menu_name']; ?></h2>
            <?php if (!empty($message)) {
                echo "<p class=\"message\">" . $message . "</p>";
            } ?>
            <?php if (!empty($errors)) {
                display_errors($errors);
            } ?>

            <form action="edit_page.php?page=<?php echo $sel_page['id']; ?>" method="post">
                <p>Page name:
                    <input type="text" name="menu_name" value="<?php echo htmlspecialchars($sel_page['menu_name']); ?>" />
                </p>
                <p>Position:
                    <input type="text" name="position" value="<?php echo $sel_page['position']; ?>" />
                </p>
                <p>Visible:
                    <input type="checkbox" name="visible" value="1" <?php echo ($sel_page['visible'] == 1) ? 'checked' : ''; ?> /> Yes
                </p>
                <p>Content:<br />
                    <textarea name="content" rows="20" cols="80"><?php echo htmlspecialchars($sel_page['content']); ?></textarea>
                </p>
                <input type="submit" name="submit" value="Update Page" />&nbsp;&nbsp;
                <a href="delete_page.php?page=<?php echo $sel_page['id']; ?>" onclick="return confirm('Are you sure you want to delete this page?');">Delete page</a>
            </form>

            <br />
            <a href="content.php?page=<?php echo $sel_page['id']; ?>">Cancel</a><br />
        </td>
    </tr>
</table>

<?php include("includes/footer.php"); ?>