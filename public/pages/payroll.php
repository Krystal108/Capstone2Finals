<?php
session_start();

// Check if form data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
    $role = isset($_POST['role']) ? htmlspecialchars($_POST['role']) : '';
    $user_department = isset($_POST['user_department']) ? htmlspecialchars($_POST['user_department']) : '';

    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['user_department'] = $user_department;

    $_SESSION['loggedin'] = true;
}

if (!isset($_SESSION['loggedin'])) {
    header('Location: /?page=login');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll</title>
    <link rel="stylesheet" href="style_index.css">
    <link rel="icon" type="image/x-icon" href="Superpack-Enterprise-Logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboardnew.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'sidebar_small.php'; ?>
    <div class="container-everything" style="height:100%;">
        <div class="container-all">
            <div class="container-top">
                <?php include 'header_2.php'; ?>
            </div>
            <div class="container-search">
                <div class="table-container">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Daily Rate</th>
                                <th>Weekly Pay</th>
                                <th>Overtime Pay</th>
                                <th>Late Deduct</th>
                                <th>SSS Deduct</th>
                                <th>Pag-IBIG Deduct</th>
                                <th>PhilHealth Deduct</th>
                                <th>Net Salary</th>
                                <th>Date Created</th>
                            </tr>
                        </thead>
                        <tbody class="payroll-table">
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-primary mb-3" type="button" data-toggle="modal" data-target="#addPayrollModal">Add Record</button>
            </div>
        </div>
    </div>

    <!-- Add Payroll Modal -->
    <div class="modal fade" id="addPayrollModal" tabindex="-1" role="dialog" aria-labelledby="addPayrollModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPayrollModalLabel">New Payroll Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addPayrollForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" class="form-control" id="position" name="position" required>
                        </div>
                        <div class="form-group">
                            <label for="daily_rate">Daily Rate</label>
                            <input type="number" class="form-control" id="daily_rate" name="daily_rate" required>
                        </div>
                        <div class="form-group">
                            <label for="basic_pay">Weekly Pay</label>
                            <input type="number" class="form-control" id="basic_pay" name="basic_pay" required>
                        </div>
                        <div class="form-group">
                            <label for="overtime_pay">Overtime Pay</label>
                            <input type="number" class="form-control" id="overtime_pay" name="overtime_pay" required>
                        </div>
                        <div class="form-group">
                            <label for="late_deduct">Late Deduct</label>
                            <input type="number" class="form-control" id="late_deduct" name="late_deduct" required>
                        </div>
                        <div class="form-group">
                            <label for="sss_deduct">SSS Deduct</label>
                            <input type="number" class="form-control" id="sss_deduct" name="sss_deduct" required>
                        </div>
                        <div class="form-group">
                            <label for="pagibig_deduct">Pag-IBIG Deduct</label>
                            <input type="number" class="form-control" id="pagibig_deduct" name="pagibig_deduct" required>
                        </div>
                        <div class="form-group">
                            <label for="philhealth_deduct">PhilHealth Deduct</label>
                            <input type="number" class="form-control" id="philhealth_deduct" name="philhealth_deduct" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Compute Net Salary dynamically
        function computeNetSalary(basicPay, overtimePay, lateDeduct, sssDeduct, pagibigDeduct, philhealthDeduct) {
            return (
                parseFloat(basicPay) +
                parseFloat(overtimePay || 0) -
                (parseFloat(lateDeduct || 0) +
                 parseFloat(sssDeduct || 0) +
                 parseFloat(pagibigDeduct || 0) +
                 parseFloat(philhealthDeduct || 0))
            );
        }

        // Load payroll records dynamically
        async function loadPayroll() {
            try {
                const response = await fetch('https://example.com/load-payroll', { method: 'GET' });
                const data = await response.json();

                if (data.success) {
                    const tbody = document.querySelector('.payroll-table');
                    tbody.innerHTML = '';

                    data.payroll.forEach(record => {
                        const netSalary = computeNetSalary(
                            record.basic_pay,
                            record.overtime_pay,
                            record.late_deduct,
                            record.sss_deduct,
                            record.pagibig_deduct,
                            record.philhealth_deduct
                        );

                        const row = `
                            <tr>
                                <td>${record.name}</td>
                                <td>${record.position}</td>
                                <td>${record.daily_rate}</td>
                                <td>${record.basic_pay}</td>
                                <td>${record.overtime_pay}</td>
                                <td>${record.late_deduct}</td>
                                <td>${record.sss_deduct}</td>
                                <td>${record.pagibig_deduct}</td>
                                <td>${record.philhealth_deduct}</td>
                                <td>${netSalary.toFixed(2)}</td>
                                <td>${record.date_created}</td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                }
            } catch (error) {
                console.error('Error loading payroll:', error);
            }
        }

        // Handle form submission for new records
        document.getElementById('addPayrollForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);

            const response = await fetch('/add-payroll-endpoint', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Payroll record added successfully!');
                loadPayroll();
            } else {
                alert('Failed to add payroll record.');
            }
        });

        // Initial load of payroll data
        loadPayroll();
    </script>
</body>
</html>
