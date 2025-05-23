    <footer class="py-5 mt-5 d-print-none">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h4 class="footer-heading">Contact Me</h4>
                    <div class="footer-contact">
                        <div class="footer-contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo htmlspecialchars($profile['email']); ?>"><?php echo htmlspecialchars($profile['email']); ?></a>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $profile['phone'])); ?>"><?php echo htmlspecialchars($profile['phone']); ?></a>
                        </div>
                        <div class="footer-contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($profile['location']); ?></span>
                        </div>
                        <?php if (!empty($profile['website'])): ?>
                        <div class="footer-contact-item">
                            <i class="fas fa-globe"></i>
                            <a href="<?php echo htmlspecialchars($profile['website']); ?>" target="_blank"><?php echo htmlspecialchars(preg_replace('#^https?://#', '', $profile['website'])); ?></a>
                        </div>
                        <?php endif; ?>
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
                        <?php if (!empty($profile['linkedin'])): ?>
                        <a href="<?php echo htmlspecialchars($profile['linkedin']); ?>" target="_blank" class="social-link">
                            <i class="fab fa-linkedin-in fa-lg"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($profile['github'])): ?>
                        <a href="<?php echo htmlspecialchars($profile['github']); ?>" target="_blank" class="social-link">
                            <i class="fab fa-github fa-lg"></i>
                        </a>
                        <?php endif; ?>
                        <a href="mailto:<?php echo htmlspecialchars($profile['email']); ?>" class="social-link">
                            <i class="fas fa-envelope fa-lg"></i>
                        </a>
                    </div>
                    <p>Feel free to reach out if you want to discuss technology, collaboration opportunities, or just have a chat!</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($profile['name']); ?>. All rights reserved.</p>
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