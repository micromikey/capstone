import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    // Feature Cards Animation
    initializeFeatureCards();

    // Mountain Parallax Effect
    initializeMountainParallax();

    // Search Input Enhancement
    initializeSearchInput();

    // Button Animations
    initializeButtons();

    // Mobile Menu Toggle
    initializeMobileMenu();

    // Mountain Logo Animation
    initializeMountainLogo();

    // Add Custom Animations
    addCustomAnimations();
});

function initializeFeatureCards() {
    const cards = document.querySelectorAll('.feature-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = `${index * 0.15}s`;
                entry.target.style.animation = 'fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards';
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => {
        observer.observe(card);

        card.addEventListener('mouseenter', function () {
            this.style.animation = 'none';
            this.style.transform = 'translateY(-12px) scale(1.02) rotateX(5deg)';
            this.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.25)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1) rotateX(0deg)';
            this.style.boxShadow = '';
        });
    });
}

function initializeMountainParallax() {
    let ticking = false;

    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(function () {
                const scrolled = window.pageYOffset;
                const parallaxElements = document.querySelectorAll('.parallax-mountain');

                parallaxElements.forEach((el, index) => {
                    const speed = (index + 1) * 0.5;
                    el.style.transform = `translateY(${scrolled * speed}px)`;
                });

                ticking = false;
            });

            ticking = true;
        }
    });
}

function initializeSearchInput() {
    const searchInput = document.querySelector('.mountain-search');
    if (searchInput) {
        searchInput.addEventListener('focus', function () {
            this.style.transform = 'scale(1.02)';
            this.style.boxShadow = '0 12px 40px rgba(37, 99, 235, 0.15)';
        });

        searchInput.addEventListener('blur', function () {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '';
        });
    }
}

function initializeButtons() {
    const buttons = document.querySelectorAll('.btn-mountain');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-3px) scale(1.05)';
            this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1)';
        });

        button.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '';
        });
    });
}

function initializeMobileMenu() {
    const menuButton = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
            this.setAttribute('aria-expanded',
                this.getAttribute('aria-expanded') === 'false' ? 'true' : 'false'
            );
        });
    }
}

function initializeMountainLogo() {
    const logo = document.querySelector('.mountain-logo');
    if (logo) {
        logo.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.05)';
        });

        logo.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
        });
    }
}

function addCustomAnimations() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes mountainFloat {
            0%, 100% { transform: translateY(0) rotateZ(0deg); }
            25% { transform: translateY(-10px) rotateZ(0.5deg); }
            50% { transform: translateY(-5px) rotateZ(0deg); }
            75% { transform: translateY(-15px) rotateZ(-0.5deg); }
        }
        
        @keyframes peakGlow {
            0%, 100% { filter: drop-shadow(0 0 0 rgba(249, 115, 22, 0)); }
            50% { filter: drop-shadow(0 0 20px rgba(249, 115, 22, 0.3)); }
        }
        
        .mountain-scene svg {
            animation: peakGlow 4s ease-in-out infinite;
        }
        
        .feature-card {
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hero-mountain-bg svg {
            animation: mountainFloat 20s ease-in-out infinite;
        }

        .cloud {
            animation: drift 20s linear infinite;
        }
    `;
    document.head.appendChild(style);
}