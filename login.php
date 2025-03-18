<?php
// login.php
include '../wellness_site/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validation
    if (empty($username) || empty($password)) {
        die("All fields are required.");
    }

    // Check user credentials
    $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE username = ? AND is_active = TRUE");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $db_username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Update last login
            $update_stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
            $update_stmt->bind_param("i", $user_id);
            $update_stmt->execute();
            $update_stmt->close();

            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $db_username;
            
            header("Location: index.html"); // Redirect to homepage/dashboard
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Username not found.";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <style>
        /* General Styling */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #ff758c, #ff7eb3);
            flex-direction: column;
            animation: fadeIn 1s ease-in;
            margin: 0;
        }
        /* Form Container */
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 320px;
            text-align: center;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out forwards;
            transition: transform 0.3s ease-in-out;
        }
        .container:hover {
            transform: scale(1.02);
        }
        h2 {
            color: #e91e63;
            font-weight: 600;
        }
        /* Input Fields */
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #e91e63;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease-in-out;
        }
        input:focus {
            border-color: #c2185b;
            box-shadow: 0px 0px 8px rgba(233, 30, 99, 0.5);
        }
         /* Password Field */
         .password-field {
            position: relative;
        }

        .view-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 20px;
        }

        /* Buttons */
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #e91e63, #ff4081);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            transition: all 0.3s ease-in-out;
            box-shadow: 0px 4px 8px rgba(233, 30, 99, 0.3);
        }
        button:hover {
            background: linear-gradient(to right, #c2185b, #d81b60);
            transform: scale(1.05);
        }
        /* Toggle Link */
        .toggle {
            margin-top: 12px;
            color: #ec4784;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }
        .toggle:hover {
            color: #ec4784;
        }
        
        /* Home Button */
        .home-button {
            margin-top: 20px;
            background: linear-gradient(to right, #ff416c, #ff4b2b);
            width: auto;
            padding: 10px 20px;
        }
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Responsive Design */
        @media (max-width: 480px) {
            .container {
                width: 90%;
            }
        }
        body {
  background-image: url('Untitled design (2).png');
  background-size: cover;
  background-repeat: no-repeat;
  background-attachment: fixed;
}
    </style>
</head>
<body>
    <div class="container">
        <h2 id="form-title">Login</h2>
        <!-- Login Form -->
<form id="login-form" action="login.php" method="POST">
    <input type="text" name="username" id="username" placeholder="Username" required>
    <div class="password-field">
        <input type="password" name="password" id="login-password" placeholder="Password" required>
        <span id="toggle-login-password" class="view-icon">show</span>
    </div>
    <button type="submit">Login</button>
</form>

<!-- Register Form -->
<form id="register-form" action="register.php" method="POST" style="display: none;">
    <input type="text" name="new-username" id="new-username" placeholder="Username" required>
    <input type="email" name="email" id="email" placeholder="Email" required>
    <div class="password-field">
        <input type="password" name="new-password" id="new-password" placeholder="Password" required>
        <span id="toggle-new-password" class="view-icon">show</span>
    </div>
    <div class="password-field">
        <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password" required>
        <span id="toggle-confirm-password" class="view-icon">show</span>
    </div>
    <button type="submit">Register</button>
</form>

        <!-- Form Toggle -->
        <p class="toggle" onclick="toggleForm()">Don't have an account? Register</p>
    </div>
    <!-- Home Button -->
    <button class="home-button" onclick="window.location.href='index.html'">Home</button>
    <script>
        function toggleForm() {
            let loginForm = document.getElementById("login-form");
            let registerForm = document.getElementById("register-form");
            let formTitle = document.getElementById("form-title");
            let toggleText = document.querySelector(".toggle");
            // Smoothly fade out current form
            loginForm.style.opacity = registerForm.style.opacity = 0;
            setTimeout(() => {
                if (loginForm.style.display === "none") {
                    loginForm.style.display = "block";
                    registerForm.style.display = "none";
                    formTitle.innerText = "Login";
                    toggleText.innerText = "Don't have an account? Register";
                } else {
                    loginForm.style.display = "none";
                    registerForm.style.display = "block";
                    formTitle.innerText = "Register";
                    toggleText.innerText = "Already have an account? Login";
                }
                // Fade in the new form
                loginForm.style.opacity = registerForm.style.opacity = 1;
            }, 200);
        }
         // Password visibility toggle for login form
         document.getElementById('toggle-login-password').addEventListener('click', function() {
            let passwordField = document.getElementById('login-password');
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
        });

        // Password visibility toggle for register form
        document.getElementById('toggle-new-password').addEventListener('click', function() {
            let newPasswordField = document.getElementById('new-password');
            newPasswordField.type = newPasswordField.type === 'password' ? 'text' : 'password';
        });

        document.getElementById('toggle-confirm-password').addEventListener('click', function() {
            let confirmPasswordField = document.getElementById('confirm-password');
            confirmPasswordField.type = confirmPasswordField.type === 'password' ? 'text' : 'password';
        });

    </script>
</body>
</html>