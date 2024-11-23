Ian Fabro
<?php
session_start();

if (isset($_POST['loggedin'])) {
    $_SESSION['loggedin'] = filter_var($_POST['loggedin'], FILTER_VALIDATE_BOOLEAN); // Convert string "true" to boolean true
}

// Initialize variables to prevent undefined variable errors
$department = $_GET['department'] ?? '';
$searchId = $_GET['search_id'] ?? '';
$tasks = isset($tasks) ? $tasks : []; // Ensure $tasks is an array if undefined

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Personnel Records</title>
    <link rel="stylesheet" href="style_index.css">
    <link rel="icon" type="image/x-icon" href="Superpack-Enterprise-Logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboardnew.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php include 'sidebar_small.php'; ?>
<?php include 'employee_filter_sidebar.php'; ?>
<div class="container-everything" style="height:100%;">
    <div class="container-all">
        <div class="container-top">
            <?php include 'header_2.php'; ?>
        </div>
        <div class="container-search">
            <div class="search-bar">
                <form method="GET" action="" class="form-inline">
                    <div class="input-group mb-3 flex-grow-1">
                        <input type="hidden" name="department" value="<?php echo htmlspecialchars($department); ?>"> 
                        <input type="text" class="form-control" name="search_id" placeholder="Search by ID" 
                               value="<?php echo htmlspecialchars($searchId); ?>" 
                               style="border-radius: 10px 0 0 10px; border: 3px solid #131313; height:42px;">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" style="border-radius: 0; border: 3px solid #131313;">Search</button>
                        </div>
                    </div>
                    <button class="btn btn-primary mb-3" type="button" data-toggle="modal" data-target="#addTaskModal" 
                            style="border-radius: 0 10px 10px 0 ; border: 3px solid #131313;">Add Employee</button>
                </form>
            </div>
        </div>

        <div class="container-bottom">
            <div class="container-table">
                <div class="tool-bar">
                    <div class="d-flex justify-content-between align-items-center mb-3" style="color:#fffafa;">
                        <div>
                            <span id="selected-count">0</span> items selected
                        </div>
                        <div class="d-flex align-items-center" style="gap:10px;">
                            <form method="POST" id="deleteForm" style="display:inline;">
                                <button type="submit" name="deleteTask" class="btn btn-danger" disabled>Del</button>
                            </form>
                            <button class="btn btn-primary" name="editTaskMod" data-toggle="modal" 
                                    data-target="#editTaskModal" disabled>Edit</button>
                            <form method="get" action="task_management.php">
                                <input type="hidden" name="department" value="<?php echo htmlspecialchars($department); ?>">
                                <input type="hidden" name="export" value="excel">
                                <button type="submit" class="btn btn-success">Export to Excel</button>
                            </form>
                            <button class="btn btn-info" onclick="window.location.href='employee_list.php'">Reset</button>
                            <button class="btn btn-warning" onclick="toggle_filter()">Filter</button>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="checkbox-col"></th> <!-- Empty column for the checkbox -->
                                <th>Employee No</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Shift</th>
                                <th>Salary</th>
                                <th>Start Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $row): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" id="chkbx" name="task_checkbox[]" form="deleteForm" 
                                               value="<?php echo $row['id']; ?>" onclick="updateSelectedCount()">
                                    </td>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['position']); ?></td>
                                    <td><?php echo htmlspecialchars($row['shift']); ?></td>
                                    <td><?php echo htmlspecialchars($row['salary']); ?></td>
                                    <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<!-- (Modal code unchanged) -->

<!-- Edit Task Modal -->
<!-- (Modal code unchanged) -->

<script>
    // JavaScript for toggling buttons and count
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('input[name="task_checkbox[]"]:checked').length;
        document.getElementById('selected-count').textContent = selectedCount;

        const deleteButton = document.querySelector('button[name="deleteTask"]');
        const editButton = document.querySelector('button[name="editTaskMod"]');

        deleteButton.disabled = selectedCount === 0;
        editButton.disabled = selectedCount !== 1;
    }
</script>
</body>
</html>
