    </main>

    <footer class="site-footer d-print-none" role="contentinfo">
        <div class="zen-container">
            <div class="site-footer__inner">
                <div class="site-footer__links">
                    <?php if (!empty($profile['linkedin'])): ?>
                    <a href="<?php echo htmlspecialchars(ensureHttps($profile['linkedin'])); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="site-footer__link"
                       aria-label="LinkedIn Profile">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($profile['github'])): ?>
                    <a href="<?php echo htmlspecialchars(ensureHttps($profile['github'])); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="site-footer__link"
                       aria-label="GitHub Profile">
                        <i class="fab fa-github"></i>
                    </a>
                    <?php endif; ?>

                    <a href="mailto:<?php echo htmlspecialchars($profile['email']); ?>"
                       class="site-footer__link"
                       aria-label="Send Email">
                        <i class="fas fa-envelope"></i>
                    </a>

                    <?php if (!empty($profile['website'])): ?>
                    <a href="<?php echo htmlspecialchars(ensureHttps($profile['website'])); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="site-footer__link"
                       aria-label="Personal Website">
                        <i class="fas fa-globe"></i>
                    </a>
                    <?php endif; ?>
                </div>

                <p class="site-footer__copyright">
                    &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($profile['name']); ?>
                </p>
            </div>
        </div>
    </footer>

    <!-- HTML2PDF.js for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="js/main.js"></script>
</body>
</html>
