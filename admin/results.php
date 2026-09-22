<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";


// Get all results
$sql = "SELECT
            r.id,
            s.name AS student_name,
            e.exam_name,
            r.score,
            r.total_questions,
            r.exam_date
        FROM results r
        LEFT JOIN students s
            ON r.student_id = s.id
        LEFT JOIN exams e
            ON r.exam_id = e.id
        ORDER BY r.id DESC";

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

    <title>View Results</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            width: 98%;
            max-width: 1300px;
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

        .buttons {
            margin-bottom: 25px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .dashboard {
            background: #007bff;
        }

        .dashboard:hover {
            background: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
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

        .correct {
            color: #28a745;
            font-weight: bold;
        }

        .wrong {
            color: #dc3545;
            font-weight: bold;
        }

        .score {
            color: #007bff;
            font-weight: bold;
        }

        .percentage {
            font-weight: bold;
        }

        .no-result {
            text-align: center;
            padding: 30px;
            font-size: 18px;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>📊 Student Results</h1>

    <div class="buttons">

        <a href="dashboard.php"
           class="btn dashboard">

            ← Back to Dashboard

        </a>

    </div>


    <?php if ($result->num_rows > 0) { ?>

        <table>

            <tr>

                <th>Student Name</th>

                <th>Exam</th>

                <th>Total Questions</th>

                <th>Correct</th>

                <th>Wrong</th>

                <th>Score</th>

                <th>Percentage</th>

                <th>Date</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()) { ?>

                <?php

                $total = (int)$row["total_questions"];

                $score = (int)$row["score"];

                $wrong = $total - $score;

                if ($total > 0) {
                    $percentage = round(
                        ($score / $total) * 100,
                        2
                    );
                } else {
                    $percentage = 0;
                }

                ?>

                <tr>

                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row["student_name"] ?? "Unknown Student"
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row["exam_name"] ?? "Unknown Exam"
                        );

                        ?>

                    </td>


                    <td>

                        <?php echo $total; ?>

                    </td>


                    <td class="correct">

                        <?php echo $score; ?>

                    </td>


                    <td class="wrong">

                        <?php echo $wrong; ?>

                    </td>


                    <td class="score">

                        <?php echo $score; ?>
                        /
                        <?php echo $total; ?>

                    </td>


                    <td class="percentage">

                        <?php echo $percentage; ?>%

                    </td>


                    <td>

                        <?php

                        echo date(
                            "d-m-Y",
                            strtotime($row["exam_date"])
                        );

                        ?>

                    </td>

                </tr>

            <?php } ?>

        </table>

    <?php } else { ?>

        <div class="no-result">

            No results available yet.

        </div>

    <?php } ?>

</div>

</body>

</html>