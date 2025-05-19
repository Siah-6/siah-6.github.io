// Initialize AOS (Animate On Scroll)
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Initialize Particles.js
    initParticles();
    
    // Mobile menu toggle
    setupMobileMenu();
    
    // Project tabs
    setupProjectTabs();
    
    // Contact form
    setupContactForm();
    
    // Back to top button
    setupBackToTop();
});

// Particles.js initialization
function initParticles() {
    if (typeof particlesJS !== 'undefined') {
        particlesJS("particles-js", {
            particles: {
                number: { 
                    value: 80, 
                    density: { 
                        enable: true, 
                        value_area: 800 
                    } 
                },
                color: { value: "#2563eb" },
                shape: { type: "circle" },
                opacity: { 
                    value: 0.5, 
                    random: true 
                },
                size: { 
                    value: 3, 
                    random: true 
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#8b5cf6",
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 1,
                    direction: "none",
                    random: true,
                    straight: false,
                    out_mode: "out",
                    bounce: false
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { 
                        enable: true, 
                        mode: "repulse" 
                    },
                    onclick: { 
                        enable: true, 
                        mode: "push" 
                    },
                    resize: true
                }
            },
            retina_detect: true
        });
    }
}

// Mobile menu functionality
function setupMobileMenu() {
    const menuBtn = document.querySelector('.menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileLinks = document.querySelectorAll('.mobile-menu a');
    
    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
        });
        
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
            });
        });
    }
    
    // Handle navbar background change on scroll
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.style.padding = '10px 0';
                navbar.style.backgroundColor = 'rgba(13, 17, 23, 0.95)';
            } else {
                navbar.style.padding = '20px 0';
                navbar.style.backgroundColor = 'rgba(13, 17, 23, 0.8)';
            }
        });
    }
}

// Project tabs functionality
function setupProjectTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const projectGrids = document.querySelectorAll('.projects-grid');
    
    if (tabBtns.length && projectGrids.length) {
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all tabs
                tabBtns.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Hide all project grids
                projectGrids.forEach(grid => grid.classList.remove('active'));
                
                // Show the corresponding project grid
                const category = this.getAttribute('data-category');
                document.getElementById(`${category}-projects`).classList.add('active');
            });
        });
    }
}

// Contact form functionality
function setupContactForm() {
    const contactForm = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');
    
    if (contactForm && formMessage) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form fields
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();
            
            // Simple validation
            if (!name || !email || !message) {
                formMessage.textContent = 'Please fill in all fields.';
                formMessage.className = 'form-message error';
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                formMessage.textContent = 'Please enter a valid email address.';
                formMessage.className = 'form-message error';
                return;
            }
            
            // Simulate form submission (in a real app, you'd submit to a server)
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;
            
            // Simulate server delay
            setTimeout(() => {
                formMessage.textContent = 'Message sent successfully! I\'ll get back to you soon.';
                formMessage.className = 'form-message success';
                
                // Reset form
                contactForm.reset();
                
                // Reset button
                submitBtn.textContent = originalBtnText;
                submitBtn.disabled = false;
                
                // Hide success message after 5 seconds
                setTimeout(() => {
                    formMessage.style.display = 'none';
                }, 5000);
            }, 1500);
        });
    }
}

// Back to top button functionality
function setupBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    
    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.style.opacity = '1';
                backToTopBtn.style.pointerEvents = 'all';
            } else {
                backToTopBtn.style.opacity = '0';
                backToTopBtn.style.pointerEvents = 'none';
            }
        });
    }
}