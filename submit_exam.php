<?php

session_start();

require_once "config/database.php";


/* ==============================
   CHECK STUDENT LOGIN
   ============================== */

if (!isset($_SESSION["student_id"])) {

    header("Location: login.php");
    exit();

}

$student_id = intval($_SESSION["student_id"]);


/* ==============================
   CHECK REQUEST
   ============================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    die("Invalid request.");

}


/* ==============================
   CHECK EXAM ID
   ============================== */

if (!isset($_POST["exam_id"])) {

    die("Exam ID missing.");

}

$exam_id = intval($_POST["exam_id"]);


if ($exam_id <= 0) {

    die("Invalid Exam ID.");

}


/* ==============================
   CHECK ALREADY ATTEMPTED
   ============================== */

$check_sql = "SELECT id
              FROM results
              WHERE student_id = ?
              AND exam_id = ?
              LIMIT 1";

$check_stmt = $conn->prepare($check_sql);

if (!$check_stmt) {

    die("Database Error: " . $conn->error);

}

$check_stmt->bind_param(
    "ii",
    $student_id,
    $exam_id
);

$check_stmt->execute();

$check_result = $check_stmt->get_result();


/*
   If result already exists,
   student cannot submit again.
*/

if ($check_result->num_rows > 0) {

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Already Submitted</title>


    <style>

        body {

            font-family: Arial, sans-serif;

            background: #f4f6f9;

            margin: 0;

            padding-top: 100px;

            text-align: center;

        }


        .box {

            background: white;

            width: 450px;

            max-width: 90%;

            margin: auto;

            padding: 40px;

            border-radius: 12px;

            box-shadow:
                0 3px 15px rgba(0,0,0,0.15);

        }


        h1 {

            color: #dc3545;

        }


        p {

            color: #555;

            font-size: 18px;

            line-height: 1.5;

        }


        .btn {

            display: inline-block;

            margin-top: 20px;

            padding: 12px 25px;

            background: #007bff;

            color: white;

            text-decoration: none;

            border-radius: 6px;

        }


        .btn:hover {

            background: #0056b3;

        }


        .result-btn {

            background: #28a745;

        }


        .result-btn:hover {

            background: #218838;

        }

    </style>

</head>


<body>


<div class="box">

    <h1>
        ⚠️ Already Submitted
    </h1>


    <p>

        You have already submitted this exam.

    </p>


    <p>

        <strong>
            You can attempt an exam only once.
        </strong>

    </p>


    <a
        href="dashboard.php"
        class="btn"
    >

        ← Back to Dashboard

    </a>


    <br>


    <a
        href="my_results.php"
        class="btn result-btn"
    >

        📊 View My Result

    </a>

</div>


</body>

</html>

<?php

    exit();

}


/* ==============================
   GET QUESTIONS
   ============================== */

$sql = "SELECT id, correct_answer
        FROM questions
        WHERE exam_id = ?
        ORDER BY id ASC";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die("Database Error: " . $conn->error);

}

$stmt->bind_param(
    "i",
    $exam_id
);

$stmt->execute();

$result = $stmt->get_result();


/* ==============================
   GET STUDENT ANSWERS
   ============================== */

$answers = $_POST["answer"] ?? [];


$score = 0;

$total = 0;


/* ==============================
   CHECK ANSWERS
   ============================== */

while ($row = $result->fetch_assoc()) {

    $total++;

    $question_id = intval($row["id"]);

    $correct_answer =
        strtoupper(
            trim($row["correct_answer"])
        );


    if (isset($answers[$question_id])) {

        $student_answer =
            strtoupper(
                trim($answers[$question_id])
            );


        if ($student_answer === $correct_answer) {

            $score++;

        }

    }

}


/* ==============================
   CALCULATE RESULT
   ============================== */

$correct = $score;

$wrong = $total - $score;


if ($total > 0) {

    $percentage =
        ($score / $total) * 100;

} else {

    $percentage = 0;

}


/* ==============================
   SAVE RESULT
   ============================== */

$insert_sql = "INSERT INTO results
               (
                   student_id,
                   exam_id,
                   score,
                   total_questions,
                   exam_date
               )
               VALUES
               (?, ?, ?, ?, NOW())";

$insert_stmt =
    $conn->prepare($insert_sql);


if (!$insert_stmt) {

    die(
        "Result Database Error: "
        . $conn->error
    );

}


$insert_stmt->bind_param(
    "iiii",
    $student_id,
    $exam_id,
    $score,
    $total
);


if (!$insert_stmt->execute()) {

    die(
        "Result Save Error: "
        . $insert_stmt->error
    );

}


/* ==============================
   GET EXAM DETAILS
   ============================== */

$exam_sql = "SELECT exam_name, subject
             FROM exams
             WHERE id = ?";

$exam_stmt =
    $conn->prepare($exam_sql);


if (!$exam_stmt) {

    die(
        "Exam Database Error: "
        . $conn->error
    );

}


$exam_stmt->bind_param(
    "i",
    $exam_id
);

$exam_stmt->execute();

$exam_result =
    $exam_stmt->get_result();


if ($exam_result->num_rows == 0) {

    die("Exam not found.");

}


$exam =
    $exam_result->fetch_assoc();


$exam_name =
    $exam["exam_name"] ?? "Exam";

$subject =
    $exam["subject"] ?? "-";


?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Exam Result</title>


    <style>

        body {

            font-family: Arial, sans-serif;

            background: #f4f6f9;

            margin: 0;

            padding: 50px 20px;

        }


        .container {

            width: 95%;

            max-width: 1000px;

            margin: auto;

            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 3px 15px rgba(0,0,0,0.15);

        }


        h1 {

            text-align: center;

            color: #28a745;

            margin-bottom: 10px;

        }


        .exam-name {

            text-align: center;

            font-size: 22px;

            font-weight: bold;

            margin-bottom: 10px;

        }


        .subject {

            text-align: center;

            color: #666;

            margin-bottom: 30px;

        }


        table {

            width: 100%;

            border-collapse: collapse;

        }


        th {

            background: #007bff;

            color: white;

            padding: 15px;

        }


        td {

            padding: 15px;

            text-align: center;

            border-bottom:
                1px solid #ddd;

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

            color: #28a745;

            font-weight: bold;

        }


        .buttons {

            text-align: center;

            margin-top: 30px;

        }


        .btn {

            display: inline-block;

            padding: 12px 25px;

            margin: 5px;

            color: white;

            text-decoration: none;

            border-radius: 6px;

        }


        .dashboard {

            background: #007bff;

        }


        .results {

            background: #28a745;

        }


        .btn:hover {

            opacity: 0.85;

        }


        @media (max-width: 700px) {

            .container {

                overflow-x: auto;

            }

            table {

                min-width: 700px;

            }

        }

    </style>

</head>


<body>


<div class="container">


    <h1>
        🎉 Exam Completed!
    </h1>


    <div class="exam-name">

        <?php

        echo htmlspecialchars(
            $exam_name
        );

        ?>

    </div>


    <div class="subject">

        Subject:

        <?php

        echo htmlspecialchars(
            $subject
        );

        ?>

    </div>


    <table>


        <tr>

            <th>
                Total Questions
            </th>

            <th>
                Correct
            </th>

            <th>
                Wrong
            </th>

            <th>
                Score
            </th>

            <th>
                Percentage
            </th>

            <th>
                Date
            </th>

        </tr>


        <tr>


            <td>

                <?php

                echo $total;

                ?>

            </td>


            <td class="correct">

                <?php

                echo $correct;

                ?>

            </td>


            <td class="wrong">

                <?php

                echo $wrong;

                ?>

            </td>


            <td class="score">

                <?php

                echo $score
                     . "/"
                     . $total;

                ?>

            </td>


            <td class="percentage">

                <?php

                echo number_format(
                    $percentage,
                    2
                );

                ?>%

            </td>


            <td>

                <?php

                echo date("d-m-Y");

                ?>

            </td>


        </tr>


    </table>


    <div class="buttons">


        <a
            href="dashboard.php"
            class="btn dashboard"
        >

            🏠 Back to Dashboard

        </a>


        <a
            href="my_results.php"
            class="btn results"
        >

            📊 My Results

        </a>


    </div>


</div>


</body>

</html>