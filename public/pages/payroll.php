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
                                <th>Basic Pay (Weekly)</th>
                                <th>Daily Rate</th>
                                <th>Overtime Pay</th>
                                <th>Late Deduct</th>
                                <th>SSS Deduct</th>
                                <th>Pag-IBIG Deduct</th>
                                <th>PhilHealth Deduct</th>
                                <th>Total Deduct</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="payroll-table">
                            <!-- Dynamic Content from JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div style="border-top:5px solid #131313; width:100%; height:1px;"></div>
            <div class="container-search" style="height:100%;">
                <div class="search-bar">
                    <form method="GET" action="" class="form-inline">
                        <div class="input-group mb-3 flex-grow-1">
                            <input type="text" class="form-control" name="search_id" placeholder="Search by ID" style="border-radius: 10px 0 0 10px; border: 3px solid #131313; height:42px;">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" style="border-radius: 0; border: 3px solid #131313;" onclick="searchPayroll()">Search</button>
                            </div>
                        </div>
                        <button class="btn btn-primary mb-3" type="button" data-toggle="modal" data-target="#addPayrollModal" style="border-radius: 0 10px 10px 0; border: 3px solid #131313;">Add Record</button>
                    </form>
                </div>
                <div class="tool-bar">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div style="color: #FFFAFA;">
                            <span id="selected-count">0</span> items selected
                        </div>
                        <div class="d-flex align-items-center" style="gap:10px;">
                            <button class="btn btn-danger" disabled onclick="deletePayroll()">Delete</button>
                            <button class="btn btn-primary" disabled data-toggle="modal" data-target="#editTaskModal">Edit</button>
                            <button class="btn btn-info" onclick="resetTable()">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Payroll Modal -->
    <div class="modal fade" id="addPayrollModal" tabindex="-1" role="dialog" aria-labelledby="addPayrollModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="addPayrollForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPayrollModalLabel">New Payroll Record</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
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
                            <label for="basic_pay">Basic Pay (Weekly)</label>
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
        async function loadPayroll() {
            const response = await fetch('/api/load-payroll');
            const data = await response.json();
            const tbody = document.querySelector('.payroll-table');
            tbody.innerHTML = '';

            data.forEach(record => {
                const dailyRate = (record.basic_pay / 5).toFixed(2); // Calculate Daily Rate
                const totalDeduct = parseFloat(record.late_deduct) + parseFloat(record.sss_deduct) + parseFloat(record.pagibig_deduct) + parseFloat(record.philhealth_deduct);

                tbody.innerHTML += `
                    <tr>
                        <td>${record.name}</td>
                        <td>${record.position}</td>
                        <td>${record.basic_pay}</td>
                        <td>${dailyRate}</td>
                        <td>${record.overtime_pay}</td>
                        <td>${record.late_deduct}</td>
                        <td>${record.sss_deduct}</td>
                        <td>${record.pagibig_deduct}</td>
                        <td>${record.philhealth_deduct}</td>
                        <td>${totalDeduct}</td>
                        <td>${record.date_created}</td>
                        <td>
                            <button class="btn btn-info" onclick="editRecord(${record.id})">Edit</button>
                        </td>
                    </tr>
                `;
            });
        }

        document.getElementById('addPayrollForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const response = await fetch('/api/add-payroll', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                alert('Record added successfully');
                loadPayroll();
                $('#addPayrollModal').modal('hide');
            } else {
                alert('Error adding record');
            }
        });

        loadPayroll();
    </script>
</body>
</html>
