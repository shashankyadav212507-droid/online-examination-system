<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";


// Check exam ID
if (!isset($_GET["id"])) {
    die("Exam ID missing.");
}

$exam_id = intval($_GET["id"]);


// ===============================
// UPDATE EXAM
// ===============================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $exam_name = trim($_POST["exam_name"]);
    $subject = trim($_POST["subject"]);
    $duration = intval($_POST["duration"]);

    if ($exam_name == "" || $subject == "" || $duration <= 0) {

        $error = "Please fill all fields correctly.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE exams
             SET exam_name = ?, subject = ?, duration = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "ssii",
            $exam_name,
            $subject,
            $duration,
            $exam_id
        );

        if ($stmt->execute()) {

            header("Location: manage_exams.php");
            exit();

        } else {

            $error = "Update failed: " . $conn->error;

        }

        $stmt->close();
    }
}


// ===============================
// GET EXISTING EXAM
// ===============================

$stmt = $conn->prepare(
    "SELECT id, exam_name, subject, duration
     FROM exams
     WHERE id = ?"
);

$stmt->bind_param("i", $exam_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    die("Exam not found.");

}

$exam = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Exam</title>


    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 40px;
        }


        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.12);
        }


        h1 {
            text-align: center;
            margin-bottom: 30px;
        }


        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
        }


        input {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 6px;
        }


        button {
            width: 100%;
            margin-top: 25px;
            padding: 13px;
            border: none;
            background: #007bff;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }


        button:hover {
            background: #0056b3;
        }


        .back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }


        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

    </style>

</head>


<body>


<div class="container">


    <h1>✏️ Edit Exam</h1>


    <?php if (isset($error)) { ?>

        <div class="error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php } ?>


    <form method="POST">


        <label>
            Exam Name
        </label>

        <input
            type="text"
            name="exam_name"
            value="<?php echo htmlspecialchars($exam["exam_name"]); ?>"
            required
        >


        <label>
            Subject
        </label>

        <input
            type="text"
            name="subject"
            value="<?php echo htmlspecialchars($exam["subject"]); ?>"
            required
        >


        <label>
            Duration (Minutes)
        </label>

        <input
            type="number"
            name="duration"
            value="<?php echo htmlspecialchars($exam["duration"]); ?>"
            min="1"
            required
        >


        <button type="submit">
            💾 Update Exam
        </button>


    </form>


    <a href="manage_exams.php" class="back">
        ← Back to Manage Exams
    </a>


</div>


</body>

</html>