<?php

session_start();

if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit();
}

require_once "config/database.php";


// Get available exams
$sql = "SELECT id, exam_name, subject, duration
        FROM exams
        ORDER BY id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        /* HEADER */

        .header {
            background: #007bff;
            color: white;
            padding: 20px 40px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .logout {
            background: #dc3545;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
        }

        .logout:hover {
            background: #b02a37;
        }


        /* CONTAINER */

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 30px auto;
        }


        /* WELCOME */

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 10px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.1);

            margin-bottom: 25px;
        }

        .welcome h2 {
            margin-top: 0;
            color: #333;
        }


        /* BUTTONS */

        .top-buttons {
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 11px 20px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-right: 10px;
        }

        .result-btn {
            background: #28a745;
        }

        .result-btn:hover {
            background: #218838;
        }


        /* EXAMS */

        .exam-section {
            background: white;
            padding: 25px;
            border-radius: 10px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        .exam-section h2 {
            margin-top: 0;
        }

        .exam-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }


        /* EXAM CARD */

        .exam-card {
            background: #f8f9fa;
            border: 1px solid #ddd;

            padding: 25px;

            border-radius: 10px;
        }

        .exam-card h3 {
            color: #007bff;
            margin-top: 0;
        }

        .exam-card p {
            color: #555;
        }

        .start-btn {
            display: inline-block;

            padding: 11px 20px;

            background: #007bff;

            color: white;

            text-decoration: none;

            border-radius: 6px;

            margin-top: 10px;
        }

        .start-btn:hover {
            background: #0056b3;
        }


        /* NO EXAM */

        .no-exam {
            text-align: center;

            padding: 30px;

            color: #777;

            font-size: 18px;
        }


        /* MOBILE */

        @media (max-width: 700px) {

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .exam-container {
                grid-template-columns: 1fr;
            }

            .container {
                width: 95%;
            }

        }

    </style>

</head>

<body>


<!-- HEADER -->

<div class="header">

    <h1>
        📝 Online Examination System
    </h1>

    <a href="logout.php" class="logout">
        Logout
    </a>

</div>


<div class="container">


    <!-- WELCOME -->

    <div class="welcome">

        <h2>
            👨‍🎓 Student Dashboard
        </h2>

        <p>

            Welcome,

            <strong>

                <?php

                echo htmlspecialchars(
                    $_SESSION["student_name"] ?? "Student"
                );

                ?>

            </strong>

            !

        </p>


        <div class="top-buttons">

            <a
                href="my_results.php"
                class="btn result-btn"
            >
                📊 My Results
            </a>

        </div>

    </div>


    <!-- AVAILABLE EXAMS -->

    <div class="exam-section">

        <h2>
            📚 Available Exams
        </h2>

        <br>


        <?php if ($result && $result->num_rows > 0) { ?>


            <div class="exam-container">


                <?php while ($exam = $result->fetch_assoc()) { ?>


                    <div class="exam-card">

                        <h3>

                            <?php

                            echo htmlspecialchars(
                                $exam["exam_name"]
                            );

                            ?>

                        </h3>


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


                        <a
                            href="start_exam.php?id=<?php echo $exam["id"]; ?>"
                            class="start-btn"
                        >
                            ▶ Start Exam
                        </a>

                    </div>


                <?php } ?>


            </div>


        <?php } else { ?>


            <div class="no-exam">

                <p>
                    📭 No Exam Available
                </p>

                <p>
                    Please check again later.
                </p>

            </div>


        <?php } ?>


    </div>


</div>


</body>

</html>