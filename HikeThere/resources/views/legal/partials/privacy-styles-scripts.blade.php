<style>
    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }
    
    /* Add scroll padding for fixed header */
    :target {
        scroll-margin-top: 100px;
    }

    /* Custom scrollbar for sidebar */
    aside nav::-webkit-scrollbar {
        width: 6px;
    }

    aside nav::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    aside nav::-webkit-scrollbar-thumb {
        background: #3b82f6;
        border-radius: 3px;
    }

    aside nav::-webkit-scrollbar-thumb:hover {
        background: #2563eb;
    }

    /* Floating sidebar animation */
    aside > div {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    aside > div:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add IDs to h2 elements for navigation
        const headings = document.querySelectorAll('#content h2');
        const ids = ['introduction', 'information-collect', 'how-use', 'data-sharing', 'payment-security', 'data-retention', 'data-security', 'privacy-rights', 'notification-preferences', 'cookies', 'children-privacy', 'international-transfers', 'third-party', 'data-breach', 'privacy-design', 'updates', 'contact', 'dpo', 'compliance', 'summary', 'consent'];
        
        headings.forEach((heading, index) => {
            if (ids[index]) {
                heading.id = ids[index];
            }
        });
    });
</script>
