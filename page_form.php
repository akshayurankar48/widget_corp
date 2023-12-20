<?php require_once("includes/session.php"); ?>
<?php confirm_logged_in(); ?>
<?php // this page is included by new_page.php and edit_page.php 
?>
<?php if (!isset($new_page)) {
    $new_page = false;
} ?>

<p>Page name: <input type="text" name="menu_name" value="<?php echo isset($sel_page['menu_name']) ? ($sel_page['menu_name']) : ''; ?>" id="menu_name" /></p>

<p>Position: <select name="position">
        <?php
        if (!$new_page) {
            $page_set = get_pages_for_subject($sel_page['subject_id'], $conn);
            $page_count = $page_set ? mysqli_num_rows($page_set) : 0;
        } else {
            $page_set = get_pages_for_subject($sel_subject['id'], $conn);
            $page_count = $page_set ? mysqli_num_rows($page_set) + 1 : 1;
        }
        for ($count = 1; $count <= $page_count; $count++) {
            echo "<option value=\"{$count}\"";
            if ($sel_page && $sel_page['position'] == $count) {
                echo " selected";
            }
            echo ">{$count}</option>";
        }
        ?>
    </select></p>

<p>Visible:
    <input type="radio" name="visible" value="0" <?php echo ($sel_page && $sel_page['visible'] == 0) ? "checked" : ""; ?> /> No
    &nbsp;
    <input type="radio" name="visible" value="1" <?php echo ($sel_page && $sel_page['visible'] == 1) ? "checked" : ""; ?> /> Yes
</p>

<p>Content:<br />
    <textarea name="content" rows="20" cols="80"><?php echo isset($sel_page['content']) ? ($sel_page['content']) : ''; ?></textarea>
</p>