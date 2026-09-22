<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Online Examination System</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            color: white;
        }

        /* NAVBAR */

        nav {
            width: 100%;
            padding: 20px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .logo span {
            color: #60a5fa;
        }

        .nav-btn {
            text-decoration: none;
            color: white;
            border: 1px solid rgba(255,255,255,0.5);
            padding: 9px 18px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .nav-btn:hover {
            background: white;
            color: #1e3a8a;
        }

        /* HERO */

        .hero {
            min-height: calc(100vh - 75px);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 50px 20px;
        }

        .hero-content {
            max-width: 850px;
        }

        .icon {
            font-size: 65px;
            margin-bottom: 15px;
        }

        h1 {
            font-size: 52px;
            margin-bottom: 18px;
        }

        h1 span {
            color: #60a5fa;
        }

        .description {
            font-size: 19px;
            line-height: 1.7;
            color: #dbeafe;
            margin-bottom: 35px;
        }

        /* LOGIN CARDS */

        .login-container {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .login-card {
            width: 270px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 15px;
            padding: 28px;
            backdrop-filter: blur(10px);
            transition: 0.3s;
        }

        .login-card:hover {
            transform: translateY(-7px);
            background: rgba(255,255,255,0.15);
        }

        .login-card .card-icon {
            font-size: 42px;
            margin-bottom: 12px;
        }

        .login-card h2 {
            margin-bottom: 10px;
        }

        .login-card p {
            color: #dbeafe;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .login-btn {
            display: block;
            width: 100%;
            padding: 13px;
            border-radius: 7px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            background: #2563eb;
            transition: 0.3s;
        }

        .login-btn:hover {
            background: #1d4ed8;
        }

        .admin-btn {
            background: #16a34a;
        }

        .admin-btn:hover {
            background: #15803d;
        }

        /* FEATURES */

        .features {
            margin-top: 35px;
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            color: #bfdbfe;
            font-size: 14px;
        }

        /* FOOTER */

        footer {
            text-align: center;
            padding: 20px;
            color: #bfdbfe;
            font-size: 13px;
        }

        /* MOBILE */

        @media (max-width: 600px) {

            h1 {
                font-size: 36px;
            }

            .description {
                font-size: 16px;
            }

            nav {
                padding: 18px 5%;
            }

            .logo {
                font-size: 19px;
            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav>

    <div class="logo">
        Online<span>Exam</span>
    </div>

    <a href="login.php" class="nav-btn">
        Student Login
    </a>

</nav>


<!-- HERO -->

<section class="hero">

    <div class="hero-content">


        <div class="icon">
            🎓
        </div>


        <h1>
            Online Examination <span>System</span>
        </h1>


        <p class="description">

            A simple, secure and efficient platform
            for conducting online examinations,
            managing questions and viewing student results.

        </p>


        <!-- LOGIN CARDS -->

        <div class="login-container">


            <!-- STUDENT -->

            <div class="login-card">

                <div class="card-icon">
                    👨‍🎓
                </div>

                <h2>
                    Student
                </h2>

                <p>
                    Login to view available exams,
                    attempt tests and check your results.
                </p>

                <a href="login.php"
                   class="login-btn">

                    Student Login →

                </a>

            </div>


            <!-- ADMIN -->

            <div class="login-card">

                <div class="card-icon">
                    👨‍💼
                </div>

                <h2>
                    Administrator
                </h2>

                <p>
                    Manage exams, questions,
                    students and examination results.
                </p>

                <a href="admin/login.php"
                   class="login-btn admin-btn">

                    Admin Login →

                </a>

            </div>


        </div>


        <!-- FEATURES -->

        <div class="features">

            <span>✓ Secure Login</span>

            <span>✓ Online MCQ Exams</span>

            <span>✓ Instant Results</span>

            <span>✓ One Attempt Per Exam</span>

        </div>


    </div>

</section>


<footer>

    © 2026 Online Examination System
    | BCA Project

</footer>


</body>

</html>