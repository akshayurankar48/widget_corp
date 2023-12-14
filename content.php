<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include("includes/header.php"); ?>
<table id="structure">
    <tr>
        <td id="navigation">
            <ul class="subjects">
                <?php
                $subject_set = get_all_subjects($conn);

                // 4. Use returned data
                while ($subject = $subject_set->fetch_assoc()) {
                    echo "<li>{$subject["menu_name"]}</li>";
                    // 2. Perform database query
                    $page_set = get_pages_for_subject($subject["id"], $conn);

                    echo "<ul class=\"pages\">";
                    // 4. Use returned data
                    while ($page = $page_set->fetch_assoc()) {
                        echo "<li>{$page["menu_name"]}</li>";
                    }
                    echo "</ul>";
                }
                ?>
            </ul>
        </td>
        <td id="page">
            <h2>Content Area</h2>
        </td>
    </tr>
</table>
<?php require("includes/footer.php"); ?>