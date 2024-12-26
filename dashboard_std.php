<?php
// Include database connection
include 'connecting.php';

// Start session and fetch user data
session_start();
if (!isset($_SESSION['username'])) {
    echo "Error Found";
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];

// Sanitize and validate session data
if (empty($username) || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    die("Invalid username.");
}

// Fetch user details using roll_number from the students table
$sql = "SELECT students.first_name, students.last_name, students.enrollment_no, students.student_id 
        FROM students
        WHERE students.enrollment_no = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}

// Get today's date in 'Y-m-d' format
$current_date = date('Y-m-d');

// Check if a date is selected or filter is "All", otherwise default to today's date
$attendance_filter = isset($_POST['attendance_filter']) ? $_POST['attendance_filter'] : 'Individual';
$selected_date = isset($_POST['date']) ? $_POST['date'] : $current_date;

// SQL query to fetch attendance based on filter selection
if ($attendance_filter === 'All') {
    // Fetch all attendance records for the student
    $sql_attendance = "
        SELECT courses.course_name, courses.course_code, attendance.attendance_date, attendance.status 
        FROM attendance
        JOIN classes ON attendance.class_id = classes.class_id
        JOIN courses ON classes.course_id = courses.course_id
        WHERE attendance.student_id = ? 
        ORDER BY attendance.attendance_date ASC";
    $stmt_attendance = $conn->prepare($sql_attendance);
    $stmt_attendance->bind_param('i', $user['student_id']);
} else {
    // Fetch attendance records for the selected date
    $sql_attendance = "
        SELECT courses.course_name, courses.course_code, attendance.attendance_date, attendance.status 
        FROM attendance
        JOIN classes ON attendance.class_id = classes.class_id
        JOIN courses ON classes.course_id = courses.course_id
        WHERE attendance.student_id = ? AND attendance.attendance_date = ? 
        ORDER BY attendance.attendance_date ASC";
    $stmt_attendance = $conn->prepare($sql_attendance);
    $stmt_attendance->bind_param('is', $user['student_id'], $selected_date);
}

if (!$stmt_attendance) {
    die("SQL Error: " . $conn->error);
}

$stmt_attendance->execute();
$attendance_result = $stmt_attendance->get_result();

// Check if query returned results
if ($attendance_result->num_rows > 0) {
    $attendance_data = [];
    while ($row = $attendance_result->fetch_assoc()) {
        $attendance_data[] = $row;
    }
    
    // Group attendance data by date
    $grouped_attendance = [];
    foreach ($attendance_data as $attendance) {
        $date = $attendance['attendance_date'];
        if (!isset($grouped_attendance[$date])) {
            $grouped_attendance[$date] = [];
        }
        $grouped_attendance[$date][] = $attendance;
    }

    // Calculate attendance statistics
    $total_records = count($attendance_data);
    $total_present = count(array_filter($attendance_data, fn($att) => $att['status'] === 'Present'));
    $total_percentage = $total_records > 0 ? ($total_present / $total_records) * 100 : 0;
} else {
    $grouped_attendance = [];
    $total_records = 0;
    $total_present = 0;
    $total_percentage = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sem-Attendance</title>
    <style>
        /* CSS Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            /* background-color: #f8e8e8; */
            background-color:rgb(176, 170, 212)
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
            /* background-color: #d9534f; */
            background-color: #6c5ce7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .sidebar button:hover {
            background-color: #4834d4
        }
        .main-content {
            flex: 1;
            padding: 20px;
        }
        .main-content h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        .filter {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            align-items: center;
        }
        .filter select, .filter input[type="date"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .filter button {
            padding: 10px 20px;
            background-color: #5cb85c;
            color: white;
            
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .filter button:hover {
            background-color: #4cae4c;
        }
        .attendance-list {
            background-color: #fff;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .attendance-date {
            font-weight: bold;
            margin: 10px 0;
            background-color: #f4f4f4;
            padding: 5px;
            border-radius: 5px;
        }
        .attendance-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #f4f4f4;
        }
        .attendance-item:last-child {
            border-bottom: none;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status.present {
            background-color: #5cb85c;
            color: white;
        }
        .status.absent {
            background-color: #d9534f;
            color: white;
        }
        .addOn{
            text-decoration: none;
            color: white;
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
                <p>Enrollment id: <?php echo htmlspecialchars($user['enrollment_no']); ?></p>
            </div>
            <div class="stats">
                <div class="stat">Total Present: <?php echo $total_present; ?></div>
                <div class="stat">Total Percentage: <?php echo number_format($total_percentage, 2); ?>%</div>
            </div>
            <button>Sem Attendance</button>
            <button><a class="addOn" href="stdMidsem_marks.php">Mid-Semester Marks</a></button>
            <button>Notes</button>
            <button>Quiz</button>
            <button onclick="window.location.href='logout.php';">Log Out</button>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h1>Sem-Attendance</h1>
            <form method="POST">
                <div class="filter">
                    <select name="attendance_filter" onchange="this.form.submit()">
                        <option value="Individual" <?php echo ($attendance_filter === 'Individual') ? 'selected' : ''; ?>>Individual</option>
                        <option value="All" <?php echo ($attendance_filter === 'All') ? 'selected' : ''; ?>>All</option>
                    </select>
                    <input type="date" name="date" value="<?php echo ($attendance_filter === 'All') ? '' : $selected_date; ?>" <?php echo ($attendance_filter === 'All') ? 'disabled' : ''; ?>>
                    <button type="submit">Submit</button>
                </div>
            </form>
            <div class="attendance-list">
                <?php if (empty($grouped_attendance)): ?>
                    <p>No attendance records for selected filter.</p>
                <?php else: ?>
                    <?php foreach ($grouped_attendance as $date => $attendances): ?>
                        <div class="attendance-date">Date: <?php echo htmlspecialchars($date); ?></div>
                        <?php foreach ($attendances as $attendance): ?>
                            <div class="attendance-item">
                                <span><?php echo htmlspecialchars($attendance['course_name']); ?> (<?php echo htmlspecialchars($attendance['course_code']); ?>)</span>
                                <span class="status <?php echo strtolower($attendance['status']); ?>">
                                    <?php echo htmlspecialchars($attendance['status']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
