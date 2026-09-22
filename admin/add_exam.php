<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $exam_name = $_POST['exam_name'];
    $subject = $_POST['subject'];
    $duration = $_POST['duration'];

    $sql = "INSERT INTO exams (exam_name, subject, duration)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $exam_name, $subject, $duration);

    if ($stmt->execute()) {
        $message = "Exam added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Exam</title>
</head>

<body>

<h1>Online Examination System</h1>

<h2>Add New Exam</h2>

<?php
if ($message != "") {
    echo "<p>$message</p>";
}
?>

<form method="POST">

    <label>Exam Name:</label><br>
    <input type="text" name="exam_name" required>

    <br><br>

    <label>Subject:</label><br>
    <input type="text" name="subject" required>

    <br><br>

    <label>Duration (minutes):</label><br>
    <input type="number" name="duration" required>

    <br><br>

    <button type="submit">Add Exam</button>

</form>

<br>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>