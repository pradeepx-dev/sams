<?php
// Include the database connection file
include 'connecting.php';

// Fetch departments and semesters for the select options
$departmentsQuery = "SELECT department_id, department_name FROM departments";
$departmentsResult = $conn->query($departmentsQuery);

$semestersQuery = "SELECT semester_id, semester_name FROM semesters";
$semestersResult = $conn->query($semestersQuery);

// Initialize error message
$error = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data and sanitize input
    $role = 3; // Role is fixed as "Student"
    $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $enrollment_number = filter_var($_POST['enrollment_number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $department_id = isset($_POST['department_id']) ? filter_var($_POST['department_id'], FILTER_SANITIZE_NUMBER_INT) : null;
    $semester_id = isset($_POST['semester_id']) ? filter_var($_POST['semester_id'], FILTER_SANITIZE_NUMBER_INT) : null;
    $session = isset($_POST['session']) ? filter_var($_POST['session'], FILTER_SANITIZE_STRING) : null;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/', $password)) {
        $error = "Password must be at least 8 characters long, contain at least one letter, one number, and one special character.";
    } elseif (!$email) {
        $error = "Invalid email address.";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Use prepared statements to prevent SQL injection
        $insertUserQuery = $conn->prepare("INSERT INTO users (username, password_hash, email, created_at, role_id) VALUES (?, ?, ?, NOW(), ?)");
        $insertUserQuery->bind_param('sssi', $enrollment_number, $password_hash, $email, $role);

        if (!$insertUserQuery->execute()) {
            $error = "Error inserting into users table: " . $conn->error;
        } else {
            // Get the user_id of the inserted user
            $user_id = $conn->insert_id;

            // Insert into 'students' and 'studentusers'
            if (empty($department_id) || empty($semester_id) || empty($session)) {
                $error = "Please provide all required student details.";
            } else {
                // Insert into 'students' table
                $insertStudentQuery = $conn->prepare("INSERT INTO students (first_name, last_name, enrollment_no, email, phone, department_id, semester_id, session) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insertStudentQuery->bind_param('sssssiis', $first_name, $last_name, $enrollment_number, $email, $phone, $department_id, $semester_id, $session);

                if (!$insertStudentQuery->execute()) {
                    $error = "Error inserting into students table: " . $conn->error;
                } else {
                    $student_id = $conn->insert_id;

                    // Insert into 'studentusers' table
                    $insertStudentUserQuery = $conn->prepare("INSERT INTO studentusers (student_id, user_id) VALUES (?, ?)");
                    $insertStudentUserQuery->bind_param('ii', $student_id, $user_id);

                    if (!$insertStudentUserQuery->execute()) {
                        $error = "Error inserting into studentusers table: " . $conn->error;
                    }
                }
            }

            if (empty($error)) {
                // Redirect user to login or success page
                header("Location: login.php");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 900px;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
            gap: 20px;
        }

        .form-group label {
            flex: 1;
            font-weight: bold;
            color: #333;
            margin-right: 10px;
        }

        .form-group input, .form-group select {
            flex: 2;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 32px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        .form-container .form-group input:focus, .form-container .form-group select:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        @media screen and (max-width: 600px) {
            .form-group {
                flex-direction: column;
            }

            .form-group label, .form-group input, .form-group select {
                flex: none;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Signup - Student</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form action="signUpStudent.php" method="post">
            <!-- Disabled Role -->
            <div class="form-group">
                <label for="role">Role:</label>
                <input type="text" id="role" value="Student" disabled>
                <input type="hidden" name="role" value="3">
            </div>

            <!-- Common Fields -->
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" id="first_name" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" id="last_name" required>
            </div>

            <div class="form-group">
                <label for="enrollment_number">Enrollment Number:</label>
                <input type="text" name="enrollment_number" id="enrollment_number" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" name="phone" id="phone" required>
            </div>

            <!-- Student-Specific Fields -->
            <div class="form-group">
                <label for="department_id">Department:</label>
                <select name="department_id" id="department_id">
                    <option value="">Select Department</option>
                    <?php
                    while ($row = $departmentsResult->fetch_assoc()) {
                        echo "<option value='" . $row['department_id'] . "'>" . $row['department_name'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="semester_id">Semester:</label>
                <select name="semester_id" id="semester_id">
                    <option value="">Select Semester</option>
                    <?php
                    while ($row = $semestersResult->fetch_assoc()) {
                        echo "<option value='" . $row['semester_id'] . "'>" . $row['semester_name'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="session">Session:</label>
                <input type="text" name="session" id="session">
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>

            <button type="submit">Signup</button>
        </form>
    </div>

</body>
</html>
