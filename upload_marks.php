<?php
// Database connection
include "connecting.php";

// Start session
session_start();

// Get POST data
$marks = $_POST['marks'];
$outof_marks = $_POST['outof_marks'];
$course_id = $_POST['course_id'];
$professor_id = $_POST['professor_id'];

// Debugging: Check received values
if (empty($professor_id) || empty($course_id)) {
    die("Error: Invalid professor_id or course_id.");
}

// Validate professor_id exists in the professors table
$professor_check = $conn->query("SELECT professor_id FROM professors WHERE professor_id = '$professor_id'");
if ($professor_check->num_rows === 0) {
    die("Error: Professor ID does not exist in the professors table.");
}

// Current date and time
$upload_date = date('Y-m-d H:i:s');

// Insert data into the midsem_marks table
foreach ($marks as $student_id => $marks_obtained) {
    // Validate student_id exists in the students table
    $student_check = $conn->query("SELECT student_id FROM students WHERE student_id = '$student_id'");
    if ($student_check->num_rows === 0) {
        echo "Error: Student ID $student_id does not exist. Skipping.<br>";
        continue;
    }

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO midsem_marks (student_id, course_id, professor_id, marks_obtained, upload_date, outof_marks) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiisi", $student_id, $course_id, $professor_id, $marks_obtained, $upload_date, $outof_marks);

    // Execute the statement
    if (!$stmt->execute()) {
        echo "Error inserting data for student ID $student_id: " . $stmt->error . "<br>";
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Redirect to a success page or refresh
header("Location: dashboard_prof.php");
exit();
?>

