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

// Fetch professor_id from professorusers table
$sqlProfessor = "SELECT professor_id FROM professorusers WHERE user_id = ?";
$stmt = $conn->prepare($sqlProfessor);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultProfessor = $stmt->get_result();
if ($row = $resultProfessor->fetch_assoc()) {
    $professor_id = $row['professor_id'];
} else {
    die("Error: Professor ID not found.");
}
$stmt->close();

// Fetch courses for the professor
$sqlCourses = "
    SELECT c.course_id, c.course_name
    FROM professor_courses pc
    JOIN courses c ON pc.course_id = c.course_id
    WHERE pc.professor_id = ?";
$stmt = $conn->prepare($sqlCourses);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$resultCourses = $stmt->get_result();

// Fetch departments and semesters for the filter options
$sqlBranches = "SELECT department_id, department_name FROM departments";
$resultBranches = $conn->query($sqlBranches);

$sqlSemesters = "SELECT semester_id, semester_name FROM semesters";
$resultSemesters = $conn->query($sqlSemesters);

// Store the department_id, semester_id, and course_id in session on form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['filter_students'])) {
    $_SESSION['department_id'] = $_POST['department_id'];
    $_SESSION['semester_id'] = $_POST['semester_id'];
    $_SESSION['course_id'] = $_POST['course_id'];

    // Redirect to the next page to show filtered students
    header("Location: attendance_list.php");
    exit();
}
?>

<!-- HTML Form for Filtering Students -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher's Dashboard</title>
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
        form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        select, input[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>

        <!-- Students List Form -->
        <h2>Filter Students by Department, Semester, and Course</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="department_id">Select Department</label>
                <select name="department_id" id="department_id" required>
                    <option value="">Select Department</option>
                    <?php while ($row = $resultBranches->fetch_assoc()): ?>
                        <option value="<?php echo $row['department_id']; ?>"><?php echo $row['department_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="semester_id">Select Semester</label>
                <select name="semester_id" id="semester_id" required>
                    <option value="">Select Semester</option>
                    <?php while ($row = $resultSemesters->fetch_assoc()): ?>
                        <option value="<?php echo $row['semester_id']; ?>"><?php echo $row['semester_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="course_id">Select Course</label>
                <select name="course_id" id="course_id" required>
                    <option value="">Select Course</option>
                    <?php while ($row = $resultCourses->fetch_assoc()): ?>
                        <option value="<?php echo $row['course_id']; ?>"><?php echo $row['course_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <input type="submit" name="filter_students" value="Filter Students">
        </form>
    </div>
</body>
</html>
