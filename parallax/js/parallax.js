// Enhanced Parallax with Smooth Scrolling and Performance Optimization
class EnhancedParallax {
    constructor() {
        this.layers = document.querySelectorAll('.parallax');
        this.heroContent = document.querySelector('.hero-content');
        this.heroOverlay = document.querySelector('.hero-overlay');
        this.mainContent = document.querySelector('.main-content');
        this.ticking = false;
        this.lastScrollY = 0;
        this.scrollDirection = 'down';
        
        this.init();
    }

    init() {
        // Check if we should enable parallax
        if (this.shouldEnableParallax()) {
            this.setupEventListeners();
            this.updateParallax();
        } else {
            this.disableParallax();
        }
    }

    shouldEnableParallax() {
        const platform = navigator.platform.toLowerCase();
        const userAgent = navigator.userAgent.toLowerCase();
        
        // Disable on mobile devices for better performance
        if (platform.includes('ipad') || platform.includes('iphone') || 
            userAgent.includes('android') || userAgent.includes('mobile')) {
            return false;
        }
        
        return true;
    }

    setupEventListeners() {
        // Use passive listeners for better performance
        window.addEventListener('scroll', this.onScroll.bind(this), { passive: true });
        window.addEventListener('resize', this.onResize.bind(this), { passive: true });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', this.smoothScroll.bind(this));
        });
    }

    onScroll() {
        this.lastScrollY = window.scrollY;
        this.scrollDirection = this.lastScrollY > this.previousScrollY ? 'down' : 'up';
        this.previousScrollY = this.lastScrollY;

        if (!this.ticking) {
            requestAnimationFrame(this.updateParallax.bind(this));
            this.ticking = true;
        }
    }

    onResize() {
        this.updateParallax();
    }

    updateParallax() {
        const scrolled = window.pageYOffset;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        
        // Update parallax layers with different speeds
        this.layers.forEach((layer, index) => {
            const speed = parseFloat(layer.getAttribute('data-speed')) || 1;
            const yPos = -(scrolled * speed / 100);
            
            // Use transform3d for hardware acceleration
            layer.style.transform = `translate3d(0, ${yPos}px, 0)`;
            layer.style.willChange = 'transform';
        });

        // Enhanced hero content animations (fixed positioning)
        if (this.heroContent) {
            const heroOpacity = Math.max(0, 1 - (scrolled / windowHeight));
            const heroScale = Math.max(0.8, 1 - (scrolled / (windowHeight * 2)));
            
            this.heroContent.style.transform = `translate(-50%, -50%) scale(${heroScale})`;
            this.heroContent.style.opacity = heroOpacity;
        }

        // Dynamic overlay opacity
        if (this.heroOverlay) {
            const overlayOpacity = Math.min(0.8, 0.3 + (scrolled / windowHeight) * 0.5);
            this.heroOverlay.style.opacity = overlayOpacity;
        }

        // Reveal main content with animation
        if (this.mainContent && scrolled > windowHeight * 0.8) {
            const revealProgress = Math.min(1, (scrolled - windowHeight * 0.8) / 200);
            this.mainContent.style.transform = `translateY(${20 * (1 - revealProgress)}px)`;
            this.mainContent.style.opacity = revealProgress;
        }

        // Add scroll-based classes for additional CSS animations
        document.body.classList.toggle('scrolled-down', this.scrollDirection === 'down');
        document.body.classList.toggle('scrolled-up', this.scrollDirection === 'up');
        document.body.classList.toggle('page-scrolled', scrolled > 100);

        this.ticking = false;
    }

    smoothScroll(e) {
        e.preventDefault();
        const targetId = e.currentTarget.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
            const targetPosition = targetElement.offsetTop - 50; // Offset for header
            const startPosition = window.pageYOffset;
            const distance = targetPosition - startPosition;
            const duration = 1000; // 1 second smooth scroll
            
            let start = null;
            
            const animation = (currentTime) => {
                if (start === null) start = currentTime;
                const timeElapsed = currentTime - start;
                const progress = Math.min(timeElapsed / duration, 1);
                
                // Easing function for smooth acceleration/deceleration
                const ease = 1 - Math.pow(1 - progress, 3);
                
                window.scrollTo(0, startPosition + (distance * ease));
                
                if (timeElapsed < duration) {
                    requestAnimationFrame(animation);
                }
            };
            
            requestAnimationFrame(animation);
        }
    }

    disableParallax() {
        // Fallback for mobile devices
        this.layers.forEach(layer => {
            layer.style.transform = 'none';
            layer.style.position = 'relative';
        });
        
        if (this.heroContent) {
            this.heroContent.style.transform = 'none';
            this.heroContent.style.opacity = '1';
        }
    }
}

// Performance monitoring and optimization
const performanceMonitor = {
    init() {
        // Reduce parallax complexity on slower devices
        if (this.isLowEndDevice()) {
            this.reduceComplexity();
        }
    },

    isLowEndDevice() {
        // Simple detection for low-end devices
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        const hardwareConcurrency = navigator.hardwareConcurrency || 4;
        
        return connection && (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') ||
               hardwareConcurrency <= 2;
    },

    reduceComplexity() {
        // Reduce number of active parallax layers
        const layers = document.querySelectorAll('.parallax');
        layers.forEach((layer, index) => {
            if (index > 3) { // Keep only first 4 layers active
                layer.style.display = 'none';
            }
        });
    }
};

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    performanceMonitor.init();
    new EnhancedParallax();
});

// Handle visibility changes for performance
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        // Pause animations when tab is not visible
        document.body.style.animationPlayState = 'paused';
    } else {
        // Resume animations when tab becomes visible
        document.body.style.animationPlayState = 'running';
    }
});

// Add smooth reveal animations for elements
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
        }
    });
}, observerOptions);

// Observe elements for reveal animations
document.addEventListener('DOMContentLoaded', () => {
    const revealElements = document.querySelectorAll('.class-card, .feature-item, .call-to-action');
    revealElements.forEach(el => revealObserver.observe(el));
});