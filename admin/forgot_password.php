
<?php

require_once "../config/database.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Check empty fields
    if (
        $username == "" ||
        $email == "" ||
        $new_password == "" ||
        $confirm_password == ""
    ) {

        $error = "Please fill all fields.";

    }

    // Check email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }

    // Password length
    elseif (strlen($new_password) < 6) {

        $error = "Password must be at least 6 characters.";

    }

    // Confirm password
    elseif ($new_password !== $confirm_password) {

        $error = "New password and confirm password do not match.";

    }

    else {

        // Find admin using username and email
        $sql = "SELECT id, username, email
                FROM admins
                WHERE username = ?
                AND email = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            $error = "Database error. Please try again.";

        } else {

            $stmt->bind_param("ss", $username, $email);

            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows == 1) {

                $admin = $result->fetch_assoc();

                // Create secure password hash
                $hashed_password = password_hash(
                    $new_password,
                    PASSWORD_DEFAULT
                );

                // Update password
                $update_sql = "UPDATE admins
                               SET password = ?
                               WHERE id = ?";

                $update_stmt = $conn->prepare($update_sql);

                if (!$update_stmt) {

                    $error = "Unable to reset password.";

                } else {

                    $update_stmt->bind_param(
                        "si",
                        $hashed_password,
                        $admin["id"]
                    );

                    if ($update_stmt->execute()) {

                        $message =
                            "Password reset successfully. You can now login.";

                    } else {

                        $error =
                            "Password reset failed. Please try again.";
                    }

                    $update_stmt->close();
                }

            } else {

                $error =
                    "Username and email do not match.";

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Forgot Password | Online Examination System
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

        .container {

            width: 100%;

            max-width: 500px;

            background: white;

            padding: 40px;

            border-radius: 18px;

            box-shadow:
                0 20px 50px
                rgba(0,0,0,0.3);
        }

        .icon {

            text-align: center;

            font-size: 55px;

            margin-bottom: 15px;
        }

        h1 {

            text-align: center;

            color: #111827;

            font-size: 28px;

            margin-bottom: 10px;
        }

        .subtitle {

            text-align: center;

            color: #6b7280;

            font-size: 14px;

            line-height: 1.6;

            margin-bottom: 25px;
        }

        .error {

            background: #fee2e2;

            color: #b91c1c;

            border-left: 4px solid #dc2626;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .success {

            background: #dcfce7;

            color: #166534;

            border-left: 4px solid #16a34a;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .form-group {

            margin-bottom: 18px;
        }

        label {

            display: block;

            color: #374151;

            font-weight: bold;

            margin-bottom: 7px;

            font-size: 14px;
        }

        input {

            width: 100%;

            padding: 13px;

            border: 1px solid #d1d5db;

            border-radius: 8px;

            outline: none;

            font-size: 15px;

            transition: 0.3s;
        }

        input:focus {

            border-color: #4f46e5;

            box-shadow:
                0 0 0 3px
                rgba(79,70,229,0.12);
        }

        .reset-btn {

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

        .reset-btn:hover {

            background: #4338ca;

            transform: translateY(-1px);
        }

        .links {

            text-align: center;

            margin-top: 25px;
        }

        .links a {

            color: #4f46e5;

            text-decoration: none;

            font-size: 14px;
        }

        .links a:hover {

            text-decoration: underline;
        }

        .note {

            margin-top: 20px;

            padding: 12px;

            background: #f3f4f6;

            border-radius: 8px;

            color: #6b7280;

            font-size: 12px;

            line-height: 1.5;

            text-align: center;
        }

        @media (max-width: 600px) {

            .container {

                padding: 30px 22px;
            }

            h1 {

                font-size: 24px;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <div class="icon">
        🔐
    </div>

    <h1>
        Reset Admin Password
    </h1>

    <p class="subtitle">

        Enter your admin username and registered
        email address to create a new password.

    </p>

    <?php if ($error != "") { ?>

        <div class="error">

            ⚠️

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php } ?>

    <?php if ($message != "") { ?>

        <div class="success">

            ✓

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php } ?>

    <form method="POST">

        <div class="form-group">

            <label for="username">
                Admin Username
            </label>

            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter admin username"
                autocomplete="off"
                required
            >

        </div>

        <div class="form-group">

            <label for="email">
                Registered Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter registered email"
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
                minlength="6"
                required
            >

        </div>

        <div class="form-group">

            <label for="confirm_password">
                Confirm New Password
            </label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Confirm new password"
                autocomplete="new-password"
                minlength="6"
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

    <div class="links">

        <a href="login.php">

            ← Back to Admin Login

        </a>

    </div>

    <div class="note">

        Your username and registered email must match
        an existing administrator account.

    </div>

</div>

</body>

</html>

