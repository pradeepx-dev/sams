<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Role Selection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 500px;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .form-group select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px 32px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        @media screen and (max-width: 600px) {
            .form-container {
                width: 100%;
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Signup Role Selection</h2>
        <form id="roleForm">
            <div class="form-group">
                <label for="role">Select Role:</label>
                <select name="role" id="role" required>
                    <option value="">Select Role</option>
                    <option value="3">Student</option>
                    <option value="2">Professor</option>
                    <option value="1">Admin</option>
                </select>
            </div>
            <button type="submit">Proceed</button>
        </form>
        <p class="signup-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script>
        const roleForm = document.getElementById('roleForm');
        const roleSelect = document.getElementById('role');

        roleForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const selectedRole = roleSelect.value;

            if (selectedRole === "3") {
                window.location.href = "signUpStudent.php";
            } else if (selectedRole === "2") {
                window.location.href = "signUpProf.php";
            } else if (selectedRole === "1") {
                window.location.href = "signUpAdmin.php";
            } else {
                alert("Please select a valid role.");
            }
        });
    </script>

</body>
</html>
