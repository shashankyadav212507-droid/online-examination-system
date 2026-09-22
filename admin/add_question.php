<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";

$message = "";
$message_type = "";


/* =========================
   GET ALL EXAMS
========================= */

$exam_sql = "SELECT id, exam_name, subject
             FROM exams
             ORDER BY id DESC";

$exam_result = $conn->query($exam_sql);


/* =========================
   SAVE MULTIPLE QUESTIONS
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $exam_id = intval($_POST["exam_id"] ?? 0);

    $questions = $_POST["question"] ?? [];
    $option_a = $_POST["option_a"] ?? [];
    $option_b = $_POST["option_b"] ?? [];
    $option_c = $_POST["option_c"] ?? [];
    $option_d = $_POST["option_d"] ?? [];
    $correct_answer = $_POST["correct_answer"] ?? [];


    if ($exam_id <= 0) {

        $message = "Please select an exam.";
        $message_type = "error";

    } else {

        $sql = "INSERT INTO questions
                (exam_id, question, option_a, option_b, option_c, option_d, correct_answer)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);


        if (!$stmt) {

            $message = "Database error: " . $conn->error;
            $message_type = "error";

        } else {

            $success_count = 0;
            $error_count = 0;


            /* =========================
               LOOP THROUGH QUESTIONS
            ========================= */

            for ($i = 0; $i < count($questions); $i++) {

                $q = trim($questions[$i] ?? "");
                $a = trim($option_a[$i] ?? "");
                $b = trim($option_b[$i] ?? "");
                $c = trim($option_c[$i] ?? "");
                $d = trim($option_d[$i] ?? "");
                $correct = $correct_answer[$i] ?? "";


                /* Skip completely empty question */

                if (
                    $q == "" &&
                    $a == "" &&
                    $b == "" &&
                    $c == "" &&
                    $d == "" &&
                    $correct == ""
                ) {
                    continue;
                }


                /* Validate question */

                if (
                    $q == "" ||
                    $a == "" ||
                    $b == "" ||
                    $c == "" ||
                    $d == "" ||
                    $correct == ""
                ) {

                    $error_count++;
                    continue;
                }


                /* Insert question */

                $stmt->bind_param(
                    "issssss",
                    $exam_id,
                    $q,
                    $a,
                    $b,
                    $c,
                    $d,
                    $correct
                );


                if ($stmt->execute()) {

                    $success_count++;

                } else {

                    $error_count++;
                }
            }


            $stmt->close();


            /* =========================
               RESULT MESSAGE
            ========================= */

            if ($success_count > 0 && $error_count == 0) {

                $message =
                    $success_count .
                    " question(s) added successfully!";

                $message_type = "success";

            } elseif ($success_count > 0 && $error_count > 0) {

                $message =
                    $success_count .
                    " question(s) added successfully, but " .
                    $error_count .
                    " question(s) could not be added.";

                $message_type = "warning";

            } else {

                $message =
                    "No question was added. Please fill the question details.";

                $message_type = "error";
            }
        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Add Multiple Questions</title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        body {

            background: #f3f4f6;

            padding: 30px;
        }


        .container {

            max-width: 1000px;

            margin: auto;
        }


        h1 {

            text-align: center;

            color: #1f2937;

            margin-bottom: 10px;
        }


        .subtitle {

            text-align: center;

            color: #6b7280;

            margin-bottom: 25px;
        }


        /* MESSAGE */

        .message {

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-weight: bold;
        }


        .success {

            background: #dcfce7;

            color: #166534;
        }


        .error {

            background: #fee2e2;

            color: #991b1b;
        }


        .warning {

            background: #fef3c7;

            color: #92400e;
        }


        /* MAIN CARD */

        .card {

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow:
                0 5px 20px
                rgba(0,0,0,0.08);

            margin-bottom: 20px;
        }


        /* EXAM SELECT */

        .exam-section {

            margin-bottom: 25px;
        }


        label {

            display: block;

            font-weight: bold;

            color: #374151;

            margin-bottom: 8px;
        }


        select,
        input,
        textarea {

            width: 100%;

            padding: 12px;

            border: 1px solid #d1d5db;

            border-radius: 7px;

            font-size: 15px;

            outline: none;
        }


        select:focus,
        input:focus,
        textarea:focus {

            border-color: #4f46e5;

            box-shadow:
                0 0 0 3px
                rgba(79,70,229,0.1);
        }


        textarea {

            resize: vertical;

            min-height: 90px;
        }


        /* QUESTION CARD */

        .question-card {

            border: 1px solid #d1d5db;

            border-radius: 10px;

            padding: 20px;

            margin-bottom: 20px;

            background: #fafafa;

            position: relative;
        }


        .question-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 15px;
        }


        .question-title {

            font-size: 18px;

            font-weight: bold;

            color: #4f46e5;
        }


        .remove-btn {

            background: #dc2626;

            color: white;

            border: none;

            padding: 7px 12px;

            border-radius: 6px;

            cursor: pointer;

            font-size: 13px;
        }


        .remove-btn:hover {

            background: #b91c1c;
        }


        .form-group {

            margin-bottom: 15px;
        }


        .options {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 15px;
        }


        /* BUTTONS */

        .buttons {

            display: flex;

            gap: 15px;

            margin-top: 20px;
        }


        .add-btn {

            flex: 1;

            padding: 14px;

            border: 2px dashed #4f46e5;

            background: #eef2ff;

            color: #4f46e5;

            border-radius: 8px;

            font-size: 15px;

            font-weight: bold;

            cursor: pointer;
        }


        .add-btn:hover {

            background: #e0e7ff;
        }


        .save-btn {

            flex: 1;

            padding: 14px;

            border: none;

            background: #4f46e5;

            color: white;

            border-radius: 8px;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;
        }


        .save-btn:hover {

            background: #4338ca;
        }


        .back {

            text-align: center;

            margin-top: 20px;
        }


        .back a {

            color: #4f46e5;

            text-decoration: none;

            font-weight: bold;
        }


        .back a:hover {

            text-decoration: underline;
        }


        /* MOBILE */

        @media (max-width: 700px) {

            body {

                padding: 15px;
            }


            .options {

                grid-template-columns: 1fr;
            }


            .buttons {

                flex-direction: column;
            }
        }

    </style>

</head>


<body>


<div class="container">


    <h1>
        Online Examination System
    </h1>


    <p class="subtitle">
        Add Multiple Questions
    </p>


    <?php if ($message != "") { ?>

        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php } ?>


    <form method="POST">


        <!-- =========================
             EXAM SELECTION
        ========================== -->

        <div class="card exam-section">

            <label for="exam_id">
                Select Exam
            </label>


            <select
                name="exam_id"
                id="exam_id"
                required
            >

                <option value="">
                    -- Select Exam --
                </option>


                <?php while ($exam = $exam_result->fetch_assoc()) { ?>

                    <option
                        value="<?php echo $exam["id"]; ?>"
                    >

                        <?php

                        echo htmlspecialchars(
                            $exam["exam_name"] .
                            " - " .
                            $exam["subject"]
                        );

                        ?>

                    </option>

                <?php } ?>

            </select>

        </div>


        <!-- =========================
             QUESTIONS
        ========================== -->

        <div id="questions-container">


            <!-- QUESTION 1 -->

            <div class="question-card">


                <div class="question-header">

                    <div class="question-title">
                        Question 1
                    </div>

                </div>


                <div class="form-group">

                    <label>
                        Question
                    </label>

                    <textarea
                        name="question[]"
                        placeholder="Enter question"
                        required
                    ></textarea>

                </div>


                <div class="options">


                    <div class="form-group">

                        <label>
                            Option A
                        </label>

                        <input
                            type="text"
                            name="option_a[]"
                            placeholder="Enter option A"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Option B
                        </label>

                        <input
                            type="text"
                            name="option_b[]"
                            placeholder="Enter option B"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Option C
                        </label>

                        <input
                            type="text"
                            name="option_c[]"
                            placeholder="Enter option C"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Option D
                        </label>

                        <input
                            type="text"
                            name="option_d[]"
                            placeholder="Enter option D"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label>
                        Correct Answer
                    </label>


                    <select
                        name="correct_answer[]"
                        required
                    >

                        <option value="">
                            -- Select Correct Answer --
                        </option>

                        <option value="A">
                            Option A
                        </option>

                        <option value="B">
                            Option B
                        </option>

                        <option value="C">
                            Option C
                        </option>

                        <option value="D">
                            Option D
                        </option>

                    </select>

                </div>


            </div>


        </div>


        <!-- =========================
             BUTTONS
        ========================== -->

        <div class="buttons">

            <button
                type="button"
                class="add-btn"
                onclick="addQuestion()"
            >
                + Add Another Question
            </button>


            <button
                type="submit"
                class="save-btn"
            >
                💾 Save All Questions
            </button>

        </div>


    </form>


    <div class="back">

        <a href="dashboard.php">
            ← Back to Dashboard
        </a>

    </div>


</div>


<script>

    let questionNumber = 1;


    function addQuestion() {

        questionNumber++;


        const container =
            document.getElementById(
                "questions-container"
            );


        const questionCard =
            document.createElement("div");


        questionCard.className =
            "question-card";


        questionCard.innerHTML = `

            <div class="question-header">

                <div class="question-title">
                    Question ${questionNumber}
                </div>

                <button
                    type="button"
                    class="remove-btn"
                    onclick="removeQuestion(this)"
                >
                    Remove
                </button>

            </div>


            <div class="form-group">

                <label>
                    Question
                </label>

                <textarea
                    name="question[]"
                    placeholder="Enter question"
                    required
                ></textarea>

            </div>


            <div class="options">

                <div class="form-group">

                    <label>
                        Option A
                    </label>

                    <input
                        type="text"
                        name="option_a[]"
                        placeholder="Enter option A"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Option B
                    </label>

                    <input
                        type="text"
                        name="option_b[]"
                        placeholder="Enter option B"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Option C
                    </label>

                    <input
                        type="text"
                        name="option_c[]"
                        placeholder="Enter option C"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Option D
                    </label>

                    <input
                        type="text"
                        name="option_d[]"
                        placeholder="Enter option D"
                        required
                    >

                </div>

            </div>


            <div class="form-group">

                <label>
                    Correct Answer
                </label>


                <select
                    name="correct_answer[]"
                    required
                >

                    <option value="">
                        -- Select Correct Answer --
                    </option>

                    <option value="A">
                        Option A
                    </option>

                    <option value="B">
                        Option B
                    </option>

                    <option value="C">
                        Option C
                    </option>

                    <option value="D">
                        Option D
                    </option>

                </select>

            </div>

        `;


        container.appendChild(questionCard);

    }


    function removeQuestion(button) {

        const card =
            button.closest(".question-card");


        card.remove();


        updateQuestionNumbers();

    }


    function updateQuestionNumbers() {

        const cards =
            document.querySelectorAll(
                ".question-card"
            );


        cards.forEach(
            function(card, index) {

                const title =
                    card.querySelector(
                        ".question-title"
                    );


                title.textContent =
                    "Question " + (index + 1);

            }
        );


        questionNumber = cards.length;

    }

</script>


</body>

</html>