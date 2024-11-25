<?php
session_start();

// Database Configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "hrm_system";
$port = 3307;

// Connect to Database
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle Add Employee Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addEmployee'])) {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $birth_date = $_POST['birth_date'];
    $emergency_contact = $_POST['emergency_contact'];
    $hire_date = $_POST['hire_date'];

    // File Handling
    $file_name = null;
    if (!empty($_FILES['file']['name'])) {
        $file_name = basename($_FILES['file']['name']);
        $file_path = "uploads/" . $file_name;

        // Move Uploaded File
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            $error_message = "Failed to upload the file.";
        }
    }

    // Insert Employee Data
    try {
        $stmt = $pdo->prepare("INSERT INTO employees (name, position, address, phone_number, email, birth_date, emergency_contact, hire_date, file_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $position, $address, $phone_number, $email, $birth_date, $emergency_contact, $hire_date, $file_name]);
        $success_message = "Employee added successfully!";
    } catch (PDOException $e) {
        $error_message = "Error adding employee: " . $e->getMessage();
    }
}

// Fetch Employees
try {
    $stmt = $pdo->query("SELECT * FROM employees ORDER BY id DESC");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching employees: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Records</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Employee Records</h1>

        <!-- Display Success or Error Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Add Employee Form -->
        <form method="POST" enctype="multipart/form-data" class="border p-4 mb-4 bg-light">
            <h3>Add New Employee</h3>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Position</label>
                <input type="text" name="position" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Birth Date</label>
                <input type="date" name="birth_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Emergency Contact</label>
                <input type="text" name="emergency_contact" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Hire Date</label>
                <input type="date" name="hire_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Upload Document (Optional)</label>
                <input type="file" name="file" class="form-control-file" accept=".pdf,.doc,.docx">
            </div>
            <button type="submit" name="addEmployee" class="btn btn-primary">Add Employee</button>
        </form>

        <!-- Employee List -->
        <h3>Employee List</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Birth Date</th>
                    <th>Emergency Contact</th>
                    <th>Hire Date</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?php echo $employee['id']; ?></td>
                        <td><?php echo $employee['name']; ?></td>
                        <td><?php echo $employee['position']; ?></td>
                        <td><?php echo $employee['address']; ?></td>
                        <td><?php echo $employee['phone_number']; ?></td>
                        <td><?php echo $employee['email']; ?></td>
                        <td><?php echo $employee['birth_date']; ?></td>
                        <td><?php echo $employee['emergency_contact']; ?></td>
                        <td><?php echo $employee['hire_date']; ?></td>
                        <td>
                            <?php if ($employee['file_name']): ?>
                                <a href="uploads/<?php echo $employee['file_name']; ?>" download><?php echo $employee['file_name']; ?></a>
                            <?php else: ?>
                                No File
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
