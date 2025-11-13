<?php
require_once 'db_connect.php';
require_once 'twilio-php-main\src\Twilio\autoload.php';
use Twilio\Rest\Client;

session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name  = trim($_POST["last_name"]);
    $email      = trim($_POST["email"]);
    $phone      = trim($_POST["phone"]);
    $address    = trim($_POST["address"]);
    $password   = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
    $role       = "Patient";

    if ($first_name && $last_name && $email && $phone && $address && $password) {
        // Save registration data temporarily in session
        $_SESSION['pending_user'] = [
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'email'      => $email,
            'phone'      => $phone,
            'address'    => $address,
            'role'       => $role,
            'password'   => $password
        ];

        // Send OTP via Twilio Verify
        $sid = "AC9c75d0e89a750bdc4ff2f0c894326a16";
        $token = "a7baaa3371668f5864cf6f74f7724a24";
        $verify_sid = "VA30a9bde26a895cd8b4b664d328a9a55d";

        $client = new Client($sid, $token);
        $to = '+63' . ltrim($phone, '0');

        try {
            $client->verify->v2->services($verify_sid)
                ->verifications
                ->create($to, 'sms');

            header("Location: verify_code.php");
            exit();
        } catch (Exception $e) {
            $message = "Error sending OTP: " . $e->getMessage();
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentLink Registration</title>
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
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
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
            min-height: 650px;
        }

        .left-side {
            flex: 1;
            background: linear-gradient(135deg, var(--light-color) 0%, var(--accent-color) 100%);
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
            border-left: 5px solid var(--primary-color);
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
            padding: 50px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .form-box {
            width: 100%;
            max-width: 450px;
        }

        .form-box h2 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .name-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        form input[type="text"],
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

        .name-fields input {
            margin-bottom: 0;
        }

        form input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(128, 161, 186, 0.1);
        }

        form input[type="submit"] {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(145, 196, 195, 0.3);
            margin-top: 10px;
        }

        form input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(145, 196, 195, 0.4);
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
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: var(--primary-color);
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
                font-size: 1.6rem;
            }

            .name-fields {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .name-fields input {
                margin-bottom: 20px;
            }
        }

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
                <h2><i class="bi bi-person-plus-fill"></i> Registration Form</h2>
                <form method="POST" action="">
                    <div class="name-fields">
                        <input type="text" name="first_name" placeholder="First Name" required>
                        <input type="text" name="last_name" placeholder="Last Name" required>
                    </div>
                    <input type="email" name="email" placeholder="📧 Email Address" required>
                    <input type="text" name="phone" placeholder="📱 Phone Number (09XXXXXXXXX)" required>
                    <input type="text" name="address" placeholder="🏠 Complete Address" required>
                    <input type="password" id="password" name="password" placeholder="🔒 Password" required>
                    <input type="password" name="confirm_password" placeholder="🔒 Confirm Password" required>
                    <input type="submit" value="Register">
                </form>

                <?php if (!empty($message)): ?>
                    <p class="message">⚠️ <?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <div class="login-link">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
