<?php

session_start();

if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit();
}

require_once "config/database.php";

$student_id = intval($_SESSION["student_id"]);

$sql = "SELECT
            r.score,
            r.total_questions,
            r.exam_date,
            e.exam_name,
            e.subject
        FROM results r
        LEFT JOIN exams e
            ON r.exam_id = e.id
        WHERE r.student_id = ?
        ORDER BY r.id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database Error: " . $conn->error);
}

$stmt->bind_param("i", $student_id);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Results</title>

    <style>

        * {
            box-sizing: border-box;
        }

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
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.12);
        }

        h1 {
            text-align: center;
            color: #343a40;
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

        .table-box {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th {
            background: #007bff;
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

        .serial {
            font-weight: bold;
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
            color: #28a745;
            font-weight: bold;
        }

        .no-result {
            text-align: center;
            padding: 30px;
            font-size: 18px;
            color: #666;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>📊 My Exam Results</h1>


    <a href="dashboard.php" class="back-btn">
        ← Back to Dashboard
    </a>


    <?php if ($result->num_rows > 0) { ?>

    <div class="table-box">

        <table>

            <tr>

                <th>Serial Number</th>

                <th>Exam</th>

                <th>Subject</th>

                <th>Total Questions</th>

                <th>Correct</th>

                <th>Wrong</th>

                <th>Score</th>

                <th>Percentage</th>

                <th>Date</th>

            </tr>


            <?php

            $serial_number = 1;

            while ($row = $result->fetch_assoc()) {

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

                <!-- SERIAL NUMBER -->

                <td class="serial">

                    <?php echo $serial_number; ?>

                </td>


                <!-- EXAM -->

                <td>

                    <?php

                    echo htmlspecialchars(
                        $row["exam_name"] ?? "Unknown Exam"
                    );

                    ?>

                </td>


                <!-- SUBJECT -->

                <td>

                    <?php

                    echo htmlspecialchars(
                        $row["subject"] ?? "-"
                    );

                    ?>

                </td>


                <!-- TOTAL -->

                <td>

                    <?php echo $total; ?>

                </td>


                <!-- CORRECT -->

                <td class="correct">

                    <?php echo $score; ?>

                </td>


                <!-- WRONG -->

                <td class="wrong">

                    <?php echo $wrong; ?>

                </td>


                <!-- SCORE -->

                <td class="score">

                    <?php echo $score; ?>

                    /

                    <?php echo $total; ?>

                </td>


                <!-- PERCENTAGE -->

                <td class="percentage">

                    <?php echo $percentage; ?>%

                </td>


                <!-- DATE -->

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

                $serial_number++;

            }

            ?>

        </table>

    </div>


    <?php } else { ?>


        <div class="no-result">

            <h3>
                No Results Found
            </h3>

            <p>
                You have not attempted any exam yet.
            </p>

        </div>


    <?php } ?>


</div>

</body>

</html>