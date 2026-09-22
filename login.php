<?php
session_start();

require_once "config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email == "" || $password == "") {

        $error = "Please enter your email and password.";

    } else {

        $sql = "SELECT id, name, email, password
                FROM students
                WHERE email = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            $error = "Database error. Please try again.";

        } else {

            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows == 1) {

                $student = $result->fetch_assoc();

                $password_correct = false;

                // Check hashed password
                if (password_verify($password, $student["password"])) {

                    $password_correct = true;

                }
                // For old/plain-text passwords
                elseif ($password === $student["password"]) {

                    $password_correct = true;
                }

                if ($password_correct) {

                    $_SESSION["student_id"] = $student["id"];
                    $_SESSION["student_name"] = $student["name"];
                    $_SESSION["student_email"] = $student["email"];

                    header("Location: dashboard.php");
                    exit();

                } else {

                    $error = "Invalid email or password.";
                }

            } else {

                $error = "Invalid email or password.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Login | Online Examination System</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #4f46e5, #2563eb);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .page {
            width: 900px;
            max-width: 100%;
            min-height: 550px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        /* LEFT SECTION */

        .welcome {
            width: 50%;
            background: linear-gradient(135deg, #3730a3, #2563eb);
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .logo span {
            color: #facc15;
        }

        .welcome-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .welcome h1 {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .welcome p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .features {
            line-height: 2.2;
            font-size: 15px;
        }

        /* RIGHT SECTION */

        .login-section {
            width: 50%;
            padding: 55px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-section h2 {
            font-size: 30px;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 30px;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #374151;
        }

        .input-box {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
        }

        .input-box input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-size: 15px;
        }

        .input-box input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4f46e5, #2563eb);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        /* FORGOT PASSWORD */

        .forgot-password {
            text-align: right;
            margin-top: 10px;
        }

        .forgot-password a {
            color: #4f46e5;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        /* REGISTER BUTTON */

        .register-text {
            text-align: center;
            margin-top: 25px;
            color: #6b7280;
            font-size: 14px;
        }

        .register-btn {
            display: block;
            width: 100%;
            text-align: center;
            padding: 13px;
            margin-top: 10px;
            background: white;
            color: #4f46e5;
            border: 2px solid #4f46e5;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .register-btn:hover {
            background: #4f46e5;
            color: white;
        }

        .back {
            text-align: center;
            margin-top: 20px;
        }

        .back a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: bold;
        }

        .back a:hover {
            text-decoration: underline;
        }

        /* MOBILE */

        @media (max-width: 768px) {

            .page {
                flex-direction: column;
            }

            .welcome {
                width: 100%;
                padding: 35px 25px;
                text-align: center;
            }

            .logo {
                margin-bottom: 20px;
            }

            .welcome-icon {
                font-size: 45px;
            }

            .welcome h1 {
                font-size: 26px;
            }

            .features {
                display: none;
            }

            .login-section {
                width: 100%;
                padding: 35px 25px;
            }

            .login-section h2 {
                font-size: 26px;
            }
        }

    </style>

</head>

<body>

    <div class="page">

        <!-- LEFT SIDE -->

        <div class="welcome">

            <div class="logo">
                Online<span>Exam</span>
            </div>

            <div class="welcome-icon">
                🎓
            </div>

            <h1>Welcome, Student!</h1>

            <p>
                Login to your student account and access your
                online examinations, submit answers and view
                your results.
            </p>

            <div class="features">

                ✓ Access Available Exams<br>

                ✓ Easy MCQ Examination<br>

                ✓ Automatic Result Calculation<br>

                ✓ View Your Exam Results

            </div>

        </div>


        <!-- RIGHT SIDE -->

        <div class="login-section">

            <h2>Student Login</h2>

            <p class="subtitle">
                Enter your credentials to continue
            </p>


            <!-- ERROR MESSAGE -->

            <?php if ($error != "") { ?>

                <div class="error">

                    ⚠️

                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php } ?>


            <!-- LOGIN FORM -->

            <form method="POST">

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <div class="input-box">

                        <span class="input-icon">
                            📧
                        </span>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            autocomplete="off"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="input-box">

                        <span class="input-icon">
                            🔒
                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="new-password"
                            required
                        >

                    </div>

                </div>


                <button
                    type="submit"
                    class="login-btn"
                >
                    Login to Dashboard →
                </button>


                <!-- FORGOT PASSWORD -->

                <div class="forgot-password">

                    <a href="forgot_password.php">
                        Forgot Password?
                    </a>

                </div>

            </form>


            <!-- REGISTER -->

            <div class="register-text">

                Don't have an account?

            </div>

            <a
                href="register.php"
                class="register-btn"
            >
                📝 Register Now
            </a>


            <!-- HOME -->

            <div class="back">

                <a href="index.php">
                    ← Back to Home
                </a>

            </div>

        </div>

    </div>

</body>

</html>