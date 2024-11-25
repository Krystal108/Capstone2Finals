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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll System</title>
    <link rel="stylesheet" href="style_index.css">
    <link rel="stylesheet" href="dashboardnew.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }
        .header {
            text-align: center;
            background: #4c7742;
            color: white;
            padding: 20px;
        }
        .content {
            padding: 20px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #4c7742;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #337ab7;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #4c7742;
            color: white;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 5px;
            width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .modal-content h2 {
            text-align: center;
            color: #4c7742;
        }
        .modal-content input {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .cancel-btn {
            background-color: #c0392b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .cancel-btn:hover {
            background-color: #e74c3c;
        }
        .print-btn {
            background-color: #4c7742;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .print-btn:hover {
            background-color: #337ab7;
        }
    </style>
</head>
<body>
    <?php include 'sidebar_small.php'; ?>
    <div class="content">
        <button class="btn" id="addRecordBtn">Add Record</button>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Position</th>
                    <th>Payslip Date</th>
                    <th>Basic Pay</th>
                    <th>Weekly Pay</th>
                    <th>Overtime Pay</th>
                    <th>Late Deduct</th>
                    <th>Total Deductions</th>
                    <th>Net Pay</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($_SESSION['records'])): ?>
                    <?php foreach ($_SESSION['records'] as $index => $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['employee_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['position']); ?></td>
                            <td><?php echo htmlspecialchars($record['payslip_date']); ?></td>
                            <td>₱<?php echo number_format($record['basic_pay'], 2); ?></td>
                            <td>₱<?php echo number_format($record['weekly_pay'], 2); ?></td>
                            <td>₱<?php echo number_format($record['overtime_pay'], 2); ?></td>
                            <td>₱<?php echo number_format($record['late_deduct'], 2); ?></td>
                            <td>₱<?php echo number_format($record['total_deductions'], 2); ?></td>
                            <td>₱<?php echo number_format($record['net_pay'], 2); ?></td>
                            <td>
                                <button class="btn print-payslip" data-index="<?php echo $index; ?>">Print Payslip</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_index" value="<?php echo $index; ?>">
                                    <button type="submit" name="delete_record" class="btn cancel-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Record Modal -->
    <div id="recordModal" class="modal">
        <div class="modal-content">
            <h2>Add Payroll Record</h2>
            <form method="POST">
                <input type="hidden" name="save_record" value="1">
                <input type="text" name="employee_name" placeholder="Employee Name" required>
                <input type="text" name="position" placeholder="Position" required>
                <input type="date" name="payslip_date" required>
                <input type="number" name="basic_pay" placeholder="Basic Pay (Daily)" step="0.01" required>
                <input type="number" name="days_per_week" placeholder="Days per Week" value="5" min="1" max="7" required>
                <input type="number" name="overtime_pay" placeholder="Overtime Pay" step="0.01">
                <input type="number" name="late_deduct" placeholder="Late Deduction" step="0.01">
                <input type="number" name="sss" placeholder="SSS Deduction" step="0.01">
                <input type="number" name="philhealth" placeholder="PhilHealth Deduction" step="0.01">
                <input type="number" name="pagibig" placeholder="Pag-IBIG Deduction" step="0.01">
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" id="cancelModal">Cancel</button>
                    <button type="submit" class="btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('recordModal');
        const addRecordBtn = document.getElementById('addRecordBtn');
        const cancelModal = document.getElementById('cancelModal');

        addRecordBtn.onclick = () => {
            modal.style.display = 'block';
        };

        cancelModal.onclick = () => {
            modal.style.display = 'none';
        };

        document.querySelectorAll('.print-payslip').forEach((btn, index) => {
            btn.addEventListener('click', () => {
                const records = <?php echo json_encode($_SESSION['records']); ?>;
                const record = records[index];

                const payslipContent = `
                    <div style="text-align:center;">
                        <h2>Superpack Enterprise</h2>
                        <p>123 Business St., Meycauayan, Bulacan</p>
                        <p>Contact: +63 912 345 6789 | Email: info@superpack.com</p>
                        <hr>
                        <h3>Payslip</h3>
                        <p><strong>Date:</strong> ${record.payslip_date}</p>
                        <p><strong>Employee Name:</strong> ${record.employee_name}</p>
                        <p><strong>Position:</strong> ${record.position}</p>
                        <table style="width:100%; border-collapse: collapse;">
                            <tr>
                                <th>Earnings</th>
                                <th>Amount</th>
                            </tr>
                            <tr>
                                <td>Basic Pay</td>
                                <td>₱${record.basic_pay.toFixed(2)}</td>
                            </tr>
                        </table>
                    </div>
                `;
                const printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.write(payslipContent);
                printWindow.print();
            });
        });
    </script>
</body>
</html>
