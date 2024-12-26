<?php
session_start();

// Include the database connection
include 'connecting.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid CSRF token!";
    } else {
        $role_id = intval($_POST['role']);
        $username = htmlspecialchars(trim($_POST['username']));
        $password = htmlspecialchars($_POST['password']);

        if (empty($role_id) || empty($username) || empty($password)) {
            $error = "All fields are required!";
        } else {
            // Query to get user information
            $stmt = $conn->prepare("SELECT user_id, password_hash FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password_hash'])) {
                    $user_id = $user['user_id'];
                    $is_verified = false;

                    // Role verification
                    if ($role_id == 3) { // Student
                        $stmt2 = $conn->prepare("SELECT * FROM studentusers WHERE user_id = ?");
                    } elseif ($role_id == 2) { // Professor
                        $stmt2 = $conn->prepare("SELECT * FROM professorusers WHERE user_id = ?");
                    } elseif ($role_id == 1) { // Admin
                        $stmt2 = $conn->prepare("SELECT * FROM adminusers WHERE user_id = ?");
                    }

                    $stmt2->bind_param("i", $user_id);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    $is_verified = ($result2->num_rows === 1);

                    if ($is_verified) {
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $username;
                        $_SESSION['role_id'] = $role_id;

                        // Redirect based on role
                        if ($role_id == 3) {
                            header("Location: dashboard_std.php");
                        } elseif ($role_id == 2) {
                            header("Location: dashboard_prof.php");
                        } elseif ($role_id == 1) {
                            header("Location: dashboard_admin.php");
                        }
                        exit();
                    } else {
                        $error = "No matching user found in the corresponding table!";
                    }
                } else {
                    $error = "Invalid password!";
                }
            } else {
                $error = "Invalid username or role!";
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .error {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            color: white;
            background-color: #e74c3c;
        }
        .signup-link {
            margin-top: 10px;
            font-size: 14px;
        }
        .signup-link a {
            color: #007bff;
            text-decoration: none;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
        .usercss input{
            width: 93.5%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label for="role">Role</label>
                <select name="role" id="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="1">HOD</option>
                    <option value="2">Professor</option>
                    <option value="3">Student</option>
                </select>
            </div>
            <div class="form-group usercss">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group usercss">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>

        <p class="signup-link">Don't have an account? <a href="signUp.php">Sign Up here</a></p>
    </div>
</body>
</html>
