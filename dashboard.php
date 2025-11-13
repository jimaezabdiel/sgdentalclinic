<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard.css">
    <title>DentLink - Clinic System</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#home" style="color: #80A1BA;">
                <img src="dentlink-logo.png" alt="Logo" width="50" height="45" class="me-2">
                <span style="font-size: 1.5rem;">DentLink</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#reviews">Reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#contact">Contact Us</a>
                    </li>
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5 me-2"></i>
                            <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section position-relative">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100 py-5">
                <div class="col-lg-8 mx-auto text-center position-relative" style="z-index: 2;">
                    <h1 class="display-3 fw-bold mb-4 hero-title text-dark">Your Journey to a Confident Smile Starts Here</h1>
                    <p class="lead mb-5 hero-subtitle text-dark">Experience exceptional dental care with DentLink - where advanced technology meets compassionate service for your perfect smile.</p>
                    <a href="book_appointment.php" class="btn btn-custom btn-lg px-5 py-3 rounded-pill shadow-lg">
                        <i class="bi bi-calendar-check me-2"></i>Book Appointment
                    </a>
                </div>
            </div>
        </div>
        <!-- Smile Curve Effect -->
        <div class="smile-curve">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 0C240 80 480 120 720 120C960 120 1200 80 1440 0V120H0V0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm h-100 text-center hover-card">
                        <div class="card-body p-4">
                            <div class="icon-circle mx-auto mb-3" style="background-color: rgba(255, 247, 221, 0.5);">
                                <i class="bi bi-list-check display-4" style="color: #80A1BA;"></i>
                            </div>
                            <h5 class="card-title fw-bold">View Appointments</h5>
                            <p class="card-text text-muted">Check your upcoming and past schedules</p>
                            <a href="view_appointment.php" class="btn btn-outline-custom rounded-pill">View All</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm h-100 text-center hover-card">
                        <div class="card-body p-4">
                            <div class="icon-circle mx-auto mb-3" style="background-color: rgba(180, 222, 189, 0.5);">
                                <i class="bi bi-folder2-open display-4" style="color: #91C4C3;"></i>
                            </div>
                            <h5 class="card-title fw-bold">Medical Records</h5>
                            <p class="card-text text-muted">Access your dental treatment history</p>
                            <a href="view_records.php" class="btn btn-outline-custom rounded-pill">View Records</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="display-5 fw-bold mb-4" style="color: #80A1BA;">Welcome to DentLink</h2>
                    <p class="lead text-muted mb-4">Your trusted partner in comprehensive dental care and oral health excellence.</p>
                    <p class="mb-4">At DentLink, we combine state-of-the-art dental technology with a patient-centered approach to deliver exceptional care. Our experienced team of dental professionals is dedicated to helping you achieve and maintain optimal oral health in a comfortable, welcoming environment.</p>
                    <p class="mb-4">We believe that everyone deserves a healthy, beautiful smile. Whether you need routine preventive care, cosmetic enhancements, or complex restorative treatments, we're here to guide you every step of the way with personalized treatment plans tailored to your unique needs.</p>
                    <div class="row g-3 mt-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3" style="color: #B4DEBD;"></i>
                                <span>Experienced Dentists</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3" style="color: #B4DEBD;"></i>
                                <span>Modern Equipment</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3" style="color: #B4DEBD;"></i>
                                <span>Comfortable Environment</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3" style="color: #B4DEBD;"></i>
                                <span>Affordable Rates</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1606811856475-5e6fcdc6e509?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=736" alt="Dental Clinic" class="img-fluid rounded-4 shadow-lg">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3" style="color: #80A1BA;">Our Services</h2>
                <p class="lead text-muted">Comprehensive dental care tailored to your needs</p>
            </div>

            <div class="row g-4">
                <!-- General Dentistry -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4" style="color: #80A1BA;">
                                <i class="bi bi-clipboard-pulse me-2" style="color: #91C4C3;"></i>General Dentistry
                            </h5>
                            <ul class="list-unstyled service-list">
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Cleaning</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Consultation</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Dental Filling</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Fluoride Treatment - ₱600</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>General Checkup</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Oral Prophylaxis - ₱1,000</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Tooth Extraction - ₱1,000</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Tooth Restoration</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Cosmetic Dentistry -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4" style="color: #80A1BA;">
                                <i class="bi bi-stars me-2" style="color: #91C4C3;"></i>Cosmetic Dentistry
                            </h5>
                            <ul class="list-unstyled service-list">
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>All Ceramic Fixed Bridge with Zirconia</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>All Ceramic Veneers with E-Max</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Cosmetic Restoration Crown Build-Up</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Dental Bridges</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Dental Crowns - ₱9,000/tooth</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Dental Veneers - ₱12,000/tooth</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Fixed Bridge - ₱8,000/unit</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Porcelain Fused to Metal Fixed Bridge</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Teeth Whitening</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Prosthodontics -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4" style="color: #80A1BA;">
                                <i class="bi bi-capsule me-2" style="color: #91C4C3;"></i>Prosthodontics
                            </h5>
                            <ul class="list-unstyled service-list">
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Dentures</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Complete Denture - ₱15,000/arch</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Flexible Dentures - ₱12,000</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Removable Partial Dentures - ₱10,000</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Oral Surgery -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4" style="color: #80A1BA;">
                                <i class="bi bi-scissors me-2" style="color: #91C4C3;"></i>Oral Surgery & Endodontics
                            </h5>
                            <ul class="list-unstyled service-list">
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Extraction of Mandibular First Molar</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Gingivectomy - ₱3,000/area</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Odontectomy (Impacted Tooth Removal) - ₱5,000</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Root Canal Treatment - ₱6,000/tooth</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Wisdom / 3rd Molar Extraction - ₱4,500</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Orthodontics -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4" style="color: #80A1BA;">
                                <i class="bi bi-grid-3x3 me-2" style="color: #91C4C3;"></i>Orthodontics
                            </h5>
                            <ul class="list-unstyled service-list">
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Dental Braces</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Metallic Ortho Braces - ₱45,000-₱60,000</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Orthodontic Treatment (Ceramic) - ₱70,000-₱90,000</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Retainers and Other Ortho Appliances - ₱5,000+</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Self-Ligating Braces - ₱80,000+</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Diagnostic Imaging -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4" style="color: #80A1BA;">
                                <i class="bi bi-camera me-2" style="color: #91C4C3;"></i>Diagnostic Imaging
                            </h5>
                            <ul class="list-unstyled service-list">
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Digital Panoramic X-Ray - ₱1,000</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Digital Periapical X-Ray & Intra-Oral Camera - ₱500</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Panoramic X-Ray - ₱1,000</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: #B4DEBD;"></i>Periapical X-Ray - ₱500</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3" style="color: #80A1BA;">Patient Reviews</h2>
                <p class="lead text-muted">What our patients say about us</p>
            </div>

            <!-- Add Review Form -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">Share Your Experience</h5>
                            <form id="reviewForm" action="submit_review.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <div class="star-rating">
                                        <i class="bi bi-star-fill fs-4 me-1" data-rating="1" style="color: #FFF7DD;"></i>
                                        <i class="bi bi-star-fill fs-4 me-1" data-rating="2" style="color: #FFF7DD;"></i>
                                        <i class="bi bi-star-fill fs-4 me-1" data-rating="3" style="color: #FFF7DD;"></i>
                                        <i class="bi bi-star-fill fs-4 me-1" data-rating="4" style="color: #FFF7DD;"></i>
                                        <i class="bi bi-star-fill fs-4" data-rating="5" style="color: #FFF7DD;"></i>
                                    </div>
                                    <input type="hidden" name="rating" id="rating" value="5">
                                </div>
                                <div class="mb-3">
                                    <label for="review" class="form-label">Your Review</label>
                                    <textarea class="form-control" id="review" name="review" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-custom">Submit Review</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Display -->
            <div class="row g-4" id="reviewsContainer">
                <!-- Reviews will be loaded dynamically from database -->
                <?php
                // Sample review structure - replace with actual database query
                ?>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle text-white me-3" style="background-color: #80A1BA;">A</div>
                                <div>
                                    <h6 class="mb-0">Anonymous Patient</h6>
                                    <div class="small">
                                        <i class="bi bi-star-fill" style="color: #FFF7DD;"></i>
                                        <i class="bi bi-star-fill" style="color: #FFF7DD;"></i>
                                        <i class="bi bi-star-fill" style="color: #FFF7DD;"></i>
                                        <i class="bi bi-star-fill" style="color: #FFF7DD;"></i>
                                        <i class="bi bi-star-fill" style="color: #FFF7DD;"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mb-0">"Excellent service and professional staff. The clinic is clean and modern. Highly recommended!"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3" style="color: #80A1BA;">Contact Us</h2>
                <p class="lead text-muted">Get in touch with us today</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body p-4">
                            <div class="icon-circle mx-auto mb-3" style="background-color: rgba(128, 161, 186, 0.15);">
                                <i class="bi bi-geo-alt-fill fs-1" style="color: #80A1BA;"></i>
                            </div>
                            <h5 class="fw-bold mb-3">Visit Us</h5>
                            <p class="text-muted">2nd Floor, CL Building, E Mayo St,<br>Brgy. 4, Lipa City,<br>4217 Batangas</p>
                            <a href="https://maps.google.com/?q=2nd+Floor+CL+Building+E+Mayo+St+Brgy+4+Lipa+City+Batangas" target="_blank" class="btn btn-outline-custom btn-sm rounded-pill">
                                <i class="bi bi-map me-2"></i>Tap to Navigate
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body p-4">
                            <div class="icon-circle mx-auto mb-3" style="background-color: rgba(145, 196, 195, 0.15);">
                                <i class="bi bi-telephone-fill fs-1" style="color: #91C4C3;"></i>
                            </div>
                            <h5 class="fw-bold mb-3">Call Us</h5>
                            <p class="text-muted mb-3">We're here to help you</p>
                            <a href="tel:+639123456789" class="btn btn-outline-custom btn-sm rounded-pill">
                                <i class="bi bi-telephone me-2"></i>Call Now
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100 text-center">
                        <div class="card-body p-4">
                            <div class="icon-circle mx-auto mb-3" style="background-color: rgba(180, 222, 189, 0.15);">
                                <i class="bi bi-share-fill fs-1" style="color: #B4DEBD;"></i>
                            </div>
                            <h5 class="fw-bold mb-3">Follow Us</h5>
                            <p class="text-muted mb-3">Stay connected with us</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="#" class="btn btn-sm rounded-circle social-btn" style="width: 40px; height: 40px; padding: 8px; background-color: #80A1BA; color: white;">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="#" class="btn btn-sm rounded-circle social-btn" style="width: 40px; height: 40px; padding: 8px; background-color: #91C4C3; color: white;">
                                    <i class="bi bi-instagram"></i>
                                </a>
                                <a href="#" class="btn btn-sm rounded-circle social-btn" style="width: 40px; height: 40px; padding: 8px; background-color: #B4DEBD; color: white;">
                                    <i class="bi bi-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white py-4" style="background: linear-gradient(135deg, #80A1BA 0%, #91C4C3 100%);">
        <div class="container text-center">
            <p class="mb-1">© 2025 SG Dental Clinic. All Rights Reserved.</p>
            <p class="mb-0 small">Developed by <strong>Clinic System Team</strong></p>
        </div>
    </footer>

    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Star rating functionality
        const stars = document.querySelectorAll('.star-rating i');
        const ratingInput = document.getElementById('rating');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingInput.value = rating;
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.remove('bi-star');
                        s.classList.add('bi-star-fill');
                    } else {
                        s.classList.remove('bi-star-fill');
                        s.classList.add('bi-star');
                    }
                });
            });
        });

        // Active nav link on scroll
        window.addEventListener('scroll', () => {
            let current = '';
            const sections = document.querySelectorAll('section');
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>
