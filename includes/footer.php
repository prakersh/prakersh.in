<?php
// Include the database connection to get profile data if not already included
if (!isset($profile)) {
    require_once __DIR__ . '/db_connect.php';
    $pdo = getDbConnection();

    // Fetch profile data
    $stmt = $pdo->prepare('SELECT * FROM profile WHERE id = 1');
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Set defaults in case the database is empty
$name = $profile ? htmlspecialchars($profile['name']) : 'Portfolio';
$email = $profile ? htmlspecialchars($profile['email']) : '';
$phone = $profile ? htmlspecialchars($profile['phone']) : '';
$location = $profile ? htmlspecialchars($profile['location']) : '';
$website = $profile ? htmlspecialchars($profile['website']) : '';
$linkedin = $profile ? htmlspecialchars($profile['linkedin']) : '';
$github = $profile ? htmlspecialchars($profile['github']) : '';
?>
    <footer class="py-5 mt-5 d-print-none">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h4 class="footer-heading">Contact Me</h4>
                    <div class="footer-contact">
                        <div class="footer-contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo str_replace(' ', '', $phone); ?>"><?php echo $phone; ?></a>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo $location; ?></span>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-globe"></i>
                            <a href="https://<?php echo $website; ?>" target="_blank"><?php echo $website; ?></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h4 class="footer-heading">Interests</h4>
                    <div class="footer-interests">
                        <div class="interest-item">
                            <i class="fas fa-laptop-code"></i>
                            <span>Computing</span>
                        </div>
                        <div class="interest-item">
                            <i class="fas fa-music"></i>
                            <span>Music</span>
                        </div>
                        <div class="interest-item">
                            <i class="fas fa-plane"></i>
                            <span>Traveling</span>
                        </div>
                        <div class="interest-item">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Mentoring</span>
                        </div>
                        <div class="interest-item">
                            <i class="fas fa-hands-helping"></i>
                            <span>Social service</span>
                        </div>
                        <div class="interest-item">
                            <i class="fas fa-tv"></i>
                            <span>TV series</span>
                        </div>
                        <div class="interest-item">
                            <i class="fas fa-microchip"></i>
                            <span>Technology</span>
                        </div>
                        <div class="interest-item">
                            <i class="fas fa-lightbulb"></i>
                            <span>Problem solving</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4 class="footer-heading">Connect With Me</h4>
                    <div class="social-links d-flex gap-3 mb-4">
                        <a href="https://<?php echo $linkedin; ?>" target="_blank" class="social-link">
                            <i class="fab fa-linkedin-in fa-lg"></i>
                        </a>
                        <a href="https://<?php echo $github; ?>" target="_blank" class="social-link">
                            <i class="fab fa-github fa-lg"></i>
                        </a>
                        <a href="mailto:<?php echo $email; ?>" class="social-link">
                            <i class="fas fa-envelope fa-lg"></i>
                        </a>
                    </div>
                    <p>Feel free to reach out if you want to discuss technology, collaboration opportunities, or just have a chat!</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $name; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <!-- HTML2PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html> 