@php
    // Read static files from public folder so we don't duplicate large SVG in repo
    $svgPath = public_path('poly-mountain/index.html');
    $scriptPath = asset('poly-mountain/script.js');
    $stylePath = asset('poly-mountain/style.css');
    $svgContent = '';
    if (file_exists($svgPath)) {
        // index.html here contains a standalone SVG; we want just the SVG markup.
        $svgContent = file_get_contents($svgPath);
    }
@endphp

<div class="poly-mountain-root w-full h-full" style="min-height:260px;">
    {{-- Inline the SVG page content (falls back to link if file missing) --}}
    @if($svgContent)
        {{-- The public/index.html contains a full HTML document in some cases; if so extract the <svg>...</svg> segment. --}}
        @php
            $matches = [];
            // Try to extract first <svg ...>...</svg> block
            preg_match('/<svg[\s\S]*?<\/svg>/i', $svgContent, $matches);
            $svgOnly = $matches[0] ?? $svgContent;

            // Ensure the SVG will 'cover' the container like background-size: cover
            // 1) Add preserveAspectRatio="xMidYMid slice" (replace existing or add)
            if (preg_match('/<svg[^>]*>/i', $svgOnly, $openTagMatch)) {
                $openTag = $openTagMatch[0];

                // replace or add preserveAspectRatio
                if (stripos($openTag, 'preserveAspectRatio=') !== false) {
                    $openTag = preg_replace('/preserveAspectRatio\s*=\s*"[^"]*"/i', 'preserveAspectRatio="xMidYMid slice"', $openTag);
                } else {
                    $openTag = rtrim($openTag, '>') . ' preserveAspectRatio="xMidYMid slice">';
                }

                // if no viewBox present but width/height exist, add viewBox to enable scaling
                if (stripos($openTag, 'viewBox=') === false) {
                    // try to capture width and height attributes
                    if (preg_match('/width\s*=\s*"([0-9\.]+)px?"/i', $openTag, $w) && preg_match('/height\s*=\s*"([0-9\.]+)px?"/i', $openTag, $h)) {
                        $vw = $w[1]; $vh = $h[1];
                        $openTag = preg_replace('/>$/', ' viewBox="0 0 ' . $vw . ' ' . $vh . '">', $openTag);
                    }
                }

                // substitute the modified opening tag back into the svg content
                $svgOnly = preg_replace('/<svg[^>]*>/i', $openTag, $svgOnly, 1);
            }
        @endphp

        {{-- Force the SVG to fill the available container and avoid layout shift --}}
        <style>
            /* Scoped styles for the embedded poly-mountain */
            .poly-mountain-root { width:100%; height:100%; display:block; box-sizing:border-box; }
            .poly-mountain-embed { width:100%; height:100%; display:flex; align-items:center; justify-content:center; overflow:hidden; }
            /* Make the SVG scale to fill the container while keeping aspect ratio */
            .poly-mountain-embed > svg { width:100% !important; height:100% !important; max-width:100%; display:block; }
            /* Ensure inner shapes don't cause extra scroll or margin */
            .poly-mountain-embed svg * { vector-effect: non-scaling-stroke; }
        </style>

        <div class="poly-mountain-embed" aria-hidden="false" role="img">{!! $svgOnly !!}</div>

        {{-- Load sample CSS (optional additional styling) --}}
        <link rel="stylesheet" href="{{ $stylePath }}">

        {{-- Load jQuery + GSAP v2 (TimelineMax) from CDNs with minimal fallbacks. These fallbacks are included synchronously so the sample script can safely run. --}}
        <script>if(!window.jQuery){document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');}</script>
        <script>if(typeof TimelineMax === 'undefined' && typeof TweenMax === 'undefined'){document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.3/TweenMax.min.js"><\/script>');}</script>

        {{-- Load the sample script after dependencies. Using defer ensures it executes after parsing and after the above document.write inserts if needed. --}}
        <script src="{{ $scriptPath }}" defer></script>
    @else
        <p class="p-4 text-sm text-gray-500">Visualization unavailable. <a href="{{ asset('poly-mountain/index.html') }}" target="_blank" rel="noopener" class="text-green-600 underline">Open visualization</a></p>
    @endif
</div>
