<?php
session_start();

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

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
    <style>
        
    </style>

    
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
                        <div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <button class="btn btn-primary mb-3" type="button" data-toggle="modal" data-target="#addLeaveModal">Create Leave Request</button>
                        </div>
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
                        <tbody class="attendance-table">
                            
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
                                    <th>Position</th>
                                    <th>Time In </th>
                                    <th>Time Out </th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody class="time-in-table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="addLeaveModal" tabindex="-1" role="dialog" aria-labelledby="addLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLeaveModalLabel">Create Leave Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="leaveType">Leave Type</label>
                            <select class="form-control" id="leaveType" name="leaveType">
                                <option value="Sick Leave">Sick Leave</option>
                                <option value="Vacation Leave">Vacation Leave</option>
                                <option value="Maternity Leave">Maternity Leave</option>
                                <option value="Paternity Leave">Paternity Leave</option>
                                <option value="Emergency Leave">Emergency Leave</option>
                            </select>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label for="startDate">Start Date</label>
                                <input type="date" class="form-control" id="startDate" name="startDate">
                            </div>
                            <div class="col">
                                <label for="endDate">End Date</label>
                                <input type="date" class="form-control" id="endDate" name="endDate">
                            </div>
                        </div>
                        <button type="submit" name="addLeave" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        const clock = document.querySelector('.current-time');
        const options = {hour: '2-digit', minute: '2-digit'};
        const locale = 'en-PH';
        setInterval(() => {
            const now = new Date();
            clock.textContent = now.toLocaleTimeString(locale, options);
        }, 1000);

        // Change logo name 
        const logoName = document.querySelector('.logo_name');
        logoName.textContent = 'Attendance Check';

        // load attendance data
      async function loadAttendance() {

    try {
        const response = await fetch('https://frfqbkrj-5000.asse.devtunnels.ms/load-attendance', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' } // Fixed headers
        });

        const data = await response.json();
        console.log(data);

        if (data.success) {
            const tbody = document.querySelector('.attendance-table');
            tbody.innerHTML = '';

            const attendanceCount = data.attendance.length;

            if (attendanceCount > 0) {
                for (let i = 0; i < attendanceCount; i++) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = <td>${data.attendance[i].leave_type}</td><td>${data.attendance[i].start_date}</td><td>${data.attendance[i].end_date}</td><td>${data.attendance[i].status}</td>;
                    tbody.appendChild(tr);
                }
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="4">No data found</td>';
                tbody.appendChild(tr);
            }
        }
    } catch (error) {
        console.error(error);
    } // Ensure this bracket closes the function
}

// Call loadAttendance function
loadAttendance();

// load time in data
async function loadTimeIn() {
    try {
        const response = await fetch('https://frfqbkrj-5000.asse.devtunnels.ms/load-time-in', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });

        const data = await response.json();
        console.log(data);

        if (data.success) {
            const tbody = document.querySelector('.time-in-table');
            tbody.innerHTML = '';

            const timeInCount = data.time_in.length;

            if (timeInCount > 0) {
                for (let i = 0; i < timeInCount; i++) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = <td>${data.time_in[i].name}</td><td>${data.time_in[i].position}</td><td>${data.time_in[i].time_in}</td><td>${data.time_in[i].time_out}</td><td>${data.time_in[i].date}</td>;
                    tbody.appendChild(tr);
                }
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="8">No data found</td>';
                tbody.appendChild(tr);
            }
        }
    } catch (error) {
        console.error(error);
    }
}

// Call loadTimeIn function
loadTimeIn();




    </script>
</body>
</html>
