<?php

require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($name) || empty($email) || empty($password)) {

        $message = "Please fill all fields.";

    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO students (name, email, password)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {

            $message = "Registration successful!";

        } else {

            $message = "Email already exists or registration failed.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Registration</title>

</head>

<body>

    <h1>Online Examination System</h1>

    <h2>Student Registration</h2>

    <?php if (!empty($message)) { ?>

        <p><?php echo htmlspecialchars($message); ?></p>

    <?php } ?>

    <form method="POST">

        <label>Name:</label>
        <br>

        <input type="text" name="name" required>

        <br><br>

        <label>Email:</label>
        <br>

        <input type="email" name="email" required>

        <br><br>

        <label>Password:</label>
        <br>

        <input type="password" name="password" required>

        <br><br>

        <button type="submit">Register</button>

    </form>

    <p>
        Already have an account?
        <a href="login.php">Login</a>
    </p>

</body>

</html>