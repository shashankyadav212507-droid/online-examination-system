<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";

$sql = "SELECT 
            results.id,
            students.name AS student_name,
            exams.exam_name,
            results.score,
            results.total_questions,
            results.exam_date
        FROM results
        INNER JOIN students 
            ON results.student_id = students.id
        INNER JOIN exams 
            ON results.exam_id = exams.id
        ORDER BY results.exam_date DESC";

$result = $conn->query($sql);

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
            width: 95%;
            max-width: 1200px;
            margin: auto;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .top {
            margin-bottom: 20px;
        }

        .back {
            display: inline-block;
            background: #343a40;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 6px;
        }

        .table-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.12);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #007bff;
            color: white;
            padding: 14px;
        }

        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background: #f5f5f5;
        }

        .percentage {
            font-weight: bold;
            color: #28a745;
        }

        .no-result {
            text-align: center;
            padding: 30px;
            color: #777;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>📊 Student Results</h1>

    <div class="top">

        <a href="dashboard.php" class="back">
            ← Back to Admin Dashboard
        </a>

    </div>

    <div class="table-box">

        <?php if ($result && $result->num_rows > 0) { ?>

        <table>

            <tr>

                <th>Serial Number</th>

                <th>Student Name</th>

                <th>Exam</th>

                <th>Total Questions</th>

                <th>Correct</th>

                <th>Wrong</th>

                <th>Score</th>

                <th>Percentage</th>

                <th>Date</th>

            </tr>

            <?php

            $count = 1;

            while ($row = $result->fetch_assoc()) {

                $total = (int)$row["total_questions"];

                $score = (int)$row["score"];

                $wrong = $total - $score;

                if ($total > 0) {
                    $percentage =
                        round(($score / $total) * 100, 2);
                } else {
                    $percentage = 0;
                }

            ?>

            <tr>

                <td>
                    <?php echo $count; ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $row["student_name"]
                    );
                    ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars(
                        $row["exam_name"]
                    );
                    ?>
                </td>

                <td>
                    <?php echo $total; ?>
                </td>

                <td>
                    <?php echo $score; ?>
                </td>

                <td>
                    <?php echo $wrong; ?>
                </td>

                <td>
                    <?php
                    echo $score . "/" . $total;
                    ?>
                </td>

                <td class="percentage">

                    <?php
                    echo $percentage . "%";
                    ?>

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

            <?php

                $count++;

            }

            ?>

        </table>

        <?php } else { ?>

            <div class="no-result">

                <h3>No Results Found</h3>

                <p>
                    No student has attempted an exam yet.
                </p>

            </div>

        <?php } ?>

    </div>

</div>

</body>

</html>