@props(['sections' => [], 'autoDetect' => false])

<!-- Floating Section Navigation -->
<div id="floating-navigation" class="fixed top-48 left-10 z-40 transition-all duration-300 transform">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-gray-200/50 p-4 min-w-[200px] max-w-[250px]">
        <!-- Header -->
        <div class="flex items-center mb-3">
            <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                Page Sections
            </h3>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-1" id="floating-nav-links">
            @if(count($sections) > 0)
                @foreach($sections as $section)
                    <a href="#{{ $section['id'] }}" 
                       class="floating-nav-link block px-3 py-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 border-l-2 border-transparent hover:border-green-500"
                       data-section="{{ $section['id'] }}">
                        <div class="flex items-center">
                            @if(isset($section['icon']))
                                <svg class="w-3 h-3 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $section['icon'] !!}
                                </svg>
                            @endif
                            <span class="truncate">{{ $section['title'] }}</span>
                        </div>
                    </a>
                @endforeach
            @else
                <!-- Auto-detect sections if enabled -->
                @if($autoDetect)
                <div class="text-xs text-gray-500 italic">
                    Sections will auto-populate
                </div>
                @else
                <div class="text-xs text-gray-500 italic">
                    No sections available
                </div>
                @endif
            @endif
        </nav>
    </div>
</div>

<style>
    /* Custom styles for floating navigation */
    .floating-nav-link.active {
        color: rgb(34 197 94);
        background-color: rgb(240 253 244);
        border-color: rgb(34 197 94);
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(34, 197, 94, 0.1);
    }

    .floating-nav-link.active svg {
        color: rgb(34 197 94);
    }

    .floating-nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 60%;
        background: linear-gradient(to bottom, rgb(34 197 94), rgb(16 185 129));
        border-radius: 0 2px 2px 0;
    }

    .floating-nav-link {
        position: relative;
    }

    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }

    /* Hide on very small screens */
    @media (max-width: 768px) {
        #floating-navigation {
            transform: scale(0.9);
            top: 40px; /* Adjust for smaller screens */
        }
    }

    @media (max-width: 640px) {
        #floating-navigation {
            min-width: 180px;
            max-width: 200px;
            left: 8px; /* Closer to edge on mobile */
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const floatingNav = document.getElementById('floating-navigation');
    const navLinks = document.querySelectorAll('.floating-nav-link');
    const navLinksContainer = document.getElementById('floating-nav-links');

    // Auto-detect sections if enabled and no sections provided
    if (navLinksContainer.textContent.includes('Sections will auto-populate')) {
        autoDetectSections();
    }

    // Smooth scrolling for navigation links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const headerOffset = 120; // Account for fixed header and navigation
                const elementPosition = targetElement.offsetTop;
                const offsetPosition = elementPosition - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                // Update active state immediately
                updateActiveLink(targetId);
            }
        });
    });

    // Update active link based on scroll position
    function updateActiveLink(activeId = null) {
        const links = document.querySelectorAll('.floating-nav-link');
        
        if (activeId) {
            // Manually set active link
            links.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-section') === activeId) {
                    link.classList.add('active');
                }
            });
        } else {
            // Auto-detect based on scroll position
            const sections = Array.from(links).map(link => {
                const sectionId = link.getAttribute('data-section');
                const element = document.getElementById(sectionId);
                return { link, element, sectionId };
            }).filter(item => item.element);

            const scrollPosition = window.scrollY + 200; // Offset for better detection

            let activeSection = null;
            for (const section of sections) {
                if (section.element.offsetTop <= scrollPosition) {
                    activeSection = section;
                }
            }

            links.forEach(link => link.classList.remove('active'));
            if (activeSection) {
                activeSection.link.classList.add('active');
            } else if (sections.length > 0) {
                // If no section is active, activate the first one
                sections[0].link.classList.add('active');
            }
        }
    }

    // Auto-detect sections function
    function autoDetectSections() {
        const headings = document.querySelectorAll('h1, h2, h3, [data-section], .trail-card, .organization-card');
        const sections = [];
        
        headings.forEach((heading, index) => {
            let id = heading.id;
            let title = heading.textContent || heading.getAttribute('data-section-title');
            
            // Special handling for trail and organization cards
            if (heading.classList.contains('trail-card')) {
                const trailName = heading.querySelector('h3, .trail-name, [data-trail-name]');
                if (trailName) {
                    title = trailName.textContent.trim();
                    if (!id) {
                        id = 'trail-' + title.toLowerCase().replace(/[^a-z0-9]/g, '-');
                        heading.id = id;
                    }
                }
            } else if (heading.classList.contains('organization-card')) {
                const orgName = heading.querySelector('h3, .org-name, [data-org-name]');
                if (orgName) {
                    title = orgName.textContent.trim();
                    if (!id) {
                        id = 'org-' + title.toLowerCase().replace(/[^a-z0-9]/g, '-');
                        heading.id = id;
                    }
                }
            }
            
            // Generate ID if none exists
            if (!id) {
                id = 'section-' + (index + 1);
                heading.id = id;
            }
            
            // Clean up title
            if (title) {
                title = title.trim().substring(0, 30); // Limit length
                if (title.length > 25) title += '...';
                
                sections.push({ id, title });
            }
        });

        // Update navigation with detected sections
        if (sections.length > 0) {
            let html = '';
            sections.forEach(section => {
                html += `
                    <a href="#${section.id}" 
                       class="floating-nav-link block px-3 py-2 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all duration-200 border-l-2 border-transparent hover:border-green-500"
                       data-section="${section.id}">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="truncate">${section.title}</span>
                        </div>
                    </a>
                `;
            });
            navLinksContainer.innerHTML = html;
            
            // Re-bind event listeners for new links
            document.querySelectorAll('.floating-nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        const headerOffset = 100;
                        const elementPosition = targetElement.offsetTop;
                        const offsetPosition = elementPosition - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });

                        updateActiveLink(targetId);
                    }
                });
            });
        }
    }

    // Scroll event listeners
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        // Debounce scroll events for better performance
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            updateActiveLink();
        }, 50);
    }, { passive: true });

    // Initial setup
    updateActiveLink();
});
</script>