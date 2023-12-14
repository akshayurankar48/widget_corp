<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php include("includes/header.php"); ?>
<table id="structure">
    <tr>
        <td id="navigation">
            <?php
            // 2. Perform database query
            $sql = "SELECT * FROM subjects";
            $result = $conn->query($sql);

            // 3.  Check if the query was successful
            if (!$result) {
                die("Database query failed: " . $conn->error);
            }

            // 4. Use returned data
            while ($row = $result->fetch_assoc()) {
                echo $row["menu_name"] . " " . $row["position"] . "<br />";
            }
            ?>
        </td>
        <td id="page">
            <h2>Content Area</h2>
        </td>
    </tr>
</table>
<?php require("includes/footer.php"); ?>