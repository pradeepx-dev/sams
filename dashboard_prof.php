<?php
// Include database connection
include 'connecting.php';
session_start();

// Check for professor session
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit;
}

// Fetch the professor's details from session or database (for example purpose, using static values)
$user_name = $_SESSION['username'];  // Assuming username is stored in the session
$department = "Computer Science & Engineering";  // Replace dynamically if needed


$sql = "SELECT professors.first_name, professors.last_name
        FROM professors
        WHERE professors.username = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param('s', $user_name);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_fullname=$user['first_name']." ".$user['last_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
        }

        .dashboard-container {
            display: flex;
            height: 100vh;
        }

        /* side bar */
        .sidebar {
            width: 250px;
            background-color: #6c5ce7;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .sidebar a {
            text-decoration: none;
            color: white;
            margin: 10px 0;
            display: block;
            padding: 10px;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #4834d4;
        }
        .sidebar a.active {
            background-color: #4834d4;
        }

        /* Main content */
        .main-content {
            flex: 1;
            background: #ffffff;
            padding: 30px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .profile-section {
            display: flex;
            align-items: center;
        }

        .profile-section .avatar {
            width: 80px;
            height: 80px;
            background-color: #c0c0c0;
            border-radius: 50%;
            margin-right: 15px;
        }

        .profile-section .name {
            font-size: 20px;
            font-weight: bold;
        }

        .switch-btn {
            background-color: #6c5ce7;
            padding: 10px 15px;
            border: 1px solid #aaa;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }

        .switch-btn:hover {
            background-color: #4834d4;
        }

        /* Buttons */
        .buttons-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .btn {
            padding: 15px;
            text-align: center;
            border: 1px solid #ccc;
            background-color: #dcdcdc;
            cursor: pointer;
            width: 30%;
            font-size: 18px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #b3b3b3;
        }

        /* Send Alert */
        .send-alert {
            background-color: #f7f7f7;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .send-alert h4 {
            margin: 0 0 10px 0;
        }

        .send-alert select, .send-alert input, .send-alert button {
            margin: 10px 0;
            padding: 10px;
            font-size: 16px;
        }

        .send-alert select {
            width: 100px;
        }

        .send-alert input {
            width: calc(100% - 150px);
            display: inline-block;
            margin-right: 10px;
        }

        .send-alert button {
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            font-size: 16px;
        }

        .send-alert button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
        <h2>Teacher Dashboard</h2>
        <a href="upload.php" class="active">Home</a>
        <a href="attendance.php">Mark Attendance</a>
        <a href="midsem_marks.php">Midsem Marks</a>
        <a href="notes_assignment.php">Notes/Assignment</a>
        <a href="review.php">Review</a>
        <a href="reports.php">Reports</a>
        <a href="notifications.php">Notifications</a>
    </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="profile-section">
                    <div class="avatar"></div>
                    <div>
                        <div class="name"><?= $user_fullname ?></div>
                        <div><?= $department ?></div>
                    </div>
                </div>
                <div>
                    <button class="switch-btn" onclick="window.location.href='logout.php'" >Log Out</button>
                </div>
            </div>

            <!-- Attendance Statistics -->
            <div class="buttons-container">
                <div class="btn">Attendance Overview</div>
                <div class="btn">Attendance Edit</div>
                <div class="btn">Attendance Report</div>
            </div>

            <!-- Send Alert Section -->
            <div class="send-alert">
                <h4>Send Alert</h4>
                <select>
                    <option>All</option>
                    <option>Teacher</option>
                    <option>Student</option>
                </select>
                <input type="text" placeholder="Send Message" />
                <button>Send</button>
            </div>
        </div>
    </div>
</body>
</html>
