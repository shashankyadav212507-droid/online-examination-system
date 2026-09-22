<?php
session_start();
include("db.php");

$sql = "SELECT * FROM exams";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Exams</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .exam-container {
            width: 80%;
            margin: 30px auto;
        }

        .exam-card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .exam-card h2 {
            color: #007bff;
        }

        .start-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .start-btn:hover {
            background: #218838;
        }

        .no-exam {
            text-align: center;
            color: red;
        }
    </style>
</head>

<body>

<h1>Available Exams</h1>

<div class="exam-container">

<?php
if (mysqli_num_rows($result) > 0) {

    while ($exam = mysqli_fetch_assoc($result)) {
?>

    <div class="exam-card">

        <h2>
            <?php echo htmlspecialchars($exam['subject']); ?>
        </h2>

        <p>
            <strong>Duration:</strong>
            <?php echo htmlspecialchars($exam['duration']); ?> minutes
        </p>

        <a href="start_exam.php?id=<?php echo $exam['id']; ?>"
           class="start-btn">
            Start Exam
        </a>

    </div>

<?php
    }

} else {
    echo "<p class='no-exam'>No Exam Available</p>";
}
?>

</div>

</body>
</html>