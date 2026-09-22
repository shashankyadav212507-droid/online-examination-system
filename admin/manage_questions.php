<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";


// =====================================
// DELETE QUESTION
// =====================================

if (isset($_GET["delete"])) {

    $question_id = intval($_GET["delete"]);

    if ($question_id > 0) {

        $stmt = $conn->prepare(
            "DELETE FROM questions WHERE id = ?"
        );

        if ($stmt) {

            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $stmt->close();

        }
    }

    header("Location: manage_questions.php");
    exit();
}


// =====================================
// GET ALL QUESTIONS
// =====================================

$sql = "SELECT
            q.id,
            q.exam_id,
            q.question,
            q.option_a,
            q.option_b,
            q.option_c,
            q.option_d,
            q.correct_answer,
            e.exam_name
        FROM questions q
        LEFT JOIN exams e
        ON q.exam_id = e.id
        ORDER BY q.id DESC";

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

    <title>Manage Questions</title>


    <style>

        body {

            font-family: Arial, sans-serif;

            background: #f4f6f9;

            margin: 0;

            padding: 30px;

        }


        .container {

            width: 98%;

            max-width: 1400px;

            margin: auto;

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow: 0 3px 15px rgba(0,0,0,0.12);

            overflow-x: auto;

        }


        h1 {

            text-align: center;

            margin-bottom: 25px;

        }


        .top-buttons {

            margin-bottom: 25px;

        }


        .btn {

            display: inline-block;

            padding: 9px 15px;

            color: white;

            text-decoration: none;

            border-radius: 6px;

            margin-right: 5px;

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


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 1200px;

        }


        th {

            background: #343a40;

            color: white;

            padding: 12px;

        }


        td {

            padding: 12px;

            text-align: center;

            border-bottom: 1px solid #ddd;

            vertical-align: middle;

        }


        tr:hover {

            background: #f5f5f5;

        }


        .question {

            text-align: left;

            min-width: 250px;

        }


        .option {

            min-width: 120px;

        }


        .correct {

            font-weight: bold;

            color: #28a745;

        }


        .no-question {

            text-align: center;

            padding: 30px;

            font-size: 18px;

            color: #666;

        }

    </style>

</head>


<body>


<div class="container">


    <h1>📋 Manage Questions</h1>


    <div class="top-buttons">

        <a href="add_question.php"
           class="btn add-btn">

            ➕ Add Question

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

                <th>Exam</th>

                <th>Question</th>

                <th>Option A</th>

                <th>Option B</th>

                <th>Option C</th>

                <th>Option D</th>

                <th>Correct</th>

                <th>Action</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()) { ?>


                <tr>


                    <!-- QUESTION ID -->

                    <td>

                        <?php echo (int)$row["id"]; ?>

                    </td>


                    <!-- EXAM -->

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row["exam_name"] ?? "Unknown Exam"
                        );

                        ?>

                    </td>


                    <!-- QUESTION -->

                    <td class="question">

                        <?php

                        echo htmlspecialchars(
                            $row["question"]
                        );

                        ?>

                    </td>


                    <!-- OPTION A -->

                    <td class="option">

                        <?php

                        echo htmlspecialchars(
                            $row["option_a"]
                        );

                        ?>

                    </td>


                    <!-- OPTION B -->

                    <td class="option">

                        <?php

                        echo htmlspecialchars(
                            $row["option_b"]
                        );

                        ?>

                    </td>


                    <!-- OPTION C -->

                    <td class="option">

                        <?php

                        echo htmlspecialchars(
                            $row["option_c"]
                        );

                        ?>

                    </td>


                    <!-- OPTION D -->

                    <td class="option">

                        <?php

                        echo htmlspecialchars(
                            $row["option_d"]
                        );

                        ?>

                    </td>


                    <!-- CORRECT ANSWER -->

                    <td class="correct">

                        <?php

                        echo htmlspecialchars(
                            $row["correct_answer"]
                        );

                        ?>

                    </td>


                    <!-- ACTIONS -->

                    <td>


                        <!-- EDIT -->

                        <a
                            href="edit_question.php?id=<?php echo (int)$row["id"]; ?>"
                            class="btn edit-btn"
                        >

                            ✏️ Edit

                        </a>


                        <br><br>


                        <!-- DELETE -->

                        <a
                            href="manage_questions.php?delete=<?php echo (int)$row["id"]; ?>"
                            class="btn delete-btn"
                            onclick="return confirm('Are you sure you want to delete this question?');"
                        >

                            🗑️ Delete

                        </a>


                    </td>


                </tr>


            <?php } ?>


        </table>


    <?php } else { ?>


        <div class="no-question">

            No questions found.

        </div>


    <?php } ?>


</div>


</body>

</html>