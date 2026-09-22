<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";


// Total Exams
$exam_query = $conn->query("SELECT COUNT(*) AS total FROM exams");
$total_exams = $exam_query->fetch_assoc()["total"];


// Total Questions
$question_query = $conn->query("SELECT COUNT(*) AS total FROM questions");
$total_questions = $question_query->fetch_assoc()["total"];


// Total Students
$student_query = $conn->query("SELECT COUNT(*) AS total FROM students");
$total_students = $student_query->fetch_assoc()["total"];


// Total Results
$result_query = $conn->query("SELECT COUNT(*) AS total FROM results");
$total_results = $result_query->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

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
        }

        .logout {
            background: #dc3545;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
        }

        /* MAIN */

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        /* STATISTICS */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            text-align: center;
            border-radius: 10px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        .stat-card h2 {
            font-size: 35px;
            margin: 10px 0;
            color: #007bff;
        }

        .stat-card p {
            margin: 0;
            color: #666;
            font-size: 17px;
        }

        /* ADMIN PANEL */

        .panel {
            background: white;
            padding: 25px;
            border-radius: 10px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .card {
            padding: 25px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .card h3 {
            color: #007bff;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .blue {
            background: #007bff;
        }

        .green {
            background: #28a745;
        }

        .orange {
            background: #fd7e14;
        }

        .purple {
            background: #6f42c1;
        }

        .dark {
            background: #343a40;
        }

        .btn:hover {
            opacity: 0.85;
        }

        /* MOBILE */

        @media (max-width: 800px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .cards {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 500px) {

            .stats {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                gap: 15px;
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
            👨‍💼 Admin Dashboard
        </h2>

        <p>

            Welcome,

            <strong>

                <?php

                echo htmlspecialchars(
                    $_SESSION["admin_username"] ?? "Admin"
                );

                ?>

            </strong>

        </p>

    </div>


    <!-- STATISTICS -->

    <div class="stats">


        <div class="stat-card">

            <p>📚 Total Exams</p>

            <h2>
                <?php echo $total_exams; ?>
            </h2>

        </div>


        <div class="stat-card">

            <p>❓ Total Questions</p>

            <h2>
                <?php echo $total_questions; ?>
            </h2>

        </div>


        <div class="stat-card">

            <p>👨‍🎓 Total Students</p>

            <h2>
                <?php echo $total_students; ?>
            </h2>

        </div>


        <div class="stat-card">

            <p>📊 Total Results</p>

            <h2>
                <?php echo $total_results; ?>
            </h2>

        </div>


    </div>


    <!-- ADMIN PANEL -->

    <div class="panel">

        <h2>
            ⚙️ Admin Panel
        </h2>

        <br>


        <div class="cards">


            <!-- ADD EXAM -->

            <div class="card">

                <h3>📝 Add Exam</h3>

                <p>
                    Create a new examination.
                </p>

                <a
                    href="add_exam.php"
                    class="btn blue"
                >
                    Add Exam
                </a>

            </div>


            <!-- MANAGE EXAMS -->

            <div class="card">

                <h3>📚 Manage Exams</h3>

                <p>
                    Edit or delete existing exams.
                </p>

                <a
                    href="manage_exams.php"
                    class="btn green"
                >
                    Manage Exams
                </a>

            </div>


            <!-- ADD QUESTIONS -->

            <div class="card">

                <h3>❓ Add Questions</h3>

                <p>
                    Add questions to exams.
                </p>

                <a
                    href="add_question.php"
                    class="btn orange"
                >
                    Add Questions
                </a>

            </div>


            <!-- MANAGE QUESTIONS -->

            <div class="card">

                <h3>📋 Manage Questions</h3>

                <p>
                    Edit or delete questions.
                </p>

                <a
                    href="manage_questions.php"
                    class="btn purple"
                >
                    Manage Questions
                </a>

            </div>


            <!-- STUDENTS -->

            <div class="card">

                <h3>👨‍🎓 Students</h3>

                <p>
                    View all registered students.
                </p>

                <a
                    href="view_students.php"
                    class="btn dark"
                >
                    View Students
                </a>

            </div>


            <!-- RESULTS -->

            <div class="card">

                <h3>📊 Results</h3>

                <p>
                    View all student results.
                </p>

                <a
                    href="view_results.php"
                    class="btn blue"
                >
                    View Results
                </a>

            </div>


        </div>

    </div>

</div>

</body>

</html>