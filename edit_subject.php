<?php
require_once("includes/connection.php");
require_once("includes/functions.php");

if (intval($_GET['subj']) == 0) {
    redirect_to("content.php");
}

if (isset($_POST['submit'])) {
    $id = mysqli_real_escape_string($conn, $_GET['subj']);
    $menu_name = mysqli_real_escape_string($conn, $_POST['menu_name']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $visible = isset($_POST['visible']) ? 1 : 0;

    // Validation
    $errors = array();

    if (empty($menu_name) || !is_numeric($position) || ($visible !== 0 && $visible !== 1)) {
        $errors[] = 'Invalid input. Please fill in all fields.';
    }

    if (empty($errors)) {
        // Perform Update
        $query = "UPDATE subjects SET 
                    menu_name = '{$menu_name}', 
                    position = {$position}, 
                    visible = {$visible} 
                WHERE id = {$id}";

        $result = $conn->query($query);

        if ($result && mysqli_affected_rows($conn) == 1) {
            // Success
            $message = "The subject was successfully updated.";
        } else {
            // Failed
            $message = "The subject update failed.";
            $message .= "<br />" . $conn->error;
        }
    } else {
        // Errors occurred
        $message = "There were errors in the form.";
    }
}

find_selected_page();

include("includes/header.php");
?>
<table id="structure">
    <tr>
        <td id="navigation">
            <?php echo navigation($sel_subject, $sel_page, $conn); ?>
        </td>
        <td id="page">
            <h2>Edit Subject: <?php echo $sel_subject['menu_name']; ?></h2>
            <?php if (!empty($message)) : ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            <?php if (!empty($errors)) : ?>
                <p class="errors"><?php echo implode("<br />", $errors); ?></p>
            <?php endif; ?>
            <form action="edit_subject.php?subj=<?php echo urlencode($sel_subject['id']); ?>" method="post">
                <p>Subject name:
                    <input type="text" name="menu_name" value="<?php echo htmlspecialchars($sel_subject['menu_name']); ?>" id="menu_name" />
                </p>
                <p>Position:
                    <select name="position">
                        <?php
                        $subject_set = get_all_subjects($conn);
                        $subject_count = mysqli_num_rows($subject_set);

                        for ($count = 1; $count <= $subject_count + 1; $count++) {
                            echo "<option value=\"{$count}\"";
                            if ($sel_subject['position'] == $count) {
                                echo " selected";
                            }
                            echo ">{$count}</option>";
                        }
                        ?>
                    </select>
                </p>
                <p>Visible:
                    <input type="checkbox" name="visible" value="1" <?php echo ($sel_subject['visible'] == 1) ? 'checked' : ''; ?> /> Yes
                </p>
                <input type="submit" name="submit" value="Edit Subject" />
                &nbsp;&nbsp;
                <a href="delete_subject.php?subj=<?php echo urlencode($sel_subject['id']); ?>" onclick="return confirm('Are you sure?');">Delete Subject</a>
            </form>
            <br />
            <a href="content.php">Cancel</a>
            <div style="margin-top: 2em; border-top: 1px solid #000000;">
                <h3>Pages in this subject:</h3>
                <ul>
                    <?php
                    $subject_pages = get_pages_for_subject($sel_subject['id'], $conn);
                    while ($page = mysqli_fetch_array($subject_pages)) {
                        echo "<li><a href=\"content.php?page={$page['id']}\">
                            {$page['menu_name']}</a></li>";
                    }
                    ?>
                </ul>
                <br />
                + <a href="new_page.php?subj=<?php echo $sel_subject['id']; ?>">Add a new page to this subject</a>
            </div>
        </td>
    </tr>
</table>
<?php require("includes/footer.php"); ?>