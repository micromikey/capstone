<x-guest-layout>
    <style>
        /* Navigation Styles */
        .nav-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            transition: all 0.3s ease;
        }

        .nav-container.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .nav-blur {
            backdrop-filter: blur(20px);
        }

        .nav-link {
            position: relative;
            transition: color 0.3s ease;
            text-decoration: none !important;
            border-bottom: none !important;
            outline: none !important;
        }

        .nav-link:hover {
            text-decoration: none !important;
            border-bottom: none !important;
            outline: none !important;
        }

        /* Override any ::after pseudo-elements */
        .nav-link::after,
        .nav-link::before {
            display: none !important;
            content: none !important;
            background: none !important;
            border: none !important;
        }

        .nav-link:hover::after,
        .nav-link:hover::before {
            display: none !important;
            content: none !important;
            background: none !important;
            border: none !important;
        }

        /* Override any other underline styles */
        .nav-link {
            text-decoration: none !important;
            border-bottom: none !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .nav-link:hover {
            text-decoration: none !important;
            border-bottom: none !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .nav-underline {
            position: absolute !important;
            bottom: -8px !important;
            left: 0 !important;
            width: 0 !important;
            height: 2px !important;
            background: linear-gradient(90deg, #20b6d2, #336d66) !important;
            transition: width 0.3s ease !important;
            z-index: 999 !important;
            pointer-events: none !important;
        }

        .nav-link:hover .nav-underline {
            width: 100% !important;
        }

        /* Button Styles */
        .btn-mountain {
            background: linear-gradient(135deg, #336d66, #20b6d2);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(51, 109, 102, 0.3);
        }

        .btn-mountain:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(51, 109, 102, 0.4);
        }

        .btn-mountain-outline {
            border: 2px solid #336d66;
            color: #336d66;
            padding: 10px 22px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-mountain-outline:hover {
            background: #336d66;
            color: white;
            transform: translateY(-2px);
        }

        .btn-mountain-large {
            background: linear-gradient(135deg, #336d66, #20b6d2);
            color: white;
            padding: 16px 32px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(51, 109, 102, 0.4);
            display: inline-flex;
            align-items: center;
        }

        .btn-mountain-large:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(51, 109, 102, 0.5);
        }

        .btn-video {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 16px 32px;
            border-radius: 16px;
            font-weight: 600;
            font-size: 18px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            display: inline-flex;
            align-items: center;
        }

        .btn-video:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        /* Hero Section */
        .hero-container {
            background: linear-gradient(135deg, #336d66 0%, #20b6d2 50%, #aec896 100%);
            position: relative;
        }

        .mountain-gradient {
            background: linear-gradient(135deg, #336d66 0%, #20b6d2 50%, #aec896 100%);
        }

        .mountain-logo {
            transition: all 0.3s ease;
        }

        .mountain-logo:hover {
            transform: scale(1.05);
        }

        /* Animation Classes */
        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stats Section */
        .stats-item {
            transition: all 0.3s ease;
        }

        .stats-item:hover {
            transform: translateY(-5px);
        }

        /* Plan Trip Section */
        .plan-trip-container {
            background: linear-gradient(135deg, rgba(174, 200, 150, 0.05) 0%, white 100%);
        }

        .planner-showcase {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(174, 200, 150, 0.2);
        }

        .mountain-search {
            background: white;
            transition: all 0.3s ease;
        }

        .mountain-search:focus {
            box-shadow: 0 0 0 4px rgba(32, 182, 210, 0.2);
        }

        .quick-search-card {
            background: white;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid rgba(174, 200, 150, 0.2);
            transition: all 0.3s ease;
            text-align: center;
        }

        .quick-search-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            border-color: rgba(51, 109, 102, 0.3);
        }

        /* Trail Cards */
        .trail-card {
            transition: all 0.3s ease;
        }

        .trail-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
        }

        .trail-card img {
            transition: transform 0.3s ease;
        }

        .trail-card:hover img {
            transform: scale(1.05);
        }

        /* Nearby Trails Section */
        .nearby-trails-container {
            background: linear-gradient(135deg, rgba(32, 182, 210, 0.05) 0%, white 100%);
        }

        .distance-filter-btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            background: transparent;
            color: #6b7280;
            cursor: pointer;
        }

        .distance-filter-btn:hover {
            color: #336d66;
            background: rgba(51, 109, 102, 0.1);
        }

        .distance-filter-btn.active {
            background: linear-gradient(135deg, #336d66, #20b6d2);
            color: white;
            box-shadow: 0 4px 15px rgba(51, 109, 102, 0.3);
        }

        /* Features Section */
        .features-container {
            background: white;
        }

        .feature-showcase {
            background: white;
            padding: 32px;
            border-radius: 20px;
            border: 1px solid rgba(174, 200, 150, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-showcase::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #336d66, #20b6d2);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-showcase:hover::before {
            transform: scaleX(1);
        }

        .feature-showcase:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12);
            border-color: rgba(51, 109, 102, 0.3);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            transition: all 0.3s ease;
        }

        .feature-showcase:hover .feature-icon {
            transform: scale(1.1);
        }

        /* Footer */
        .footer-container {
            background: white;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(51, 109, 102, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #336d66;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: #336d66;
            color: white;
            transform: translateY(-2px);
        }

        /* Trail Showcase Animation Styles */
        .trails-showcase-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            pointer-events: none;
        }

        /* Parallax hero (scoped) */
        .parallax-hero {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
            /* sit above section background but behind hero content */
        }

        .parallax-hero .parallax-layer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200%;
            /* wider to allow horizontal parallax movement */
            height: 100%;
            background-repeat: repeat-x;
            background-position: 0 100%;
            will-change: background-position;
            opacity: 1;
            z-index: 1;
            /* base layer index */
        }

        .parallax-hero .layer-1 {
            background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/24650/1.png) 0 100% repeat-x;
            background-size: auto 136px;
            animation: parallax_fg_1 linear 20s infinite both;
        }

        .parallax-hero .layer-2 {
            background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/24650/2.png) 0 100% repeat-x;
            background-size: auto 145px;
            animation: parallax_fg_2 linear 30s infinite both;
        }

        .parallax-hero .layer-3 {
            background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/24650/3.png) 0 100% repeat-x;
            background-size: auto 158px;
            animation: parallax_fg_3 linear 55s infinite both;
        }

        .parallax-hero .layer-4 {
            background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/24650/4.png) 0 100% repeat-x;
            background-size: auto 468px;
            animation: parallax_fg_4 linear 75s infinite both;
        }

        .parallax-hero .layer-5 {
            background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/24650/5.png) 0 100% repeat-x;
            background-size: auto 311px;
            animation: parallax_fg_5 linear 95s infinite both;
        }

        .parallax-hero .layer-6 {
            background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/24650/6.png) 0 100% repeat-x;
            background-size: auto 222px;
            animation: parallax_fg_6 linear 120s infinite both;
        }

        /* simple bike/walker decorative layers (reduced complexity) */
        .parallax-hero .bike-1 {
            background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/24650/bike.png) 0 100% no-repeat;
            background-size: auto 75px;
            bottom: 120px;
            animation: parallax_bike 18s linear infinite;
            opacity: .6;
            z-index: 3;
        }

        .parallax-hero .bike-2 {
            background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/24650/bike.png) 0 100% no-repeat;
            background-size: auto 75px;
            bottom: 110px;
            animation: parallax_bike 25s linear infinite;
            opacity: .5;
            z-index: 3;
        }

        /* walker positioning and animation (updated) */
        /* ensure walker elements override .parallax-layer defaults */
        .parallax-hero .walker-1,
        .parallax-hero .walker-2 {
            position: absolute !important;
            bottom: 100px !important;
            width: 100px !important;
            height: 100px !important;
            pointer-events: none;
            z-index: 5;
            /* above base layers and bikes, below hero content */
        }

        /* walker-1 starts on the right and walks left across the visible area */
        .parallax-hero .walker-1 {
            right: 12% !important;
            left: auto !important;
            transform: translateX(0);
            animation: walk-left 9s linear infinite, bob 0.9s ease-in-out infinite;
        }

        /* walker-2 starts on the left and walks right */
        .parallax-hero .walker-2 {
            left: 10% !important;
            right: auto !important;
            transform: translateX(0);
            animation: walk-left 9s linear infinite, bob 0.9s ease-in-out infinite;
        }

        /* hiker image in ::before, shadow in ::after (so only image is semi-transparent) */
        .parallax-hero .walker-1::before,
        .parallax-hero .walker-2::before {
            content: '';
            position: absolute;
            inset: 0;
            display: block;
            background-image: url("/img/hiker.svg");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-position: left bottom;
            opacity: 0.6;
            pointer-events: none;
            z-index: 2;
        }

        .parallax-hero .walker-1::after,
        .parallax-hero .walker-2::after {
            content: '';
            position: absolute;
            left: 20px;
            right: 20px;
            bottom: 12px;
            height: 6px;
            background: rgba(0, 0, 0, 0.15);
            filter: blur(4px);
            transform: translateZ(0);
            border-radius: 50%;
            z-index: 1;
            pointer-events: none;
        }

       

        /* walking motion: move across +/- X distance then loop */
        @keyframes walk-left {
            0% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(-60vw);
            }

            100% {
                transform: translateX(0);
            }
        }

        @keyframes walk-right {
            0% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(60vw);
            }

            100% {
                transform: translateX(0);
            }
        }

        /* subtle vertical bob to simulate walking */
        @keyframes bob {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }

            100% {
                transform: translateY(0);
            }
        }

        /* keyframes (scoped names to avoid collisions) */
        @keyframes parallax_fg_1 {
            0% {
                background-position: 2765px 100%;
            }

            100% {
                background-position: 550px 100%;
            }
        }

        @keyframes parallax_fg_2 {
            0% {
                background-position: 2765px 100%;
            }

            100% {
                background-position: 550px 100%;
            }
        }

        @keyframes parallax_fg_3 {
            0% {
                background-position: 2765px 100%;
            }

            100% {
                background-position: 550px 100%;
            }
        }

        @keyframes parallax_fg_4 {
            0% {
                background-position: 2765px 100%;
            }

            100% {
                background-position: 550px 100%;
            }
        }

        @keyframes parallax_fg_5 {
            0% {
                background-position: 2765px 100%;
            }

            100% {
                background-position: 550px 100%;
            }
        }

        @keyframes parallax_fg_6 {
            0% {
                background-position: 2765px 100%;
            }

            100% {
                background-position: 550px 100%;
            }
        }

        @keyframes parallax_bike {
            0% {
                background-position: -300px 100%;
            }

            100% {
                background-position: 2000px 100%;
            }
        }

        /* Ensure hero content stays on top of the parallax layers */
        .hero-container .max-w-6xl,
        .hero-container .hero-content {
            position: relative;
            z-index: 10;
        }

        /* Per-pixel text blending - fix overlay positioning */
        .text-with-mask {
            position: relative;
            display: block;
            /* Changed from inline-block to block for proper stacking */
        }

        .text-mask-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: transparent;
            mix-blend-mode: multiply;
            pointer-events: none;
            z-index: 1;
            /* Ensure it's above the text but below other content */
        }

        /* Ensure text is behind the overlay for proper blending */
        .text-with-mask {
            isolation: isolate;
            /* Create new stacking context */
        }

        .hero-trail-card {
            position: absolute;
            width: 280px;
            height: 180px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            transition: all 0.3s ease;
            pointer-events: auto;
            cursor: pointer;
            /* Start cards off-screen to the right */
            transform: translateX(calc(100vw + 280px));
            opacity: 0;
        }

        .hero-trail-card:hover {
            transform: scale(1.05) !important;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
        }

        .hero-trail-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-trail-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
            padding: 20px 16px 16px 16px;
            color: white;
        }

        .hero-trail-title {
            font-size: 18px;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
            line-height: 1.3;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
        }

        .hero-trail-location {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        /* Trail Movement Animations */
        @keyframes heroTrailMove {
            0% {
                transform: translateX(calc(100vw + 280px));
                opacity: 0;
            }

            3% {
                opacity: 1;
            }

            97% {
                opacity: 1;
            }

            100% {
                transform: translateX(-280px);
                opacity: 0;
            }
        }

        /* Ensure cards start hidden and only animate when their turn comes */
        .hero-trail-card {
            animation-fill-mode: both;
        }

        /* Individual Trail Positions and Timings */
        .hero-trail-1 {
            top: 15%;
            animation: heroTrailMove 30s linear infinite;
            animation-delay: 0s;
        }

        .hero-trail-2 {
            top: 25%;
            animation: heroTrailMove 35s linear infinite;
            animation-delay: 4s;
        }

        .hero-trail-3 {
            top: 35%;
            animation: heroTrailMove 28s linear infinite;
            animation-delay: 8s;
        }

        .hero-trail-4 {
            top: 45%;
            animation: heroTrailMove 32s linear infinite;
            animation-delay: 12s;
        }

        .hero-trail-5 {
            top: 55%;
            animation: heroTrailMove 29s linear infinite;
            animation-delay: 16s;
        }

        .hero-trail-6 {
            top: 65%;
            animation: heroTrailMove 33s linear infinite;
            animation-delay: 20s;
        }

        .hero-trail-7 {
            top: 75%;
            animation: heroTrailMove 31s linear infinite;
            animation-delay: 24s;
        }

        .hero-trail-8 {
            top: 20%;
            animation: heroTrailMove 34s linear infinite;
            animation-delay: 28s;
        }

        .hero-trail-9 {
            top: 60%;
            animation: heroTrailMove 27s linear infinite;
            animation-delay: 32s;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-container {
                padding-top: 24px;
                padding-bottom: 16px;
            }

            h1 {
                font-size: 3rem;
            }

            .planner-showcase {
                padding: 24px;
            }

            .feature-showcase {
                padding: 24px;
            }

            /* Nearby trails mobile styles */
            .nearby-trails-container .text-center h2 {
                font-size: 2.5rem;
            }

            .distance-filter-btn {
                padding: 8px 16px;
                font-size: 0.875rem;
            }

            .nearby-trails-container .planner-showcase {
                padding: 16px;
            }

            #nearby-trails-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .trail-card {
                margin: 0 auto;
                max-width: 350px;
            }

            .hero-trail-card {
                width: 220px;
            }

            .hero-trail-image {
                height: 100px;
            }

            .hero-trail-content {
                padding: 12px;
            }

            .hero-trail-title {
                font-size: 14px;
            }

            .hero-trail-location {
                font-size: 11px;
                margin-bottom: 8px;
            }

            .hero-trail-stats {
                font-size: 10px;
            }

            .hero-trail-card {
                width: 220px;
                transform: translateX(calc(100vw + 220px));
            }

            @keyframes heroTrailMove {
                0% {
                    transform: translateX(calc(100vw + 220px));
                    opacity: 0;
                }

                3% {
                    opacity: 1;
                }

                97% {
                    opacity: 1;
                }

                100% {
                    transform: translateX(-220px);
                    opacity: 0;
                }
            }
        }

        @media (max-width: 480px) {
            .distance-filter-btn {
                padding: 6px 12px;
                font-size: 0.75rem;
            }

            .nearby-trails-container .max-w-6xl {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .trail-card .p-6 {
                padding: 1rem;
            }
        }

        /* Trail Details Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            padding: 20px;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            max-width: 5xl;
            width: 100%;
            max-height: 90vh;
            overflow: hidden;
            transform: scale(0.8);
            transition: transform 0.3s ease;
            box-shadow: 0 25px 100px rgba(0, 0, 0, 0.3);
        }

        .modal-overlay.active .modal-content {
            transform: scale(1);
        }

        .modal-body {
            max-height: calc(90vh - 80px);
            overflow-y: auto;
        }

        .weather-card {
            background: linear-gradient(135deg, #336d66 0%, #20b6d2 100%);
            color: white;
            border-radius: 16px;
            padding: 24px;
        }

        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
        }

        .photo-item {
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .photo-item:hover {
            transform: scale(1.05);
        }

        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .modal-content {
                margin: 10px;
                max-height: 95vh;
            }
        }

        /* Feature Tutorial Modal Styles */
        .feature-tutorial-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            padding: 20px;
        }

        .feature-tutorial-modal.active {
            opacity: 1;
            visibility: visible;
        }

        .feature-tutorial-content {
            background: white;
            border-radius: 24px;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow: hidden;
            transform: scale(0.8) translateY(50px);
            transition: all 0.3s ease;
            box-shadow: 0 25px 100px rgba(0, 0, 0, 0.3);
        }

        .feature-tutorial-modal.active .feature-tutorial-content {
            transform: scale(1) translateY(0);
        }

        .feature-tutorial-header {
            padding: 24px 32px;
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .feature-tutorial-body {
            padding: 32px;
            max-height: calc(90vh - 120px);
            overflow-y: auto;
        }

        .tutorial-step {
            background: white;
            border: 1px solid rgba(229, 231, 235, 0.5);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .tutorial-step:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .tutorial-step::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #336d66, #20b6d2);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .tutorial-step:hover::before {
            transform: scaleX(1);
        }

        .step-number {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #336d66, #20b6d2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .tutorial-animation {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px;
            padding: 20px;
            margin: 16px 0;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .animation-element {
            background: linear-gradient(135deg, #336d66, #20b6d2);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .tutorial-benefits {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .benefit-item {
            background: rgba(51, 109, 102, 0.05);
            border: 1px solid rgba(51, 109, 102, 0.1);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .benefit-item:hover {
            background: rgba(51, 109, 102, 0.1);
            transform: translateY(-2px);
        }

        .interactive-demo {
            background: white;
            border: 2px solid rgba(51, 109, 102, 0.2);
            border-radius: 16px;
            padding: 24px;
            margin: 20px 0;
            position: relative;
        }

        .demo-button {
            background: linear-gradient(135deg, #336d66, #20b6d2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .demo-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(51, 109, 102, 0.3);
        }

        .progress-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin: 20px 0;
        }

        .progress-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(51, 109, 102, 0.3);
            transition: all 0.3s ease;
        }

        .progress-dot.active {
            background: #336d66;
            transform: scale(1.5);
        }

        @media (max-width: 768px) {
            .feature-tutorial-content {
                margin: 10px;
                max-height: 95vh;
            }

            .feature-tutorial-header,
            .feature-tutorial-body {
                padding: 20px;
            }

            .tutorial-step {
                padding: 16px;
            }
        }
    </style>

    <!-- Navigation -->
    <nav class="nav-container nav-blur fixed w-full top-0 left-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center h-20">
            <a href="/" class="flex items-center space-x-3 mountain-logo group">
                <div class="relative">
                    <img src="{{ asset('img/icon1.png') }}" alt="Icon" class="h-9 w-auto">
                </div>
                <span class="font-bold text-xl text-[#336d66]">HikeThere</span>
            </a>

            <div class="hidden md:flex items-center space-x-8">
                <div class="flex space-x-8 font-medium text-gray-700">
                    <a href="#plan" class="relative nav-link hover:text-[#20b6d2] transition-all duration-300">
                        Plan Trip
                        <span class="nav-underline"></span>
                    </a>
                    <a href="#features" class="relative nav-link hover:text-[#20b6d2] transition-all duration-300">
                        Features
                        <span class="nav-underline"></span>
                    </a>
                    <a href="#community" class="relative nav-link hover:text-[#20b6d2] transition-all duration-300">
                        Community
                        <span class="nav-underline"></span>
                    </a>
                </div>
                <div class="flex items-center space-x-4 ml-8">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-[#20b6d2] font-medium transition-colors duration-300">
                        Login
                    </a>
                    <a href="{{ route('register.select') }}" class="btn-mountain-outline">
                        Get Started
                    </a>
                </div>
            </div>

            <button id="mobile-menu-btn" class="md:hidden p-3 text-gray-600 hover:text-[#20b6d2] transition-colors duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-lg border-t border-gray-200">
            <div class="px-4 py-6 space-y-4">
                <a href="#plan" class="block py-3 px-4 hover:bg-gray-50 rounded-lg transition-colors">Plan Trip</a>
                <a href="#features" class="block py-3 px-4 hover:bg-gray-50 rounded-lg transition-colors">Features</a>
                <a href="#community" class="block py-3 px-4 hover:bg-gray-50 rounded-lg transition-colors">Community</a>
                <hr class="border-gray-200 my-4">
                <a href="{{ route('login') }}" class="block py-3 px-4 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">Login</a>
                <a href="{{ route('register.select') }}" class="block w-full btn-mountain text-center">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-container bg-mountain-gradient pt-32 pb-24 relative overflow-hidden">
        <!-- Animated Trail Cards Background -->
        <div class="trails-showcase-container">
            <!-- Trail cards will be dynamically populated here -->
        </div>

        <!-- Parallax hero background (inserted from sample) -->
        <div class="parallax-hero" aria-hidden="true">
            <div class="parallax-layer layer-6"></div>
            <div class="parallax-layer layer-5"></div>
            <div class="parallax-layer layer-4"></div>
            <div class="parallax-layer bike-1"></div>
            <div class="parallax-layer bike-2"></div>
            <div class="parallax-layer walker-1"></div>
            <div class="parallax-layer walker-2"></div>
            <div class="parallax-layer layer-3"></div>
            <div class="parallax-layer layer-2"></div>
            <div class="parallax-layer layer-1"></div>
        </div>

        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-[#20b6d2]/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        </div>

        <div class="max-w-6xl mx-auto px-6 text-center relative z-10">
            <div class="hero-content space-y-8 animate-fade-in">
                <div class="inline-flex items-center space-x-2 bg-white/90 backdrop-blur-sm rounded-full px-6 py-3 text-sm font-medium text-[#336d66] shadow-lg">
                    <span class="iconify text-[#e3a746]" data-icon="mdi:mountain" style="font-size: 1.5rem;"></span>
                    <span>Your Adventure Starts Here</span>
                </div>

                <div class="relative">
                    <h1 class="relative text-5xl md:text-7xl font-extrabold leading-tight py-8 z-20">
                        <div id="text-line-1" class="block text-white mb-4 pb-2 drop-shadow-2xl transition-all duration-100 ease-out text-with-mask">
                            Explore Trails
                            <div class="text-mask-overlay"></div>
                        </div>
                        <div id="text-line-2" class="block text-white mb-4 pb-2 drop-shadow-2xl transition-all duration-100 ease-out text-with-mask">
                            Plan Safely
                            <div class="text-mask-overlay"></div>
                        </div>
                        <div id="text-line-3" class="block text-white pb-2 drop-shadow-2xl transition-all duration-100 ease-out text-with-mask">
                            Hike Confidently
                            <div class="text-mask-overlay"></div>
                        </div>
                    </h1>
                </div>

                <p class="text-xl md:text-2xl text-white/90 max-w-3xl mx-auto leading-relaxed backdrop-blur-sm bg-black/10 rounded-2xl px-6 py-4 shadow-lg">
                    Your all-in-one hiking companion for safe, enjoyable, and well-prepared adventures in nature's most beautiful places.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6 pt-6">
                    <a href="#plan" class="btn-mountain-large group">
                        <span>Start Planning</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    <button onclick="openBrowseTrailsModal()" class="btn-video group">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <span>Browse Trails</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>

    <!-- Value Proposition Section -->
    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center p-6 rounded-lg bg-gradient-to-br from-[#336d66]/5 to-white">
                    <div class="w-12 h-12 bg-[#336d66]/10 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <span class="iconify text-[#336d66]" data-icon="mdi:map-search" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Easy Discovery</h3>
                    <p class="text-gray-600 text-sm">Find trails perfect for your skill level</p>
                </div>

                <div class="text-center p-6 rounded-lg bg-gradient-to-br from-[#20b6d2]/5 to-white">
                    <div class="w-12 h-12 bg-[#20b6d2]/10 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <span class="iconify text-[#20b6d2]" data-icon="mdi:shield-check" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Stay Safe</h3>
                    <p class="text-gray-600 text-sm">Real-time conditions and emergency tools</p>
                </div>

                <div class="text-center p-6 rounded-lg bg-gradient-to-br from-[#e3a746]/5 to-white">
                    <div class="w-12 h-12 bg-[#e3a746]/10 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <span class="iconify text-[#e3a746]" data-icon="mdi:account-group" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Join Community</h3>
                    <p class="text-gray-600 text-sm">Connect with fellow hiking enthusiasts</p>
                </div>

                <div class="text-center p-6 rounded-lg bg-gradient-to-br from-[#dfa648]/5 to-white">
                    <div class="w-12 h-12 bg-[#dfa648]/10 rounded-xl flex items-center justify-center mb-4 mx-auto">
                        <span class="iconify text-[#dfa648]" data-icon="mdi:cellphone" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Go Offline</h3>
                    <p class="text-gray-600 text-sm">Download maps for adventures anywhere</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Trail Details Modal -->
    <div id="trail-details-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="p-6 border-b border-gray-200 flex justify-between items-start">
                <div class="flex-1 min-w-0">
                    <h2 id="modal-trail-name" class="text-2xl font-bold text-gray-800 truncate">Trail Details</h2>
                    <p id="modal-trail-location-header" class="text-sm text-gray-500 mt-1 break-words">Mountain • Location</p>
                </div>
                <button onclick="closeTrailModal()" class="p-2 hover:bg-gray-100 rounded-full transition-colors flex-shrink-0 ml-4">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="modal-body p-6 pb-12 relative">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-full">
                    <!-- Main Trail Info -->
                    <div class="lg:col-span-2 space-y-6 overflow-y-auto max-h-[70vh] pr-4 pb-6">
                        <!-- Hero Section with Image and Photo Gallery Side by Side -->
                        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                                <!-- Left Column: Main Image -->
                                <div class="relative">
                                    <img id="modal-trail-image" src="" alt="Trail Image" class="w-full h-80 object-cover rounded-xl">

                                    <!-- Previous Button -->
                                    <button id="prev-image-btn" onclick="previousModalImage()"
                                        class="absolute inset-y-0 left-0 flex items-center ml-4 p-3 bg-black/50 hover:bg-black/70 text-white rounded-full backdrop-blur-sm transition-all opacity-0 invisible">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Next Button -->
                                    <button id="next-image-btn" onclick="nextModalImage()"
                                        class="absolute inset-y-0 right-0 flex items-center mr-4 p-3 bg-black/50 hover:bg-black/70 text-white rounded-full backdrop-blur-sm transition-all opacity-0 invisible">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>

                                    <!-- Image Counter -->
                                    <div id="image-counter"
                                        class="absolute top-4 right-4 bg-black/60 text-white px-3 py-1 rounded-full text-sm backdrop-blur-sm opacity-0 invisible">
                                        <span id="current-image-num">1</span> / <span id="total-images">1</span>
                                    </div>

                                    <!-- Difficulty Badge -->
                                    <div class="absolute top-4 left-4">
                                        <span id="modal-difficulty-badge" class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            Easy
                                        </span>
                                    </div>

                                    <!-- Rating Badge -->
                                    <div class="absolute bottom-4 left-4 bg-white bg-opacity-90 rounded-full px-3 py-1">
                                        <div class="flex items-center space-x-1">
                                            <span class="iconify text-yellow-400" data-icon="solar:star-bold"></span>
                                            <span id="modal-trail-rating" class="text-sm font-semibold text-gray-800">4.5</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column: Trail Route Preview Only -->
                                <div class="h-80 flex flex-col">
                                    <h3 class="text-lg font-bold text-gray-800 flex items-center mb-3 flex-shrink-0">
                                        <span class="iconify text-[#336d66] mr-2" data-icon="heroicons:map" style="font-size:1.2rem;"></span>
                                        Trail Route Preview
                                    </h3>
                                    <div id="modal-trail-map" class="flex-1 bg-gray-100 rounded-lg overflow-hidden relative min-h-0">
                                        <!-- This will be replaced with actual map when trail data is loaded -->
                                        <div id="trail-map-placeholder" class="absolute inset-0 flex items-center justify-center">
                                            <div class="text-center">
                                                <span class="iconify text-gray-400 mb-2" data-icon="heroicons:map" style="font-size:2rem;"></span>
                                                <p class="text-sm text-gray-600">Loading trail path...</p>
                                            </div>
                                        </div>
                                        <!-- Actual map container -->
                                        <div id="trail-route-map" class="w-full h-full"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trail Basic Details -->
                        <div class="bg-gray-50 rounded-2xl p-6">
                            <h3 class="text-lg font-bold mb-4 text-gray-800">Trail Information</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="text-center">
                                    <div class="flex items-center justify-center mb-2">
                                        <span class="iconify text-[#336d66]" data-icon="heroicons:map" style="font-size:1.5rem;"></span>
                                    </div>
                                    <p class="text-sm text-gray-500">Distance</p>
                                    <p id="modal-trail-distance" class="font-semibold text-gray-800">5.2 km</p>
                                </div>
                                <div class="text-center">
                                    <div class="flex items-center justify-center mb-2">
                                        <span class="iconify text-[#336d66]" data-icon="heroicons:clock" style="font-size:1.5rem;"></span>
                                    </div>
                                    <p class="text-sm text-gray-500">Duration</p>
                                    <p id="modal-trail-duration" class="font-semibold text-gray-800">2-3 hours</p>
                                </div>
                                <div class="text-center">
                                    <div class="flex items-center justify-center mb-2">
                                        <span class="iconify text-[#336d66]" data-icon="heroicons:arrow-trending-up" style="font-size:1.5rem;"></span>
                                    </div>
                                    <p class="text-sm text-gray-500">Elevation</p>
                                    <p id="modal-trail-elevation" class="font-semibold text-gray-800">850m</p>
                                </div>
                                <div class="text-center">
                                    <div class="flex items-center justify-center mb-2">
                                        <span class="iconify text-[#336d66]" data-icon="heroicons:map-pin" style="font-size:1.5rem;"></span>
                                    </div>
                                    <p class="text-sm text-gray-500">Location</p>
                                    <p id="modal-trail-location" class="font-semibold text-gray-800">Mountain View</p>
                                </div>
                            </div>
                        </div>

                        <!-- Trail Description -->
                        <div>
                            <h3 class="text-lg font-bold mb-3 text-gray-800">Description</h3>
                            <p id="modal-trail-description" class="text-gray-600 leading-relaxed">
                                A beautiful trail that offers stunning views of the surrounding landscape. Perfect for hikers of all skill levels.
                            </p>
                        </div>

                        <!-- Trail Features -->
                        <div id="modal-trail-features">
                            <!-- Trail features will be dynamically loaded here -->
                        </div>

                        <!-- Reviews Section -->
                        <div id="modal-reviews-section">
                            <!-- Reviews will be dynamically loaded here -->
                        </div>
                    </div>

                    <!-- Sidebar - Fixed Position (No Scrolling) -->
                    <div class="space-y-6">
                        <!-- Weather -->
                        <div class="weather-card">
                            <h3 class="text-lg font-bold mb-4 text-white">Current Weather</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-white/80">Temperature</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="iconify text-white" data-icon="mdi:thermometer" style="font-size:1.2rem;"></span>
                                        <span id="modal-weather-temp" class="text-white font-semibold">22°C</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-white/80">Condition</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="iconify text-white" data-icon="mdi:weather-partly-cloudy" style="font-size:1.2rem;"></span>
                                        <span id="modal-weather-condition" class="text-white font-semibold">Partly Cloudy</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-white/80">Wind</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="iconify text-white" data-icon="mdi:weather-windy" style="font-size:1.2rem;"></span>
                                        <span id="modal-weather-wind" class="text-white font-semibold">8 km/h</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-white/80">Humidity</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="iconify text-white" data-icon="mdi:water-percent" style="font-size:1.2rem;"></span>
                                        <span id="modal-weather-humidity" class="text-white font-semibold">65%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Organization Info -->
                        <div id="modal-organization-info">
                            <!-- Organization info will be dynamically loaded here -->
                        </div>

                        <!-- Trail Stats -->
                        <div class="bg-white border border-gray-200 rounded-2xl p-6">
                            <h3 class="text-lg font-bold mb-4 text-gray-800">Trail Stats</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Reviews</span>
                                    <span id="modal-review-count" class="font-semibold text-gray-800">124</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Completed</span>
                                    <span id="modal-completed-count" class="font-semibold text-gray-800">1,847</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Difficulty</span>
                                    <span id="modal-difficulty-text" class="font-semibold text-gray-800">Beginner</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Best Season</span>
                                    <span class="font-semibold text-gray-800">Spring - Fall</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('register.select') }}" class="btn-mountain text-center text-sm px-3 py-2">
                                Sign Up to Plan Trip
                            </a>
                            <a href="{{ route('login') }}" class="btn-mountain-outline text-center text-sm px-3 py-2">
                                Login for Full Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Loading Overlay -->
                <div id="modal-loading-overlay" class="absolute inset-0 bg-white bg-opacity-90 hidden">
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <div class="animate-spin w-8 h-8 border-4 border-[#336d66] border-t-transparent rounded-full mx-auto mb-4"></div>
                            <p class="text-gray-600">Loading trail details...</p>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div id="modal-error-message" class="absolute top-4 left-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded hidden">
                    <!-- Error message will be displayed here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Trip Section -->
    <section id="plan" class="plan-trip-container py-20 bg-gradient-to-br from-[#aec896]/5 to-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-[#336d66] mb-6">Plan Your Perfect Adventure</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Easily build your itinerary, check real-time trail conditions, and prepare for your next unforgettable hiking experience.
                </p>
            </div>

            <div class="planner-showcase p-8 md:p-12 mb-12">
                <div class="max-w-4xl mx-auto">
                    <!-- Search and Filter Row -->
                    <div class="flex flex-col md:flex-row gap-4 mb-6">
                        <!-- Search Input -->
                        <div class="relative flex-1">
                            <input
                                id="trail-search-input"
                                type="text"
                                placeholder="Search trails, locations, or difficulty levels..."
                                class="mountain-search w-full p-4 pl-12 pr-16 text-lg rounded-2xl border-2 border-gray-200 focus:ring-4 focus:ring-[#20b6d2]/20 focus:border-[#20b6d2] transition-all duration-300 shadow-lg" />
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <button id="search-btn" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-[#336d66] text-white px-6 py-2 rounded-xl hover:bg-[#20b6d2] transition-colors duration-300">
                                Search
                            </button>
                        </div>

                        <!-- Filter Dropdown -->
                        <div class="relative">
                            <select id="trail-filter" class="appearance-none bg-white border-2 border-gray-200 rounded-2xl px-6 py-4 pr-12 text-lg font-medium text-gray-700 focus:ring-4 focus:ring-[#20b6d2]/20 focus:border-[#20b6d2] transition-all duration-300 shadow-lg cursor-pointer min-w-[200px]">
                                <option value="">All Trails</option>
                                <option value="popular">Most Popular</option>
                                <option value="newest">Newest Trails</option>
                                <option value="shortest">Shortest Route</option>
                                <option value="longest">Longest Route</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Results Section -->
                <div id="search-results" class="mt-12 hidden">
                    <div class="text-center mb-8">
                        <h3 id="results-title" class="text-2xl font-bold text-[#336d66] mb-2">Search Results</h3>
                        <p id="results-subtitle" class="text-gray-600">Found trails matching your search</p>
                        <button id="back-to-browse" onclick="clearSearch()"
                            class="mt-4 text-[#336d66] hover:text-[#20b6d2] font-medium transition-colors duration-300">
                            ← Back to Browse Categories
                        </button>
                    </div>
                    <div id="results-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Dynamic trail results will be inserted here -->
                    </div>
                    <!-- Show More Button -->
                    <div id="show-more-container" class="text-center mt-8 hidden">
                        <button id="show-more-btn"
                            class="bg-[#336d66] text-white px-8 py-3 rounded-xl hover:bg-[#20b6d2] transition-colors duration-300 font-medium">
                            Show More Trails
                        </button>
                        <p id="trails-count-info" class="text-gray-500 text-sm mt-2">Showing 9 of X trails</p>
                    </div>
                    <div id="no-results" class="text-center py-12 hidden">
                        <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                            <span class="iconify text-gray-400" data-icon="heroicons:magnifying-glass" style="font-size:2rem;"></span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No trails found</h3>
                        <p class="text-gray-500">Try adjusting your search terms or browse our categories below</p>
                        <button onclick="clearSearch()"
                            class="mt-4 bg-[#336d66] text-white px-6 py-2 rounded-lg hover:bg-[#20b6d2] transition-colors duration-300">
                            Browse Categories
                        </button>
                    </div>
                </div>

                <!-- Default Quick Search Cards -->
                <div id="default-cards" class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-12">
                    <div class="quick-search-card text-center cursor-pointer" onclick="quickSearch('beginner')">
                        <div class="w-12 h-12 bg-[#336d66]/10 rounded-xl flex items-center justify-center mb-4 mx-auto">
                            <span class="iconify text-green-500" data-icon="heroicons:sparkles-solid" style="font-size:1.5rem;"></span>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Beginner Trails</h3>
                        <p class="text-gray-600 text-sm">Perfect for first-time hikers</p>
                    </div>

                    <div class="quick-search-card text-center cursor-pointer" onclick="quickSearch('popular')">
                        <div class="w-12 h-12 bg-[#20b6d2]/10 rounded-xl flex items-center justify-center mb-4 mx-auto">
                            <span class="iconify text-yellow-500" data-icon="heroicons:star-solid" style="font-size:1.5rem;"></span>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Popular Trails</h3>
                        <p class="text-gray-600 text-sm">Highly rated by hikers</p>
                    </div>

                    <div class="quick-search-card text-center cursor-pointer" onclick="quickSearch('challenging')">
                        <div class="w-12 h-12 bg-[#e3a746]/10 rounded-xl flex items-center justify-center mb-4 mx-auto">
                            <span class="iconify text-orange-500" data-icon="heroicons:fire-solid" style="font-size:1.5rem;"></span>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Challenging</h3>
                        <p class="text-gray-600 text-sm">For experienced hikers</p>
                    </div>

                    <div class="quick-search-card text-center cursor-pointer" onclick="quickSearch('scenic')">
                        <div class="w-12 h-12 bg-[#aec896]/20 rounded-xl flex items-center justify-center mb-4 mx-auto">
                            <span class="iconify text-blue-500" data-icon="heroicons:camera-solid" style="font-size:1.5rem;"></span>
                        </div>
                        <h3 class="font-semibold text-lg mb-2">Scenic Views</h3>
                        <p class="text-gray-600 text-sm">Instagram-worthy spots</p>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="loading-state" class="text-center py-12 hidden">
                    <div class="animate-spin w-8 h-8 border-4 border-[#336d66] border-t-transparent rounded-full mx-auto mb-4"></div>
                    <p class="text-gray-600">Searching for trails...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Nearby Trails Section -->
    <section id="nearby-trails" class="nearby-trails-container py-20 bg-gradient-to-br from-[#20b6d2]/5 to-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-[#336d66] mb-6">Trails Near You</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Discover amazing hiking trails within 50km of your current location.
                </p>
            </div>

            <!-- Location Permission Request -->
            <div id="location-permission" class="text-center mb-12">
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 max-w-md mx-auto">
                    <div class="w-16 h-16 bg-[#336d66]/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <span class="iconify text-[#336d66]" data-icon="heroicons:map-pin" style="font-size:2rem;"></span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Allow Location Access</h3>
                    <p class="text-gray-600 mb-6">We need your location to show you nearby trails and provide the best hiking recommendations.</p>
                    <div class="space-y-4">
                        <button id="enable-location-btn" class="w-full bg-[#336d66] text-white px-6 py-3 rounded-xl hover:bg-[#20b6d2] transition-colors duration-300 font-medium">
                            Enable Location
                        </button>
                        <button id="manual-location-btn" class="w-full border-2 border-[#336d66] text-[#336d66] px-6 py-3 rounded-xl hover:bg-[#336d66] hover:text-white transition-colors duration-300 font-medium">
                            Enter Location Manually
                        </button>
                    </div>
                </div>
            </div>

            <!-- Manual Location Input -->
            <div id="manual-location-input" class="text-center mb-12 hidden">
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 max-w-md mx-auto">
                    <div class="w-16 h-16 bg-[#20b6d2]/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <span class="iconify text-[#20b6d2]" data-icon="heroicons:map" style="font-size:2rem;"></span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Enter Your Location</h3>
                    <div class="space-y-4">
                        <input id="manual-lat" type="number" step="any" placeholder="Latitude (e.g., 14.5995)"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#20b6d2] focus:border-transparent">
                        <input id="manual-lng" type="number" step="any" placeholder="Longitude (e.g., 120.9842)"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#20b6d2] focus:border-transparent">
                        <div class="flex space-x-3">
                            <button id="use-manual-location-btn" class="flex-1 bg-[#336d66] text-white px-4 py-3 rounded-xl hover:bg-[#20b6d2] transition-colors duration-300 font-medium">
                                Find Trails
                            </button>
                            <button id="back-to-auto-btn" class="flex-1 border-2 border-gray-300 text-gray-600 px-4 py-3 rounded-xl hover:bg-gray-50 transition-colors duration-300 font-medium">
                                Back
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Tip: You can find your coordinates using Google Maps</p>
                </div>
            </div>

            <!-- Location Error -->
            <div id="location-error" class="text-center mb-12 hidden">
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 max-w-md mx-auto">
                    <div class="w-16 h-16 bg-yellow-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <span class="iconify text-yellow-600" data-icon="heroicons:exclamation-triangle" style="font-size:2rem;"></span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Location Access Required</h3>
                    <p class="text-gray-600 mb-6">To show you nearby trails, please enable location access in your browser settings.</p>
                    <button id="retry-location-btn" class="bg-[#336d66] text-white px-6 py-3 rounded-xl hover:bg-[#20b6d2] transition-colors duration-300 font-medium">
                        Try Again
                    </button>
                </div>
            </div>

            <!-- Nearby Trails Loading -->
            <div id="nearby-trails-loading" class="text-center py-12 hidden">
                <div class="animate-spin w-8 h-8 border-4 border-[#336d66] border-t-transparent rounded-full mx-auto mb-4"></div>
                <p class="text-gray-600">Finding trails near you...</p>
            </div>

            <!-- Nearby Trails Content -->
            <div id="nearby-trails-content" class="hidden">
                <!-- Distance Filter -->
                <div class="text-center mb-8">
                    <div class="inline-flex bg-white rounded-2xl p-2 shadow-lg border border-gray-200">
                        <button class="distance-filter-btn active" data-distance="5">Within 5km</button>
                        <button class="distance-filter-btn" data-distance="15">Within 15km</button>
                        <button class="distance-filter-btn" data-distance="30">Within 30km</button>
                        <button class="distance-filter-btn" data-distance="50">Within 50km</button>
                    </div>
                </div>

                <!-- Trails Grid -->
                <div id="nearby-trails-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Dynamic nearby trails will be inserted here -->
                </div>

                <!-- Show More Button -->
                <div id="nearby-show-more-container" class="text-center hidden">
                    <button id="nearby-show-more-btn"
                        class="bg-[#336d66] text-white px-8 py-3 rounded-xl hover:bg-[#20b6d2] transition-colors duration-300 font-medium">
                        Show More Nearby Trails
                    </button>
                    <p id="nearby-trails-count" class="text-gray-500 text-sm mt-2">Showing 3 of X trails</p>
                </div>

                <!-- No Nearby Trails -->
                <div id="no-nearby-trails" class="text-center py-12 hidden">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <span class="iconify text-gray-400" data-icon="heroicons:map-pin" style="font-size:2rem;"></span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No trails found nearby</h3>
                    <p class="text-gray-500">Try expanding your search radius or explore our featured trails above</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-container py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-[#336d66] mb-6">Why Choose HikeThere?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to plan, track, and enjoy your hiking adventures safely and confidently.
                </p>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div class="feature-showcase group cursor-pointer" onclick="openFeatureTutorial('smart-recommendations')">
                    <div class="feature-icon bg-[#e3a746]/10">
                        <span class="iconify text-green-500" data-icon="fa6-solid:thumbs-up" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Smart Trail Recommendations</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Get personalized suggestions based on your skill level, fitness, and adventure preferences using our AI-powered matching system.</p>
                    <div class="text-[#e3a746] font-medium text-sm">Learn More →</div>
                </div>

                <div class="feature-showcase group cursor-pointer" onclick="openFeatureTutorial('trail-conditions')">
                    <div class="feature-icon bg-[#20b6d2]/10">
                        <span class="iconify text-blue-500" data-icon="mdi:weather-partly-cloudy" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Real-time Trail Conditions</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Stay informed with live trail alerts, weather updates, route closures, and safety notifications from local authorities.</p>
                    <div class="text-[#20b6d2] font-medium text-sm">Learn More →</div>
                </div>

                <div class="feature-showcase group cursor-pointer" onclick="openFeatureTutorial('community-safety')">
                    <div class="feature-icon bg-[#dfa648]/10">
                        <span class="iconify text-indigo-500" data-icon="heroicons:user-group-solid" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Community & Safety Network</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Connect with fellow hikers, share experiences, get local insights, and access emergency support when you need it most.</p>
                    <div class="text-[#dfa648] font-medium text-sm">Learn More →</div>
                </div>

                <div class="feature-showcase group cursor-pointer" onclick="openFeatureTutorial('offline-maps')">
                    <div class="feature-icon bg-[#336d66]/10">
                        <span class="iconify text-green-600" data-icon="mdi:map" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Offline Maps & GPS</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Download detailed topographic maps for offline use and never lose your way with precise GPS tracking, even without cell service.</p>
                    <div class="text-[#336d66] font-medium text-sm">Learn More →</div>
                </div>

                <div class="feature-showcase group cursor-pointer" onclick="openFeatureTutorial('trip-planning')">
                    <div class="feature-icon bg-[#aec896]/20">
                        <span class="iconify text-purple-500" data-icon="heroicons:calendar-solid" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Trip Planning Tools</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">Plan your entire adventure with packing lists, weather forecasts, difficulty assessments, and estimated timing calculations.</p>
                    <div class="text-[#336d66] font-medium text-sm">Learn More →</div>
                </div>

                <div class="feature-showcase group cursor-pointer" onclick="openFeatureTutorial('safety-framework')">
                    <div class="feature-icon bg-[#20b6d2]/10">
                        <span class="iconify text-red-600" data-icon="fa6-solid:phone" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="font-bold text-xl mb-3 text-gray-800">Safety Framework</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">HikeThere’s safety framework is a structured set of guidelines and tools that help ensure every trip is planned and enjoyed with minimal risk to hikers and the environment.</p>
                    <div class="text-[#20b6d2] font-medium text-sm">Learn More →</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Tutorial Modals -->
    <!-- Smart Trail Recommendations Modal -->
    <div id="smart-recommendations-modal" class="feature-tutorial-modal">
        <div class="feature-tutorial-content">
            <div class="feature-tutorial-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Smart Trail Recommendations</h2>
                        <p class="text-gray-600">Discover how HikeThere helps you find the perfect trail</p>
                    </div>
                    <button onclick="closeFeatureTutorial()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="feature-tutorial-body">
                <div class="tutorial-step">
                    <div class="step-number">1</div>
                    <h3 class="text-lg font-semibold mb-3">Advanced Search & Filtering</h3>
                    <p class="text-gray-600 mb-4">Find trails that match your preferences using our comprehensive search and filter system.</p>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-semibold mb-3">Available Filters:</h5>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <span class="iconify text-green-500 mr-2" data-icon="heroicons:check-circle"></span>
                                Difficulty Level (Beginner, Intermediate, Advanced)
                            </li>
                            <li class="flex items-center">
                                <span class="iconify text-green-500 mr-2" data-icon="heroicons:check-circle"></span>
                                Location & Mountain Name Search
                            </li>
                            <li class="flex items-center">
                                <span class="iconify text-green-500 mr-2" data-icon="heroicons:check-circle"></span>
                                Distance from Your Location (5km - 50km radius)
                            </li>
                            <li class="flex items-center">
                                <span class="iconify text-green-500 mr-2" data-icon="heroicons:check-circle"></span>
                                Quick filters: Beginner-friendly, Popular, Challenging, Scenic
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">2</div>
                    <h3 class="text-lg font-semibold mb-3">Interactive Map Explorer</h3>
                    <p class="text-gray-600 mb-4">Explore trails visually with our interactive map featuring clustering and real-time location services.</p>
                    <div class="tutorial-benefits">
                        <div class="benefit-item">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="iconify text-blue-600" data-icon="heroicons:map" style="font-size:1.5rem;"></span>
                            </div>
                            <h4 class="font-semibold text-sm">Google Maps Integration</h4>
                            <p class="text-xs text-gray-600">Visual trail locations with satellite view</p>
                        </div>
                        <div class="benefit-item">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                <span class="iconify text-green-600" data-icon="heroicons:map-pin" style="font-size:1.5rem;"></span>
                            </div>
                            <h4 class="font-semibold text-sm">GPS Trail Paths</h4>
                            <p class="text-xs text-gray-600">Upload and view actual GPS tracks</p>
                        </div>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">3</div>
                    <h3 class="text-lg font-semibold mb-3">Detailed Trail Information</h3>
                    <p class="text-gray-600 mb-4">Access comprehensive trail details to make informed decisions about your hike.</p>
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg p-4">
                        <h5 class="font-semibold mb-3">Trail Details Include:</h5>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="flex items-center">
                                <span class="iconify text-emerald-600 mr-2" data-icon="heroicons:chart-bar"></span>
                                <span>Distance & Elevation Gain</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-emerald-600 mr-2" data-icon="heroicons:clock"></span>
                                <span>Estimated Duration</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-emerald-600 mr-2" data-icon="heroicons:signal"></span>
                                <span>Difficulty Rating</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-emerald-600 mr-2" data-icon="heroicons:photo"></span>
                                <span>Photo Galleries</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-emerald-600 mr-2" data-icon="heroicons:star"></span>
                                <span>Reviews & Ratings</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-emerald-600 mr-2" data-icon="heroicons:currency-dollar"></span>
                                <span>Pricing & Booking Info</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trail Conditions Modal -->
    <div id="trail-conditions-modal" class="feature-tutorial-modal">
        <div class="feature-tutorial-content">
            <div class="feature-tutorial-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Real-time Trail Conditions</h2>
                        <p class="text-gray-600">Stay informed with weather forecasts and trail information</p>
                    </div>
                    <button onclick="closeFeatureTutorial()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="feature-tutorial-body">
                <div class="tutorial-step">
                    <div class="step-number">1</div>
                    <h3 class="text-lg font-semibold mb-3">Comprehensive Weather Forecasts</h3>
                    <p class="text-gray-600 mb-4">Get detailed weather information specific to each trail location using OpenWeather API integration.</p>
                    <div class="bg-gradient-to-br from-blue-50 to-sky-50 rounded-lg p-4">
                        <h5 class="font-semibold mb-3">Weather Data Includes:</h5>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="flex items-center">
                                <span class="iconify text-blue-500 mr-2" data-icon="mdi:thermometer"></span>
                                <span>Current Temperature</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-blue-500 mr-2" data-icon="mdi:weather-cloudy"></span>
                                <span>Sky Conditions</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-blue-500 mr-2" data-icon="mdi:water-percent"></span>
                                <span>Humidity Levels</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-blue-500 mr-2" data-icon="mdi:weather-windy"></span>
                                <span>Wind Speed & Direction</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-blue-500 mr-2" data-icon="mdi:weather-sunset"></span>
                                <span>Sunrise & Sunset Times</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-blue-500 mr-2" data-icon="mdi:calendar-range"></span>
                                <span>7-Day Forecast</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">2</div>
                    <h3 class="text-lg font-semibold mb-3">Hourly & Daily Predictions</h3>
                    <p class="text-gray-600 mb-4">Plan your hike with confidence using hour-by-hour and extended weather forecasts.</p>
                    <div class="space-y-3">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <div class="flex items-center mb-2">
                                <span class="iconify text-yellow-600 mr-2" data-icon="heroicons:clock"></span>
                                <h5 class="font-semibold text-yellow-800">6-Hour Forecasts</h5>
                            </div>
                            <p class="text-sm text-yellow-700">Track weather changes throughout your hiking day with detailed hourly predictions.</p>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                            <div class="flex items-center mb-2">
                                <span class="iconify text-purple-600 mr-2" data-icon="heroicons:calendar"></span>
                                <h5 class="font-semibold text-purple-800">Weekly Planning</h5>
                            </div>
                            <p class="text-sm text-purple-700">Choose the best day for your adventure with 7-day weather calendars in your itinerary planner.</p>
                        </div>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">3</div>
                    <h3 class="text-lg font-semibold mb-3">Smart Weather Caching</h3>
                    <p class="text-gray-600 mb-4">Fast loading times with intelligent weather data caching and automatic updates.</p>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h5 class="font-semibold text-green-800 mb-2">Performance Features:</h5>
                        <ul class="space-y-2 text-sm text-green-700">
                            <li class="flex items-start">
                                <span class="iconify text-green-600 mr-2 mt-0.5" data-icon="heroicons:check-circle"></span>
                                <span><strong>Cached Weather Data:</strong> Stores recent forecasts for instant loading</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-green-600 mr-2 mt-0.5" data-icon="heroicons:check-circle"></span>
                                <span><strong>Fallback System:</strong> Shows cached data while fetching fresh updates</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-green-600 mr-2 mt-0.5" data-icon="heroicons:check-circle"></span>
                                <span><strong>Location-Specific:</strong> Weather data tailored to each trail's coordinates</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Community Safety Modal -->
    <div id="community-safety-modal" class="feature-tutorial-modal">
        <div class="feature-tutorial-content">
            <div class="feature-tutorial-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Community & Safety Network</h2>
                        <p class="text-gray-600">Connect with hikers and share your experiences</p>
                    </div>
                    <button onclick="closeFeatureTutorial()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="feature-tutorial-body">
                <div class="tutorial-step">
                    <div class="step-number">1</div>
                    <h3 class="text-lg font-semibold mb-3">Community Dashboard</h3>
                    <p class="text-gray-600 mb-4">Share your hiking experiences and connect with fellow adventurers through our interactive community platform.</p>
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg p-4">
                        <h5 class="font-semibold mb-3 text-emerald-800">Community Features:</h5>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start">
                                <span class="iconify text-emerald-600 mr-2 mt-0.5" data-icon="heroicons:pencil-square"></span>
                                <span><strong>Create Posts:</strong> Share stories, photos, and tips from your hikes</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-emerald-600 mr-2 mt-0.5" data-icon="heroicons:heart"></span>
                                <span><strong>Like & Comment:</strong> Engage with other hikers' adventures</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-emerald-600 mr-2 mt-0.5" data-icon="heroicons:bookmark"></span>
                                <span><strong>Follow Trails:</strong> Get updates from your favorite hiking spots</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-emerald-600 mr-2 mt-0.5" data-icon="heroicons:funnel"></span>
                                <span><strong>Smart Filters:</strong> View all posts or filter by followed trails</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">2</div>
                    <h3 class="text-lg font-semibold mb-3">Trail Reviews & Ratings</h3>
                    <p class="text-gray-600 mb-4">Help others make informed decisions by sharing your trail experiences and reading reviews from the community.</p>
                    <div class="space-y-3">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center mb-2">
                                <span class="iconify text-blue-600 mr-2" data-icon="heroicons:star"></span>
                                <h5 class="font-semibold text-blue-800">Rate Your Experience</h5>
                            </div>
                            <p class="text-sm text-blue-700">Leave star ratings and detailed reviews to help other hikers choose the right trail.</p>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                            <div class="flex items-center mb-2">
                                <span class="iconify text-purple-600 mr-2" data-icon="heroicons:photo"></span>
                                <h5 class="font-semibold text-purple-800">Photo Sharing</h5>
                            </div>
                            <p class="text-sm text-purple-700">Upload photos to showcase trail conditions, views, and memorable moments.</p>
                        </div>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">3</div>
                    <h3 class="text-lg font-semibold mb-3">Real-Time Updates</h3>
                    <p class="text-gray-600 mb-4">Stay connected with instant notifications and live activity feeds.</p>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h5 class="font-semibold text-orange-800 mb-3">Live Features:</h5>
                        <div class="space-y-2 text-sm text-orange-700">
                            <div class="flex items-center">
                                <span class="iconify text-orange-600 mr-2" data-icon="heroicons:bell"></span>
                                <span>Notifications for likes, comments, and new followers</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-orange-600 mr-2" data-icon="heroicons:arrow-path"></span>
                                <span>Auto-refreshing community feed (every 30 seconds)</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-orange-600 mr-2" data-icon="heroicons:user-group"></span>
                                <span>See who completed trails recently</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offline Maps Modal -->
    <div id="offline-maps-modal" class="feature-tutorial-modal">
        <div class="feature-tutorial-content">
            <div class="feature-tutorial-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Offline Maps & GPS</h2>
                        <p class="text-gray-600">Navigate with confidence using GPS trail paths</p>
                    </div>
                    <button onclick="closeFeatureTutorial()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="feature-tutorial-body">
                <div class="tutorial-step">
                    <div class="step-number">1</div>
                    <h3 class="text-lg font-semibold mb-3">GPS File Upload System</h3>
                    <p class="text-gray-600 mb-4">Upload actual GPS tracks from your hiking devices or apps to show real trail paths on the map.</p>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4">
                        <h5 class="font-semibold mb-3 text-indigo-800">Supported File Formats:</h5>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="text-center p-3 bg-white rounded-lg border border-indigo-200">
                                <span class="iconify text-indigo-600 text-2xl mb-1" data-icon="heroicons:document"></span>
                                <p class="text-xs font-semibold">GPX Files</p>
                                <p class="text-xs text-gray-600">GPS Exchange</p>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg border border-indigo-200">
                                <span class="iconify text-indigo-600 text-2xl mb-1" data-icon="heroicons:document"></span>
                                <p class="text-xs font-semibold">KML Files</p>
                                <p class="text-xs text-gray-600">Google Earth</p>
                            </div>
                            <div class="text-center p-3 bg-white rounded-lg border border-indigo-200">
                                <span class="iconify text-indigo-600 text-2xl mb-1" data-icon="heroicons:document"></span>
                                <p class="text-xs font-semibold">KMZ Files</p>
                                <p class="text-xs text-gray-600">Compressed KML</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">2</div>
                    <h3 class="text-lg font-semibold mb-3">Interactive Trail Mapping</h3>
                    <p class="text-gray-600 mb-4">View detailed trail paths with elevation profiles and waypoint information directly on Google Maps.</p>
                    <div class="space-y-3">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-center mb-2">
                                <span class="iconify text-green-600 mr-2" data-icon="heroicons:map"></span>
                                <h5 class="font-semibold text-green-800">Visual Trail Paths</h5>
                            </div>
                            <p class="text-sm text-green-700">See the exact route displayed as a line on the map with accurate GPS coordinates.</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center mb-2">
                                <span class="iconify text-blue-600 mr-2" data-icon="heroicons:chart-bar"></span>
                                <h5 class="font-semibold text-blue-800">Auto-Calculated Metrics</h5>
                            </div>
                            <p class="text-sm text-blue-700">Distance, elevation gain, and difficulty are automatically extracted from GPS data.</p>
                        </div>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">3</div>
                    <h3 class="text-lg font-semibold mb-3">Multiple Mapping Integrations</h3>
                    <p class="text-gray-600 mb-4">Access different map views and trail data from various sources.</p>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-semibold mb-3">Available Map Systems:</h5>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start">
                                <span class="iconify text-gray-600 mr-2 mt-0.5" data-icon="heroicons:map-pin"></span>
                                <span><strong>Google Maps:</strong> Satellite, terrain, and street views for trail visualization</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-gray-600 mr-2 mt-0.5" data-icon="heroicons:globe-alt"></span>
                                <span><strong>OpenStreetMap Integration:</strong> Community-sourced trail data and points of interest</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-gray-600 mr-2 mt-0.5" data-icon="heroicons:arrow-down-tray"></span>
                                <span><strong>Downloadable Tracks:</strong> Export GPS files for use on your hiking devices</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-gray-600 mr-2 mt-0.5" data-icon="heroicons:pencil"></span>
                                <span><strong>Manual Drawing Tool:</strong> Draw trails directly on the map if you don't have GPS files</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trip Planning Modal -->
    <div id="trip-planning-modal" class="feature-tutorial-modal">
        <div class="feature-tutorial-content">
            <div class="feature-tutorial-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Trip Planning Tools</h2>
                        <p class="text-gray-600">Build comprehensive itineraries with our advanced planner</p>
                    </div>
                    <button onclick="closeFeatureTutorial()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="feature-tutorial-body">
                <div class="tutorial-step">
                    <div class="step-number">1</div>
                    <h3 class="text-lg font-semibold mb-3">Interactive Itinerary Builder</h3>
                    <p class="text-gray-600 mb-4">Create detailed day-by-day plans with our comprehensive itinerary system featuring map integration and activity customization.</p>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4">
                        <h5 class="font-semibold mb-3 text-purple-800">Itinerary Features:</h5>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start">
                                <span class="iconify text-purple-600 mr-2 mt-0.5" data-icon="heroicons:calendar"></span>
                                <span><strong>Multi-Day Planning:</strong> Organize trips spanning multiple days and nights</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-purple-600 mr-2 mt-0.5" data-icon="heroicons:map"></span>
                                <span><strong>Interactive Map:</strong> Select trails, stopovers, and side trips visually</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-purple-600 mr-2 mt-0.5" data-icon="heroicons:pencil-square"></span>
                                <span><strong>Custom Activities:</strong> Add meals, rest stops, and special activities</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-purple-600 mr-2 mt-0.5" data-icon="heroicons:truck"></span>
                                <span><strong>Transportation Planning:</strong> Include vehicle or included transport options</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">2</div>
                    <h3 class="text-lg font-semibold mb-3">Smart Weather Integration</h3>
                    <p class="text-gray-600 mb-4">Plan with confidence using weather forecasts and intelligent activity recommendations.</p>
                    <div class="space-y-3">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center mb-2">
                                <span class="iconify text-blue-600 mr-2" data-icon="mdi:weather-partly-cloudy"></span>
                                <h5 class="font-semibold text-blue-800">Weather Calendar</h5>
                            </div>
                            <p class="text-sm text-blue-700">View 7-day weather forecasts for selected trails with hourly predictions to choose the best hiking days.</p>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-center mb-2">
                                <span class="iconify text-green-600 mr-2" data-icon="heroicons:light-bulb"></span>
                                <h5 class="font-semibold text-green-800">Activity Recommendations</h5>
                            </div>
                            <p class="text-sm text-green-700">Get intelligent suggestions for gear, precautions, and timing based on weather and trail conditions.</p>
                        </div>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">3</div>
                    <h3 class="text-lg font-semibold mb-3">Booking & Payment Integration</h3>
                    <p class="text-gray-600 mb-4">Seamlessly book trails and manage payments directly from your itinerary.</p>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h5 class="font-semibold text-orange-800 mb-3">Booking System:</h5>
                        <div class="space-y-2 text-sm text-orange-700">
                            <div class="flex items-center">
                                <span class="iconify text-orange-600 mr-2" data-icon="heroicons:calendar-days"></span>
                                <span>Real-time availability checking for fully booked dates</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-orange-600 mr-2" data-icon="heroicons:credit-card"></span>
                                <span>Integrated payment via GCash with automatic calculation</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-orange-600 mr-2" data-icon="heroicons:receipt-percent"></span>
                                <span>Track booking status and payment verification in real-time</span>
                            </div>
                            <div class="flex items-center">
                                <span class="iconify text-orange-600 mr-2" data-icon="heroicons:arrow-path"></span>
                                <span>Edit or cancel bookings with automatic notifications</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Safety Framework Modal -->
    <div id="safety-framework-modal" class="feature-tutorial-modal">
        <div class="feature-tutorial-content">
            <div class="feature-tutorial-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Safety Framework</h2>
                        <p class="text-gray-600">Comprehensive safety guidelines and emergency information system</p>
                    </div>
                    <button onclick="closeFeatureTutorial()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="feature-tutorial-body">
                <div class="tutorial-step">
                    <div class="step-number">1</div>
                    <h3 class="text-lg font-semibold mb-3">Emergency Information System</h3>
                    <p class="text-gray-600 mb-4">Access critical emergency information and contact details specific to each trail location.</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h5 class="font-semibold text-red-800 mb-3">Emergency Info Includes:</h5>
                        <ul class="space-y-2 text-sm text-red-700">
                            <li class="flex items-start">
                                <span class="iconify text-red-600 mr-2 mt-0.5" data-icon="heroicons:phone"></span>
                                <span><strong>Emergency Contacts:</strong> Local rescue services, park rangers, and medical facilities</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-red-600 mr-2 mt-0.5" data-icon="heroicons:map-pin"></span>
                                <span><strong>Nearest Hospital:</strong> Distance and directions to closest medical center</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-red-600 mr-2 mt-0.5" data-icon="heroicons:identification"></span>
                                <span><strong>Police Station:</strong> Contact info and location of local authorities</span>
                            </li>
                            <li class="flex items-start">
                                <span class="iconify text-red-600 mr-2 mt-0.5" data-icon="heroicons:building-office"></span>
                                <span><strong>Management Office:</strong> Trail organization's emergency contact</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">2</div>
                    <h3 class="text-lg font-semibold mb-3">Safety Guidelines & Readiness Assessment</h3>
                    <p class="text-gray-600 mb-4">Prepare properly with comprehensive safety checklists and emergency preparedness information.</p>
                    <div class="space-y-3">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <div class="flex items-center mb-2">
                                <span class="iconify text-yellow-600 mr-2" data-icon="heroicons:clipboard-document-check"></span>
                                <h5 class="font-semibold text-yellow-800">Pre-Hike Safety Checklist</h5>
                            </div>
                            <p class="text-sm text-yellow-700">Trail-specific safety recommendations including required gear, weather warnings, and terrain considerations.</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center mb-2">
                                <span class="iconify text-blue-600 mr-2" data-icon="heroicons:shield-check"></span>
                                <h5 class="font-semibold text-blue-800">Emergency Readiness</h5>
                            </div>
                            <p class="text-sm text-blue-700">Guidance on what to do in emergencies, including first aid tips and communication protocols.</p>
                        </div>
                    </div>
                </div>

                <div class="tutorial-step">
                    <div class="step-number">3</div>
                    <h3 class="text-lg font-semibold mb-3">Organization Safety Support</h3>
                    <p class="text-gray-600 mb-4">Trail organizations provide structured safety guidelines and emergency response procedures.</p>
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg p-4">
                        <h5 class="font-semibold mb-3 text-emerald-800">Organization Features:</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">
                            <div class="flex items-start">
                                <span class="iconify text-emerald-600 mr-2 mt-0.5" data-icon="heroicons:document-text"></span>
                                <span><strong>Safety Documents:</strong> Trail-specific safety PDFs and guidelines</span>
                            </div>
                            <div class="flex items-start">
                                <span class="iconify text-emerald-600 mr-2 mt-0.5" data-icon="heroicons:exclamation-triangle"></span>
                                <span><strong>Risk Warnings:</strong> Current alerts about trail conditions</span>
                            </div>
                            <div class="flex items-start">
                                <span class="iconify text-emerald-600 mr-2 mt-0.5" data-icon="heroicons:user-group"></span>
                                <span><strong>Group Size Limits:</strong> Safety-based capacity management</span>
                            </div>
                            <div class="flex items-start">
                                <span class="iconify text-emerald-600 mr-2 mt-0.5" data-icon="heroicons:calendar"></span>
                                <span><strong>Seasonal Closures:</strong> Updates on accessibility and restrictions</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Community Section -->
    <section id="community" class="community-container py-20 bg-gradient-to-br from-[#336d66]/5 to-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-[#336d66] mb-6">Join Our Hiking Community</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Be part of a passionate network of hikers who value safety, preparedness, and the joy of exploring nature together.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="community-feature text-center">
                    <div class="w-16 h-16 bg-[#336d66]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="iconify text-blue-500" data-icon="mdi:share-variant" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Share Adventures</h3>
                    <p class="text-gray-600">Post photos, tips, and stories from your hiking experiences to inspire others.</p>
                </div>

                <div class="community-feature text-center">
                    <div class="w-16 h-16 bg-[#20b6d2]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="iconify text-green-600" data-icon="heroicons:user-group-solid" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Find Hiking Buddies</h3>
                    <p class="text-gray-600">Connect with like-minded hikers in your area and plan group adventures.</p>
                </div>

                <div class="community-feature text-center">
                    <div class="w-16 h-16 bg-[#e3a746]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="iconify text-amber-500" data-icon="mdi:account-tie" style="font-size:1.5rem;"></span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Expert Guidance</h3>
                    <p class="text-gray-600">Get advice from experienced hikers and certified outdoor professionals.</p>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('register.select') }}" class="btn-mountain-large">
                    <span>Join Our Community</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
                <p class="mt-4 text-gray-600">Free to join • No credit card required</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900">
        <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
            <div class="md:flex md:justify-between">
                <div class="mb-6 md:mb-0">
                    <a href="/" class="flex items-center space-x-3 mountain-logo group">
                        <div class="relative">
                            <img src="{{ asset('img/icon1.png') }}" alt="{{ config('app.name', 'HikeThere') }} logo" class="h-9 w-auto">
                        </div>
                        <span class="font-bold text-xl text-[#336d66]">{{ config('app.name', 'HikeThere') }}</span>
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Resources</h2>
                        <ul class="text-gray-500 dark:text-gray-400 font-medium">
                            <li class="mb-4">
                                <a href="/trails" class="hover:underline">Trails</a>
                            </li>
                            <li>
                                <a href="https://tailwindcss.com/" class="hover:underline">Tailwind CSS</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Follow us</h2>
                        <ul class="text-gray-500 dark:text-gray-400 font-medium">
                            <li class="mb-4">
                                <a href="https://github.com/themesberg/flowbite" class="hover:underline ">Github</a>
                            </li>
                            <li>
                                <a href="https://discord.gg/4eeurUVvTy" class="hover:underline">Discord</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Legal</h2>
                        <ul class="text-gray-500 dark:text-gray-400 font-medium">
                            <li class="mb-4">
                                <a href="{{ route('privacy') }}" class="hover:underline">Privacy Policy</a>
                            </li>
                            <li>
                                <a href="{{ route('terms') }}" class="hover:underline">Terms &amp; Conditions</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
            <div class="sm:flex sm:items-center sm:justify-between">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© {{ date('Y') }} <a href="/" class="hover:underline">{{ config('app.name', 'HikeThere') }}</a>. All Rights Reserved.
                </span>
                <div class="flex mt-4 sm:justify-center sm:mt-0">
                    <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 8 19">
                            <path fill-rule="evenodd" d="M6.135 3H8V0H6.135a4.147 4.147 0 0 0-4.142 4.142V6H0v3h2v9.938h3V9h2.021l.592-3H5V3.591A.6.6 0 0 1 5.592 3h.543Z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Facebook page</span>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white ms-5">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 21 16">
                            <path d="M16.942 1.556a16.3 16.3 0 0 0-4.126-1.3 12.04 12.04 0 0 0-.529 1.1 15.175 15.175 0 0 0-4.573 0 11.585 11.585 0 0 0-.535-1.1 16.274 16.274 0 0 0-4.129 1.3A17.392 17.392 0 0 0 .182 13.218a15.785 15.785 0 0 0 4.963 2.521c.41-.564.773-1.16 1.084-1.785a10.63 10.63 0 0 1-1.706-.83c.143-.106.283-.217.418-.33a11.664 11.664 0 0 0 10.118 0c.137.113.277.224.418.33-.544.328-1.116.606-1.71.832a12.52 12.52 0 0 0 1.084 1.785 16.46 16.46 0 0 0 5.064-2.595 17.286 17.286 0 0 0-2.973-11.59ZM6.678 10.813a1.941 1.941 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.919 1.919 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Zm6.644 0a1.94 1.94 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.918 1.918 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Z" />
                        </svg>
                        <span class="sr-only">Discord community</span>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white ms-5">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 17">
                            <path fill-rule="evenodd" d="M20 1.892a8.178 8.178 0 0 1-2.355.635 4.074 4.074 0 0 0 1.8-2.235 8.344 8.344 0 0 1-2.605.98A4.13 4.13 0 0 0 13.85 0a4.068 4.068 0 0 0-4.1 4.038 4 4 0 0 0 .105.919A11.705 11.705 0 0 1 1.4.734a4.006 4.006 0 0 0 1.268 5.392 4.165 4.165 0 0 1-1.859-.5v.05A4.057 4.057 0 0 0 4.1 9.635a4.19 4.19 0 0 1-1.856.07 4.108 4.108 0 0 0 3.831 2.807A8.36 8.36 0 0 1 0 14.184 11.732 11.732 0 0 0 6.291 16 11.502 11.502 0 0 0 17.964 4.5c0-.177 0-.35-.012-.523A8.143 8.143 0 0 0 20 1.892Z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Twitter page</span>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-gray-900 dark:hover:text-white ms-5">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 .333A9.911 9.911 0 0 0 6.866 19.65c.5.092.678-.215.678-.477 0-.237-.01-1.017-.014-1.845-2.757.6-3.338-1.169-3.338-1.169a2.627 2.627 0 0 0-1.1-1.451c-.9-.615.07-.6.07-.6a2.084 2.084 0 0 1 1.518 1.021 2.11 2.11 0 0 0 2.884.823c.044-.503.268-.973.63-1.325-2.2-.25-4.516-1.1-4.516-4.9A3.832 3.832 0 0 1 4.7 7.068a3.56 3.56 0 0 1 .095-2.623s.832-.266 2.726 1.016a9.409 9.409 0 0 1 4.962 0c1.89-1.282 2.717-1.016 2.717-1.016.366.83.402 1.768.1 2.623a3.827 3.827 0 0 1 1.02 2.659c0 3.807-2.319 4.644-4.525 4.889a2.366 2.366 0 0 1 .673 1.834c0 1.326-.012 2.394-.012 2.72 0 .263.18.572.681.475A9.911 9.911 0 0 0 10 .333Z" clip-rule="evenodd" />
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Toggle Script -->
    <script type="application/json" id="app-config">
        {!! json_encode([
            'authenticated' => auth()->check(),
            'defaultTrailImage' => asset('img/default-trail.jpg'),
            'appName' => config('app.name'),
        ]) !!}
    </script>

    <script>
        // Read pre-rendered app config to avoid Blade tokens inside JS
        const APP_CONFIG = (() => {
            try {
                const el = document.getElementById('app-config');
                return el ? JSON.parse(el.textContent || '{}') : {};
            } catch (e) {
                console.error('Failed to parse APP_CONFIG', e);
                return {};
            }
        })();

        const DEFAULT_TRAIL_IMAGE = APP_CONFIG.defaultTrailImage || '';
        const IS_AUTHENTICATED = !!APP_CONFIG.authenticated;

        // Expose quickSearch globally for inline onclick (must be at top)
        function quickSearch(category) {
            const searchInput = document.getElementById('trail-search-input');
            const filterSelect = document.getElementById('trail-filter');
            // Clear any existing search and filter
            searchInput.value = '';
            filterSelect.value = '';
            // Perform search with category only
            performSearch('', category);
        }
        window.quickSearch = quickSearch;

        // API configuration
        const API_BASE_URL = '/api';

        let trailsData = [];
        let currentPage = 1;
        let hasMoreTrails = false;
        let currentQuery = '';
        let currentFilter = '';
        let heroTrailsData = [];

        // Nearby trails variables
        let nearbyTrailsData = [];
        let nearbyCurrentPage = 1;
        let nearbyHasMore = false;
        let userLocation = null;
        let currentDistance = 5; // Default to 5km

        // Initialize hero trail showcase
        async function initHeroTrailsShowcase() {
            try {
                const response = await fetch(`${API_BASE_URL}/trails/search-trails?limit=9`);
                const data = await response.json();

                if (data.success && data.trails.length > 0) {
                    heroTrailsData = data.trails;
                    displayHeroTrails();
                    initTextBlending(); // Initialize text blending effects
                }
            } catch (error) {
                console.log('Hero trails not loaded:', error);
            }
        }

        // Geolocation functionality for nearby trails
        function requestLocation() {
            console.log('Requesting location...');
            const locationPermission = document.getElementById('location-permission');
            const locationError = document.getElementById('location-error');
            const nearbyLoading = document.getElementById('nearby-trails-loading');

            locationPermission.classList.add('hidden');
            locationError.classList.add('hidden');
            nearbyLoading.classList.remove('hidden');

            // Check if geolocation is supported
            if (!navigator.geolocation) {
                console.error('Geolocation not supported');
                showLocationError('Geolocation is not supported by this browser.');
                return;
            }

            // Check if we're on HTTPS (required for geolocation in most browsers)
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                console.warn('Geolocation requires HTTPS');
                showLocationError('Location services require a secure connection (HTTPS).');
                return;
            }

            console.log('Calling getCurrentPosition...');

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    console.log('Location success:', position);
                    userLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    console.log('User location set:', userLocation);
                    nearbyLoading.classList.add('hidden');
                    document.getElementById('nearby-trails-content').classList.remove('hidden');
                    fetchNearbyTrails();
                },
                (error) => {
                    console.error('Geolocation error:', error);
                    let errorMessage = 'Unable to retrieve your location.';
                    let debugInfo = '';

                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location access was denied. Please check your browser settings and try again.';
                            debugInfo = 'Error code: PERMISSION_DENIED';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information is unavailable. Please check your device settings.';
                            debugInfo = 'Error code: POSITION_UNAVAILABLE';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out. Please try again.';
                            debugInfo = 'Error code: TIMEOUT';
                            break;
                        default:
                            errorMessage = 'An unknown error occurred while retrieving your location.';
                            debugInfo = `Error code: ${error.code}`;
                            break;
                    }

                    console.log('Debug info:', debugInfo);
                    console.log('Error message:', error.message);

                    // Show both user-friendly message and debug info
                    showLocationError(errorMessage + (debugInfo ? ` (${debugInfo})` : ''));
                }, {
                    enableHighAccuracy: false, // Changed to false for better compatibility
                    timeout: 15000, // Increased timeout
                    maximumAge: 300000 // 5 minutes
                }
            );
        }

        function showLocationError(message) {
            const nearbyLoading = document.getElementById('nearby-trails-loading');
            const locationError = document.getElementById('location-error');

            nearbyLoading.classList.add('hidden');
            locationError.classList.remove('hidden');
            locationError.querySelector('p').textContent = message;
        }

        // Test function for debugging - can be called from browser console
        function testNearbyWithCoords(lat, lng) {
            console.log('Testing with coordinates:', lat, lng);
            userLocation = {
                latitude: lat,
                longitude: lng
            };
            document.getElementById('location-permission').classList.add('hidden');
            document.getElementById('location-error').classList.add('hidden');
            document.getElementById('manual-location-input').classList.add('hidden');
            document.getElementById('nearby-trails-content').classList.remove('hidden');
            fetchNearbyTrails();
        }

        // Test function to check if there are ANY trails in the database
        async function testAllTrails() {
            console.log('=== Testing All Trails Endpoint ===');
            try {
                const response = await fetch(`${API_BASE_URL}/trails?limit=10`);
                console.log('All trails response status:', response.status);

                if (response.ok) {
                    const data = await response.json();
                    console.log('Total trails in database:', data);
                    return data;
                } else {
                    console.error('Error fetching all trails:', response.status);
                }
            } catch (error) {
                console.error('Error in testAllTrails:', error);
            }
        }

        async function testDebugDatabase() {
            console.log('=== Testing Debug Database Endpoint ===');
            try {
                const response = await fetch(`${API_BASE_URL}/trails/debug`);
                console.log('Debug response status:', response.status);

                if (response.ok) {
                    const data = await response.json();
                    console.log('Debug database info:', data);
                    alert(`Database Stats:
Total Trails: ${data.database_stats.total_trails}
Active Trails: ${data.database_stats.active_trails}
Approved Trails: ${data.database_stats.approved_trails}
Trails with Coordinates: ${data.database_stats.trails_with_coordinates}
Sample Trails: ${data.sample_trails.length}`);
                    return data;
                } else {
                    console.error('Error fetching debug info:', response.status);
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                }
            } catch (error) {
                console.error('Error in testDebugDatabase:', error);
                alert('Error testing debug endpoint: ' + error.message);
            }
        }

        // Make test functions globally available
        window.testNearbyWithCoords = testNearbyWithCoords;
        window.testAllTrails = testAllTrails;
        window.testDebugDatabase = testDebugDatabase;

        // Fetch nearby trails based on user location
        async function fetchNearbyTrails(page = 1) {
            console.log('=== Fetching Nearby Trails ===');
            console.log('User location:', userLocation);
            console.log('Page:', page);
            console.log('Current distance:', currentDistance);

            if (!userLocation) {
                console.error('No user location available');
                showLocationError('Location not available. Please enable location access.');
                return;
            }

            const nearbyLoading = document.getElementById('nearby-trails-loading');
            const nearbyContent = document.getElementById('nearby-trails-content');
            const nearbyGrid = document.getElementById('nearby-trails-grid');
            const showMoreContainer = document.getElementById('nearby-show-more-container');
            const noNearbyTrails = document.getElementById('no-nearby-trails');

            if (page === 1) {
                nearbyLoading.classList.remove('hidden');
                nearbyContent.classList.add('hidden');
            }

            try {
                const params = new URLSearchParams({
                    latitude: userLocation.latitude,
                    longitude: userLocation.longitude,
                    radius: currentDistance,
                    page: page,
                    per_page: page === 1 ? 3 : 6 // First load: 3 trails, subsequent: 6 trails
                });

                const apiUrl = `${API_BASE_URL}/trails/nearby?${params}`;
                console.log('API URL:', apiUrl);

                const response = await fetch(apiUrl);
                console.log('Response status:', response.status);
                console.log('Response headers:', Object.fromEntries(response.headers.entries()));

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('API Error Response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }

                const data = await response.json();
                console.log('API Response data:', data);

                if (page === 1) {
                    nearbyTrailsData = data.data || [];
                    nearbyCurrentPage = 1;
                } else {
                    nearbyTrailsData = [...nearbyTrailsData, ...(data.data || [])];
                }

                nearbyHasMore = data.has_more_pages || false;
                console.log('Trails found:', nearbyTrailsData.length);
                console.log('Has more pages:', nearbyHasMore);

                nearbyLoading.classList.add('hidden');
                nearbyContent.classList.remove('hidden');

                if (nearbyTrailsData.length === 0) {
                    console.log('No trails found - showing no results message');
                    nearbyGrid.innerHTML = '';
                    showMoreContainer.classList.add('hidden');
                    noNearbyTrails.classList.remove('hidden');

                    // Update the no results message with more helpful info
                    const noResultsMessage = document.querySelector('#no-nearby-trails p');
                    noResultsMessage.textContent = `No trails found within ${currentDistance}km of your location. Try expanding your search radius or check back later as we add more trails.`;
                } else {
                    console.log('Rendering trails...');
                    noNearbyTrails.classList.add('hidden');
                    renderNearbyTrails();
                    updateNearbyShowMore();
                }

            } catch (error) {
                console.error('=== Fetch Error ===');
                console.error('Error message:', error.message);
                console.error('Full error:', error);
                nearbyLoading.classList.add('hidden');

                // Show more specific error message
                let errorMessage = 'Failed to load nearby trails. ';
                if (error.message.includes('404')) {
                    errorMessage += 'API endpoint not found. Please contact support.';
                } else if (error.message.includes('500')) {
                    errorMessage += 'Server error. Please try again later.';
                } else if (error.message.includes('NetworkError') || error.message.includes('fetch')) {
                    errorMessage += 'Network connection problem. Please check your internet connection.';
                } else {
                    errorMessage += `Error: ${error.message}`;
                }

                showLocationError(errorMessage);
            }
        }

        // Helper function to get difficulty color classes
        function getDifficultyColor(difficulty) {
            const difficultyMap = {
                'Easy': 'green-600',
                'Beginner': 'green-600',
                'Moderate': 'yellow-600',
                'Intermediate': 'yellow-600',
                'Hard': 'orange-600',
                'Difficult': 'orange-600',
                'Very Hard': 'red-600',
                'Very_hard': 'red-600',
                'Expert': 'red-600'
            };
            return difficultyMap[difficulty] || 'gray-600';
        }

        // Render nearby trails in the grid
        function renderNearbyTrails() {
            const nearbyGrid = document.getElementById('nearby-trails-grid');

            nearbyGrid.innerHTML = nearbyTrailsData.map(trail => {
                const imageUrl = trail.images && trail.images.length > 0 ?
                    trail.images[0].url :
                    DEFAULT_TRAIL_IMAGE;

                const distance = trail.distance ? parseFloat(trail.distance).toFixed(1) : 'N/A';
                const difficultyColor = getDifficultyColor(trail.difficulty_level);

                return `
                    <div class="trail-card bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-200 cursor-pointer" 
                         onclick="openTrailModal(${trail.id})">
                        <div class="relative h-48 overflow-hidden">
                            <img src="${imageUrl}" 
                                 alt="${trail.name}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='${DEFAULT_TRAIL_IMAGE}'" />
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1">
                                <span class="text-sm font-medium text-gray-700">${distance}km away</span>
                            </div>
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1">
                                <div class="flex items-center space-x-1">
                                    <span class="iconify text-yellow-500" data-icon="heroicons:star-solid" style="font-size:0.875rem;"></span>
                                    <span class="text-sm font-medium text-gray-700">${trail.average_rating || 'N/A'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">${trail.name}</h3>
                            <p class="text-gray-600 mb-3 flex items-center">
                                <span class="iconify text-gray-400 mr-2" data-icon="heroicons:map-pin" style="font-size:1rem;"></span>
                                ${trail.location || 'Location not specified'}
                            </p>
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span class="flex items-center">
                                    <span class="iconify mr-1" data-icon="heroicons:clock" style="font-size:1rem;"></span>
                                    ${trail.estimated_duration || 'N/A'}
                                </span>
                                <span class="px-3 py-1 bg-${difficultyColor}/10 text-${difficultyColor} rounded-full text-xs font-medium">
                                    ${trail.difficulty_level || 'Unknown'}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Update show more button for nearby trails
        function updateNearbyShowMore() {
            const showMoreContainer = document.getElementById('nearby-show-more-container');
            const nearbyTrailsCount = document.getElementById('nearby-trails-count');

            if (nearbyHasMore) {
                showMoreContainer.classList.remove('hidden');
                nearbyTrailsCount.textContent = `Showing ${nearbyTrailsData.length} trails`;
            } else {
                showMoreContainer.classList.add('hidden');
            }
        }

        // Handle distance filter changes
        function handleDistanceFilter(distance) {
            currentDistance = distance;
            nearbyCurrentPage = 1;

            // Update active filter button
            document.querySelectorAll('.distance-filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-distance="${distance}"]`).classList.add('active');

            // Fetch trails with new distance
            fetchNearbyTrails(1);
        }

        // Initialize nearby trails on page load
        function initNearbyTrails() {
            // Show location permission request initially
            document.getElementById('location-permission').classList.remove('hidden');

            // Auto-trigger location request after a short delay
            setTimeout(() => {
                console.log('Auto-triggering location request...');
                requestLocation();
            }, 1000);

            // Event listeners
            document.getElementById('enable-location-btn').addEventListener('click', requestLocation);
            document.getElementById('retry-location-btn').addEventListener('click', requestLocation);

            // Manual location functionality
            document.getElementById('manual-location-btn').addEventListener('click', () => {
                document.getElementById('location-permission').classList.add('hidden');
                document.getElementById('location-error').classList.add('hidden');
                document.getElementById('manual-location-input').classList.remove('hidden');
            });

            document.getElementById('back-to-auto-btn').addEventListener('click', () => {
                document.getElementById('manual-location-input').classList.add('hidden');
                document.getElementById('location-permission').classList.remove('hidden');
            });

            document.getElementById('use-manual-location-btn').addEventListener('click', () => {
                const lat = parseFloat(document.getElementById('manual-lat').value);
                const lng = parseFloat(document.getElementById('manual-lng').value);

                if (isNaN(lat) || isNaN(lng)) {
                    alert('Please enter valid latitude and longitude values.');
                    return;
                }

                if (lat < -90 || lat > 90) {
                    alert('Latitude must be between -90 and 90.');
                    return;
                }

                if (lng < -180 || lng > 180) {
                    alert('Longitude must be between -180 and 180.');
                    return;
                }

                console.log('Using manual location:', lat, lng);
                userLocation = {
                    latitude: lat,
                    longitude: lng
                };

                document.getElementById('manual-location-input').classList.add('hidden');
                document.getElementById('nearby-trails-content').classList.remove('hidden');
                fetchNearbyTrails();
            });

            // Distance filter buttons
            document.querySelectorAll('.distance-filter-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const distance = parseInt(btn.dataset.distance);
                    handleDistanceFilter(distance);
                });
            });

            // Show more nearby trails button
            document.getElementById('nearby-show-more-btn').addEventListener('click', () => {
                nearbyCurrentPage++;
                fetchNearbyTrails(nearbyCurrentPage);
            });
        }

        // Dynamic per-pixel text blending based on trail card positions
        function initTextBlending() {
            const textElements = [
                document.getElementById('text-line-1'),
                document.getElementById('text-line-2'),
                document.getElementById('text-line-3')
            ];

            // Initialize all text elements to white on page load
            textElements.forEach(textElement => {
                if (textElement) {
                    textElement.style.background = 'none';
                    textElement.style.backgroundClip = 'initial';
                    textElement.style.webkitBackgroundClip = 'initial';
                    textElement.style.color = 'white';
                }
            });

            // Store previous states to prevent rapid switching
            let previousStates = new Map();

            // Track trail card positions and update text blending
            function updateTextBlending() {
                const trailCards = document.querySelectorAll('.hero-trail-card');

                textElements.forEach((textElement, index) => {
                    if (!textElement) return;

                    const textRect = textElement.getBoundingClientRect();
                    const maskOverlay = textElement.querySelector('.text-mask-overlay');

                    if (!maskOverlay) return;

                    let cardPositions = [];

                    trailCards.forEach(card => {
                        const cardRect = card.getBoundingClientRect();

                        // Check if card overlaps with text horizontally
                        const horizontalOverlap = !(cardRect.right < textRect.left || cardRect.left > textRect.right);

                        // Check if card is vertically near the text
                        const verticalDistance = Math.min(
                            Math.abs(cardRect.top - textRect.bottom),
                            Math.abs(cardRect.bottom - textRect.top),
                            cardRect.top < textRect.bottom && cardRect.bottom > textRect.top ? 0 : Infinity
                        );

                        if (horizontalOverlap && verticalDistance < 120) {
                            // Calculate the horizontal overlap area
                            const overlapStart = Math.max(cardRect.left, textRect.left);
                            const overlapEnd = Math.min(cardRect.right, textRect.right);

                            // Convert to percentage of text width
                            const startPercent = ((overlapStart - textRect.left) / textRect.width) * 100;
                            const endPercent = ((overlapEnd - textRect.left) / textRect.width) * 100;

                            cardPositions.push({
                                start: Math.max(0, startPercent),
                                end: Math.min(100, endPercent),
                                intensity: Math.max(0, 1 - (verticalDistance / 120))
                            });
                        }
                    });

                    if (cardPositions.length > 0) {
                        // Merge overlapping card positions for smooth blending
                        const mergedRanges = [];

                        // Sort card positions by start position
                        cardPositions.sort((a, b) => a.start - b.start);

                        for (let card of cardPositions) {
                            let merged = false;

                            // Try to merge with existing ranges
                            for (let range of mergedRanges) {
                                // Check if this card overlaps with existing range
                                if (card.start <= range.end + 10 && card.end >= range.start - 10) {
                                    // Merge the ranges
                                    range.start = Math.min(range.start, card.start);
                                    range.end = Math.max(range.end, card.end);
                                    range.intensity = Math.max(range.intensity, card.intensity);
                                    merged = true;
                                    break;
                                }
                            }

                            // If not merged, add as new range
                            if (!merged) {
                                mergedRanges.push({
                                    start: card.start,
                                    end: card.end,
                                    intensity: card.intensity
                                });
                            }
                        }

                        // Create gradient with all merged ranges
                        const gradientStops = [];
                        const featherSize = 8; // Slightly larger feather for smoother transitions

                        let currentPos = 0;

                        for (let i = 0; i < mergedRanges.length; i++) {
                            const range = mergedRanges[i];

                            // Add white area before this range
                            if (range.start > currentPos) {
                                if (currentPos === 0) {
                                    gradientStops.push(`white 0%`);
                                }
                                gradientStops.push(`white ${Math.max(0, range.start - featherSize)}%`);
                            }

                            // Add the colored range with soft edges using the new color
                            gradientStops.push(`rgba(248, 179, 72, ${range.intensity * 0.8}) ${range.start}%`);
                            gradientStops.push(`rgba(248, 179, 72, ${range.intensity}) ${Math.min(100, range.start + featherSize)}%`);
                            gradientStops.push(`rgba(248, 179, 72, ${range.intensity}) ${Math.max(0, range.end - featherSize)}%`);
                            gradientStops.push(`rgba(248, 179, 72, ${range.intensity * 0.8}) ${range.end}%`);

                            currentPos = range.end;
                        }

                        // Add white area after last range
                        if (currentPos < 100) {
                            gradientStops.push(`white ${Math.min(100, currentPos + featherSize)}%`);
                            gradientStops.push(`white 100%`);
                        }

                        // Apply gradient using background-clip for true text color change
                        const gradientBackground = `linear-gradient(90deg, ${gradientStops.join(', ')})`;

                        // Create a composite background: white base + teal overlay
                        textElement.style.background = `
                            ${gradientBackground},
                            linear-gradient(90deg, white 0%, white 100%)
                        `;
                        textElement.style.backgroundClip = 'text';
                        textElement.style.webkitBackgroundClip = 'text';
                        textElement.style.color = 'transparent';
                        textElement.style.transition = 'all 0.1s ease-out';

                        // Hide the overlay since we're using background-clip
                        maskOverlay.style.opacity = '0';
                    } else {
                        // No cards nearby, reset to normal white text
                        textElement.style.background = 'none';
                        textElement.style.backgroundClip = 'initial';
                        textElement.style.webkitBackgroundClip = 'initial';
                        textElement.style.color = 'white';
                        maskOverlay.style.background = 'transparent';
                        maskOverlay.style.opacity = '0';
                    }
                });
            }

            // Update blending on animation frame for smooth effect
            function animateBlending() {
                updateTextBlending();
                requestAnimationFrame(animateBlending);
            }

            // Start animation loop
            animateBlending();
        }

        function displayHeroTrails() {
            const container = document.querySelector('.trails-showcase-container');
            container.innerHTML = '';

            // Create trail cards for each position (9 total)
            for (let i = 1; i <= 9; i++) {
                const trailIndex = (i - 1) % heroTrailsData.length;
                const trail = heroTrailsData[trailIndex];

                if (trail) {
                    const trailCard = createHeroTrailCard(trail, i);
                    container.appendChild(trailCard);
                }
            }
        }

        function createHeroTrailCard(trail, position) {
            const card = document.createElement('div');
            card.className = `hero-trail-card hero-trail-${position}`;
            card.onclick = () => viewTrailDetails(trail.slug);

            const imageUrl = trail.featured_image || trail.image || DEFAULT_TRAIL_IMAGE;

            card.innerHTML = `
                <img src="${imageUrl}" alt="${trail.name}" class="hero-trail-image" onerror="this.src='${DEFAULT_TRAIL_IMAGE}'">
                <div class="hero-trail-content">
                    <h3 class="hero-trail-title">${trail.name}</h3>
                    <div class="hero-trail-location">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        ${trail.location || 'Location not specified'}
                    </div>
                </div>
            `;

            return card;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Small delay to ensure CSS animations are ready
            setTimeout(() => {
                initHeroTrailsShowcase();
                initNearbyTrails();
            }, 100);
        });

        async function searchTrails(query = '', category = '', filter = '', page = 1) {
            try {
                const params = new URLSearchParams();
                if (query) params.append('query', query);
                if (category) params.append('category', category);
                if (filter) params.append('filter', filter);
                params.append('page', page);
                params.append('limit', 9);

                const response = await fetch(`${API_BASE_URL}/trails/search-trails?${params.toString()}`);
                const data = await response.json();

                if (data.success) {
                    return data;
                } else {
                    console.error('Search failed:', data);
                    return {
                        success: false,
                        trails: [],
                        total: 0,
                        has_more: false
                    };
                }
            } catch (error) {
                console.error('Search error:', error);
                return {
                    success: false,
                    trails: [],
                    total: 0,
                    has_more: false
                };
            }
        }

        // (quickSearch now defined globally at top)

        function createTrailCard(trail) {
            const difficultyColors = {
                'Easy': 'bg-green-100 text-green-800',
                'Beginner': 'bg-green-100 text-green-800',
                'Moderate': 'bg-yellow-100 text-yellow-800',
                'Intermediate': 'bg-yellow-100 text-yellow-800',
                'Hard': 'bg-orange-100 text-orange-800',
                'Difficult': 'bg-orange-100 text-orange-800',
                'Very Hard': 'bg-red-100 text-red-800',
                'Very_hard': 'bg-red-100 text-red-800',
                'Expert': 'bg-red-100 text-red-800'
            };

            const stars = Math.floor(trail.rating);
            const emptyStars = 5 - stars;

            return `
                <div class="trail-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div class="relative">
                    <img src="${trail.image ? trail.image : DEFAULT_TRAIL_IMAGE}" alt="${trail.name}" class="w-full h-48 object-cover" 
                        onerror="this.src='${DEFAULT_TRAIL_IMAGE}'">
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${difficultyColors[trail.difficulty] || 'bg-gray-100 text-gray-800'}">
                                ${trail.difficulty}
                            </span>
                        </div>
                        ${trail.rating > 0 ? `
                        <div class="absolute top-4 left-4 bg-white bg-opacity-90 rounded-full px-2 py-1">
                            <div class="flex items-center space-x-1">
                                <span class="iconify text-yellow-400 text-sm" data-icon="solar:star-bold"></span>
                                <span class="text-xs font-semibold text-gray-800">${trail.rating.toFixed(1)}</span>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl mb-2 text-gray-800">${trail.name}</h3>
                        ${trail.mountain_name ? `<p class="text-sm text-gray-500 mb-2">${trail.mountain_name}</p>` : ''}
                        <p class="text-gray-600 mb-4 flex items-center">
                            <span class="iconify mr-1" data-icon="heroicons:map-pin" style="font-size:1rem;"></span>
                            ${trail.location}
                        </p>
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                            <div class="flex items-center text-gray-600">
                                <span class="iconify mr-1" data-icon="heroicons:map" style="font-size:1rem;"></span>
                                ${trail.distance}
                            </div>
                            <div class="flex items-center text-gray-600">
                                <span class="iconify mr-1" data-icon="heroicons:clock" style="font-size:1rem;"></span>
                                ${trail.duration}
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex text-yellow-400">
                                    ${'★'.repeat(stars)}${'☆'.repeat(emptyStars)}
                                </div>
                                <span class="ml-2 text-gray-600 text-sm">${trail.rating.toFixed(1)} (${trail.review_count})</span>
                            </div>
                            <button onclick="viewTrailDetails('${trail.slug}')" 
                                    class="bg-[#336d66] text-white px-4 py-2 rounded-lg hover:bg-[#20b6d2] transition-colors duration-300 text-sm font-medium">
                                View Details
                            </button>
                        </div>
                        ${trail.organization ? `<p class="text-xs text-gray-500 mt-2">by ${trail.organization}</p>` : ''}
                    </div>
                </div>
            `;
        }

        function viewTrailDetails(slug) {
            // Find trail data from current results
            const trail = trailsData.find(t => t.slug === slug);
            if (trail) {
                // Fetch complete trail details from API
                fetchTrailDetails(trail.slug);
            } else {
                // Show modal with loading state and then fetch details
                showModalLoading(true);
                fetchTrailDetails(slug);
            }
        }

        async function fetchTrailDetails(slug) {
            try {
                // Show loading state in modal
                showModalLoading(true);
                console.log('Fetching trail details for slug:', slug);

                // Fetch trail details from API
                const response = await fetch(`${API_BASE_URL}/trails/${slug}/details`);
                const data = await response.json();
                console.log('API response:', data);

                // Check if API returned valid data
                if (!data || !data.id) {
                    throw new Error('Trail details not available');
                }

                // Open modal with trail data
                openTrailModal(data, data.images || [], [], null);

            } catch (error) {
                console.error('Error fetching trail details:', error);
                // Show modal with minimal trail info and error message
                openTrailModal({
                    trail_name: 'Trail Details',
                    description: 'Trail details could not be loaded at this time.',
                    difficulty_level: 'Unknown',
                    rating: 0,
                    distance: 'N/A',
                    location: 'Location not available'
                });
                showModalError(error.message || 'Failed to load trail details.');
            } finally {
                showModalLoading(false);
            }
        }

        function openTrailModal(trail, images = [], reviews = [], weather = null) {
            const modal = document.getElementById('trail-details-modal');
            if (!modal) return;

            // Define difficulty colors
            const difficultyColors = {
                'Easy': 'bg-green-100 text-green-800',
                'easy': 'bg-green-100 text-green-800',
                'Beginner': 'bg-green-100 text-green-800',
                'beginner': 'bg-green-100 text-green-800',
                'Moderate': 'bg-yellow-100 text-yellow-800',
                'moderate': 'bg-yellow-100 text-yellow-800',
                'Intermediate': 'bg-yellow-100 text-yellow-800',
                'intermediate': 'bg-yellow-100 text-yellow-800',
                'Hard': 'bg-orange-100 text-orange-800',
                'hard': 'bg-orange-100 text-orange-800',
                'Difficult': 'bg-orange-100 text-orange-800',
                'difficult': 'bg-orange-100 text-orange-800',
                'Very Hard': 'bg-red-100 text-red-800',
                'very hard': 'bg-red-100 text-red-800',
                'Very_hard': 'bg-red-100 text-red-800',
                'Expert': 'bg-red-100 text-red-800',
                'expert': 'bg-red-100 text-red-800'
            };

            // Update modal content with real data
            document.getElementById('modal-trail-name').textContent = trail.trail_name || trail.name || 'Trail Details';

            // Update location header
            const mountainName = trail.mountain_name || trail.mountain || '';
            const location = trail.location ? (trail.location.full_name || trail.location.name) : '';
            const locationText = mountainName && location ? `${mountainName} • ${location}` :
                mountainName || location || 'Location not specified';
            document.getElementById('modal-trail-location-header').textContent = locationText;

            let mainImage = '';
            if (images && images.length > 0 && images[0].url) {
                mainImage = images[0].url;
            } else if (trail.images && trail.images.length > 0 && trail.images[0].url) {
                mainImage = trail.images[0].url;
            } else if (trail.featured_image) {
                mainImage = trail.featured_image;
            } else if (trail.image) {
                mainImage = trail.image;
            } else {
                mainImage = DEFAULT_TRAIL_IMAGE;
            }
            document.getElementById('modal-trail-image').src = mainImage;
            document.getElementById('modal-trail-rating').textContent = (trail.average_rating || trail.rating || 0).toFixed(1);
            document.getElementById('modal-trail-distance').textContent = trail.length ? `${trail.length} km` : (trail.distance || 'N/A');
            document.getElementById('modal-trail-duration').textContent = trail.estimated_time_formatted || trail.estimated_duration || trail.duration || 'N/A';
            document.getElementById('modal-trail-location').textContent = trail.location ? (trail.location.full_name || trail.location.name) : (trail.mountain_name || 'Location not specified');
            document.getElementById('modal-trail-elevation').textContent = trail.elevation_gain ? `${trail.elevation_gain}m` : (trail.elevation || 'N/A');
            document.getElementById('modal-trail-description').textContent = trail.description || trail.summary || 'Trail description not available.';
            document.getElementById('modal-review-count').textContent = trail.total_reviews || trail.reviews_count || trail.review_count || reviews.length || 0;
            document.getElementById('modal-completed-count').textContent = trail.completions_count || trail.completed_count || 0;
            document.getElementById('modal-difficulty-text').textContent = trail.difficulty_label || trail.difficulty_level || trail.difficulty || 'Moderate';

            // Update difficulty badge
            const difficultyBadge = document.getElementById('modal-difficulty-badge');
            const difficulty = trail.difficulty_label || trail.difficulty_level || trail.difficulty || 'Moderate';
            difficultyBadge.textContent = difficulty;
            difficultyBadge.className = `px-3 py-1 rounded-full text-sm font-medium ${difficultyColors[difficulty] || 'bg-gray-100 text-gray-800'}`;

            // Update weather data (use real weather if available, otherwise generate sample)
            const weatherData = weather || generateWeatherData();
            updateWeatherDisplay(weatherData);

            // Update image navigation for main trail image
            updateModalImageNavigation(images, trail);

            // Update reviews section
            updateReviewsSection(reviews);

            // Add organization info if available
            updateOrganizationInfo(trail.organization);

            // Update trail features/amenities
            updateTrailFeatures(trail.features || trail.amenities);

            // Update trail map
            updateTrailMap(trail.coordinates);

            // Show modal
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Helper functions for modal
        function showModalLoading(show) {
            const loadingOverlay = document.getElementById('modal-loading-overlay');
            if (loadingOverlay) {
                if (show) {
                    loadingOverlay.classList.remove('hidden');
                } else {
                    loadingOverlay.classList.add('hidden');
                }
            }
        }

        function showModalError(message) {
            const errorMsg = document.getElementById('modal-error-message');
            if (errorMsg) {
                errorMsg.textContent = message;
                errorMsg.classList.remove('hidden');
            }
        }

        function generateWeatherData() {
            const conditions = ['Sunny', 'Partly Cloudy', 'Cloudy', 'Light Rain'];
            const temp = Math.floor(Math.random() * 25) + 10; // 10-35°C
            const humidity = Math.floor(Math.random() * 40) + 30; // 30-70%
            const wind = Math.floor(Math.random() * 20) + 5; // 5-25 km/h
            const condition = conditions[Math.floor(Math.random() * conditions.length)];

            return {
                current: {
                    temperature: temp,
                    condition: condition,
                    wind_speed: wind,
                    humidity: humidity
                }
            };
        }

        function updateWeatherDisplay(weather) {
            if (weather && weather.current) {
                document.getElementById('modal-weather-temp').textContent = `${weather.current.temperature}°C`;
                document.getElementById('modal-weather-condition').textContent = weather.current.condition;
                document.getElementById('modal-weather-wind').textContent = `${weather.current.wind_speed} km/h`;
                document.getElementById('modal-weather-humidity').textContent = `${weather.current.humidity}%`;
            } else {
                // Use generated weather data as fallback
                const generatedWeather = generateWeatherData();
                document.getElementById('modal-weather-temp').textContent = `${generatedWeather.current.temperature}°C`;
                document.getElementById('modal-weather-condition').textContent = generatedWeather.current.condition;
                document.getElementById('modal-weather-wind').textContent = `${generatedWeather.current.wind_speed} km/h`;
                document.getElementById('modal-weather-humidity').textContent = `${generatedWeather.current.humidity}%`;
            }
        }

        function updateReviewsSection(reviews) {
            const reviewsContainer = document.getElementById('modal-reviews-section');
            if (!reviewsContainer) return;

            if (reviews && reviews.length > 0) {
                const recentReviews = reviews.slice(0, 3);
                reviewsContainer.innerHTML = `
                    <h3 class="text-lg font-bold mb-3 text-gray-800">Recent Reviews</h3>
                    <div class="space-y-4">
                        ${recentReviews.map(review => `
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <img src="${review.user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(review.user.name)}" 
                                             alt="${review.user.name}" class="w-8 h-8 rounded-full">
                                        <span class="font-medium text-sm">${review.user.name}</span>
                                    </div>
                                    <div class="flex text-yellow-400">
                                        ${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm">${review.review_text}</p>
                                <p class="text-xs text-gray-400 mt-2">${new Date(review.created_at).toLocaleDateString()}</p>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                reviewsContainer.innerHTML = `
                    <h3 class="text-lg font-bold mb-3 text-gray-800">Reviews</h3>
                    <p class="text-gray-500 text-sm">No reviews yet. Be the first to review this trail!</p>
                `;
            }
        }

        function updateOrganizationInfo(organization) {
            const orgContainer = document.getElementById('modal-organization-info');
            if (!orgContainer) return;

            if (organization) {
                orgContainer.innerHTML = `
                    <div class="bg-white border border-gray-200 rounded-2xl p-4">
                        <h4 class="font-bold text-gray-800 mb-2">Trail Organizer</h4>
                        <div class="flex items-center space-x-3">
                            <img src="${organization.logo || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(organization.name)}" 
                                 alt="${organization.name}" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="font-medium text-gray-800">${organization.name}</p>
                                <p class="text-xs text-gray-500">${organization.followers_count || 0} followers</p>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        function updateTrailFeatures(features) {
            const featuresContainer = document.getElementById('modal-trail-features');
            if (!featuresContainer || !features) return;

            const featureIcons = {
                'Dog Friendly': 'mdi:dog',
                'Water Source': 'mdi:water',
                'Restrooms': 'mdi:toilet',
                'Parking': 'mdi:parking',
                'Camping': 'mdi:tent',
                'Views': 'mdi:mountain',
                'Wildlife': 'mdi:paw',
                'Waterfall': 'mdi:waterfall'
            };

            featuresContainer.innerHTML = `
                <h3 class="text-lg font-bold mb-3 text-gray-800">Trail Features</h3>
                <div class="flex flex-wrap gap-2">
                    ${features.map(feature => `
                        <div class="flex items-center space-x-1 bg-gray-100 rounded-full px-3 py-1">
                            <span class="iconify text-[#336d66]" data-icon="${featureIcons[feature] || 'mdi:check'}" style="font-size:1rem;"></span>
                            <span class="text-sm text-gray-700">${feature}</span>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        function updateTrailMap(coordinates) {
            const mapContainer = document.getElementById('trail-route-map');
            const placeholder = document.getElementById('trail-map-placeholder');

            if (!mapContainer) return;

            if (coordinates && coordinates.length > 0) {
                // Hide placeholder and show map
                if (placeholder) placeholder.style.display = 'none';

                // Check if Google Maps is loaded
                if (typeof google === 'undefined' || !google.maps) {
                    console.log('Google Maps not loaded yet, showing placeholder');
                    showMapPlaceholder(mapContainer, coordinates);
                    return;
                }

                try {
                    // Parse coordinates if they're in string format
                    let trailCoords = coordinates;
                    if (typeof coordinates === 'string') {
                        trailCoords = JSON.parse(coordinates);
                    }

                    // Ensure coordinates are in the correct format
                    if (!Array.isArray(trailCoords) || trailCoords.length === 0) {
                        console.log('Invalid coordinates format');
                        showMapPlaceholder(mapContainer, coordinates);
                        return;
                    }

                    // Convert coordinates to Google Maps LatLng format
                    const path = trailCoords.map(coord => {
                        if (coord.lat && coord.lng) {
                            return {
                                lat: parseFloat(coord.lat),
                                lng: parseFloat(coord.lng)
                            };
                        } else if (coord.latitude && coord.longitude) {
                            return {
                                lat: parseFloat(coord.latitude),
                                lng: parseFloat(coord.longitude)
                            };
                        } else if (Array.isArray(coord) && coord.length >= 2) {
                            return {
                                lat: parseFloat(coord[0]),
                                lng: parseFloat(coord[1])
                            };
                        }
                        return null;
                    }).filter(coord => coord !== null);

                    if (path.length === 0) {
                        console.log('No valid coordinates found');
                        showMapPlaceholder(mapContainer, coordinates);
                        return;
                    }

                    // Calculate center point
                    const bounds = new google.maps.LatLngBounds();
                    path.forEach(coord => bounds.extend(coord));
                    const center = bounds.getCenter();

                    // Create map
                    const map = new google.maps.Map(mapContainer, {
                        center: center,
                        zoom: 14,
                        mapTypeId: google.maps.MapTypeId.HYBRID,
                        disableDefaultUI: true,
                        zoomControl: true,
                        styles: [{
                            featureType: "poi",
                            elementType: "labels",
                            stylers: [{
                                visibility: "off"
                            }]
                        }]
                    });

                    // Create trail polyline
                    const trailPath = new google.maps.Polyline({
                        path: path,
                        geodesic: true,
                        strokeColor: '#9ACD32',
                        strokeOpacity: 1.0,
                        strokeWeight: 3
                    });

                    trailPath.setMap(map);

                    // Add start marker
                    if (path.length > 0) {
                        new google.maps.Marker({
                            position: path[0],
                            map: map,
                            title: 'Trail Start',
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 8,
                                fillColor: '#20b6d2',
                                fillOpacity: 1,
                                strokeColor: '#ffffff',
                                strokeWeight: 2
                            }
                        });
                    }

                    // Add end marker
                    if (path.length > 1) {
                        new google.maps.Marker({
                            position: path[path.length - 1],
                            map: map,
                            title: 'Trail End',
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 8,
                                fillColor: '#e3a746',
                                fillOpacity: 1,
                                strokeColor: '#ffffff',
                                strokeWeight: 2
                            }
                        });
                    }

                    // Fit map to trail bounds
                    map.fitBounds(bounds);

                    // Store map instance for potential cleanup
                    window.trailMaps['modal'] = map;

                } catch (error) {
                    console.error('Error creating trail map:', error);
                    showMapPlaceholder(mapContainer, coordinates);
                }

            } else {
                // Show placeholder for no coordinates
                if (placeholder) {
                    placeholder.style.display = 'flex';
                    placeholder.innerHTML = `
                        <div class="text-center">
                            <span class="iconify text-gray-400 mb-2" data-icon="heroicons:map" style="font-size:2rem;"></span>
                            <p class="text-sm text-gray-600">No trail path available</p>
                        </div>
                    `;
                }
                mapContainer.innerHTML = '';
            }
        }

        function showMapPlaceholder(container, coordinates) {
            const coordCount = Array.isArray(coordinates) ? coordinates.length :
                (typeof coordinates === 'string' ? JSON.parse(coordinates).length : 0);

            container.innerHTML = `
                <div class="h-full bg-gradient-to-br from-green-100 to-blue-100 flex items-center justify-center rounded-lg">
                    <div class="text-center">
                        <span class="iconify text-[#336d66] mb-2" data-icon="heroicons:map" style="font-size:2rem;"></span>
                        <p class="text-sm text-gray-600 mb-1">Trail route available</p>
                        <p class="text-xs text-gray-500">${coordCount} GPS points recorded</p>
                        <p class="text-xs text-gray-400 mt-2">Loading map...</p>
                    </div>
                </div>
            `;
        }

        function generatePhotoGallery(trail) {
            const gallery = document.getElementById('modal-photo-gallery');
            if (!gallery) return;

            // Generate sample photos for demo in 2x2 grid
            const samplePhotos = [
                DEFAULT_TRAIL_IMAGE,
                DEFAULT_TRAIL_IMAGE,
                DEFAULT_TRAIL_IMAGE,
                DEFAULT_TRAIL_IMAGE
            ];

            gallery.innerHTML = samplePhotos.map((photo, index) => `
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:scale-105 transition-transform">
                    <img src="${photo}" alt="Trail Photo ${index + 1}" class="w-full h-full object-cover" loading="lazy">
                </div>
            `).join('');
        }

        function openImageInFullscreen(imageUrl, caption) {
            // Simple fullscreen image viewer - could be enhanced
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4';
            overlay.onclick = () => overlay.remove();

            const img = document.createElement('img');
            img.src = imageUrl;
            img.className = 'max-w-full max-h-full object-contain';
            img.alt = caption;

            overlay.appendChild(img);
            document.body.appendChild(overlay);
        }

        function closeTrailModal() {
            const modal = document.getElementById('trail-details-modal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // Global variables for image navigation
        let currentModalImages = [];
        let currentModalImageIndex = 0;

        function updateModalImageNavigation(images, trail) {
            currentModalImages = [];
            currentModalImageIndex = 0;

            // Prepare images array
            if (images && images.length > 0) {
                currentModalImages = images.map(img => img.url || img);
            } else if (trail.images && trail.images.length > 0) {
                currentModalImages = trail.images.map(img => img.url || img);
            } else if (trail.featured_image) {
                currentModalImages = [trail.featured_image];
            } else if (trail.image) {
                currentModalImages = [trail.image];
            } else {
                currentModalImages = [DEFAULT_TRAIL_IMAGE];
            }

            // Update navigation visibility
            const prevBtn = document.getElementById('prev-image-btn');
            const nextBtn = document.getElementById('next-image-btn');
            const counter = document.getElementById('image-counter');

            if (currentModalImages.length > 1) {
                if (prevBtn) {
                    prevBtn.classList.remove('opacity-0', 'invisible');
                    prevBtn.classList.add('opacity-100', 'visible');
                }
                if (nextBtn) {
                    nextBtn.classList.remove('opacity-0', 'invisible');
                    nextBtn.classList.add('opacity-100', 'visible');
                }
                if (counter) {
                    counter.classList.remove('opacity-0', 'invisible');
                    counter.classList.add('opacity-100', 'visible');
                }
                updateModalImageCounter();
            } else {
                if (prevBtn) {
                    prevBtn.classList.add('opacity-0', 'invisible');
                    prevBtn.classList.remove('opacity-100', 'visible');
                }
                if (nextBtn) {
                    nextBtn.classList.add('opacity-0', 'invisible');
                    nextBtn.classList.remove('opacity-100', 'visible');
                }
                if (counter) {
                    counter.classList.add('opacity-0', 'invisible');
                    counter.classList.remove('opacity-100', 'visible');
                }
            }

            // Set initial image
            if (currentModalImages.length > 0) {
                document.getElementById('modal-trail-image').src = currentModalImages[0];
            }
        }

        function nextModalImage() {
            if (currentModalImages.length <= 1) return;
            currentModalImageIndex = (currentModalImageIndex + 1) % currentModalImages.length;
            document.getElementById('modal-trail-image').src = currentModalImages[currentModalImageIndex];
            updateModalImageCounter();
        }

        function previousModalImage() {
            if (currentModalImages.length <= 1) return;
            currentModalImageIndex = currentModalImageIndex === 0 ? currentModalImages.length - 1 : currentModalImageIndex - 1;
            document.getElementById('modal-trail-image').src = currentModalImages[currentModalImageIndex];
            updateModalImageCounter();
        }

        function updateModalImageCounter() {
            const currentNum = document.getElementById('current-image-num');
            const totalNum = document.getElementById('total-images');
            if (currentNum && totalNum) {
                currentNum.textContent = currentModalImageIndex + 1;
                totalNum.textContent = currentModalImages.length;
            }
        }

        function showTrailPreview() {
            alert('Trail route preview would show an interactive map with the hiking path. Login for full GPS tracking and navigation features.');
        }

        function showModalLoading(show) {
            const loadingOverlay = document.getElementById('modal-loading-overlay');
            if (loadingOverlay) {
                if (show) {
                    loadingOverlay.classList.remove('hidden');
                } else {
                    loadingOverlay.classList.add('hidden');
                }
            }
        }

        function showTrailError(message) {
            const errorContainer = document.getElementById('modal-error-message');
            if (errorContainer) {
                errorContainer.textContent = message;
                errorContainer.classList.remove('hidden');
                setTimeout(() => {
                    errorContainer.classList.add('hidden');
                }, 5000);
            }
        }

        function openImageInFullscreen(imageUrl, caption) {
            // Create fullscreen image viewer
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4';
            overlay.innerHTML = `
                <div class="relative max-w-4xl max-h-full">
                    <img src="${imageUrl}" alt="${caption}" class="max-w-full max-h-full object-contain">
                    <button onclick="this.parentElement.parentElement.remove()" 
                            class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <div class="absolute bottom-4 left-4 right-4 text-center">
                        <p class="text-white bg-black bg-opacity-50 rounded px-4 py-2">${caption}</p>
                    </div>
                </div>
            `;

            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.remove();
                }
            });

            document.body.appendChild(overlay);
        }

        function closeTrailModal() {
            const modal = document.getElementById('trail-details-modal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('trail-details-modal');
            if (e.target === modal) {
                closeTrailModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTrailModal();
            }
        });

        function showResults(trails, query, category = '', filter = '', hasMore = false, total = 0) {
            const searchResults = document.getElementById('search-results');
            const resultsContainer = document.getElementById('results-container');
            const noResults = document.getElementById('no-results');
            const defaultCards = document.getElementById('default-cards');
            const resultsTitle = document.getElementById('results-title');
            const resultsSubtitle = document.getElementById('results-subtitle');
            const showMoreContainer = document.getElementById('show-more-container');
            const trailsCountInfo = document.getElementById('trails-count-info');
            const resultsHeader = resultsTitle.parentElement; // The div containing title and subtitle

            // Hide default cards and show results section
            defaultCards.classList.add('hidden');
            searchResults.classList.remove('hidden');

            if (trails.length > 0) {
                resultsContainer.classList.remove('hidden');
                noResults.classList.add('hidden');
                resultsHeader.classList.remove('hidden'); // Show the title/subtitle area

                // Update title and subtitle
                resultsTitle.textContent = `Found ${total} trail${total !== 1 ? 's' : ''}`;

                let subtitle = '';
                if (query && category) {
                    const categoryDisplayNames = {
                        'beginner': 'Beginner Trails',
                        'popular': 'Popular Trails',
                        'challenging': 'Challenging Trails',
                        'scenic': 'Scenic Views'
                    };
                    subtitle = `Matching "${query}" in ${categoryDisplayNames[category] || category}`;
                } else if (query && filter) {
                    const filterText = {
                        'popular': 'Most Popular Trails',
                        'newest': 'Newest Trails',
                        'shortest': 'Shortest Routes',
                        'longest': 'Longest Routes'
                    };
                    subtitle = `Matching "${query}" - ${filterText[filter] || filter}`;
                } else if (query) {
                    subtitle = `Matching "${query}"`;
                } else if (category) {
                    const categoryDisplayNames = {
                        'beginner': 'Beginner Trails',
                        'popular': 'Popular Trails',
                        'challenging': 'Challenging Trails',
                        'scenic': 'Scenic Views'
                    };
                    subtitle = categoryDisplayNames[category] || `Category: ${category}`;
                } else if (filter) {
                    const filterText = {
                        'popular': 'Most Popular Trails',
                        'newest': 'Newest Trails',
                        'shortest': 'Shortest Routes',
                        'longest': 'Longest Routes'
                    };
                    subtitle = filterText[filter] || 'Filtered Trails';
                } else {
                    subtitle = 'All available trails';
                }
                resultsSubtitle.textContent = subtitle;

                // Display trail cards
                resultsContainer.innerHTML = trails.map(trail => createTrailCard(trail)).join('');

                // Show/hide "Show More" button
                if (hasMore) {
                    showMoreContainer.classList.remove('hidden');
                    trailsCountInfo.textContent = `Showing ${trails.length} of ${total} trails`;
                } else {
                    showMoreContainer.classList.add('hidden');
                }
            } else {
                // No results found - hide title area and show styled no-results section
                resultsContainer.classList.add('hidden');
                showMoreContainer.classList.add('hidden');
                resultsHeader.classList.add('hidden'); // Hide the title/subtitle area
                noResults.classList.remove('hidden'); // Show the styled no-results section
            }
        }

        function hideResults() {
            const searchResults = document.getElementById('search-results');
            const defaultCards = document.getElementById('default-cards');

            searchResults.classList.add('hidden');
            defaultCards.classList.remove('hidden');
        }

        function clearSearch() {
            const searchInput = document.getElementById('trail-search-input');
            const filterSelect = document.getElementById('trail-filter');
            searchInput.value = '';
            filterSelect.value = '';
            currentQuery = '';
            currentFilter = '';
            currentPage = 1;
            trailsData = [];
            hideResults();
        }

        function showLoading() {
            const loadingState = document.getElementById('loading-state');
            const defaultCards = document.getElementById('default-cards');
            const searchResults = document.getElementById('search-results');

            if (loadingState) {
                defaultCards.classList.add('hidden');
                searchResults.classList.add('hidden');
                loadingState.classList.remove('hidden');
            }
        }

        function hideLoading() {
            const loadingState = document.getElementById('loading-state');
            if (loadingState) {
                loadingState.classList.add('hidden');
            }
        }

        async function performSearch(query = '', category = '', filter = '', page = 1) {
            // Clean up the query
            query = query.trim();

            if (!query && !category && !filter && page === 1) {
                hideResults();
                return;
            }

            if (page === 1) {
                showLoading();
            }

            try {
                // Update current search parameters
                currentQuery = query;
                currentFilter = filter;
                currentPage = page;

                const result = await searchTrails(query, category, filter, page);

                if (page === 1) {
                    hideLoading();
                    trailsData = result.trails;
                } else {
                    // Append new results for "Show More"
                    trailsData = trailsData.concat(result.trails);
                }

                hasMoreTrails = result.has_more;
                showResults(trailsData, query, category, filter, hasMoreTrails, result.total);
            } catch (error) {
                hideLoading();
                console.error('Search failed:', error);
                showResults([], query, category, filter, false, 0);
            }
        }

        function loadMoreTrails() {
            if (hasMoreTrails) {
                // Use the current search query and filter for loading more trails
                const currentSearchQuery = document.getElementById('trail-search-input').value.trim();
                performSearch(currentSearchQuery, '', currentFilter, currentPage + 1);
            }
        }

        // Search functionality
        document.getElementById('search-btn').addEventListener('click', function() {
            const query = document.getElementById('trail-search-input').value.trim();
            const filter = document.getElementById('trail-filter').value;
            performSearch(query, '', filter, 1);
        });

        // Search on Enter key
        document.getElementById('trail-search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                const filter = document.getElementById('trail-filter').value;
                performSearch(query, '', filter, 1);
            }
        });

        // Filter dropdown change
        document.getElementById('trail-filter').addEventListener('change', function() {
            const query = document.getElementById('trail-search-input').value.trim();
            const filter = this.value;
            // If there's a search query, search with the filter applied
            // If no search query, apply filter to all trails
            performSearch(query, '', filter, 1);
        });

        // Clear search when input is empty
        document.getElementById('trail-search-input').addEventListener('input', function() {
            const query = this.value.trim();
            const filter = document.getElementById('trail-filter').value;

            // If both search and filter are empty, show default cards
            if (query === '' && !filter) {
                hideResults();
            }
            // If search is empty but filter exists, show filtered results
            else if (query === '' && filter) {
                performSearch('', '', filter, 1);
            }
        });

        // Show More button
        document.getElementById('show-more-btn').addEventListener('click', loadMoreTrails);

        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu if open
                    document.getElementById('mobile-menu').classList.add('hidden');
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.nav-container');
            if (window.scrollY > 100) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Initialize page - keep default categories visible
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded - keeping default category cards visible');
            // Don't auto-search, let users choose their category
        });

        // Feature Tutorial Modal Functions
        function openFeatureTutorial(featureType) {
            const modal = document.getElementById(featureType + '-modal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling

                // Reset any previous animations
                resetTutorialAnimations(modal);

                // Start tutorial animations
                setTimeout(() => {
                    startTutorialAnimations(modal);
                }, 300);
            }
        }

        function closeFeatureTutorial() {
            const activeModal = document.querySelector('.feature-tutorial-modal.active');
            if (activeModal) {
                activeModal.classList.remove('active');
                document.body.style.overflow = ''; // Restore scrolling

                // Stop any running animations
                stopTutorialAnimations(activeModal);
            }
        }

        function resetTutorialAnimations(modal) {
            // Reset progress indicators
            const progressDots = modal.querySelectorAll('.progress-dot');
            progressDots.forEach((dot, index) => {
                dot.classList.toggle('active', index === 0);
            });

            // Reset any demo states
            const demoElements = modal.querySelectorAll('.demo-button');
            demoElements.forEach(button => {
                button.textContent = button.textContent.replace('Running...', 'See AI in Action');
                button.disabled = false;
            });
        }

        function startTutorialAnimations(modal) {
            // Animate tutorial steps with staggered entrance
            const steps = modal.querySelectorAll('.tutorial-step');
            steps.forEach((step, index) => {
                step.style.opacity = '0';
                step.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    step.style.transition = 'all 0.6s ease';
                    step.style.opacity = '1';
                    step.style.transform = 'translateY(0)';
                }, index * 200);
            });

            // Start cycling through progress indicators
            let currentStep = 0;
            const totalSteps = modal.querySelectorAll('.tutorial-step').length;
            const progressDots = modal.querySelectorAll('.progress-dot');

            if (progressDots.length > 0) {
                const progressInterval = setInterval(() => {
                    currentStep = (currentStep + 1) % totalSteps;

                    progressDots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentStep);
                    });
                }, 3000);

                // Store interval for cleanup
                modal.setAttribute('data-progress-interval', progressInterval);
            }
        }

        function stopTutorialAnimations(modal) {
            // Clear any intervals
            const progressInterval = modal.getAttribute('data-progress-interval');
            if (progressInterval) {
                clearInterval(parseInt(progressInterval));
                modal.removeAttribute('data-progress-interval');
            }
        }

        // Interactive Demo Functions
        function animateRecommendationDemo() {
            const button = event.target;
            const originalText = button.textContent;

            button.textContent = 'Analyzing...';
            button.disabled = true;

            // Simulate AI processing
            setTimeout(() => {
                button.textContent = 'Match Found!';
                button.style.background = 'linear-gradient(135deg, #10b981, #059669)';

                setTimeout(() => {
                    button.textContent = originalText;
                    button.style.background = '';
                    button.disabled = false;
                }, 2000);
            }, 1500);
        }

        function demonstrateEmergencyFeature() {
            const button = event.target;
            const originalContent = button.innerHTML;

            button.innerHTML = '<span class="iconify mr-2 animate-pulse" data-icon="heroicons:exclamation-triangle"></span>Emergency Activated!';
            button.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';

            setTimeout(() => {
                button.innerHTML = '<span class="iconify mr-2" data-icon="heroicons:check-circle"></span>Help Dispatched';
                button.style.background = 'linear-gradient(135deg, #10b981, #059669)';

                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.style.background = '';
                }, 2000);
            }, 2000);
        }

        function simulateOfflineDemo() {
            const button = event.target;
            const container = button.closest('.interactive-demo');
            const originalContent = button.textContent;

            button.textContent = 'Simulating...';
            button.disabled = true;

            // Create demo overlay
            const demoOverlay = document.createElement('div');
            demoOverlay.className = 'absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center rounded-lg';
            demoOverlay.innerHTML = `
                <div class="text-center text-white">
                    <div class="animate-spin w-8 h-8 border-4 border-white border-t-transparent rounded-full mx-auto mb-4"></div>
                    <p class="font-semibold">Testing Offline Mode...</p>
                    <p class="text-sm opacity-75">GPS: Active | Maps: Downloaded</p>
                </div>
            `;

            container.style.position = 'relative';
            container.appendChild(demoOverlay);

            setTimeout(() => {
                demoOverlay.innerHTML = `
                    <div class="text-center text-white">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="iconify" data-icon="heroicons:check"></span>
                        </div>
                        <p class="font-semibold">Offline Mode Success!</p>
                        <p class="text-sm opacity-75">Navigation working without internet</p>
                    </div>
                `;

                setTimeout(() => {
                    container.removeChild(demoOverlay);
                    button.textContent = originalContent;
                    button.disabled = false;
                }, 2000);
            }, 3000);
        }

        function demonstrateSafetyMonitoring() {
            const button = event.target;
            const statusContainer = button.previousElementSibling;
            const originalContent = button.textContent;

            button.textContent = 'Monitoring...';
            button.disabled = true;

            // Simulate real-time updates
            const updates = [{
                    time: '4 min ago',
                    status: 'Active',
                    contacts: '3 notified'
                },
                {
                    time: '3 min ago',
                    status: 'Active',
                    contacts: '4 notified'
                },
                {
                    time: '2 min ago',
                    status: 'Active',
                    contacts: '5 notified'
                },
                {
                    time: 'Just now',
                    status: 'Active',
                    contacts: '6 notified'
                }
            ];

            let updateIndex = 0;
            const updateInterval = setInterval(() => {
                if (updateIndex < updates.length) {
                    const update = updates[updateIndex];
                    const timeElement = statusContainer.querySelector('div:nth-child(2) span:last-child');
                    const contactsElement = statusContainer.querySelector('div:nth-child(4) span:last-child');

                    timeElement.textContent = update.time;
                    contactsElement.textContent = update.contacts;

                    updateIndex++;
                } else {
                    clearInterval(updateInterval);
                    button.textContent = originalContent;
                    button.disabled = false;
                }
            }, 1000);
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('feature-tutorial-modal')) {
                closeFeatureTutorial();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeFeatureTutorial();
            }
        });

        // Expose functions globally for inline onclick
        window.openFeatureTutorial = openFeatureTutorial;
        window.closeFeatureTutorial = closeFeatureTutorial;
        window.animateRecommendationDemo = animateRecommendationDemo;
        window.demonstrateEmergencyFeature = demonstrateEmergencyFeature;
        window.simulateOfflineDemo = simulateOfflineDemo;
        window.demonstrateSafetyMonitoring = demonstrateSafetyMonitoring;
    </script>

    <!-- Browse Trails Modal -->
    <div id="browse-trails-modal" class="fixed inset-0 bg-black bg-opacity-80 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 py-8">
            <div class="bg-white rounded-3xl shadow-2xl max-w-6xl w-full overflow-hidden transform scale-95 transition-transform duration-300 relative my-auto" id="browse-modal-content">
                <!-- Close Button -->
                <button onclick="closeBrowseTrailsModal()" class="absolute top-4 right-4 z-20 p-2 bg-white hover:bg-gray-100 rounded-full transition-colors shadow-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <!-- Left Column: Trail Slideshow -->
                    <div class="relative bg-gray-900 overflow-hidden h-64 md:h-96 lg:h-[600px]">
                        <div id="trail-slideshow" class="h-full relative">
                            <!-- Trail slides will be dynamically inserted here -->
                        </div>
                        
                        <!-- Slideshow Navigation -->
                        <div class="absolute bottom-4 md:bottom-8 left-0 right-0 flex items-center justify-center space-x-2 z-10">
                            <button onclick="previousSlide()" class="p-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full transition-all">
                                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <div id="slide-indicators" class="flex space-x-2">
                                <!-- Dots will be inserted here -->
                            </div>
                            <button onclick="nextSlide()" class="p-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-full transition-all">
                                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Column: Logo & Auth -->
                    <div class="flex flex-col items-center justify-center p-6 md:p-8 lg:p-12 bg-gradient-to-br from-white to-gray-50">
                        <!-- Logo -->
                        <div class="mb-6 md:mb-8">
                            <div class="flex items-center space-x-2 md:space-x-3 mb-3 md:mb-4 justify-center">
                                <img src="{{ asset('img/icon1.png') }}" alt="HikeThere Logo" class="h-12 md:h-16 w-auto">
                                <span class="text-2xl md:text-3xl font-bold text-[#336d66]">HikeThere</span>
                            </div>
                            <p class="text-gray-600 text-center text-base md:text-lg">Your Adventure Starts Here</p>
                        </div>

                        <!-- Description -->
                        <div class="mb-6 md:mb-8 lg:mb-10 text-center max-w-md">
                            <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-3 md:mb-4">Discover Amazing Trails</h3>
                            <p class="text-sm md:text-base text-gray-600 leading-relaxed">
                                Join HikeThere to explore trails, plan your adventures, and connect with a community of hiking enthusiasts. Start your journey today!
                            </p>
                        </div>

                        <!-- Auth Buttons -->
                        <div class="w-full max-w-sm space-y-3 md:space-y-4">
                            <a href="{{ route('login') }}" class="block w-full btn-mountain text-center py-3 md:py-4 text-base md:text-lg font-semibold">
                                Login to Your Account
                            </a>
                            <a href="{{ route('register.select') }}" class="block w-full btn-mountain-outline text-center py-3 md:py-4 text-base md:text-lg font-semibold">
                                Create New Account
                            </a>
                        </div>

                        <!-- Features List -->
                        <div class="mt-6 md:mt-8 lg:mt-10 space-y-2 md:space-y-3 text-xs md:text-sm text-gray-600">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-[#336d66] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Access detailed trail information</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-[#336d66] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Plan and book your adventures</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-[#336d66] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Join the hiking community</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #browse-trails-modal.active {
            opacity: 1;
        }

        #browse-trails-modal.active #browse-modal-content {
            transform: scale(1);
        }

        .trail-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .trail-slide.active {
            opacity: 1;
        }

        .trail-slide-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .trail-slide-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            padding: 40px 30px 30px;
            color: white;
        }

        .slide-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .slide-indicator.active {
            background: white;
            width: 24px;
            border-radius: 4px;
        }
    </style>

    <script>
        let currentSlide = 0;
        let slideInterval;
        let browseTrails = [];

        function openBrowseTrailsModal() {
            const modal = document.getElementById('browse-trails-modal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.add('active');
            }, 10);
            
            // Fetch trails for slideshow
            fetchTrailsForSlideshow();
            document.body.style.overflow = 'hidden';
        }

        function closeBrowseTrailsModal() {
            const modal = document.getElementById('browse-trails-modal');
            modal.classList.remove('active');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
            
            if (slideInterval) {
                clearInterval(slideInterval);
            }
            document.body.style.overflow = '';
        }

        function fetchTrailsForSlideshow() {
            fetch('/api/trails/featured')
                .then(response => response.json())
                .then(data => {
                    browseTrails = data.trails.slice(0, 6); // Get first 6 trails
                    renderSlideshow();
                    startSlideshow();
                })
                .catch(error => {
                    console.error('Error fetching trails:', error);
                    // Use fallback data if API fails
                    renderFallbackSlideshow();
                    startSlideshow();
                });
        }

        function renderSlideshow() {
            const slideshow = document.getElementById('trail-slideshow');
            const indicators = document.getElementById('slide-indicators');
            
            slideshow.innerHTML = '';
            indicators.innerHTML = '';
            
            browseTrails.forEach((trail, index) => {
                // Create slide
                const slide = document.createElement('div');
                slide.className = `trail-slide ${index === 0 ? 'active' : ''}`;
                slide.innerHTML = `
                    <img src="${trail.image}" alt="${trail.name}" class="trail-slide-image">
                    <div class="trail-slide-overlay">
                        <h3 class="text-2xl font-bold mb-2">${trail.name}</h3>
                        <p class="text-sm text-gray-200 mb-3">${trail.location}</p>
                        <div class="flex items-center space-x-4 text-sm">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                ${trail.distance || 'N/A'}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                ${trail.difficulty || 'N/A'}
                            </span>
                        </div>
                    </div>
                `;
                slideshow.appendChild(slide);
                
                // Create indicator
                const indicator = document.createElement('div');
                indicator.className = `slide-indicator ${index === 0 ? 'active' : ''}`;
                indicator.onclick = () => goToSlide(index);
                indicators.appendChild(indicator);
            });
        }

        function renderFallbackSlideshow() {
            // Fallback trails if API fails
            browseTrails = [
                { name: 'Mount Pulag', location: 'Benguet', distance: '8.5 km', difficulty: 'Moderate', image: 'https://images.unsplash.com/photo-1551632811-561732d1e306' },
                { name: 'Mount Apo', location: 'Davao', distance: '12 km', difficulty: 'Difficult', image: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4' },
                { name: 'Taal Volcano', location: 'Batangas', distance: '3 km', difficulty: 'Easy', image: 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b' },
                { name: 'Mount Ulap', location: 'Benguet', distance: '6 km', difficulty: 'Easy', image: 'https://images.unsplash.com/photo-1519904981063-b0cf448d479e' },
                { name: 'Mount Pinatubo', location: 'Zambales', distance: '14 km', difficulty: 'Moderate', image: 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4' },
                { name: 'Mount Batulao', location: 'Batangas', distance: '10 km', difficulty: 'Moderate', image: 'https://images.unsplash.com/photo-1551632811-561732d1e306' }
            ];
            renderSlideshow();
        }

        function startSlideshow() {
            slideInterval = setInterval(() => {
                nextSlide();
            }, 5000); // Change slide every 5 seconds
        }

        function nextSlide() {
            goToSlide((currentSlide + 1) % browseTrails.length);
        }

        function previousSlide() {
            goToSlide((currentSlide - 1 + browseTrails.length) % browseTrails.length);
        }

        function goToSlide(index) {
            const slides = document.querySelectorAll('.trail-slide');
            const indicators = document.querySelectorAll('.slide-indicator');
            
            slides[currentSlide].classList.remove('active');
            indicators[currentSlide].classList.remove('active');
            
            currentSlide = index;
            
            slides[currentSlide].classList.add('active');
            indicators[currentSlide].classList.add('active');
        }

        // Make functions global
        window.openBrowseTrailsModal = openBrowseTrailsModal;
        window.closeBrowseTrailsModal = closeBrowseTrailsModal;
        window.nextSlide = nextSlide;
        window.previousSlide = previousSlide;
    </script>

    <!-- Google Maps API -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap&libraries=geometry"></script>
    <script>
        // Global variable to store map instances
        window.trailMaps = {};

        // Initialize Google Maps (callback function)
        function initMap() {
            // Google Maps is now loaded and ready
            console.log('Google Maps API loaded');
        }
    </script>
</x-guest-layout>