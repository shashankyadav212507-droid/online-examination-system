<?php

session_start();

if (!isset($_SESSION["student_id"])) {
    header("Location: ../login.php");
    exit();
}

require_once "../config/database.php";

$student_id = intval($_SESSION["student_id"]);

// Get student's results
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
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #007bff;
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

        .back-btn {
            display: inline-block;
            margin-bottom: 25px;
            padding: 10px 18px;
            background: #343a40;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .back-btn:hover {
            background: #222;
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

    <h1>📊 My Results</h1>

    <a href="../dashboard.php" class="back-btn">
        ← Back to Dashboard
    </a>

    <?php if ($result->num_rows > 0) { ?>

        <table>

            <tr>

                <th>Exam</th>

                <th>Subject</th>

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
                            $row["exam_name"] ?? "Unknown Exam"
                        );
                        ?>
                    </td>

                    <td>
                        <?php
                        echo htmlspecialchars(
                            $row["subject"] ?? "-"
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
                        <?php echo $score; ?> / <?php echo $total; ?>
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

            You have not attempted any exam yet.

        </div>

    <?php } ?>

</div>

</body>

</html>