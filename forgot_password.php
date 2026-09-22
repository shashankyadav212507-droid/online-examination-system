<?php

require_once "config/database.php";

$error = "";
$success = "";

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

        $error = "New password and confirm password do not match.";

    } else {

        // Check student email
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

                // Hash new password
                $hashed_password = password_hash(
                    $new_password,
                    PASSWORD_DEFAULT
                );

                // Update password
                $update_sql = "UPDATE students
                               SET password = ?
                               WHERE id = ?";

                $update_stmt = $conn->prepare($update_sql);

                if ($update_stmt) {

                    $update_stmt->bind_param(
                        "si",
                        $hashed_password,
                        $student["id"]
                    );

                    if ($update_stmt->execute()) {

                        $success =
                            "Password reset successfully. You can now login.";

                    } else {

                        $error =
                            "Unable to reset password. Please try again.";
                    }

                    $update_stmt->close();

                } else {

                    $error = "Database error. Please try again.";
                }

            } else {

                $error = "No student account found with this email.";
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

    <title>Forgot Password | Student</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {

            min-height: 100vh;

            background:
                linear-gradient(
                    135deg,
                    #4f46e5,
                    #2563eb
                );

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 20px;
        }

        .container {

            width: 450px;

            max-width: 100%;

            background: white;

            padding: 40px;

            border-radius: 18px;

            box-shadow:
                0 15px 40px
                rgba(0, 0, 0, 0.2);
        }

        h2 {

            text-align: center;

            color: #1f2937;

            margin-bottom: 10px;
        }

        .subtitle {

            text-align: center;

            color: #6b7280;

            font-size: 14px;

            margin-bottom: 25px;
        }

        .message {

            padding: 12px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .error {

            background: #fee2e2;

            color: #b91c1c;
        }

        .success {

            background: #dcfce7;

            color: #166534;
        }

        .form-group {

            margin-bottom: 18px;
        }

        label {

            display: block;

            margin-bottom: 7px;

            font-weight: bold;

            color: #374151;

            font-size: 14px;
        }

        input {

            width: 100%;

            padding: 13px;

            border: 1px solid #d1d5db;

            border-radius: 8px;

            outline: none;

            font-size: 15px;
        }

        input:focus {

            border-color: #4f46e5;

            box-shadow:
                0 0 0 3px
                rgba(79, 70, 229, 0.1);
        }

        .reset-btn {

            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 8px;

            background:
                linear-gradient(
                    135deg,
                    #4f46e5,
                    #2563eb
                );

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;
        }

        .reset-btn:hover {

            opacity: 0.9;
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

    </style>

</head>

<body>

    <div class="container">

        <h2>Forgot Password?</h2>

        <p class="subtitle">
            Enter your registered email and create a new password.
        </p>


        <?php if ($error != "") { ?>

            <div class="message error">

                ⚠️
                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php } ?>


        <?php if ($success != "") { ?>

            <div class="message success">

                ✅
                <?php echo htmlspecialchars($success); ?>

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
                    placeholder="Enter your registered email"
                    autocomplete="off"
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
                    autocomplete="new-password"
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
                    autocomplete="new-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="reset-btn"
            >
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