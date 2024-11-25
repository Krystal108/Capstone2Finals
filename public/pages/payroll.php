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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .modal-content h2 {
            text-align: center;
            color: #4c7742;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payroll System</h1>
    </div>
    <div class="content">
        <button class="btn" id="addRecordBtn">Add Record</button>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Payslip Date</th>
                    <th>Basic Pay</th>
                    <th>Weekly Pay</th>
                    <th>Net Pay</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($_SESSION['records'])): ?>
                    <?php foreach ($_SESSION['records'] as $index => $record): ?>
                        <tr>
                            <td><?= $record['employee_name']; ?></td>
                            <td><?= $record['position']; ?></td>
                            <td><?= $record['payslip_date']; ?></td>
                            <td>₱<?= number_format($record['basic_pay'], 2); ?></td>
                            <td>₱<?= number_format($record['weekly_pay'], 2); ?></td>
                            <td>₱<?= number_format($record['net_pay'], 2); ?></td>
                            <td>
                                <button class="btn btn-primary print-payslip" data-index="<?= $index; ?>">Print</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_index" value="<?= $index; ?>">
                                    <button type="submit" name="delete_record" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No records found.</td>
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
                <input type="number" name="sss" placeholder="SSS Deduction (Optional)" step="0.01">
                <input type="number" name="philhealth" placeholder="PhilHealth Deduction (Optional)" step="0.01">
                <input type="number" name="pagibig" placeholder="Pag-IBIG Deduction (Optional)" step="0.01">
                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" id="closeAddModal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Print Payslip Modal -->
    <div id="printPayslipModal" class="modal">
        <div class="modal-content">
            <h2>Payslip</h2>
            <div id="payslipContent"></div>
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" id="closePrintModal">Back</button>
                <button class="btn btn-success" onclick="printPayslip()">Print</button>
            </div>
        </div>
    </div>

    <script>
        const addModal = document.getElementById('recordModal');
        const printModal = document.getElementById('printPayslipModal');
        const closeAddModal = document.getElementById('closeAddModal');
        const closePrintModal = document.getElementById('closePrintModal');

        document.getElementById('addRecordBtn').addEventListener('click', () => {
            addModal.style.display = 'block';
        });

        closeAddModal.onclick = () => {
            addModal.style.display = 'none';
        };

        closePrintModal.onclick = () => {
            printModal.style.display = 'none';
        };

        document.querySelectorAll('.print-payslip').forEach((btn, index) => {
            btn.addEventListener('click', () => {
                const records = <?php echo json_encode($_SESSION['records']); ?>;
                const record = records[index];
                const payslipContent = `
                    <div>
                        <h3>${record.employee_name}'s Payslip</h3>
                        <p><strong>Position:</strong> ${record.position}</p>
                        <p><strong>Date:</strong> ${record.payslip_date}</p>
                        <p><strong>Net Pay:</strong> ₱${record.net_pay}</p>
                    </div>`;
                document.getElementById('payslipContent').innerHTML = payslipContent;
                printModal.style.display = 'block';
            });
        });

        function printPayslip() {
            const content = document.getElementById('payslipContent').innerHTML;
            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write('<html><head><title>Payslip</title></head><body>' + content + '</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</body>
</html>
