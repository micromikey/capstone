// GSAP timeline options
var tmax_opts = {
  delay: 0.5,         // Initial delay before the animation starts
  repeat: -1,         // Infinite repeat
  repeatDelay: 0.5,   // Delay between repeats
  yoyo: true          // Enables yoyo effect (reverse animation on every other repeat)
};

// Initialize the GSAP timeline with the specified options
var tmax_tl = new TimelineMax(tmax_opts);

// Select both polygon, polyline, circles and rectangle elements within the SVG with class 'landscape'
var polyland_shapes = $('svg.landscape polygon, svg.landscape polyline, svg.landscape path, svg.landscape circle, svg.landscape rect');

// Stagger and duration settings for the animation
var polyland_stagger = 0.00475;
var polyland_duration = 1.5;

// Enable SVG transform attributes in GSAP (recommended by Jack Doyle@GreenSock)
CSSPlugin.useSVGTransformAttr = true;

// Define the initial state for the staggered animation
var polyland_staggerFrom = {
  scale: 0,                         // Start scaled down to 0
  opacity: 0,                       // Start fully transparent
  transformOrigin: 'center center', // Set the origin for transformations
  ease: Elastic.easeInOut,          // Easing function for a bouncy effect
  force3D: true                     // Forces 3D rendering for better performance
};

// Define the end state for the staggered animation
var polyland_staggerTo = {
  opacity: 1,      // Fade in to full opacity
  scale: 1,        // Scale up to original size
  ease: Elastic.easeInOut, // Easing function for a bouncy effect
  force3D: true     // Maintains 3D rendering
};

// Apply the staggered animation to both polygons and polylines
tmax_tl.staggerFromTo(
  polyland_shapes,        // Elements to animate
  polyland_duration,      // Duration of each animation
  polyland_staggerFrom,   // Starting properties
  polyland_staggerTo,     // Ending properties
  polyland_stagger,       // Stagger delay between animations
  0                        // Initial delay before starting the stagger
);