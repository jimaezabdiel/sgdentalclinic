<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch logged-in user info
$user_stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$full_name = $user['first_name'] . ' ' . $user['last_name'];
$email = $user['email'];

$message = '';
$alertScript = '';

// =================== HANDLE BOOKING ===================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'book') {
    $date = $_POST['date'];
    $location = $_POST['location'];
    $time = $_POST['time'];
    $service = $_POST['service'];

    // Limit to 2 bookings per user per day
    $limit_sql = "SELECT COUNT(*) AS total FROM appointments 
                  WHERE user_id = ? 
                  AND DATE(created_at) = CURDATE() 
                  AND status IN ('pending', 'approved')";
    $limit_stmt = $conn->prepare($limit_sql);
    $limit_stmt->bind_param("i", $user_id);
    $limit_stmt->execute();
    $limit_result = $limit_stmt->get_result();
    $limit_row = $limit_result->fetch_assoc();

    if ($limit_row['total'] >= 2) {
        $alertScript = "Swal.fire({
          title: 'Booking Limit Reached!',
          text: 'You can only make 2 booking requests per day.',
          icon: 'warning',
          confirmButtonColor: '#80A1BA'
        });";
    } else {
        // Check for time slot conflict
        $conflict_sql = "SELECT * FROM appointments 
                         WHERE status = 'approved' 
                         AND date = ? 
                         AND start_time = ? 
                         AND location = ?";
        $stmt = $conn->prepare($conflict_sql);
        $stmt->bind_param("sss", $date, $time, $location);
        $stmt->execute();
        $conflict_result = $stmt->get_result();

        if ($conflict_result->num_rows > 0) {
            $alertScript = "Swal.fire({
              title: 'Time Slot Unavailable!',
              text: 'This time slot is already booked at $location. Please choose another time.',
              icon: 'error',
              confirmButtonColor: '#91C4C3'
            });";
        } else {
            $insert_sql = "INSERT INTO appointments 
                           (user_id, name, email, date, location, start_time, description, status, created_at, qr_code_url, calendar_link) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NULL, NULL)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("issssss", $user_id, $full_name, $email, $date, $location, $time, $service);

            if ($insert_stmt->execute()) {
                $alertScript = "Swal.fire({
                  title: 'Appointment Booked!',
                  text: 'Your request for $location has been submitted successfully!',
                  icon: 'success',
                  confirmButtonColor: '#B4DEBD'
                });";
            } else {
                $alertScript = "Swal.fire({
                  title: 'Booking Failed!',
                  text: 'Something went wrong. Please try again.',
                  icon: 'error',
                  confirmButtonColor: '#91C4C3'
                });";
            }
        }
    }
}

// =================== HANDLE DELETE ===================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = intval($_POST['id']);
    $verify_sql = "SELECT * FROM appointments WHERE id = ? AND user_id = ? AND status != 'approved'";
    $stmt = $conn->prepare($verify_sql);
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM appointments WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $alertScript = "Swal.fire({
              title: 'Deleted!',
              text: 'Your appointment was successfully deleted.',
              icon: 'success',
              confirmButtonColor: '#B4DEBD'
            });";
        } else {
            $alertScript = "Swal.fire({
              title: 'Delete Failed!',
              text: 'Unable to delete appointment. Please try again.',
              icon: 'error',
              confirmButtonColor: '#91C4C3'
            });";
        }
    } else {
        $message = "<div class='alert alert-danger'>⚠️ You cannot delete this appointment.</div>";
    }
}

// =================== FETCH APPOINTMENTS ===================
$pending = $conn->query("SELECT * FROM appointments WHERE user_id = $user_id AND status = 'pending' ORDER BY date DESC");
$denied = $conn->query("SELECT * FROM appointments WHERE user_id = $user_id AND status = 'denied' ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Dental Appointment - DentLink</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(128, 161, 186, 0.15);
            margin-bottom: 30px;
            text-align: center;
            border-top: 5px solid var(--primary-color);
        }

        .page-header h1 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .calendar-container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(128, 161, 186, 0.15);
            margin-bottom: 30px;
            border-left: 5px solid var(--secondary-color);
        }

        .calendar-container h4 {
            color: var(--primary-color);
            font-weight: 600;
        }

        iframe {
            border: 2px solid var(--accent-color);
            border-radius: 12px;
            width: 100%;
            height: 500px;
        }

        .booking-section {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .booking-form {
            flex: 2;
            background: white;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(128, 161, 186, 0.15);
            border-top: 5px solid var(--accent-color);
        }

        .booking-form h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 25px;
        }

        .form-label {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(128, 161, 186, 0.25);
        }

        .sidebar {
            flex: 1;
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 24px rgba(128, 161, 186, 0.15);
            max-height: 700px;
            overflow-y: auto;
            border-top: 5px solid var(--light-color);
        }

        .sidebar h5 {
            color: var(--primary-color);
            font-weight: 600;
            border-bottom: 3px solid var(--accent-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .appointment-item {
            background: linear-gradient(135deg, rgba(180, 222, 189, 0.1) 0%, rgba(255, 247, 221, 0.1) 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--secondary-color);
            transition: all 0.3s ease;
        }

        .appointment-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(128, 161, 186, 0.2);
        }

        .appointment-item h6 {
            color: var(--primary-color);
            margin-bottom: 12px;
            font-weight: 600;
        }

        .appointment-item .small {
            color: #666;
        }

        .delete-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .delete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .btn-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(128, 161, 186, 0.3);
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

        .alert-info {
            background-color: rgba(180, 222, 189, 0.2);
            border-left: 4px solid var(--accent-color);
            border-radius: 10px;
            color: #2d3748;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .booking-section {
                flex-direction: column;
            }

            .page-header {
                padding: 20px;
            }

            .booking-form {
                padding: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <a href="dashboard.php" class="btn btn-back mb-3">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>

        <div class="page-header">
            <h1><i class="bi bi-calendar-check"></i> Book Your Dental Appointment</h1>
            <p class="text-muted mb-0">Welcome, <strong><?= htmlspecialchars($full_name) ?></strong> (<?= htmlspecialchars($email) ?>)</p>
        </div>

        <?= $message ?>

        <!-- Google Calendar -->
        <div class="calendar-container">
            <h4 class="mb-3"><i class="bi bi-calendar3"></i> Check Available Time Slots</h4>
            <p class="text-muted small">Calendar shows available time slots. Booked times will appear as busy blocks.</p>
            <iframe
                src="https://calendar.google.com/calendar/embed?height=500&wkst=1&bgcolor=%23ffffff&ctz=Asia%2FManila&showTitle=0&showPrint=0&showCalendars=0&showTz=0&mode=WEEK&src=c2dkZW50YWxjbGluaWNjY0BnbWFpbC5jb20&color=%234CAF50"
                frameborder="0"
                scrolling="no">
            </iframe>
        </div>

        <div class="booking-section">
            <!-- Booking Form -->
            <div class="booking-form">
                <h4><i class="bi bi-clipboard-check"></i> Appointment Details</h4>
                <form method="POST">
                    <input type="hidden" name="action" value="book">

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-calendar-date"></i> Date:</label>
                        <input type="date" name="date" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-geo-alt"></i> Location:</label>
                        <select name="location" class="form-select" required>
                            <option value="">--Select Location--</option>
                            <option value="Dental Clinic, Lipa City">Lipa City</option>
                            <option value="Dental Clinic, San Pablo City">San Pablo City</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-clock"></i> Time:</label>
                        <select name="time" id="timeSlot" class="form-select" required>
                            <option value="">-- Select Time --</option>
                            <?php
                            for ($hour = 8; $hour <= 16; $hour++) {
                                $timeValue = sprintf("%02d:00:00", $hour);
                                $displayTime = date("h:i A", strtotime($timeValue));
                                echo "<option value='$timeValue' data-label='$displayTime'>$displayTime</option>";
                            }
                            ?>
                        </select>
                        <small class="text-muted">Clinic hours: 8:00 AM - 5:00 PM</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-heart-pulse"></i> Service:</label>
                        <select name="service" id="serviceSelect" class="form-select" required>
                            <option value="">--Select Service--</option>
                            <option value="All Ceramic Veneers with E-Max">All Ceramic Veneers with E-Max</option>
                            <option value="All Ceramic Fixed Bridge with Zirconia">All Ceramic Fixed Bridge with Zirconia</option>
                            <option value="Cleaning">Cleaning</option>
                            <option value="Consultation">Consultation</option>
                            <option value="Cosmetic Restoration Crown Build-Up">Cosmetic Restoration Crown Build-Up</option>
                            <option value="Dental Braces">Dental Braces</option>
                            <option value="Dental Bridges">Dental Bridges</option>
                            <option value="Dental Crowns">Dental Crowns</option>
                            <option value="Dental Filling">Dental Filling</option>
                            <option value="Dental Veneers">Dental Veneers</option>
                            <option value="Dentures">Dentures</option>
                            <option value="Digital Panoramic X-Ray">Digital Panoramic X-Ray</option>
                            <option value="Digital Periapical X-Ray & Intra-Oral Camera">Digital Periapical X-Ray & Intra-Oral Camera</option>
                            <option value="Extraction of Mandibular First Molar">Extraction of Mandibular First Molar</option>
                            <option value="Fixed Bridge">Fixed Bridge</option>
                            <option value="Flexible Dentures">Flexible Dentures</option>
                            <option value="Fluoride Treatment">Fluoride Treatment</option>
                            <option value="General Checkup">General Checkup</option>
                            <option value="Gingivectomy">Gingivectomy</option>
                            <option value="Metallic Ortho Braces">Metallic Ortho Braces</option>
                            <option value="Odontectomy (Impacted Tooth Removal)">Odontectomy (Impacted Tooth Removal)</option>
                            <option value="Oral Prophylaxis">Oral Prophylaxis</option>
                            <option value="Orthodontic Treatment">Orthodontic Treatment</option>
                            <option value="Panoramic X-Ray">Panoramic X-Ray</option>
                            <option value="Periapical X-Ray">Periapical X-Ray</option>
                            <option value="Porcelain Fused to Metal Fixed Bridge">Porcelain Fused to Metal Fixed Bridge</option>
                            <option value="Removable Partial Dentures">Removable Partial Dentures</option>
                            <option value="Retainers and Other Ortho Appliances">Retainers and Other Ortho Appliances</option>
                            <option value="Root Canal Treatment">Root Canal Treatment</option>
                            <option value="Self-Ligating Braces">Self-Ligating Braces</option>
                            <option value="Teeth Whitening">Teeth Whitening</option>
                            <option value="Tooth Extraction">Tooth Extraction</option>
                            <option value="Tooth Restoration">Tooth Restoration</option>
                            <option value="Wisdom / 3rd Molar Extraction">Wisdom / 3rd Molar Extraction</option>
                        </select>
                    </div>

                    <div id="serviceDescription" class="alert alert-info" style="display: none;">
                        <strong><i class="bi bi-info-circle"></i> Service Details:</strong>
                        <p id="descriptionText" class="mb-0 mt-2"></p>
                    </div>

                    <button type="submit" class="btn btn-custom w-100 mt-3">
                        <i class="bi bi-send"></i> Submit Booking Request
                    </button>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <h5><i class="bi bi-hourglass-split text-warning"></i> Pending Requests</h5>
                <?php if ($pending && $pending->num_rows > 0): ?>
                    <?php while ($p = $pending->fetch_assoc()): ?>
                        <div class="appointment-item">
                            <h6><?= htmlspecialchars($p['description']) ?></h6>
                            <p class="mb-1 small">
                                <i class="bi bi-calendar-date"></i> <?= htmlspecialchars($p['date']) ?>
                                @ <?= date("h:i A", strtotime($p['start_time'])) ?>
                            </p>
                            <p class="mb-2 small">
                                <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($p['location']) ?>
                            </p>
                            <button type="button" class="delete-btn swal-delete-btn" data-id="<?= $p['id'] ?>">
                                <i class="bi bi-trash"></i> Delete Request
                            </button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center">No pending requests.</p>
                <?php endif; ?>

                <h5 class="mt-4"><i class="bi bi-x-circle text-danger"></i> Denied Requests</h5>
                <?php if ($denied && $denied->num_rows > 0): ?>
                    <?php while ($d = $denied->fetch_assoc()): ?>
                        <div class="appointment-item">
                            <h6 class="text-danger"><?= htmlspecialchars($d['description']) ?></h6>
                            <p class="mb-1 small">
                                <i class="bi bi-calendar-date"></i> <?= htmlspecialchars($d['date']) ?>
                                @ <?= date("h:i A", strtotime($d['start_time'])) ?>
                            </p>
                            <p class="mb-2 small">
                                <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($d['location']) ?>
                            </p>
                            <button type="button" class="delete-btn swal-delete-btn" data-id="<?= $d['id'] ?>">
                                <i class="bi bi-trash"></i> Delete Request
                            </button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center">No denied requests.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.querySelector('input[name="date"]');
            const locationSelect = document.querySelector('select[name="location"]');
            const timeSelect = document.getElementById('timeSlot');

            function fetchBookedSlots() {
                const date = dateInput.value;
                const location = locationSelect.value;

                if (!date || !location) return;

                fetch(`get_booked_times.php?date=${encodeURIComponent(date)}&location=${encodeURIComponent(location)}`)
                    .then(response => response.json())
                    .then(bookedTimes => {
                        for (let option of timeSelect.options) {
                            if (option.value === "") continue;
                            if (bookedTimes.includes(option.value)) {
                                option.disabled = true;
                                if (!option.textContent.includes('(Booked)')) {
                                    option.textContent += ' (Booked)';
                                }
                            } else {
                                option.disabled = false;
                                option.textContent = option.textContent.replace(' (Booked)', '');
                            }
                        }
                    })
                    .catch(err => console.error('Error loading booked slots:', err));
            }

            dateInput.addEventListener('change', fetchBookedSlots);
            locationSelect.addEventListener('change', fetchBookedSlots);
        });

        const serviceDescriptions = {
            "All Ceramic Veneers with E-Max": "Tooth-colored, durable ceramic veneers made from E-Max material, used to enhance the appearance, shape, and color of front teeth for a natural, aesthetic smile.",
            "All Ceramic Fixed Bridge with Zirconia": "Strong, tooth-colored bridge made from zirconia, used to replace missing teeth while providing a natural appearance and durable long-term function.",
            "Cleaning": "Professional removal of plaque, tartar, and stains, followed by polishing and often fluoride treatment to maintain oral health.",
            "Consultation": "Initial appointment to discuss dental concerns, evaluate oral health, and create a personalized treatment plan.",
            "Cosmetic Restoration Crown Build-Up": "Procedure that rebuilds and strengthens damaged teeth using aesthetic materials.",
            "Dental Braces": "Correct problems with crooked teeth, crowding and out of alignment.",
            "Dental Bridges": "Dental restoration to replace one or more missing teeth.",
            "Dental Crowns": "Protect, cover and restore the shape of your broken, weak or worn-down teeth.",
            "Dental Filling": "Procedure to restore a tooth damaged by decay using composite resin or amalgam.",
            "Dental Veneers": "Cosmetic dental treatment to conceal cracks, chips, stains and other imperfections.",
            "Dentures": "Removable replacement for missing teeth and surrounding tissues.",
            "Digital Panoramic X-Ray": "Wide-view dental imaging technique that captures the entire mouth in a single image.",
            "Digital Periapical X-Ray & Intra-Oral Camera": "Advanced imaging tools for accurate diagnosis and treatment planning.",
            "Extraction of Mandibular First Molar": "Surgical removal of the lower first molar tooth.",
            "Fixed Bridge": "Permanent restoration used to replace missing teeth.",
            "Flexible Dentures": "Lightweight and flexible partial dentures for improved comfort.",
            "Fluoride Treatment": "Application of fluoride to strengthen tooth enamel and prevent cavities.",
            "General Checkup": "Comprehensive oral examination to assess overall dental health.",
            "Gingivectomy": "Surgical removal of diseased gum tissue.",
            "Metallic Ortho Braces": "Traditional orthodontic braces made of stainless steel.",
            "Odontectomy (Impacted Tooth Removal)": "Surgical extraction of an impacted tooth.",
            "Oral Prophylaxis": "Professional cleaning that removes plaque, tartar, and stains.",
            "Orthodontic Treatment": "Dental procedure that aligns and straightens teeth using braces or aligners.",
            "Panoramic X-Ray": "Comprehensive dental imaging that captures the entire mouth.",
            "Periapical X-Ray": "Detailed X-ray focused on one or a few teeth.",
            "Porcelain Fused to Metal Fixed Bridge": "Durable restoration with metal base and porcelain covering.",
            "Removable Partial Dentures": "Prosthesis that replaces some missing teeth.",
            "Retainers and Other Ortho Appliances": "Devices used after orthodontic treatment.",
            "Root Canal Treatment": "Endodontic procedure to remove infected pulp tissue.",
            "Self-Ligating Braces": "Advanced braces system using clips instead of elastic bands.",
            "Teeth Whitening": "Cosmetic procedure that brightens and whitens teeth.",
            "Tooth Extraction": "Removal of a damaged or decayed tooth.",
            "Tooth Restoration": "Repair of tooth decay or structural damage.",
            "Wisdom / 3rd Molar Extraction": "Removal of impacted or misaligned wisdom teeth."
        };

        const serviceSelect = document.getElementById('serviceSelect');
        const descriptionBox = document.getElementById('serviceDescription');
        const descriptionText = document.getElementById('descriptionText');

        if (serviceSelect) {
            serviceSelect.addEventListener('change', function() {
                const selectedService = this.value;

                if (selectedService && serviceDescriptions[selectedService]) {
                    descriptionText.textContent = serviceDescriptions[selectedService];
                    descriptionBox.style.display = 'block';
                } else {
                    descriptionBox.style.display = 'none';
                }
            });
        }

        document.querySelectorAll('.swal-delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentId = this.dataset.id;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to undo this action!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#B4DEBD',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';

                        const actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        actionInput.value = 'delete';

                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'id';
                        idInput.value = appointmentId;

                        form.appendChild(actionInput);
                        form.appendChild(idInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    </script>

    <?php if (!empty($alertScript)): ?>
        <script>
            <?= $alertScript ?>
        </script>
    <?php endif; ?>

</body>

</html>
