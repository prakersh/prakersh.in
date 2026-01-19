/**
 * Zen Portfolio - Main JavaScript
 * Theme toggle, scroll animations, and interactions
 */

(function() {
    'use strict';

    // Theme System
    const ThemeManager = {
        themes: ['light', 'dark', 'peach', 'blue'],
        currentIndex: 0,
        storageKey: 'zen-theme',

        init() {
            // Get saved theme or default to light
            const saved = localStorage.getItem(this.storageKey);
            if (saved && this.themes.includes(saved)) {
                this.currentIndex = this.themes.indexOf(saved);
            }

            // Apply initial theme
            this.applyTheme(this.themes[this.currentIndex]);

            // Bind toggle button
            const toggleBtn = document.getElementById('theme-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => this.toggle());
            }
        },

        toggle() {
            this.currentIndex = (this.currentIndex + 1) % this.themes.length;
            const newTheme = this.themes[this.currentIndex];
            this.applyTheme(newTheme);
            localStorage.setItem(this.storageKey, newTheme);
        },

        applyTheme(theme) {
            // Remove existing theme CSS
            const existingThemeCss = document.getElementById('theme-css');

            // Update data-theme attribute
            document.documentElement.setAttribute('data-theme', theme);

            // Load theme CSS if not light (light is default in style.css)
            if (theme !== 'light') {
                if (existingThemeCss) {
                    existingThemeCss.href = `css/theme-${theme}.css`;
                } else {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.id = 'theme-css';
                    link.href = `css/theme-${theme}.css`;
                    document.head.appendChild(link);
                }
            } else if (existingThemeCss) {
                existingThemeCss.remove();
            }

            // Update toggle button icon
            this.updateIcon(theme);
        },

        updateIcon(theme) {
            const toggleBtn = document.getElementById('theme-toggle');
            if (!toggleBtn) return;

            const icon = toggleBtn.querySelector('i');
            if (!icon) return;

            // Remove all icon classes
            icon.className = '';

            // Set icon based on theme
            switch(theme) {
                case 'light':
                    icon.className = 'fas fa-moon';
                    break;
                case 'dark':
                    icon.className = 'fas fa-sun';
                    break;
                case 'peach':
                    icon.className = 'fas fa-leaf';
                    break;
                case 'blue':
                    icon.className = 'fas fa-water';
                    break;
                default:
                    icon.className = 'fas fa-adjust';
            }
        }
    };

    // Mobile Navigation
    const MobileNav = {
        init() {
            const toggleBtn = document.getElementById('mobile-menu-toggle');
            const mobileNav = document.getElementById('mobile-nav');

            if (toggleBtn && mobileNav) {
                toggleBtn.addEventListener('click', () => {
                    const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';
                    toggleBtn.setAttribute('aria-expanded', !isExpanded);
                    toggleBtn.classList.toggle('active');
                    mobileNav.classList.toggle('active');
                });

                // Close menu when clicking a link
                mobileNav.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        toggleBtn.setAttribute('aria-expanded', 'false');
                        toggleBtn.classList.remove('active');
                        mobileNav.classList.remove('active');
                    });
                });
            }
        }
    };

    // Scroll Animations
    const ScrollAnimations = {
        observer: null,

        init() {
            // Check for reduced motion preference
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                // Make all elements visible immediately
                document.querySelectorAll('.animate-on-scroll, .bento-card').forEach(el => {
                    el.classList.add('visible');
                });
                return;
            }

            // Create intersection observer
            this.observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            this.observer.unobserve(entry.target);
                        }
                    });
                },
                {
                    root: null,
                    rootMargin: '0px 0px -50px 0px',
                    threshold: 0.1
                }
            );

            // Observe all animatable elements (both .animate-on-scroll and .bento-card)
            document.querySelectorAll('.animate-on-scroll, .bento-card').forEach(el => {
                this.observer.observe(el);
            });
        }
    };

    // Smooth Scroll
    const SmoothScroll = {
        init() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', (e) => {
                    const targetId = anchor.getAttribute('href');
                    if (targetId === '#') return;

                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        e.preventDefault();

                        const header = document.querySelector('.site-header');
                        const headerHeight = header ? header.offsetHeight : 0;
                        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;

                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });

                        // Update URL without jumping
                        history.pushState(null, null, targetId);
                    }
                });
            });
        }
    };

    // Active Navigation Highlighting
    const NavHighlight = {
        sections: [],
        navLinks: [],

        init() {
            this.sections = document.querySelectorAll('section[id]');
            this.navLinks = document.querySelectorAll('.site-nav__link, .mobile-nav__link');

            if (this.sections.length && this.navLinks.length) {
                window.addEventListener('scroll', () => this.update(), { passive: true });
                this.update();
            }
        },

        update() {
            const header = document.querySelector('.site-header');
            const headerHeight = header ? header.offsetHeight : 0;
            const scrollPosition = window.pageYOffset + headerHeight + 100;

            let currentSection = '';

            this.sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;

                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    currentSection = section.getAttribute('id');
                }
            });

            this.navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${currentSection}`) {
                    link.classList.add('active');
                }
            });
        }
    };

    // Header Scroll Effect
    const HeaderScroll = {
        header: null,
        lastScrollY: 0,

        init() {
            this.header = document.querySelector('.site-header');
            if (this.header) {
                window.addEventListener('scroll', () => this.onScroll(), { passive: true });
            }
        },

        onScroll() {
            const currentScrollY = window.pageYOffset;

            if (currentScrollY > 50) {
                this.header.classList.add('scrolled');
            } else {
                this.header.classList.remove('scrolled');
            }

            this.lastScrollY = currentScrollY;
        }
    };

    // PDF Download
    const PDFDownload = {
        init() {
            const downloadBtn = document.getElementById('download-resume-btn');
            if (downloadBtn) {
                downloadBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.generate();
                });
            }
        },

        generate() {
            // Show loading state
            const btn = document.getElementById('download-resume-btn');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;

            // Generate timestamp to prevent caching
            const timestamp = new Date().getTime();

            // Redirect to PDF generation script
            window.location.href = `generate-pdf-wk.php?t=${timestamp}`;

            // Reset button after delay
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }, 3000);
        }
    };

    // Print Functionality
    const PrintManager = {
        init() {
            // Handle beforeprint and afterprint events
            window.addEventListener('beforeprint', () => {
                document.body.classList.add('is-printing');
            });

            window.addEventListener('afterprint', () => {
                document.body.classList.remove('is-printing');
            });
        }
    };

    // Duration Bar Animation
    const DurationBars = {
        init() {
            // Check for reduced motion preference
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const fill = entry.target.querySelector('.experience-card__duration-fill');
                            if (fill) {
                                // Trigger animation by re-applying the width
                                const width = fill.style.width;
                                fill.style.width = '0';
                                requestAnimationFrame(() => {
                                    fill.style.width = width;
                                });
                            }
                            observer.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.5 }
            );

            document.querySelectorAll('.experience-card__duration-bar').forEach(bar => {
                observer.observe(bar);
            });
        }
    };

    // Initialize everything when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        ThemeManager.init();
        MobileNav.init();
        ScrollAnimations.init();
        SmoothScroll.init();
        NavHighlight.init();
        HeaderScroll.init();
        PDFDownload.init();
        PrintManager.init();
        DurationBars.init();
    });

})();
