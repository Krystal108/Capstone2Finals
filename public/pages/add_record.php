<?php
// Database connection
$host = "localhost";
$user = "root";
$password = ""; // Replace with your password
$database = "workers_salary";
$port = 3306;

$conn = new mysqli($host, $user, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert record into the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $position = $conn->real_escape_string($_POST['position']);
    $basic_pay = $conn->real_escape_string($_POST['basic_pay']);
    $overtime_pay = $conn->real_escape_string($_POST['overtime_pay']);
    $late_deduct = $conn->real_escape_string($_POST['late_deduct']);
    $sss_deduct = $conn->real_escape_string($_POST['sss_deduct']);
    $pagibig_deduct = $conn->real_escape_string($_POST['pagibig_deduct']);
    $philhealth_deduct = $conn->real_escape_string($_POST['philhealth_deduct']);

    $sql = "INSERT INTO payroll (name, position, basic_pay, overtime_pay, late_deduct, sss_deduct, pagibig_deduct, philhealth_deduct)
            VALUES ('$name', '$position', '$basic_pay', '$overtime_pay', '$late_deduct', '$sss_deduct', '$pagibig_deduct', '$philhealth_deduct')";

    if ($conn->query($sql) === TRUE) {
        echo "Record added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
