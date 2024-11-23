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

// Fetch records
$sql = "SELECT id, name, position, basic_pay, daily_rate, overtime_pay, late_deduct, sss_deduct, pagibig_deduct, philhealth_deduct, total_deduct, date_created FROM payroll";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['position']}</td>
                <td>{$row['basic_pay']}</td>
                <td>{$row['daily_rate']}</td>
                <td>{$row['overtime_pay']}</td>
                <td>{$row['late_deduct']}</td>
                <td>{$row['sss_deduct']}</td>
                <td>{$row['pagibig_deduct']}</td>
                <td>{$row['philhealth_deduct']}</td>
                <td>{$row['total_deduct']}</td>
                <td>{$row['date_created']}</td>
                <td>
                    <button class='btn btn-primary btn-sm'>Edit</button>
                    <button class='btn btn-danger btn-sm'>Delete</button>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='12' class='text-center'>No records found.</td></tr>";
}

$conn->close();
?>
