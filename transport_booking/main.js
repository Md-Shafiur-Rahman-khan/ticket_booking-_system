// Enhanced main.js with Motion Graphics
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize particles background
    createParticles();
    
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            this.querySelector('i').classList.toggle('fa-bars');
            this.querySelector('i').classList.toggle('fa-times');
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.navbar') && window.innerWidth <= 768) {
            navLinks.classList.remove('active');
            if (menuToggle) {
                menuToggle.querySelector('i').classList.add('fa-bars');
                menuToggle.querySelector('i').classList.remove('fa-times');
            }
        }
    });
    
    // Animate stats counting
    animateStats();
    
    // Scroll progress bar
    const scrollProgress = document.createElement('div');
    scrollProgress.className = 'scroll-progress';
    document.body.appendChild(scrollProgress);
    
    window.addEventListener('scroll', function() {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        scrollProgress.style.width = scrolled + '%';
        
        // Back to top button
        const backToTop = document.querySelector('.back-to-top') || createBackToTopButton();
        if (winScroll > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });
    
    // Create floating notification
    createNotification();
    
    // Typewriter effect for hero text
    const heroText = document.querySelector('.hero h1');
    if (heroText) {
        const text = heroText.textContent;
        heroText.innerHTML = '';
        heroText.classList.add('typewriter');
        heroText.style.width = '0';
        
        setTimeout(() => {
            heroText.textContent = text;
            heroText.style.width = '100%';
        }, 100);
    }
    
    // Card hover effects
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateY = (x - centerX) / 25;
            const rotateX = (centerY - y) / 25;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(20px)`;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)';
        });
    });
    
    // Animate features list items on scroll
    const features = document.querySelectorAll('.features li');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateX(0)';
                }, index * 100);
            }
        });
    }, { threshold: 0.5 });
    
    features.forEach(feature => {
        feature.style.opacity = '0';
        feature.style.transform = 'translateX(-20px)';
        feature.style.transition = 'all 0.5s';
        observer.observe(feature);
    });
    
    // Parallax effect for hero section
    window.addEventListener('scroll', function() {
        const hero = document.querySelector('.hero');
        if (hero) {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            hero.style.backgroundPosition = `center ${rate}px`;
        }
    });
    
    // Booking form enhancements
    const bookingForms = document.querySelectorAll('form[action="book.php"]');
    bookingForms.forEach(form => {
        const seatsInput = form.querySelector('input[name="seats"]');
        const bookButton = form.querySelector('button');
        
        if (seatsInput && bookButton) {
            seatsInput.addEventListener('input', function() {
                const max = parseInt(this.getAttribute('max'));
                const value = parseInt(this.value) || 0;
                
                if (value > max) {
                    this.value = max;
                    shakeElement(this);
                    showError(this, 'Maximum seats available: ' + max);
                } else if (value < 1) {
                    this.value = 1;
                }
                
                // Animate button on valid input
                if (value >= 1 && value <= max) {
                    bookButton.classList.add('pulse');
                    setTimeout(() => {
                        bookButton.classList.remove('pulse');
                    }, 500);
                }
            });
            
            form.addEventListener('submit', function(e) {
                const seats = parseInt(seatsInput.value);
                const max = parseInt(seatsInput.getAttribute('max'));
                
                if (seats < 1 || seats > max) {
                    e.preventDefault();
                    shakeElement(seatsInput);
                    showError(seatsInput, `Please enter seats between 1 and ${max}`);
                } else {
                    // Add loading animation
                    bookButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';
                    bookButton.disabled = true;
                }
            });
        }
    });
});

// Helper Functions
function createParticles() {
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'particles';
    document.body.appendChild(particlesContainer);
    
    for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        
        const size = Math.random() * 20 + 5;
        const x = Math.random() * 100;
        const y = Math.random() * 100;
        const duration = Math.random() * 10 + 10;
        const delay = Math.random() * 5;
        
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${x}vw`;
        particle.style.top = `${y}vh`;
        particle.style.animationDuration = `${duration}s`;
        particle.style.animationDelay = `${delay}s`;
        particle.style.opacity = Math.random() * 0.3 + 0.1;
        
        particlesContainer.appendChild(particle);
    }
}

function animateStats() {
    const stats = document.querySelectorAll('.stat-card h3');
    stats.forEach(stat => {
        const target = parseInt(stat.textContent.replace(/,/g, ''));
        if (!isNaN(target)) {
            animateValue(stat, 0, target, 2000);
        }
    });
}

function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const value = Math.floor(progress * (end - start) + start);
        element.textContent = value.toLocaleString();
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

function createBackToTopButton() {
    const button = document.createElement('button');
    button.className = 'back-to-top';
    button.innerHTML = '<i class="fas fa-chevron-up"></i>';
    button.title = 'Back to Top';
    document.body.appendChild(button);
    
    button.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    return button;
}

function createNotification() {
    const notification = document.createElement('div');
    notification.className = 'floating-notification';
    notification.innerHTML = `
        <i class="fas fa-bell" style="color: #667eea;"></i>
        <div>
            <strong>Special Offer!</strong>
            <p>Get 20% off on your first booking</p>
        </div>
        <button class="notification-close"><i class="fas fa-times"></i></button>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 2000);
    
    notification.querySelector('.notification-close').addEventListener('click', function() {
        notification.style.transform = 'translateX(-100%)';
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 500);
    });
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.style.transform = 'translateX(-100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 500);
        }
    }, 10000);
}

function shakeElement(element) {
    element.classList.add('shake');
    setTimeout(() => {
        element.classList.remove('shake');
    }, 500);
}

function showError(element, message) {
    // Remove existing error
    const existingError = element.parentElement.querySelector('.error-message');
    if (existingError) existingError.remove();
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        color: #f56565;
        font-size: 12px;
        margin-top: 5px;
        animation: fadeIn 0.3s;
    `;
    
    element.parentElement.appendChild(errorDiv);
    
    // Auto-remove error after 3 seconds
    setTimeout(() => {
        errorDiv.style.animation = 'fadeOut 0.3s';
        setTimeout(() => {
            errorDiv.remove();
        }, 300);
    }, 3000);
}

// Add shake animation to CSS
const style = document.createElement('style');
style.textContent = `
    .shake {
        animation: shake 0.5s;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }
    
    .pulse {
        animation: buttonPulse 0.5s;
    }
    
    @keyframes buttonPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);

// Add scroll reveal animations
const scrollReveal = () => {
    const reveals = document.querySelectorAll('.card, .stat-card, .feature');
    
    for (let i = 0; i < reveals.length; i++) {
        const windowHeight = window.innerHeight;
        const elementTop = reveals[i].getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < windowHeight - elementVisible) {
            reveals[i].classList.add('active');
        }
    }
};

window.addEventListener('scroll', scrollReveal);