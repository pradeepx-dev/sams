<?php
// Include database connection
include "connecting.php";

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("User not logged in. Please log in to access this page.");
}

// Fetch session data
$username = $_SESSION['username'];

// Sanitize and validate session data
if (empty($username) || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    die("Invalid username.");
}

// Fetch user details for the sidebar
$sql_user = "SELECT students.first_name, students.last_name, students.enrollment_no, students.student_id 
             FROM students
             WHERE students.enrollment_no = ?";
$stmt_user = $conn->prepare($sql_user);
if (!$stmt_user) {
    die("SQL Error: " . $conn->error);
}
$stmt_user->bind_param('s', $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Fetch student_id using user_id for marks
try {
    $query_student = "SELECT student_id FROM studentusers WHERE user_id = ?";
    $stmt_student = $conn->prepare($query_student);
    if (!$stmt_student) {
        throw new Exception("Failed to prepare student query: " . $conn->error);
    }
    $stmt_student->bind_param("i", $_SESSION['user_id']);
    $stmt_student->execute();
    $result_student = $stmt_student->get_result();

    if ($result_student->num_rows > 0) {
        $row_student = $result_student->fetch_assoc();
        $student_id = $row_student['student_id'];

        // Fetch marks for the student
        $query_marks = "
            SELECT 
                mm.marks_obtained, 
                mm.outof_marks, 
                c.course_name, 
                p.first_name AS professor_first_name, 
                p.last_name AS professor_last_name, 
                mm.upload_date
            FROM midsem_marks mm
            JOIN courses c ON mm.course_id = c.course_id
            JOIN professors p ON mm.professor_id = p.professor_id
            WHERE mm.student_id = ?";
        $stmt_marks = $conn->prepare($query_marks);
        if (!$stmt_marks) {
            throw new Exception("Failed to prepare marks query: " . $conn->error);
        }
        $stmt_marks->bind_param("i", $student_id);
        $stmt_marks->execute();
        $result_marks = $stmt_marks->get_result();
    } else {
        throw new Exception("Invalid user or student not found.");
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Midsem Marks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            /* background-color: #f9f9f9; */
            background-color:rgb(176, 170, 212);
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 300px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .profile {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #ccc;
        }
        .profile h3 {
            margin: 10px 0 5px;
            font-size: 18px;
        }
        .profile p {
            font-size: 14px;
            color: #555;
        }
        .stats .stat {
            margin: 10px 0;
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .sidebar button {
            width: 100%;
            padding: 10px;
            background-color: #6c5ce7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .sidebar button:hover {
            background-color: #4834d4;
        }
        .addOn {
            text-decoration: none;
            color: white;
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #f9f9f9;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        p {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="profile">
                <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'profile.jpg'); ?>" alt="Profile Picture">
                <h3><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></h3>
                <p>Enrollment ID: <?php echo htmlspecialchars($user['enrollment_no']); ?></p>
            </div>
            <div class="stats">
                <div class="stat">Total Present: <?php echo $total_present ?? 0; ?></div>
                <div class="stat">Total Percentage: <?php echo number_format($total_percentage ?? 0, 2); ?>%</div>
            </div>
            <button><a class="addOn" href="dashboard_std.php">Sem Attendance</a></button>
            <button>Mid-Semester Marks</button>
            <button>Notes</button>
            <button>Quiz</button>
            <button onclick="window.location.href='logout.php';">Log Out</button>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Your Midsem Marks</h1>
            <?php if (isset($result_marks) && $result_marks->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Marks Obtained</th>
                            <th>Out of Marks</th>
                            <th>Professor</th>
                            <th>Upload Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_marks->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['course_name']) ?></td>
                                <td><?= htmlspecialchars($row['marks_obtained']) ?></td>
                                <td><?= htmlspecialchars($row['outof_marks']) ?></td>
                                <td><?= htmlspecialchars($row['professor_first_name'] . " " . $row['professor_last_name']) ?></td>
                                <td><?= htmlspecialchars($row['upload_date']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No marks have been uploaded yet.</p>
            <?php endif; ?>

            <?php
            // Close the statements and connection
            if (isset($stmt_student)) $stmt_student->close();
            if (isset($stmt_marks)) $stmt_marks->close();
            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
