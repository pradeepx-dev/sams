<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) { // Role ID 1 = HoD/Admin
    header("Location: login.php");
    exit();
}

// Include the database connection
include 'connecting.php';

// Replace with dynamic department ID based on HoD login
$department_id = 1;

// Total Students
$students_query = "SELECT COUNT(*) AS total_students FROM students WHERE department_id = ?";
$stmt = $conn->prepare($students_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$students_result = $stmt->get_result();
$students = $students_result->fetch_assoc()['total_students'];

// Total Professors
$professors_query = "SELECT COUNT(*) AS total_professors FROM professors WHERE department_id = ?";
$stmt = $conn->prepare($professors_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$professors_result = $stmt->get_result();
$professors = $professors_result->fetch_assoc()['total_professors'];

// List of Courses
$courses_query = "SELECT course_name, course_code, credit_hours FROM courses WHERE department_id = ?";
$stmt = $conn->prepare($courses_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$courses_result = $stmt->get_result();

// Attendance
$attendance_query = "SELECT s.first_name, s.last_name, a.status, a.attendance_date
                     FROM attendance a
                     JOIN students s ON a.student_id = s.student_id
                     WHERE s.department_id = ?";
$stmt = $conn->prepare($attendance_query);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$attendance_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HoD Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .container {
            margin-top: 30px;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Head of Department Dashboard</h1>
        <div class="row">
            <!-- Total Students -->
            <div class="col-md-6">
                <div class="card bg-info text-white text-center">
                    <div class="card-body">
                        <h4>Total Students</h4>
                        <h2><?= $students ?></h2>
                    </div>
                </div>
            </div>
            <!-- Total Professors -->
            <div class="col-md-6">
                <div class="card bg-success text-white text-center">
                    <div class="card-body">
                        <h4>Total Professors</h4>
                        <h2><?= $professors ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Section -->
        <h2 class="mt-5">Courses</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Credit Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($course = $courses_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $course['course_name'] ?></td>
                        <td><?= $course['course_code'] ?></td>
                        <td><?= $course['credit_hours'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Attendance Section -->
        <h2 class="mt-5">Attendance</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($attendance = $attendance_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $attendance['first_name'] . ' ' . $attendance['last_name'] ?></td>
                        <td><?= $attendance['status'] ?></td>
                        <td><?= $attendance['attendance_date'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
$conn->close();
?>
