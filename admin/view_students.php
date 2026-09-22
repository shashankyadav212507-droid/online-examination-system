<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";


// Get all students
$sql = "SELECT id, name, email
        FROM students
        ORDER BY id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Database Error: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>View Students</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.12);
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 25px;
            padding: 10px 18px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .back-btn:hover {
            background: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #343a40;
            color: white;
            padding: 13px;
        }

        td {
            padding: 13px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background: #f5f5f5;
        }

        .no-student {
            text-align: center;
            padding: 30px;
            font-size: 18px;
            color: #666;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>👨‍🎓 Registered Students</h1>

    <a href="dashboard.php" class="back-btn">
        ← Back to Dashboard
    </a>


    <?php if ($result->num_rows > 0) { ?>

        <table>

            <tr>

                <th>ID</th>

                <th>Student Name</th>

                <th>Email</th>

            </tr>


            <?php while ($student = $result->fetch_assoc()) { ?>

                <tr>

                    <td>
                        <?php echo (int)$student["id"]; ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $student["name"]
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $student["email"]
                        );
                        ?>
                    </td>

                </tr>

            <?php } ?>

        </table>

    <?php } else { ?>

        <div class="no-student">

            No students registered yet.

        </div>

    <?php } ?>

</div>

</body>

</html>