<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";


// ===============================
// DELETE EXAM
// ===============================

if (isset($_GET["delete"])) {

    $exam_id = intval($_GET["delete"]);

    // Delete questions related to exam
    $stmt1 = $conn->prepare(
        "DELETE FROM questions WHERE exam_id = ?"
    );

    if ($stmt1) {
        $stmt1->bind_param("i", $exam_id);
        $stmt1->execute();
        $stmt1->close();
    }


    // Delete results related to exam
    $stmt2 = $conn->prepare(
        "DELETE FROM results WHERE exam_id = ?"
    );

    if ($stmt2) {
        $stmt2->bind_param("i", $exam_id);
        $stmt2->execute();
        $stmt2->close();
    }


    // Delete exam
    $stmt3 = $conn->prepare(
        "DELETE FROM exams WHERE id = ?"
    );

    if ($stmt3) {
        $stmt3->bind_param("i", $exam_id);
        $stmt3->execute();
        $stmt3->close();
    }


    header("Location: manage_exams.php");
    exit();
}


// ===============================
// GET ALL EXAMS
// ===============================

$sql = "SELECT id, exam_name, subject, duration
        FROM exams
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

    <title>Manage Exams</title>


    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }


        .container {
            width: 95%;
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.12);
        }


        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }


        .top-buttons {
            margin-bottom: 25px;
        }


        .btn {
            display: inline-block;
            padding: 10px 18px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-right: 8px;
        }


        .add-btn {
            background: #28a745;
        }


        .add-btn:hover {
            background: #218838;
        }


        .dashboard-btn {
            background: #007bff;
        }


        .dashboard-btn:hover {
            background: #0056b3;
        }


        table {
            width: 100%;
            border-collapse: collapse;
        }


        th {
            background: #343a40;
            color: white;
            padding: 14px;
        }


        td {
            padding: 13px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }


        tr:hover {
            background: #f5f5f5;
        }


        .edit-btn {
            background: #ffc107;
            color: black;
        }


        .edit-btn:hover {
            background: #e0a800;
        }


        .delete-btn {
            background: #dc3545;
        }


        .delete-btn:hover {
            background: #a71d2a;
        }


        .no-exam {
            text-align: center;
            padding: 30px;
            font-size: 18px;
            color: #666;
        }

    </style>

</head>


<body>


<div class="container">


    <h1>📝 Manage Exams</h1>


    <div class="top-buttons">

        <a href="add_exam.php"
           class="btn add-btn">

            ➕ Add New Exam

        </a>


        <a href="dashboard.php"
           class="btn dashboard-btn">

            ← Dashboard

        </a>

    </div>


    <?php if ($result->num_rows > 0) { ?>


        <table>

            <tr>

                <th>ID</th>

                <th>Exam Name</th>

                <th>Subject</th>

                <th>Duration</th>

                <th>Actions</th>

            </tr>


            <?php while ($exam = $result->fetch_assoc()) { ?>


                <tr>

                    <td>
                        <?php echo $exam["id"]; ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $exam["exam_name"]
                        );
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $exam["subject"]
                        );
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars(
                            $exam["duration"]
                        );
                        ?>
                        Minutes
                    </td>


                    <td>

                        <a
                            href="edit_exam.php?id=<?php echo $exam["id"]; ?>"
                            class="btn edit-btn"
                        >
                            ✏️ Edit
                        </a>


                        <a
                            href="manage_exams.php?delete=<?php echo $exam["id"]; ?>"
                            class="btn delete-btn"
                            onclick="return confirm('Are you sure you want to delete this exam?');"
                        >
                            🗑️ Delete
                        </a>

                    </td>

                </tr>


            <?php } ?>


        </table>


    <?php } else { ?>


        <div class="no-exam">

            No exams available yet.

        </div>


    <?php } ?>


</div>


</body>

</html>