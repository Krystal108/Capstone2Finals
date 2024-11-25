<?php
session_start();

// Initialize records if not set
if (!isset($_SESSION['records'])) {
    $_SESSION['records'] = [];
}

// Handle Save Record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_record'])) {
    $employee_name = htmlspecialchars($_POST['employee_name']);
    $position = htmlspecialchars($_POST['position']);
    $payslip_date = htmlspecialchars($_POST['payslip_date']);
    $basic_pay = floatval($_POST['basic_pay']);
    $days_per_week = intval($_POST['days_per_week']);
    $overtime_pay = floatval($_POST['overtime_pay']);
    $late_deduct = floatval($_POST['late_deduct']);
    $sss = isset($_POST['sss']) ? floatval($_POST['sss']) : 0;
    $philhealth = isset($_POST['philhealth']) ? floatval($_POST['philhealth']) : 0;
    $pagibig = isset($_POST['pagibig']) ? floatval($_POST['pagibig']) : 0;

    // Calculate Weekly Pay
    $weekly_pay = $basic_pay * $days_per_week;

    // Calculate Total Deductions
    $total_deductions = $late_deduct + $sss + $philhealth + $pagibig;

    // Calculate Net Pay
    $net_pay = ($weekly_pay + $overtime_pay) - $total_deductions;

    // Add record to session
    $_SESSION['records'][] = [
        'employee_name' => $employee_name,
        'position' => $position,
        'payslip_date' => $payslip_date,
        'basic_pay' => $basic_pay,
        'weekly_pay' => $weekly_pay,
        'overtime_pay' => $overtime_pay,
        'late_deduct' => $late_deduct,
        'sss' => $sss,
        'philhealth' => $philhealth,
        'pagibig' => $pagibig,
        'total_deductions' => $total_deductions,
        'net_pay' => $net_pay,
    ];
}

// Handle Delete Record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_record'])) {
    $index = intval($_POST['delete_index']);
    unset($_SESSION['records'][$index]);
    $_SESSION['records'] = array_values($_SESSION['records']); // Reindex the array
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll System</title>
    <link rel="stylesheet" href="style_index.css">
    <link rel="stylesheet" href="dashboardnew.css">
    <link rel="icon" type="image/x-icon" href="Superpack-Enterprise-Logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'sidebar_small.php'; ?>
    <div class="container-everything">
        <div class="container-all">
            <div class="container-top">
                <?php include 'header_2.php'; ?>
            </div>

            <!-- Search Bar -->
            <div class="container-search">
                <form method="GET" action="" class="form-inline">
                    <input type="text" name="search_id" class="form-control" placeholder="Search by ID" style="margin-right:10px;">
                    <button class="btn btn-primary" type="submit">Search</button>
                    <button class="btn btn-success" type="button" data-toggle="modal" data-target="#addPayrollModal">Add Record</button>
                </form>
            </div>

            <!-- Payroll Table -->
            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Payslip Date</th>
                            <th>Basic Pay (Daily)</th>
                            <th>Weekly Pay</th>
                            <th>Overtime Pay</th>
                            <th>Late Deduct</th>
                            <th>Total Deductions</th>
                            <th>Net Pay</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['records'] as $index => $record): ?>
                            <tr>
                                <td><?php echo $record['employee_name']; ?></td>
                                <td><?php echo $record['position']; ?></td>
                                <td><?php echo $record['payslip_date']; ?></td>
                                <td>₱<?php echo number_format($record['basic_pay'], 2); ?></td>
                                <td>₱<?php echo number_format($record['weekly_pay'], 2); ?></td>
                                <td>₱<?php echo number_format($record['overtime_pay'], 2); ?></td>
                                <td>₱<?php echo number_format($record['late_deduct'], 2); ?></td>
                                <td>₱<?php echo number_format($record['total_deductions'], 2); ?></td>
                                <td>₱<?php echo number_format($record['net_pay'], 2); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="printPayslip(<?php echo $index; ?>)">Print</button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_index" value="<?php echo $index; ?>">
                                        <button type="submit" name="delete_record" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Payroll Modal -->
    <div class="modal fade" id="addPayrollModal" tabindex="-1" role="dialog" aria-labelledby="addPayrollModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPayrollModalLabel">New Payroll Record</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="employee_name" placeholder="Employee Name" class="form-control mb-2" required>
                        <input type="text" name="position" placeholder="Position" class="form-control mb-2" required>
                        <input type="date" name="payslip_date" class="form-control mb-2" required>
                        <input type="number" name="basic_pay" placeholder="Basic Pay (Daily)" class="form-control mb-2" required>
                        <input type="number" name="days_per_week" placeholder="Days per Week" class="form-control mb-2" value="5" required>
                        <input type="number" name="overtime_pay" placeholder="Overtime Pay" class="form-control mb-2">
                        <input type="number" name="late_deduct" placeholder="Late Deduction" class="form-control mb-2">
                        <input type="number" name="sss" placeholder="SSS Deduction" class="form-control mb-2">
                        <input type="number" name="philhealth" placeholder="PhilHealth Deduction" class="form-control mb-2">
                        <input type="number" name="pagibig" placeholder="Pag-IBIG Deduction" class="form-control mb-2">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="save_record" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Print Payslip Modal -->
    <div id="printPayslipModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payslip</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="payslipContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" onclick="printPayslip()">Print</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function printPayslip(index) {
            const records = <?php echo json_encode($_SESSION['records']); ?>;
            const record = records[index];

            const payslipHTML = `
                <h5>${record.employee_name}'s Payslip</h5>
                <p><strong>Date:</strong> ${record.payslip_date}</p>
                <p><strong>Position:</strong> ${record.position}</p>
                <p><strong>Basic Pay (Daily):</strong> ₱${record.basic_pay.toFixed(2)}</p>
                <p><strong>Weekly Pay:</strong> ₱${record.weekly_pay.toFixed(2)}</p>
                <p><strong>Overtime Pay:</strong> ₱${record.overtime_pay.toFixed(2)}</p>
                <p><strong>Late Deduction:</strong> ₱${record.late_deduct.toFixed(2)}</p>
                <p><strong>Total Deductions:</strong> ₱${record.total_deductions.toFixed(2)}</p>
                <p><strong>Net Pay:</strong> ₱${record.net_pay.toFixed(2)}</p>
            `;
            document.getElementById('payslipContent').innerHTML = payslipHTML;
            $('#printPayslipModal').modal('show');
        }
    </script>
</body>
</html>
