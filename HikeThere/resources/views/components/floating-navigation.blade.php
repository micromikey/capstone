@props(['sections' => [], 'autoDetect' => false])

<!-- Floating Section Navigation -->
<div id="floating-navigation" class="fixed top-48 left-10 z-40 transition-all duration-300 transform">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-gray-200/50 p-4 min-w-[200px] max-w-[250px]">
        <!-- Toggle Button with Guide Message (visible when collapsed) -->
        <button id="nav-toggle-btn" class="absolute -right-3 top-1/2 -translate-y-1/2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white rounded-full p-3 shadow-2xl transition-all duration-300 opacity-0 pointer-events-none hover:scale-110 group">
            <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
            </svg>
            <!-- Tooltip on hover -->
            <div class="absolute left-full ml-3 top-1/2 -translate-y-1/2 bg-gray-900 text-white text-xs font-medium px-3 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none shadow-xl">
                <div class="font-bold text-green-400">HikeThere! 🥾</div>
                <div class="text-gray-300">I'm your Page Guide!</div>
                <!-- Arrow pointing left -->
                <div class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-gray-900"></div>
            </div>
            <!-- Pulsing ring animation -->
            <div class="absolute inset-0 rounded-full bg-green-500 animate-ping opacity-75"></div>
        </button>

        <!-- Header -->
        <div class="flex items-center mb-3 nav-content">
            <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                Page Sections
            </h3>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-1 nav-content" id="floating-nav-links">
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

    /* Collapsed state styles */
    #floating-navigation.collapsed {
        opacity: 0.5;
        transform: translateX(-10px);
    }

    #floating-navigation.collapsed:hover {
        opacity: 0.8;
    }

    #floating-navigation.collapsed .bg-white\/95 {
        padding: 0.5rem !important;
        min-width: 50px !important;
        max-width: 50px !important;
    }

    #floating-navigation.collapsed .nav-content {
        opacity: 0;
        max-height: 0;
        overflow: hidden;
        transition: opacity 0.2s, max-height 0.2s;
    }

    #floating-navigation.collapsed #nav-toggle-btn {
        opacity: 1;
        pointer-events: auto;
        animation: pulseButton 9s infinite;
    }

    #floating-navigation.collapsed #nav-toggle-btn svg {
        transform: rotate(0deg);
    }

    #floating-navigation:not(.collapsed) #nav-toggle-btn svg {
        transform: rotate(180deg);
    }

    /* Pulsing animation for the toggle button */
    @keyframes pulseButton {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        }
        5% {
            box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
        }
        10%, 100% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        }
    }

    @keyframes pingRing {
        0%, 100% {
            opacity: 0;
        }
        2% {
            opacity: 0.75;
        }
        5% {
            opacity: 0;
        }
        10%, 100% {
            opacity: 0;
        }
    }

    #floating-navigation.collapsed #nav-toggle-btn .animate-ping {
        animation: pingRing 9s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    .nav-content {
        transition: opacity 0.3s, max-height 0.3s;
        max-height: 1000px;
    }

    /* Hide on mobile and tablet devices for better responsiveness */
    @media (max-width: 1280px) {
        #floating-navigation {
            display: none !important;
        }
    }

    /* For medium screens, reduce size and reposition */
    @media (min-width: 1281px) and (max-width: 1440px) {
        #floating-navigation {
            left: 5px !important;
            top: 200px !important;
        }
        
        #floating-navigation .bg-white\/95 {
            padding: 0.75rem !important;
            min-width: 160px !important;
            max-width: 200px !important;
        }
        
        #floating-navigation .floating-nav-link {
            padding: 0.375rem 0.5rem !important;
            font-size: 0.8125rem !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const floatingNav = document.getElementById('floating-navigation');
    const navLinks = document.querySelectorAll('.floating-nav-link');
    const navLinksContainer = document.getElementById('floating-nav-links');
    const toggleBtn = document.getElementById('nav-toggle-btn');
    
    let scrollTimeout;
    let expandTimeout;
    let lastScrollY = window.scrollY;
    let isManuallyExpanded = false;

    // Auto-detect sections if enabled and no sections provided
    if (navLinksContainer.textContent.includes('Sections will auto-populate')) {
        autoDetectSections();
    }

    // Toggle button click handler
    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (floatingNav.classList.contains('collapsed')) {
            expandNav();
            isManuallyExpanded = true;
            // Reset auto-collapse after manual expansion
            resetAutoCollapse();
        } else {
            collapseNav();
            isManuallyExpanded = false;
        }
    });

    // Collapse navigation
    function collapseNav() {
        floatingNav.classList.add('collapsed');
        clearTimeout(expandTimeout);
    }

    // Expand navigation
    function expandNav() {
        floatingNav.classList.remove('collapsed');
    }

    // Reset auto-collapse timer
    function resetAutoCollapse() {
        clearTimeout(expandTimeout);
        expandTimeout = setTimeout(() => {
            if (!isManuallyExpanded) {
                expandNav();
            }
            isManuallyExpanded = false;
        }, 10000); // 10 seconds
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

    // Scroll event handler
    window.addEventListener('scroll', function() {
        const currentScrollY = window.scrollY;
        
        // Collapse on any scroll (up or down) after initial scroll threshold
        if (currentScrollY > 100 && Math.abs(currentScrollY - lastScrollY) > 5) {
            collapseNav();
            resetAutoCollapse();
        }
        
        lastScrollY = currentScrollY;
        
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