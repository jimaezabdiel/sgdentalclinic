<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];

// Fetch user's approved appointments
$approved_sql = "SELECT * FROM appointments WHERE user_id = ? AND status = 'approved' ORDER BY date DESC, start_time DESC";
$stmt = $conn->prepare($approved_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$approved = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - DentLink Clinic</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #80A1BA;
            --secondary-color: #91C4C3;
            --accent-color: #B4DEBD;
            --light-color: #FFF7DD;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #B4DEBD 0%, #FFF7DD 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(128, 161, 186, 0.15);
            text-align: center;
            margin-bottom: 30px;
            border-top: 5px solid var(--primary-color);
        }

        .header h1 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .appointment-card {
            background: white;
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 25px;
            box-shadow: 0 8px 24px rgba(128, 161, 186, 0.15);
            transition: all 0.3s ease;
            border-left: 6px solid var(--secondary-color);
        }

        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(128, 161, 186, 0.2);
        }

        .appointment-card h4 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 25px;
        }

        .qr-section {
            text-align: center;
            margin-top: 25px;
            padding: 30px;
            background: linear-gradient(135deg, rgba(180, 222, 189, 0.1) 0%, rgba(255, 247, 221, 0.1) 100%);
            border-radius: 15px;
            border: 3px solid var(--accent-color);
        }

        .qr-section h5 {
            color: var(--accent-color);
            font-weight: 700;
            margin-bottom: 15px;
        }

        .qr-section img {
            max-width: 280px;
            border: 4px solid var(--accent-color);
            border-radius: 12px;
            padding: 20px;
            background: white;
            margin: 20px 0;
            box-shadow: 0 6px 20px rgba(180, 222, 189, 0.3);
        }

        .appointment-details {
            background: linear-gradient(135deg, rgba(180, 222, 189, 0.2) 0%, rgba(255, 247, 221, 0.2) 100%);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            border-left: 5px solid var(--accent-color);
        }

        .appointment-details p {
            margin: 10px 0;
            color: #2c3e50;
            font-weight: 500;
        }

        .appointment-details strong {
            color: var(--primary-color);
        }

        .no-qr-message {
            color: #e67e22;
            font-weight: 600;
            padding: 20px;
            background: linear-gradient(135deg, rgba(255, 247, 221, 0.5) 0%, rgba(255, 247, 221, 0.8) 100%);
            border-radius: 12px;
            margin-top: 20px;
            border-left: 4px solid var(--light-color);
        }

        .btn-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(128, 161, 186, 0.3);
            color: white;
        }

        .btn-outline-custom {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }

        .btn-outline-custom:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--accent-color) 0%, #91C4C3 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 5px;
        }

        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(180, 222, 189, 0.4);
            color: white;
        }

        .btn-back {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .badge-custom {
            background: linear-gradient(135deg, var(--accent-color) 0%, #91C4C3 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }

        .alert-info-custom {
            background: linear-gradient(135deg, rgba(145, 196, 195, 0.1) 0%, rgba(180, 222, 189, 0.1) 100%);
            border-left: 4px solid var(--secondary-color);
            border-radius: 12px;
            color: #2c3e50;
        }

        .alert-info-custom h6 {
            color: var(--primary-color);
            font-weight: 700;
        }

        .no-appointments {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(128, 161, 186, 0.15);
            border-top: 5px solid var(--accent-color);
        }

        .no-appointments i {
            font-size: 80px;
            color: var(--light-color);
            margin-bottom: 20px;
        }

        .no-appointments h4 {
            color: var(--primary-color);
            font-weight: 700;
        }

        @media print {
            body {
                background: white;
            }
            .btn-custom, .btn-outline-custom, .btn-back, .alert-info-custom {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="btn btn-back mb-3">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>

        <div class="header">
            <h1><i class="bi bi-calendar-check"></i> My Appointments</h1>
            <p class="text-muted mb-0">Welcome, <strong><?= htmlspecialchars($full_name) ?></strong></p>
        </div>

        <?php if ($approved->num_rows > 0): ?>
            <?php while ($appt = $approved->fetch_assoc()): ?>
                <div class="appointment-card">
                    <h4>
                        <i class="bi bi-clipboard2-pulse"></i> 
                        <?= htmlspecialchars($appt['description']) ?>
                    </h4>

                    <div class="appointment-details">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="bi bi-calendar-date"></i> Date:</strong> 
                                    <?php 
                                    $date = new DateTime($appt['date']);
                                    echo $date->format('F j, Y (l)');
                                    ?>
                                </p>
                                <p><strong><i class="bi bi-clock"></i> Time:</strong> 
                                    <?php 
                                    $time = new DateTime($appt['start_time']);
                                    echo $time->format('g:i A');
                                    ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="bi bi-geo-alt"></i> Location:</strong> <?= htmlspecialchars($appt['location']) ?></p>
                                <p><strong><i class="bi bi-check-circle"></i> Status:</strong> 
                                    <span class="badge-custom">Approved</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($appt['calendar_link'])): ?>
                        <div class="text-center mb-3">
                            <a href="<?= htmlspecialchars($appt['calendar_link']) ?>" 
                               target="_blank" 
                               class="btn btn-outline-custom">
                                <i class="bi bi-calendar-event"></i> View in Google Calendar
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($appt['qr_code_url'])): ?>
                        <div class="qr-section">
                            <h5>
                                <i class="bi bi-qr-code"></i> Your Appointment QR Code
                            </h5>
                            <p class="text-muted">Present this QR code at the clinic for quick check-in</p>
                            <img src="<?= htmlspecialchars($appt['qr_code_url']) ?>" 
                                 alt="Appointment QR Code"
                                 id="qr_<?= $appt['id'] ?>">
                            <div class="mt-3">
                                <a href="<?= htmlspecialchars($appt['qr_code_url']) ?>" 
                                   download="dentlink_appointment_<?= $appt['id'] ?>.png" 
                                   class="btn btn-success-custom">
                                    <i class="bi bi-download"></i> Download QR Code
                                </a>
                                <button onclick="printQR(<?= $appt['id'] ?>, '<?= htmlspecialchars($appt['description']) ?>', '<?= $date->format('F j, Y') ?>', '<?= $time->format('g:i A') ?>')" 
                                        class="btn btn-outline-custom">
                                    <i class="bi bi-printer"></i> Print QR Code
                                </button>
                                <button onclick="shareQR('<?= htmlspecialchars($appt['qr_code_url']) ?>')" 
                                        class="btn btn-custom">
                                    <i class="bi bi-share"></i> Share
                                </button>
                            </div>
                            <p class="text-muted small mt-3">
                                <i class="bi bi-info-circle"></i> Save this QR code to your phone or take a screenshot
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="no-qr-message">
                            <i class="bi bi-exclamation-triangle"></i> 
                            QR code is being generated. Please refresh this page in a moment.
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info-custom mt-3 mb-0">
                        <h6><i class="bi bi-info-circle"></i> Important Reminders:</h6>
                        <ul class="mb-0 small">
                            <li>Arrive 15 minutes before your appointment time</li>
                            <li>Bring a valid ID along with your QR code</li>
                            <li>Contact the clinic if you need to reschedule</li>
                        </ul>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-appointments">
                <i class="bi bi-calendar-x"></i>
                <h4>No Approved Appointments Yet</h4>
                <p class="text-muted">Your approved appointments will appear here with QR codes</p>
                <a href="book_appointment.php" class="btn btn-custom mt-3">
                    <i class="bi bi-plus-circle"></i> Book New Appointment
                </a>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="book_appointment.php" class="btn btn-outline-custom">
                <i class="bi bi-plus-circle"></i> Book Another Appointment
            </a>
        </div>
    </div>

    <script>
    function printQR(id, service, date, time) {
        var qrImage = document.getElementById('qr_' + id).src;
        var printWindow = window.open('', '', 'height=700,width=800');
        printWindow.document.write('<html><head><title>Print Appointment QR Code</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }');
        printWindow.document.write('.header { background: linear-gradient(135deg, #80A1BA 0%, #91C4C3 100%); color: white; padding: 30px; margin-bottom: 30px; border-radius: 15px; }');
        printWindow.document.write('.details { background: linear-gradient(135deg, rgba(180, 222, 189, 0.2) 0%, rgba(255, 247, 221, 0.2) 100%); padding: 25px; margin: 20px; border-radius: 15px; border-left: 5px solid #B4DEBD; }');
        printWindow.document.write('img { max-width: 350px; border: 4px solid #B4DEBD; padding: 20px; margin: 20px; background: white; border-radius: 12px; }');
        printWindow.document.write('.footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #B4DEBD; color: #80A1BA; }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<div class="header">');
        printWindow.document.write('<h1>🦷 DentLink Dental Clinic</h1>');
        printWindow.document.write('<h3>Appointment QR Code</h3>');
        printWindow.document.write('</div>');
        printWindow.document.write('<div class="details">');
        printWindow.document.write('<p><strong>Patient:</strong> <?= htmlspecialchars($full_name) ?></p>');
        printWindow.document.write('<p><strong>Service:</strong> ' + service + '</p>');
        printWindow.document.write('<p><strong>Date:</strong> ' + date + '</p>');
        printWindow.document.write('<p><strong>Time:</strong> ' + time + '</p>');
        printWindow.document.write('</div>');
        printWindow.document.write('<img src="' + qrImage + '" alt="QR Code">');
        printWindow.document.write('<p><strong style="color: #80A1BA;">Please present this QR code at the clinic</strong></p>');
        printWindow.document.write('<div class="footer">');
        printWindow.document.write('<p>DentLink Dental Clinic</p>');
        printWindow.document.write('<p>2nd Floor, CL Building, E Mayo St, Brgy. 4, Lipa City, 4217 Batangas</p>');
        printWindow.document.write('</div>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        setTimeout(function() {
            printWindow.print();
        }, 250);
    }

    function shareQR(qrUrl) {
        if (navigator.share) {
            navigator.share({
                title: 'My DentLink Appointment QR Code',
                text: 'Here is my dental appointment QR code',
                url: qrUrl
            }).catch(err => console.log('Error sharing:', err));
        } else {
            navigator.clipboard.writeText(qrUrl).then(() => {
                alert('QR code link copied to clipboard!');
            }).catch(err => {
                alert('Share link: ' + qrUrl);
            });
        }
    }

    <?php 
    $hasEmptyQR = false;
    mysqli_data_seek($approved, 0);
    while ($check = $approved->fetch_assoc()) {
        if (empty($check['qr_code_url'])) {
            $hasEmptyQR = true;
            break;
        }
    }
    if ($hasEmptyQR): 
    ?>
    setTimeout(function() {
        location.reload();
    }, 30000);
    <?php endif; ?>
    </script>

    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
