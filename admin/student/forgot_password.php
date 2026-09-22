<?php

require_once "../config/database.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($email == "" || $new_password == "" || $confirm_password == "") {

        $error = "Please fill all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($new_password) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($new_password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        /*
         * Check student email
         *
         * IMPORTANT:
         * If your table name is different,
         * change "students" below.
         */

        $sql = "SELECT id
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

                $hashed_password =
                    password_hash(
                        $new_password,
                        PASSWORD_DEFAULT
                    );

                $update_sql = "UPDATE students
                               SET password = ?
                               WHERE id = ?";

                $update_stmt =
                    $conn->prepare($update_sql);

                if ($update_stmt) {

                    $update_stmt->bind_param(
                        "si",
                        $hashed_password,
                        $student["id"]
                    );

                    if ($update_stmt->execute()) {

                        $message =
                            "Password reset successfully. You can now login.";

                    } else {

                        $error =
                            "Password could not be updated.";
                    }

                    $update_stmt->close();

                } else {

                    $error =
                        "Database error. Please try again.";
                }

            } else {

                $error =
                    "No student account found with this email.";
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

    <title>Student Forgot Password</title>

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

        .box {

            width: 100%;

            max-width: 450px;

            background: white;

            padding: 40px;

            border-radius: 18px;

            box-shadow:
                0 20px 50px rgba(0,0,0,0.3);
        }

        h2 {

            text-align: center;

            color: #111827;

            margin-bottom: 10px;
        }

        .subtitle {

            text-align: center;

            color: #6b7280;

            margin-bottom: 30px;
        }

        .form-group {

            margin-bottom: 20px;
        }

        label {

            display: block;

            font-weight: bold;

            color: #374151;

            margin-bottom: 8px;
        }

        input {

            width: 100%;

            padding: 13px;

            border: 1px solid #d1d5db;

            border-radius: 8px;

            font-size: 15px;

            outline: none;
        }

        input:focus {

            border-color: #4f46e5;

            box-shadow:
                0 0 0 3px
                rgba(79,70,229,0.12);
        }

        button {

            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 8px;

            background: #4f46e5;

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;
        }

        button:hover {

            background: #4338ca;
        }

        .error {

            background: #fee2e2;

            color: #b91c1c;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .success {

            background: #dcfce7;

            color: #166534;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .back {

            text-align: center;

            margin-top: 20px;
        }

        .back a {

            color: #4f46e5;

            text-decoration: none;

            font-size: 14px;
        }

        .back a:hover {

            text-decoration: underline;
        }

    </style>

</head>

<body>

<div class="box">

    <h2>Forgot Password?</h2>

    <p class="subtitle">
        Reset your student account password
    </p>


    <?php if ($error != "") { ?>

        <div class="error">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php } ?>


    <?php if ($message != "") { ?>

        <div class="success">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php } ?>


    <form method="POST">

        <div class="form-group">

            <label for="email">
                Registered Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter registered email"
                required
            >

        </div>


        <div class="form-group">

            <label for="new_password">
                New Password
            </label>

            <input
                type="password"
                id="new_password"
                name="new_password"
                placeholder="Enter new password"
                required
            >

        </div>


        <div class="form-group">

            <label for="confirm_password">
                Confirm Password
            </label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Confirm new password"
                required
            >

        </div>


        <button type="submit">
            Reset Password
        </button>

    </form>


    <div class="back">

        <a href="login.php">
            ← Back to Student Login
        </a>

    </div>

</div>

</body>

</html>