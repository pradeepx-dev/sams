tudent Attendance Management System (SAMS)
Overview
Student Attendance Management System (SAMS) is a web-based application designed to streamline and simplify the process of managing student attendance. Developed as a minor project for our college, SAMS aims to provide an intuitive, efficient, and user-friendly solution for teachers and administrators to track and analyze student attendance.

Key Features
Role-Based Login System: Secure access for students, professors, and administrators.
Attendance Recording: Easy recording of daily attendance with just a few clicks.
Attendance Reports: Generate detailed attendance reports for individual students or entire classes.
Student Dashboard: Students can view their attendance records and overall statistics.
Responsive Design: Accessible on both desktops and mobile devices.
Secure Data Management: Utilizes encrypted passwords and secure database connections.
Technologies Used
Frontend: HTML, CSS, JavaScript
Backend: PHP
Database: MySQL
Additional Libraries: Bootstrap for responsive design
Team Members
Anurag Kumar
Priya Singh
Priya Yadav
Pradeep Kumar Maurya
Installation Guide
Clone the repository to your local machine:
git clone https://github.com/your-repo/sams.git
Move the project folder to your local web server directory (e.g., htdocs for XAMPP).
Start your web server (e.g., XAMPP or WAMP) and open the PHPMyAdmin panel.
Create a database named sams and import the provided sams.sql file.
Update the database configuration in config.php:
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sams";
Open the application in your browser:
http://localhost/sams
Usage Instructions
Admin Login:
Admin can manage students, professors, and attendance records.
Professor Login:
Professors can mark attendance, view class attendance reports, upload notes/assignment, etc.
Student Login:
Students can view their attendance history, midsem marks, notes, quiz and analytics, etc.
Screenshots
![Screenshot 2024-12-26 235040](https://github.com/user-attachments/assets/c644a1c9-6645-4acb-a2de-5d5524e10dc9)
![Screenshot 2024-12-26 235609](https://github.com/user-attachments/assets/7c50404e-1bb3-491f-a4b5-3118c8aad36a)

Future Enhancements
Integration with email notifications for attendance alerts.
Advanced analytics and insights for attendance trends.
Integration with mobile applications for better accessibility.
Facial recognition for automated attendance marking.
Acknowledgments
We would like to express our gratitude to our mentors and faculty members for their guidance and support throughout the development of this project. Special thanks to our college for providing us with the opportunity to work on this project.

Developed with dedication by Team SAMS:

Anurag Kumar
Priya Singh
Priya Yadav
Pradeep Kumar Maurya
