
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f5f6fa;
            color: #333;
            /* overflow-x: hidden; */
        }
        body::-webkit-scrollbar {
            display: none;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 50px;
            background-color: #6c5ce7;
            color: white;
            position: sticky;
            top: 0;
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }
        
        .navbar .nav-links li {
            cursor: pointer;
            font-size: 16px;
            transition: color 0.3s;
        }
        .navbar .nav-links a {
            text-decoration: none;
            color: white;
        }

        .navbar .nav-links li:hover {
            color: grey;
            
        }

        .hero-section {
            /* text-align: center; */
            padding: 1px 20px;
            height: 100vh;
            background-color: #6c5ce7;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .hero-section h1 {
            font-size: 48px;
            margin: 0 0 20px 0;
        }

        .hero-section p {
            font-size: 20px;
            margin: 0 0 30px 0;
        }

        .hero-section .cta-button {
            padding: 10px 30px;
            font-size: 18px;
            font-weight: bold;
            color: #6c5ce7;
            background-color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .hero-section .cta-button:hover {
            border: 1px solid white;
            background-color: #6c5ce7;
            color: white;

        }

        .features-section, .about-section {
            padding: 50px 20px;
            text-align: center;
        }

        .features-section h2, .about-section h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .features-container, .about-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }

        .feature-box, .about-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .feature-box:hover, .about-card:hover {
            transform: translateY(-10px);
        }

        .about-card img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            margin-bottom: 1px;
        }

        .about-card h3 {
            font-size: 22px;
            margin-bottom: 5px;
            color: #4caf50;
        }

        .about-card p {
            font-size: 16px;
            color: #555;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .social-links a {
            text-decoration: none;
            color: #4caf50;
            font-size: 20px;
        }
        .social-links a:hover {
            text-decoration: none;
            color: black;
            font-size: 20.5px;
        }

        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">SAMS</div>
        <ul class="nav-links">
            <a  href="#hero-section"><li>Home</li></a>
            <a  href="#features-section"><li>Features</li></a>
            <a  href="#about-section"><li>About</li></a>
            <a  href="login.php"><li>Login</li></a>
        </ul>
    </div>

    <div class="hero-section" id="hero-section">
        <h1>Welcome to the Student Attendance Management System</h1>
        <p>Effortlessly manage student attendance and stay organized!</p>
        <a href="login.php"><button class="cta-button">Get Started</button></a>
    </div>

    <div class="features-section" id="features-section">
        <h2>Features</h2>
        <div class="features-container">
            <div class="feature-box">
                <h3>Mark Attendance</h3>
                <p>Mark and manage attendance seamlessly with our user-friendly interface.</p>
            </div>
            <div class="feature-box">
                <h3>Detailed Reports</h3>
                <p>Generate insightful attendance reports for better analysis.</p>
            </div>
            <div class="feature-box">
                <h3>Secure Data</h3>
                <p>Keep all your data safe with robust security measures.</p>
            </div>
        </div>
        <div class="features-container">
            <div class="feature-box">
                <h3>Organize Quiz</h3>
                <p>Organize a fun and engaging quiz to challenge knowledge and encourage learning.</p>
            </div>
            <div class="feature-box">
                <h3>Upload & Get Notes</h3>
                <p>Upload and access study notes anytime, fostering collaboration and easy sharing of resources.</p>
            </div>
            <div class="feature-box">
                <h3>Get Information About Exams</h3>
                <p>Get detailed information about upcoming exams, schedules, and requirements easily.</p>
            </div>
        </div>
    </div>

    <div class="about-section" id="about-section">
        <h2>About Us</h2>
        <div class="about-container">
            <div class="about-card">
                <img src="profile.jpg" alt="Person 2">
                <h3>Anurag Kumar</h3>
                <p>Frontend Developer</p>
                <div class="social-links">
                    <a href="#"><i class="fa-brands fa-github"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-solid fa-download"></i></a>
                </div>
            </div>
            <div class="about-card">
                <img src="profile.jpg" alt="Person 3">
                <h3>Priya Singh</h3>
                <p>Frontend Developer</p>
                <div class="social-links">
                    <a href="#"><i class="fa-brands fa-github"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-solid fa-download"></i></a>
                </div>
            </div>
            <div class="about-card">
                <img src="profile.jpg" alt="Person 4">
                <h3>Priya Yadav</h3>
                <p>Backend Developer</p>
                <div class="social-links">
                    <a href="#"><i class="fa-brands fa-github"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-solid fa-download"></i></a>
                </div>
            </div>
            <div class="about-card">
                <img src="profile.jpg" alt="Person 1">
                <h3>Pradeep Kumar Maurya</h3>
                <p>Backend Developer</p>
                <div class="social-links">
                    <a href="#"><i class="fa-brands fa-github"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-solid fa-download"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; 2024 Student Attendance Management System. All rights reserved.
    </div>
</body>
</html>
