<?php
require_once 'db_connect.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    try {
        $db = new Database();
        $conn = $db->getConnect();

        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'Admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $message = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentLink Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #80A1BA;
            --secondary-color: #91C4C3;
            --accent-color: #B4DEBD;
            --light-color: #FFF7DD;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            min-height: 600px;
        }

        .left-side {
            flex: 1;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--light-color) 100%);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .left-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1606811971618-4486d14f3f99?w=800') center/cover;
            opacity: 0.08;
        }

        .left-side img {
            width: 150px;
            height: auto;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }

        .left-side > p {
            color: var(--primary-color);
            font-size: 1.3rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .info-box {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(128, 161, 186, 0.2);
            position: relative;
            z-index: 1;
            border-left: 5px solid var(--secondary-color);
        }

        .info-box p {
            color: #555;
            line-height: 1.8;
            font-size: 0.95rem;
        }

        .info-box strong {
            color: var(--primary-color);
        }

        .right-side {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-box {
            width: 100%;
            max-width: 400px;
        }

        .form-box h2 {
            color: var(--primary-color);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .form-box > p {
            color: #666;
            margin-bottom: 35px;
            font-size: 1rem;
        }

        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        form input[type="email"]:focus,
        form input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(128, 161, 186, 0.1);
        }

        form input[type="submit"] {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(128, 161, 186, 0.3);
        }

        form input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(128, 161, 186, 0.4);
        }

        .message {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 0.95rem;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        @media (max-width: 968px) {
            .container {
                flex-direction: column;
            }

            .left-side {
                padding: 40px 30px;
            }

            .left-side > p {
                font-size: 1.1rem;
            }

            .right-side {
                padding: 40px 30px;
            }

            .form-box h2 {
                font-size: 1.8rem;
            }
        }

        /* Loading Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left-side">
            <img src="dentlink-logo.png" alt="DentLink Logo">
            <p><strong>DentLink: Dental Clinic Digital Appointment and Patient Records Management System</strong></p>
            <div class="info-box">
                <p>
                    <strong>DentLink</strong> is a web-based platform that simplifies dental appointment scheduling and
                    patient record management. Patients can easily book appointments online, view available time slots,
                    and receive email notifications for confirmations and reminders.
                </p>
                <br>
                <p>
                    The system ensures accurate record-keeping by tracking treatment histories and identifying new or returning patients,
                    resulting in efficient and reliable dental services.
                </p>
            </div>
        </div>

        <div class="right-side">
            <div class="form-box">
                <h2><i class="bi bi-box-arrow-in-right"></i> Welcome!</h2>
                <p>Please login to your account.</p>
                <form method="post" action="">
                    <input type="email" name="email" placeholder="📧 Email Address" required>
                    <input type="password" name="password" placeholder="🔒 Password" required>
                    <input type="submit" value="Login">
                </form>

                <?php if (!empty($message)): ?>
                    <p class="message">⚠️ <?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <div class="login-link">
                    Don't have an account? <a href="registration.php">Sign up</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
