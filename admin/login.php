
<?php

session_start();

require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username == "" || $password == "") {

        $error = "Please enter username and password.";

    } else {

        $sql = "SELECT id, username, password
                FROM admins
                WHERE username = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            $error = "Database error. Please try again.";

        } else {

            $stmt->bind_param("s", $username);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows == 1) {

                $admin = $result->fetch_assoc();

                $password_correct = false;

                /*
                 * Supports:
                 * 1. password_hash() password
                 * 2. Plain text password
                 */

                if (password_verify($password, $admin["password"])) {

                    $password_correct = true;

                } elseif ($password === $admin["password"]) {

                    $password_correct = true;
                }


                if ($password_correct) {

                    $_SESSION["admin_id"] = $admin["id"];

                    $_SESSION["admin_username"] =
                        $admin["username"];

                    header("Location: dashboard.php");

                    exit();

                } else {

                    $error = "Invalid username or password.";
                }

            } else {

                $error = "Invalid username or password.";
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

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Admin Login | Online Examination System
    </title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {

            font-family: Arial, Helvetica, sans-serif;

            min-height: 100vh;

            background:
                linear-gradient(
                    135deg,
                    #111827,
                    #312e81
                );

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;
        }


        .page {

            width: 100%;

            max-width: 1000px;

            display: grid;

            grid-template-columns: 1fr 1fr;

            background: white;

            border-radius: 20px;

            overflow: hidden;

            box-shadow:
                0 20px 50px rgba(0,0,0,0.3);
        }


        /* ================= LEFT SIDE ================= */

        .welcome {

            background:
                linear-gradient(
                    145deg,
                    #4f46e5,
                    #312e81
                );

            color: white;

            padding: 60px 45px;

            display: flex;

            flex-direction: column;

            justify-content: center;
        }


        .logo {

            font-size: 24px;

            font-weight: bold;

            margin-bottom: 50px;
        }


        .logo span {

            color: #c7d2fe;
        }


        .admin-icon {

            font-size: 65px;

            margin-bottom: 20px;
        }


        .welcome h1 {

            font-size: 38px;

            line-height: 1.2;

            margin-bottom: 18px;
        }


        .welcome p {

            color: #e0e7ff;

            line-height: 1.7;

            font-size: 16px;
        }


        .features {

            margin-top: 30px;
        }


        .feature {

            margin: 13px 0;

            color: #eef2ff;
        }


        /* ================= RIGHT SIDE ================= */

        .login-section {

            padding: 55px 45px;

            display: flex;

            flex-direction: column;

            justify-content: center;
        }


        .login-section h2 {

            color: #111827;

            font-size: 30px;

            margin-bottom: 8px;
        }


        .subtitle {

            color: #6b7280;

            margin-bottom: 30px;
        }


        /* ERROR */

        .error {

            background: #fee2e2;

            color: #b91c1c;

            border-left: 4px solid #dc2626;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        /* FORM */

        .form-group {

            margin-bottom: 20px;
        }


        .form-group label {

            display: block;

            color: #374151;

            font-weight: bold;

            margin-bottom: 8px;

            font-size: 14px;
        }


        .input-box {

            position: relative;
        }


        .input-icon {

            position: absolute;

            left: 14px;

            top: 50%;

            transform: translateY(-50%);

            font-size: 18px;
        }


        input {

            width: 100%;

            padding: 14px 14px 14px 45px;

            border: 1px solid #d1d5db;

            border-radius: 8px;

            outline: none;

            font-size: 15px;

            transition: 0.3s;
        }


        input:focus {

            border-color: #4f46e5;

            box-shadow:
                0 0 0 3px rgba(79,70,229,0.12);
        }


        /* LOGIN BUTTON */

        .login-btn {

            width: 100%;

            border: none;

            padding: 14px;

            background: #4f46e5;

            color: white;

            border-radius: 8px;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.3s;
        }


        .login-btn:hover {

            background: #4338ca;

            transform: translateY(-1px);
        }


        /* FORGOT PASSWORD */

        .forgot-password {

            text-align: right;

            margin-top: 10px;

            margin-bottom: 5px;
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


        /* BACK */

        .back {

            text-align: center;

            margin-top: 25px;
        }


        .back a {

            color: #4f46e5;

            text-decoration: none;

            font-size: 14px;
        }


        .back a:hover {

            text-decoration: underline;
        }


        /* SECURITY */

        .security {

            margin-top: 25px;

            text-align: center;

            font-size: 12px;

            color: #9ca3af;
        }


        /* MOBILE */

        @media (max-width: 750px) {

            .page {

                grid-template-columns: 1fr;
            }


            .welcome {

                padding: 35px;

                text-align: center;
            }


            .logo {

                margin-bottom: 25px;
            }


            .features {

                display: none;
            }


            .welcome h1 {

                font-size: 30px;
            }


            .login-section {

                padding: 40px 30px;
            }

        }

    </style>

</head>


<body>


<div class="page">


    <!-- ================= LEFT ================= -->

    <div class="welcome">


        <div class="logo">

            Online<span>Exam</span>

        </div>


        <div class="admin-icon">

            👨‍💼

        </div>


        <h1>

            Administrator Portal

        </h1>


        <p>

            Manage your online examination system
            from one secure dashboard.

            Create exams, manage questions,
            view students and analyze results.

        </p>


        <div class="features">


            <div class="feature">

                ✓ Manage Exams

            </div>


            <div class="feature">

                ✓ Manage Questions

            </div>


            <div class="feature">

                ✓ Manage Students

            </div>


            <div class="feature">

                ✓ View Examination Results

            </div>


        </div>


    </div>


    <!-- ================= RIGHT ================= -->

    <div class="login-section">


        <h2>

            Admin Login

        </h2>


        <p class="subtitle">

            Sign in to access the administrator dashboard

        </p>


        <?php if ($error != "") { ?>

            <div class="error">

                ⚠️

                <?php

                echo htmlspecialchars($error);

                ?>

            </div>

        <?php } ?>


        <form method="POST">


            <div class="form-group">


                <label for="username">

                    Username

                </label>


                <div class="input-box">


                    <span class="input-icon">

                        👤

                    </span>


                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter admin username"
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
                        placeholder="Enter admin password"
                        autocomplete="new-password"
                        required
                    >


                </div>


            </div>


            <button
                type="submit"
                class="login-btn"
            >

                Login to Admin Dashboard →

            </button>


            <!-- FORGOT PASSWORD -->

            <div class="forgot-password">

                <a href="forgot_password.php">

                    Forgot Password?

                </a>

            </div>


        </form>


        <div class="security">

            🔐 Authorized administrators only

        </div>


        <div class="back">

            <a href="../index.php">

                ← Back to Home

            </a>

        </div>


    </div>


</div>


</body>

</html>

