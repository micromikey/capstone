import './bootstrap';
import initializeTrailPreview from './trailPreview';

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

    // Trail Explorer Component
    const trailExplorerComponent = trailExplorer();
    trailExplorerComponent.init();

    // Expose trail preview initializer globally so Blade templates can call it
    try {
        window.initializeTrailPreview = initializeTrailPreview;
    } catch (e) {
        console.warn('Unable to attach initializeTrailPreview to window', e);
    }
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

// Trail Explorer Component
function trailExplorer() {
    return {
        searchQuery: '',
        selectedLocation: '',
        filters: {
            difficulty: '',
            season: ''
        },
        sortBy: 'name',
        trails: [],
        filteredTrails: [],
        locations: [],
        loading: false,


        init() {
            console.log('Initializing trailExplorer');
            this.loadLocations();
            this.fetchTrails();
        },

        async loadLocations() {
            try {
                const response = await fetch('/api/locations');
                const allLocations = await response.json();
                // Filter out any locations with blank or null names
                this.locations = allLocations.filter(location => 
                    location.name && location.name.trim() !== '' && location.slug
                );
                console.log('Loaded locations:', this.locations); // Debug log
            } catch (error) {
                console.error('Error loading locations:', error);
                this.locations = [];
            }
        },

        getValidLocations() {
            return this.locations.filter(location => 
                location.name && location.name.trim() !== '' && location.slug
            );
        },

        async fetchTrails() {
            this.loading = true;
            try {
                let url = '/api/trails';
                const params = new URLSearchParams();
                
                if (this.selectedLocation) {
                    params.append('location', this.selectedLocation);
                }
                if (this.filters.difficulty) {
                    params.append('difficulty', this.filters.difficulty);
                }
                if (this.filters.season) {
                    params.append('season', this.filters.season);
                }
                
                if (params.toString()) {
                    url += '?' + params.toString();
                }
                
                const response = await fetch(url);
                this.trails = await response.json();
                this.filteredTrails = [...this.trails];
                this.sortTrails();
            } catch (error) {
                console.error('Error fetching trails:', error);
            } finally {
                this.loading = false;
            }
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 300);
        },

        performSearch() {
            if (!this.searchQuery.trim()) {
                this.filteredTrails = [...this.trails];
            } else {
                const query = this.searchQuery.toLowerCase();
                this.filteredTrails = this.trails.filter(trail => 
                    trail.name.toLowerCase().includes(query) ||
                    trail.mountain_name.toLowerCase().includes(query) ||
                    trail.location_name?.toLowerCase().includes(query) ||
                    trail.organization?.toLowerCase().includes(query)
                );
            }
            this.sortTrails();
        },





        sortTrails() {
            if (!this.sortBy) return;
            
            this.filteredTrails.sort((a, b) => {
                switch (this.sortBy) {
                    case 'name':
                        return a.name.localeCompare(b.name);
                    case 'difficulty':
                        const difficultyOrder = { 'beginner': 1, 'intermediate': 2, 'advanced': 3 };
                        return difficultyOrder[a.difficulty] - difficultyOrder[b.difficulty];
                    case 'length':
                        return parseFloat(a.length) - parseFloat(b.length);
                    case 'rating':
                        return (b.average_rating || 0) - (a.average_rating || 0);
                    case 'price':
                        return parseFloat(a.price) - parseFloat(b.price);
                    default:
                        return 0;
                }
            });
        },

        resetFilters() {
            this.searchQuery = '';
            this.selectedLocation = '';
            this.filters.difficulty = '';
            this.filters.season = '';
            this.sortBy = 'name';
            this.fetchTrails();
        },





        viewFullTrail(trail) {
            window.location.href = `/trails/${trail.slug}`;
        },

        downloadTrail(trail) {
            // Implementation for downloading trail information
            console.log('Downloading trail:', trail.name);
            // You can implement actual download functionality here
            alert(`Downloading trail information for ${trail.name}`);
        },

        getTrailImage(trail) {
            // Fallback image if no primary image is available
            const fallbackImages = [
                'https://images.unsplash.com/photo-1551632811-561732d1e306?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1464822759844-d150baec0134?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1551632811-561732d1e306?w=800&h=600&fit=crop'
            ];
            
            // Use trail ID to consistently get the same image for the same trail
            const index = trail.id % fallbackImages.length;
            return fallbackImages[index];
        },

        getDifficultyBadgeClass(difficulty) {
            const classes = {
                'beginner': 'bg-green-500 text-white',
                'intermediate': 'bg-yellow-500 text-white',
                'advanced': 'bg-red-500 text-white'
            };
            return classes[difficulty] || 'bg-gray-500 text-white';
        },

        getDifficultyLabel(difficulty) {
            const labels = {
                'beginner': 'Beginner',
                'intermediate': 'Intermediate',
                'advanced': 'Advanced'
            };
            return labels[difficulty] || difficulty;
        },

        formatPrice(price) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(price);
        }
    };
}

// Make it globally available
window.trailExplorer = trailExplorer;