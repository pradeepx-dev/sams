<?php
// Include the database connection file
include 'connecting.php';

// Fetch departments for the select options
$departmentsQuery = "SELECT department_id, department_name FROM departments";
$departmentsResult = $conn->query($departmentsQuery);

// Initialize error message
$error = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data and sanitize input
    $role = 2; // Role is fixed as "Professor"
    $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $department_id = isset($_POST['department_id']) ? filter_var($_POST['department_id'], FILTER_SANITIZE_NUMBER_INT) : null;
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
        // Check if the username already exists in the 'users' table
        $checkUsernameQuery = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $checkUsernameQuery->bind_param('s', $username);
        $checkUsernameQuery->execute();
        $checkUsernameQuery->store_result(); // Store the result to clear the result set

        $checkUsernameQuery->bind_result($usernameCount);
        $checkUsernameQuery->fetch(); // Fetch the result

        // Free the result after fetching
        $checkUsernameQuery->free_result();

        if ($usernameCount > 0) {
            $error = "Username already taken. Please choose another one.";
        } else {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Use prepared statements to prevent SQL injection
            $insertUserQuery = $conn->prepare("INSERT INTO users (username, password_hash, email, created_at, role_id) VALUES (?, ?, ?, NOW(), ?)");
            $insertUserQuery->bind_param('sssi', $username, $password_hash, $email, $role);

            if (!$insertUserQuery->execute()) {
                $error = "Error inserting into users table: " . $conn->error;
            } else {
                // Get the user_id of the inserted user
                $user_id = $conn->insert_id;

                // Check if department_id is valid before inserting
                if (empty($department_id)) {
                    $error = "Please provide a department.";
                } else {
                    // Insert into 'professors' table
                    $insertProfessorQuery = $conn->prepare("INSERT INTO professors (first_name, last_name, username, email, phone, department_id) VALUES (?, ?, ?, ?, ?, ?)");
                    $insertProfessorQuery->bind_param('sssssi', $first_name, $last_name, $username, $email, $phone, $department_id);

                    if (!$insertProfessorQuery->execute()) {
                        $error = "Error inserting into professors table: " . $conn->error;
                    } else {
                        $professor_id = $conn->insert_id;

                        // Insert into 'professorUsers' table
                        $insertProfessorUserQuery = $conn->prepare("INSERT INTO professorUsers (username, user_id, professor_id) VALUES (?, ?, ?)");
                        $insertProfessorUserQuery->bind_param('sii', $username, $user_id, $professor_id);

                        if (!$insertProfessorUserQuery->execute()) {
                            $error = "Error inserting into professorUsers table: " . $conn->error;
                        } else {
                            // Redirect to login page or success page
                            header("Location: login.php");
                            exit;
                        }
                    }
                }
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
    <title>Signup - Professor</title>
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
        <h2>Signup - Professor</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <form action="signUpProf.php" method="post">
            <!-- Disabled Role -->
            <div class="form-group">
                <label for="role">Role:</label>
                <input type="text" id="role" value="Professor" disabled>
                <input type="hidden" name="role" value="2">
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
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" name="phone" id="phone" required>
            </div>

            <!-- Department Dropdown -->
            <div class="form-group">
                <label for="department_id">Department:</label>
                <select name="department_id" id="department_id" required>
                    <option value="">Select Department</option>
                    <?php
                    while ($row = $departmentsResult->fetch_assoc()) {
                        echo "<option value='" . $row['department_id'] . "'>" . $row['department_name'] . "</option>";
                    }
                    ?>
                </select>
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
