<?php

function check_required_fields($required_fields, $form_data)
{
    $errors = array();
    foreach ($required_fields as $field) {
        if (empty(trim($form_data[$field]))) {
            $errors[] = $field;
        }
    }
    return $errors;
}

function check_max_field_lengths($fields_with_lengths, $form_data, $conn)
{
    $errors = array();
    foreach ($fields_with_lengths as $field => $max_length) {
        $value = trim(mysqli_real_escape_string($conn, $form_data[$field]));
        if (strlen($value) > $max_length) {
            $errors[] = $field;
        }
    }
    return $errors;
}

function display_errors($error_array)
{
    echo "<p class=\"errors\">";
    echo "Please review the following fields:<br />";
    foreach ($error_array as $error) {
        echo " - " . $error . "<br />";
    }
    echo "</p>";
}
