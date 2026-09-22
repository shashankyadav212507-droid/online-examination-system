<?php

session_start();

if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit();
}

require_once "config/database.php";

$student_id = intval($_SESSION["student_id"]);


/* Check Exam ID */

if (!isset($_GET["id"])) {
    die("Exam not selected.");
}

$exam_id = intval($_GET["id"]);


/* =====================================================
   CHECK IF STUDENT ALREADY ATTEMPTED THIS EXAM
   ===================================================== */

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


if ($check_result->num_rows > 0) {

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Already Attempted</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            text-align: center;
            padding-top: 100px;
        }

        .box {
            background: white;
            width: 450px;
            max-width: 90%;
            margin: auto;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.15);
        }

        h1 {
            color: #dc3545;
        }

        p {
            font-size: 18px;
            color: #555;
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

    <h1>⚠️ Already Attempted</h1>

    <p>
        You have already attempted this exam.
    </p>

    <p>
        <strong>
            You can attempt an exam only once.
        </strong>
    </p>

    <a href="dashboard.php" class="btn">
        ← Back to Dashboard
    </a>

    <br>

    <a href="my_results.php"
       class="btn result-btn">
        📊 View My Result
    </a>

</div>

</body>

</html>

<?php

    exit();

}


/* =====================================================
   GET EXAM DETAILS
   ===================================================== */

$exam_sql = "SELECT
                id,
                exam_name,
                subject,
                duration
             FROM exams
             WHERE id = ?";

$exam_stmt = $conn->prepare($exam_sql);

if (!$exam_stmt) {
    die("Database Error: " . $conn->error);
}

$exam_stmt->bind_param(
    "i",
    $exam_id
);

$exam_stmt->execute();

$exam_result = $exam_stmt->get_result();

if ($exam_result->num_rows == 0) {
    die("Exam not found.");
}

$exam = $exam_result->fetch_assoc();


/* =====================================================
   GET QUESTIONS
   ===================================================== */

$sql = "SELECT
            id,
            question,
            option_a,
            option_b,
            option_c,
            option_d
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

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>

        <?php
        echo htmlspecialchars($exam["exam_name"]);
        ?>

    </title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background: #f4f6f9;

        }


        /* ================= HEADER ================= */

        .header {

            background: #007bff;

            color: white;

            padding: 18px 30px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            position: sticky;

            top: 0;

            z-index: 1000;

        }

        .header h2 {
            margin: 0;
        }


        /* ================= TIMER ================= */

        .timer {

            background: #dc3545;

            color: white;

            padding: 10px 18px;

            border-radius: 6px;

            font-size: 20px;

            font-weight: bold;

        }


        /* ================= CONTAINER ================= */

        .container {

            width: 90%;

            max-width: 900px;

            margin: 30px auto;

        }


        /* ================= EXAM INFO ================= */

        .exam-info {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.1);

            margin-bottom: 25px;

        }

        .exam-info h1 {

            margin-top: 0;

            color: #007bff;

        }


        /* ================= QUESTION ================= */

        .question {

            background: white;

            padding: 25px;

            margin-bottom: 20px;

            border-radius: 10px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.1);

        }

        .question h3 {

            margin-top: 0;

            color: #333;

            line-height: 1.5;

        }


        /* ================= CLICKABLE OPTIONS ================= */

        .option {

            display: block;

            width: 100%;

            padding: 14px;

            margin: 12px 0;

            border: 1px solid #ddd;

            border-radius: 8px;

            background: white;

            cursor: pointer;

            transition: 0.2s;

            font-size: 16px;

        }


        .option:hover {

            background: #f0f7ff;

            border-color: #007bff;

        }


        .option input[type="radio"] {

            margin-right: 10px;

            cursor: pointer;

        }


        .option span {

            cursor: pointer;

        }


        /* Selected option */

        .option:has(input[type="radio"]:checked) {

            background: #e7f1ff;

            border-color: #007bff;

        }


        /* ================= SUBMIT BUTTON ================= */

        .submit-btn {

            width: 100%;

            padding: 15px;

            background: #28a745;

            color: white;

            border: none;

            border-radius: 6px;

            font-size: 18px;

            cursor: pointer;

            margin-bottom: 30px;

        }


        .submit-btn:hover {

            background: #218838;

        }


        /* ================= MOBILE ================= */

        @media (max-width: 600px) {

            .header {

                flex-direction: column;

                gap: 10px;

                text-align: center;

            }

            .container {

                width: 95%;

            }

        }

    </style>

</head>


<body>


<!-- ================= HEADER ================= -->

<div class="header">

    <h2>
        📝 Online Examination
    </h2>

    <div class="timer">

        ⏱️

        <span id="timer">
            Loading...
        </span>

    </div>

</div>


<div class="container">


    <!-- ================= EXAM INFORMATION ================= -->

    <div class="exam-info">

        <h1>

            <?php

            echo htmlspecialchars(
                $exam["exam_name"]
            );

            ?>

        </h1>


        <p>

            <strong>
                Subject:
            </strong>

            <?php

            echo htmlspecialchars(
                $exam["subject"]
            );

            ?>

        </p>


        <p>

            <strong>
                Duration:
            </strong>

            <?php

            echo htmlspecialchars(
                $exam["duration"]
            );

            ?>

            Minutes

        </p>


        <p>

            <strong>
                Total Questions:
            </strong>

            <?php

            echo $result->num_rows;

            ?>

        </p>

    </div>


    <!-- ================= QUESTIONS ================= -->

    <?php if ($result->num_rows > 0) { ?>


    <form
        id="examForm"
        action="submit_exam.php"
        method="POST"
    >


        <input
            type="hidden"
            name="exam_id"
            value="<?php echo $exam_id; ?>"
        >


        <?php

        $number = 1;

        while ($row = $result->fetch_assoc()) {

        ?>


        <div class="question">


            <h3>

                <?php

                echo $number . ". " .
                     htmlspecialchars(
                         $row["question"]
                     );

                ?>

            </h3>


            <!-- OPTION A -->

            <label class="option">

                <input
                    type="radio"
                    name="answer[<?php echo $row['id']; ?>]"
                    value="A"
                    required
                >

                <span>
                    A.
                    <?php
                    echo htmlspecialchars(
                        $row["option_a"]
                    );
                    ?>
                </span>

            </label>


            <!-- OPTION B -->

            <label class="option">

                <input
                    type="radio"
                    name="answer[<?php echo $row['id']; ?>]"
                    value="B"
                >

                <span>
                    B.
                    <?php
                    echo htmlspecialchars(
                        $row["option_b"]
                    );
                    ?>
                </span>

            </label>


            <!-- OPTION C -->

            <label class="option">

                <input
                    type="radio"
                    name="answer[<?php echo $row['id']; ?>]"
                    value="C"
                >

                <span>
                    C.
                    <?php
                    echo htmlspecialchars(
                        $row["option_c"]
                    );
                    ?>
                </span>

            </label>


            <!-- OPTION D -->

            <label class="option">

                <input
                    type="radio"
                    name="answer[<?php echo $row['id']; ?>]"
                    value="D"
                >

                <span>
                    D.
                    <?php
                    echo htmlspecialchars(
                        $row["option_d"]
                    );
                    ?>
                </span>

            </label>


        </div>


        <?php

            $number++;

        }

        ?>


        <button
            type="submit"
            class="submit-btn"
        >

            ✅ Submit Exam

        </button>


    </form>


    <?php } else { ?>


        <div class="question">

            <h3>
                No questions available for this exam.
            </h3>

        </div>


    <?php } ?>


</div>


<script>


/* ================= TIMER ================= */

let duration =
    <?php echo (int)$exam["duration"]; ?> * 60;


let timer =
    document.getElementById("timer");


let examForm =
    document.getElementById("examForm");


function updateTimer() {


    let minutes =
        Math.floor(duration / 60);


    let seconds =
        duration % 60;


    if (seconds < 10) {

        seconds = "0" + seconds;

    }


    timer.innerHTML =
        minutes + ":" + seconds;


    if (duration <= 0) {

        alert(
            "⏰ Time is over! Your exam will be submitted automatically."
        );


        if (examForm) {

            examForm.submit();

        }


        return;

    }


    duration--;

}


updateTimer();


setInterval(
    updateTimer,
    1000
);


</script>


</body>

</html>