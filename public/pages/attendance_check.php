<?php 
session_start();

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: ../welcome.php');
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$database = "face_id";
$port = 3307;

// mysqli connection
$conn = new mysqli($host, $user, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addLeave'])) {
        $leave = [
            'leaveType' => $_POST['leaveType'],
            'startDate' => $_POST['startDate'],
            'endDate' => $_POST['endDate'],
            'status' => 'Pending',
        ];

        $stmt = $conn->prepare("INSERT INTO leave_request (leave_type, start_date, end_date, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $leave['leaveType'], $leave['startDate'], $leave['endDate'], $leave['status']);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style_index.css">
    <link rel="icon" type="image/x-icon" href="Superpack-Enterprise-Logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboardnew.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include 'filter_sidebar.php'?>
<?php include 'sidebar_small.php'?>
<div class="container-everything" style="height:100%;">
    <div class="container-all">
        <div class="container-top">
            <?php include 'header_2.php';?>
        </div>
        <div class="container-search">
            <div class="tool-bar">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button class="btn btn-primary mb-3" type="button" data-toggle="modal" data-target="#addLeaveModal">Create Leave Request</button>
                </div>
            </div>
            <div class="table-container">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT leave_type, start_date, end_date, status FROM leave_request WHERE name = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $username);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr><td>" . $row['leave_type'] . "</td><td>" . $row['start_date'] . "</td><td>" . $row['end_date'] . "</td><td>" . $row['status'] . "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No leave requests found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container-bottom">
            <div class="container-table">
                <div class="table-container">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT name, role, time_in, time_out, date FROM attendance WHERE name = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("s", $username);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $timeIn = $row['time_in'] ? date('h:i:s A', strtotime($row['time_in'])) : "Not Recorded";
                                    $timeOut = $row['time_out'] ? date('h:i:s A', strtotime($row['time_out'])) : "Not Recorded";
                                    $date = $row['date'];
                                    echo "<tr><td>{$row['name']}</td><td>{$row['role']}</td><td>$timeIn</td><td>$timeOut</td><td>$date</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No attendance records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
