@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'imageAlt' => null,
    'type' => 'website',
    'keywords' => null,
    'author' => null,
])

@php
    $defaultTitle = config('app.name', 'HikeThere') . ' - Your Ultimate Hiking Companion';
    $defaultDescription = 'Discover breathtaking hiking trails, join exciting outdoor events, connect with fellow adventurers, and ensure your safety with emergency readiness features. Start your hiking journey with HikeThere today!';
    
    // Use GCS URL if available, otherwise fallback to local asset
    $defaultImage = env('OG_IMAGE_URL') 
        ? env('OG_IMAGE_URL') 
        : (config('filesystems.default') === 'gcs' && env('GCS_BUCKET')
            ? 'https://storage.googleapis.com/' . env('GCS_BUCKET') . '/img/og-image.png'
            : asset('img/og-image.png'));
    
    $defaultKeywords = 'hiking, trails, outdoor events, hiking community, emergency readiness, trail maps, hiking safety, adventure, nature, outdoor activities';
    
    $metaTitle = $title ?? $defaultTitle;
    $metaDescription = $description ?? $defaultDescription;
    $metaImage = $image ?? $defaultImage;
    $metaImageAlt = $imageAlt ?? 'HikeThere - Discover hiking trails and join outdoor adventures';
    $metaKeywords = $keywords ?? $defaultKeywords;
    $metaAuthor = $author ?? 'HikeThere';
@endphp

<!-- Open Graph / Facebook / Messenger -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:secure_url" content="{{ $metaImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $metaImageAlt }}">
<meta property="og:site_name" content="{{ config('app.name', 'HikeThere') }}">
<meta property="og:locale" content="en_US">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@HikeThere">
<meta name="twitter:creator" content="@HikeThere">
<meta name="twitter:url" content="{{ url()->current() }}">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
<meta name="twitter:image:alt" content="{{ $metaImageAlt }}">

<!-- General Meta -->
<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<meta name="author" content="{{ $metaAuthor }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url()->current() }}">
