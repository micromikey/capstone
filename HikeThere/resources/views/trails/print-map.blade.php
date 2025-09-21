<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $trail->trail_name }} — HikeThere</title>
    <style>
        :root{
            --accent:#2d7a3d;
            --muted:#6b7280;
            --bg:#ffffff;
            --card:#fbfcfd;
            --radius:10px;
            --pad:12px;
        }
        *{box-sizing:border-box}
        body{font-family:Inter, 'Helvetica Neue', Arial, sans-serif;background:var(--bg);color:#111;margin:14px}

    /* Page wrapper to center content and constrain width for better print layout */
    /* Reduced max-width to give thumbnails a larger visual presence next to the map */
    .page{max-width:1100px;margin:0 auto}

        /* Floating print button top-right */
        .print-btn{position:fixed;right:18px;top:18px;z-index:60}
        .print-btn button{background:var(--accent);color:#fff;border:0;padding:10px 14px;border-radius:8px;cursor:pointer;font-weight:700;box-shadow:0 6px 18px rgba(45,122,61,0.18)}

        header{display:flex;align-items:center;justify-content:space-between;padding:6px 0 12px}
        .brand{display:flex;align-items:center;gap:10px}
        .brand h2{margin:0;color:var(--accent);font-size:18px}
        .meta{font-size:12px;color:var(--muted)}

        .card{background:var(--card);border-radius:var(--radius);padding:var(--pad);box-shadow:0 6px 20px rgba(15,23,42,0.04);overflow:hidden}

     .map-wrap{margin-top:8px}
     /* Constrain the map visually so it doesn't overpower the thumbnails.
         Force a smaller max width and lower height so thumbnails occupy more visual weight. */
     .map{width:100%;height:min(480px,40vh);max-width:520px;object-fit:cover;border-radius:8px;border:1px solid #e6eef0;display:block}

    /* Two-column media layout: map on the left, stacked thumbnails on the right */
    .media-row{display:flex;gap:12px;align-items:flex-start;margin-top:12px}
    /* Give the map a fluid area but prefer leaving room for a larger thumbnail column */
    .map-col{flex:1 1 calc(100% - 420px);min-width:0}
    .thumbs-col{width:420px;display:flex;flex-direction:column;gap:12px}
    .thumb{flex:1 1 0;overflow:hidden;border-radius:8px;border:1px solid #eef2f3;min-height:140px;background:#f8fafb}
    .thumb img{width:100%;height:100%;object-fit:cover;display:block}

        .title{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
        .title h1{font-size:18px;margin:0}
        .subtitle{color:var(--muted);font-size:13px;margin-top:4px}

        /* Metrics grid: 4 columns on wide screens, responsive down to 1 column */
        .metrics-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:12px}
        .metric-card{background:#fff;border-radius:8px;padding:10px 12px;text-align:center;border:1px solid #eef2f3}
        .metric-label{font-size:11px;color:var(--muted);font-weight:800;letter-spacing:0.6px;text-transform:uppercase}
        .metric-value{font-size:15px;font-weight:800;margin-top:6px}

        .desc{margin-top:12px;font-size:13px;color:#333}

        /* Print adjustments */
        @media print{
            .print-btn{display:none}
            body{margin:6mm}
            @page{size:auto;margin:10mm}
            .page{max-width:100%}
        }

        @media (max-width:980px){
            .metrics-grid{grid-template-columns:repeat(2,1fr)}
            .page{max-width:100%}
            /* Reduce thumbs column on medium screens but keep it visible */
            .thumbs-col{width:260px}
            /* Allow map to grow to available space on smaller viewports */
            .map{max-width:unset;height:auto}
        }
        @media (max-width:480px){
            .metrics-grid{grid-template-columns:1fr}
            header{flex-direction:column;align-items:flex-start;gap:6px}
            .print-btn{position:static;margin-bottom:8px}
            /* On very small screens stack media vertically */
            .media-row{flex-direction:column}
            .thumbs-col{width:100%}
            .map{height:auto;max-width:100%}
        }
    </style>
</head>
<body>

<div class="print-btn" aria-hidden="false"><button type="button" onclick="window.print()">Print</button></div>

<main class="page">
    <header>
        <div class="brand">
            <div style="width:44px;height:44px;border-radius:8px;background:linear-gradient(180deg,#e6f3ea,#ffffff);display:flex;align-items:center;justify-content:center;color:var(--accent);font-weight:800">HT</div>
            <div>
                <h2>HikeThere</h2>
                <div class="meta">Generated {{ date('M d, Y') }}</div>
            </div>
        </div>
        <div class="meta">{{ $trail->mountain_name ?? '' }} @if(!empty($trail->location->name))• {{ $trail->location->name }}@endif</div>
    </header>

    <section class="card">
        <div class="title">
            <div>
                <h1>{{ $trail->trail_name }}</h1>
                <div class="subtitle">{{ $trail->summary ? Str::limit(strip_tags($trail->summary), 140) : '' }}</div>
            </div>
            <div style="text-align:right;color:var(--muted);font-size:12px">Trail ID: {{ $trail->id ?? '—' }}</div>
        </div>

        {{-- Two-column media area: map left, two stacked photos right (if available) --}}
        @php
            $mapSrc = $staticMapUrl ?? null;
            $photos = [];

            // If the trail model provides images (as arrays or objects), prefer those and render like show.blade.php
            if(!empty($trail->images) && count($trail->images)){
                foreach($trail->images as $img){
                    $path = null;

                    // If the image entry is an array with a path
                    if(is_array($img) && isset($img['path'])){
                        $path = $img['path'];

                    // If it's a plain string (likely a URL or storage path)
                    } elseif(is_string($img)) {
                        $path = $img;

                    // If it's an object (Eloquent model or object with id)
                    } elseif(is_object($img)) {
                        // If it's a TrailImage model or contains an id, prefer resolving by id
                        $imgId = $img->id ?? null;
                        if($imgId){
                            try{
                                $ti = \App\Models\TrailImage::find($imgId);
                                if($ti){
                                    // If stored image_path is a full URL, use it; otherwise use the storage-relative path
                                    if(filter_var($ti->image_path, FILTER_VALIDATE_URL)){
                                        $path = $ti->image_path;
                                    } else {
                                        $path = ltrim($ti->image_path, '/');
                                    }
                                }
                            } catch(\Exception $e){
                                // ignore and fall back to other properties
                            }
                        }

                        // If we still don't have a path, try common property names on the object
                        if(!$path){
                            $path = $img->image_path ?? $img->path ?? $img->file ?? $img->filename ?? null;
                        }
                    }

                    if($path){
                        if(\Illuminate\Support\Str::startsWith($path, ['http://','https://'])){
                            $photos[] = $path;
                        } else {
                            // keep storage-relative path (do not convert to asset) so we can embed or resolve later
                            $rel = preg_replace('#^.*/storage/#','',$path);
                            $photos[] = ltrim($rel, '/');
                        }
                    }
                    if(count($photos) >= 2) break;
                }
            }

            // Fallback: scan common storage folders for images (primary, additional)
            if(count($photos) < 2){
                try{
                    $prim = \Illuminate\Support\Facades\Storage::disk('public')->files('trail-images/primary');
                    $add = \Illuminate\Support\Facades\Storage::disk('public')->files('trail-images/additional');
                    $candidates = array_merge($prim ?? [], $add ?? []);
                } catch(\Exception $e){
                    $candidates = [];
                }

                foreach($candidates as $c){
                    if(count($photos) >= 2) break;
                    // store storage-relative path
                    $photos[] = ltrim($c, '/');
                }
            }
        @endphp

        {{-- Controller may provide $photoSrcs (preferred). Fallback to building renderPhotos below. --}}

        <div class="media-row">
            <div class="map-col">
                @if($mapSrc)
                    <img class="map" src="{{ $mapSrc }}" alt="Map for {{ $trail->trail_name }}">
                @else
                    <div style="padding:32px;border-radius:8px;border:1px dashed #e6eef0;color:var(--muted);text-align:center">Map not available</div>
                @endif
            </div>

            @php
                // If controller provided $photoSrcs, use them. Otherwise, build renderPhotos from $photos.
                $renderPhotos = [];
                if (!empty($photoSrcs) && is_array($photoSrcs) && count($photoSrcs)) {
                    $renderPhotos = $photoSrcs;
                } else {
                    // Build renderable photo sources. Prefer data URIs for local storage images to avoid request issues.
                    foreach($photos as $p){
                        if(\Illuminate\Support\Str::startsWith($p, ['http://','https://'])){
                            $renderPhotos[] = $p;
                            continue;
                        }

                        // If p looks like an absolute /storage URL, strip host and leading /storage/
                        if(\Illuminate\Support\Str::contains($p, '/storage/')){
                            $rel = preg_replace('#^.*/storage/#','',$p);
                        } else {
                            $rel = ltrim($p, '/');
                        }

                        try{
                            $disk = \Illuminate\Support\Facades\Storage::disk('public');
                            if($disk->exists($rel)){
                                $contents = $disk->get($rel);
                                try{
                                    $mime = $disk->mimeType($rel) ?? 'image/jpeg';
                                } catch(\Throwable $ex){
                                    $mime = 'image/jpeg';
                                }
                                $renderPhotos[] = 'data:' . $mime . ';base64,' . base64_encode($contents);
                            } else {
                                // fallback to asset URL
                                $renderPhotos[] = asset('storage/' . $rel);
                            }
                        } catch(\Exception $e){
                            $renderPhotos[] = asset('storage/' . $rel);
                        }
                    }
                }
            @endphp

            <div class="thumbs-col" aria-hidden="{{ empty($renderPhotos) ? 'true' : 'false' }}">
                @if(count($renderPhotos) > 0)
                    @foreach($renderPhotos as $src)
                        <div class="thumb"><img src="{{ $src }}" alt="Trail photo"></div>
                    @endforeach
                    @if(count($photos) === 1)
                        <div class="thumb" style="display:flex;align-items:center;justify-content:center;color:var(--muted);">No second photo</div>
                    @endif
                @else
                    <div class="thumb" style="display:flex;align-items:center;justify-content:center;color:var(--muted);">No photos available</div>
                    <div class="thumb" style="display:flex;align-items:center;justify-content:center;color:var(--muted);">&nbsp;</div>
                @endif
            </div>
        </div>

        <div class="metrics-grid" role="list">
            <div class="metric-card" role="listitem">
                <div class="metric-label">Total Distance</div>
                <div class="metric-value">{{ $trail->length ?? 'N/A' }} km</div>
            </div>
            <div class="metric-card" role="listitem">
                <div class="metric-label">Elevation Gain</div>
                <div class="metric-value">{{ $trail->elevation_gain ?? 'N/A' }} m</div>
            </div>
            <div class="metric-card" role="listitem">
                <div class="metric-label">Trail Duration</div>
                <div class="metric-value">{{ $trail->duration ?? 'N/A' }}</div>
            </div>
            <div class="metric-card" role="listitem">
                <div class="metric-label">Est. Hiking Time</div>
                <div class="metric-value">{{ $trail->estimated_time_formatted ?? 'N/A' }}</div>
            </div>
        </div>

        @if($trail->summary && trim($trail->summary))
            <div class="desc">{!! nl2br(e($trail->summary)) !!}</div>
        @endif
    </section>

    <div style="margin-top:12px;color:#6b7280;font-size:12px">Always check weather and trail status before hiking.</div>
</main>

</body>
</html>