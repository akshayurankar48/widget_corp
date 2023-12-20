<?php require_once("includes/session.php"); ?>
<?php require_once("includes/connection.php"); ?>
<?php require_once("includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php

$errors = array();

//   Form Validation
$required_fields = ['menu_name', 'position', 'visible'];
$errors = [];

foreach ($required_fields as $fieldname) {
    if (!isset($_POST[$fieldname]) || empty($_POST[$fieldname])) {
        $errors[] = $fieldname;
    }
}

$fields_with_lengths = array('menu_name' => 30);
foreach ($fields_with_lengths as $fieldname => $maxlength) {
    if (strlen(trim(mysqli_real_escape_string($conn, $_POST[$fieldname]))) > $maxlength) {
        $errors[] = $fieldname;
    }
}

if (!empty($errors)) {
    redirect_to("new_subject.php");
}
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menu_name = $_POST['menu_name'];
    $position = $_POST['position'];
    $visible = $_POST['visible'];


    $query = "INSERT INTO subjects (menu_name, position, visible) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $menu_name, $position, $visible);

    if ($stmt->execute()) {
        redirect_to("content.php");
    } else {
        // Display error message.
        echo "<p>Subject creation failed.</p>";
        echo "<p>" . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>
