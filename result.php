<?php
session_start();
include "db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$sql = "SELECT * FROM results 
        WHERE student_id = '$student_id'
        ORDER BY id DESC
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Result not found!");
}

$row = mysqli_fetch_assoc($result);

$total_questions = $row['total_questions'];
$correct_questions = $row['score'];
$wrong_questions = $total_questions - $correct_questions;

$percentage = ($correct_questions / $total_questions) * 100;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam Result</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
        }

        .result-box {
            width: 500px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }

        h1 {
            text-align: center;
        }

        .item {
            padding: 15px;
            margin: 10px 0;
            background: #f5f5f5;
            display: flex;
            justify-content: space-between;
            font-size: 18px;
        }

        .value {
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="result-box">

    <h1>Exam Result</h1>

    <div class="item">
        <span>Total Questions:</span>
        <span class="value"><?php echo $total_questions; ?></span>
    </div>

    <div class="item">
        <span>Correct Questions:</span>
        <span class="value"><?php echo $correct_questions; ?></span>
    </div>

    <div class="item">
        <span>Wrong Questions:</span>
        <span class="value"><?php echo $wrong_questions; ?></span>
    </div>

    <div class="item">
        <span>Score:</span>
        <span class="value">
            <?php echo $correct_questions . "/" . $total_questions; ?>
        </span>
    </div>

    <div class="item">
        <span>Percentage:</span>
        <span class="value">
            <?php echo number_format($percentage, 2); ?>%
        </span>
    </div>

</div>

</body>
</html>