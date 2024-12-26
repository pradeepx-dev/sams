<?php
session_start();

// Include the database connection
include 'connecting.php';

// Check if the user is logged in and is a professor
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$department_id = $_SESSION['department_id'];
$semester_id = $_SESSION['semester_id'];
$course_id = $_SESSION['course_id']; // Course selected in the previous step

// Fetch professor_id from professorusers table
$sqlProfessor = "SELECT professor_id FROM professorusers WHERE user_id = ?";
$stmt = $conn->prepare($sqlProfessor);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultProfessor = $stmt->get_result();
if ($row = $resultProfessor->fetch_assoc()) {
    $professor_id = $row['professor_id'];
} else {
    die("Error: Professor ID not found.");
}
$stmt->close();

// Fetch students based on department_id and semester_id
$sqlStudents = "
SELECT s.student_id, s.first_name, s.last_name, s.enrollment_no
FROM students s
WHERE s.department_id = ? AND s.semester_id = ?";
$stmt = $conn->prepare($sqlStudents);
$stmt->bind_param("ii", $department_id, $semester_id);
$stmt->execute();
$resultStudents = $stmt->get_result();

$students = [];
while ($row = $resultStudents->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_attendance'])) {
    $attendance_status = $_POST['attendance_status'];
    $current_datetime = date("Y-m-d H:i:s");
    $current_date = date("Y-m-d");

    // Add new class to the classes table
    $sqlAddClass = "
        INSERT INTO classes (course_id, professor_id, semester_id, class_schedule, department_id)
        VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlAddClass);
    $stmt->bind_param("iiisi", $course_id, $professor_id, $semester_id, $current_datetime, $department_id);
    $stmt->execute();
    $class_id = $conn->insert_id; // Get the auto-generated class_id
    $stmt->close();

    // Add attendance for each student
    $sqlAddAttendance = "
        INSERT INTO attendance (student_id, class_id, attendance_date, status)
        VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlAddAttendance);

    foreach ($attendance_status as $student_id => $status) {
        $stmt->bind_param("iiss", $student_id, $class_id, $current_date, $status);
        $stmt->execute();
    }
    $stmt->close();

    // Redirect to a success page or refresh
    header("Location: dashboard_prof.php");
    exit();
}
?>

<!-- Display Filtered Students with Attendance Radio Buttons -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtered Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #007bff;
            color: #fff;
        }
        .attendance-radio {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .attendance-radio input {
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Filtered Students</h1>

        <form method="POST" action="">
            <table>
                <thead>
                    <tr>
                        <th>Student Name (Enrollment No)</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td>
                                    <?php echo $student['first_name'] . " " . $student['last_name'] . " (" . $student['enrollment_no'] . ")"; ?>
                                </td>
                                <td>
                                    <div class="attendance-radio">
                                        <label>
                                            <input type="radio" name="attendance_status[<?php echo $student['student_id']; ?>]" value="present" required> Present
                                        </label>
                                        <label>
                                            <input type="radio" name="attendance_status[<?php echo $student['student_id']; ?>]" value="absent" required checked> Absent
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No students found for the selected department and semester.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <button type="submit" name="mark_attendance">Mark Attendance</button>
        </form>
    </div>
</body>
</html>
