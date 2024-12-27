<?php
// Database connection
include "connecting.php";

// Start session
session_start();

// Fetch professor's username from session
$username = $_SESSION['username'];

// Get POST data from previous page
$department_id = $_POST['department_id'];
$semester_id = $_POST['semester_id'];
$course_id = $_POST['course_id'];

// Fetch professor_id using username
$professor_query = $conn->query("SELECT professor_id FROM professors WHERE username='$username'");
if ($professor_query->num_rows > 0) {
    $professor_row = $professor_query->fetch_assoc();
    $professor_id = $professor_row['professor_id'];
} else {
    die("Professor not found.");
}

// Fetch students for the selected department and semester
$students = $conn->query("SELECT student_id, first_name, last_name FROM students WHERE department_id='$department_id' AND semester_id='$semester_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Marks</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-group {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-group label {
            font-weight: 500;
            font-size: 1rem;
            color: #333;
        }

        .form-group input {
            width: 150px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            margin-top: 20px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Upload Mid Semester Marks</h1>

        <form action="upload_marks.php" method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Marks Obtained</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $students->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['first_name'].' '.$row['last_name'] ?></td>
                            <td>
                                <input type="number" name="marks[<?= $row['student_id'] ?>]" min="0" required>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div class="form-group">
                <label for="outof_marks">Out of Marks:</label>
                <input type="number" id="outof_marks" name="outof_marks" required>
            </div>

            <input type="hidden" name="course_id" value="<?= $course_id ?>">
            <input type="hidden" name="professor_id" value="<?= $professor_id ?>">

            <button type="submit">Submit Marks</button>
        </form>
    </div>

</body>
</html>
