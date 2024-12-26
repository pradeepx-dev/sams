<?php
// Database connection
include "connecting.php";

// Fetch options for Department and Semester
$departments = $conn->query("SELECT department_id, department_name FROM departments");
$semesters = $conn->query("SELECT semester_id, semester_name FROM semesters");

// Fetch professor_id using username (replace $_SESSION['username'] with actual session username)
session_start();
$username = $_SESSION['username'];
$professor = $conn->query("SELECT professor_id FROM professorusers WHERE username='$username'")->fetch_assoc();
$professor_id = $professor['professor_id'];

// Fetch professor's full name
$professorDetails = $conn->query("SELECT first_name, last_name FROM professors WHERE professor_id='$professor_id'")->fetch_assoc();
$professor_name = $professorDetails['first_name'] . ' ' . $professorDetails['last_name'];

// Fetch courses for the professor (via professor_courses and courses table)
$courses = $conn->query(
    "SELECT pc.course_id, c.course_name 
     FROM professor_courses pc
     JOIN courses c ON pc.course_id = c.course_id
     WHERE pc.professor_id='$professor_id'"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Mid-Sem Marks</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }
        label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?= $professor_name ?>!</h1>
        <form action="midsem_marks1.php" method="POST">
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select name="department_id" id="department" class="form-select" required>
                    <option value="" disabled selected>-- Select Department --</option>
                    <?php while ($row = $departments->fetch_assoc()) { ?>
                        <option value="<?= $row['department_id'] ?>"><?= $row['department_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="semester" class="form-label">Semester</label>
                <select name="semester_id" id="semester" class="form-select" required>
                    <option value="" disabled selected>-- Select Semester --</option>
                    <?php while ($row = $semesters->fetch_assoc()) { ?>
                        <option value="<?= $row['semester_id'] ?>"><?= $row['semester_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="course" class="form-label">Course</label>
                <select name="course_id" id="course" class="form-select" required>
                    <option value="" disabled selected>-- Select Course --</option>
                    <?php while ($row = $courses->fetch_assoc()) { ?>
                        <option value="<?= $row['course_id'] ?>"><?= $row['course_name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Next</button>
            </div>
        </form>
    </div>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
