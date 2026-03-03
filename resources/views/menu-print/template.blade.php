<!DOCTYPE html>
<html lang="{{ $config['lang'] ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $config['title'] }} — Menu</title>
    <style>
        {!! file_get_contents(public_path('css/menu-print.css')) !!}
    </style>
</head>
@php
    $layout          = $config['layout']         ?? 'classic';
    $showDesc        = (bool) ($config['descriptions']    ?? true);
    $showAlrg        = (bool) ($config['allergens']       ?? true);
    $showAlrgIcons   = (bool) ($config['allergenIcons']   ?? true);
    $showLegend      = (bool) ($config['allergenLegend']  ?? true);
    $paperClass      = 'paper-'  . ($config['paper']      ?? 'A4');
    $layoutClass     = 'layout-' . $layout;
    $colorClass      = 'colors-' . ($config['colors']     ?? 'brand');
    $pageWidthClass  = 'pg-'     . ($config['pageWidth']  ?? 'standard');
    $autoOrganize    = (bool) ($config['autoOrganize']    ?? false);
    $repeatHeader    = (bool) ($config['repeatHeader']    ?? false);
    $isPdf           = (bool) ($isPdf ?? false);

    // EU 14 allergen icon map: slugged key → [CSS modifier, 2-letter code, full label]
    $allergenIconMap = [
        'gluten'                 => ['gl', 'GL', 'Gluten'],
        'crustaceans'            => ['cr', 'CR', 'Crustaceans'],
        'crustacean'             => ['cr', 'CR', 'Crustaceans'],
        'eggs'                   => ['eg', 'EG', 'Eggs'],
        'egg'                    => ['eg', 'EG', 'Eggs'],
        'fish'                   => ['fi', 'FI', 'Fish'],
        'peanuts'                => ['pn', 'PN', 'Peanuts'],
        'peanut'                 => ['pn', 'PN', 'Peanuts'],
        'soybeans'               => ['sy', 'SY', 'Soybeans'],
        'soya-beans'             => ['sy', 'SY', 'Soybeans'],
        'soybean'                => ['sy', 'SY', 'Soybeans'],
        'soy'                    => ['sy', 'SY', 'Soybeans'],
        'milk'                   => ['ml', 'ML', 'Milk'],
        'dairy'                  => ['ml', 'ML', 'Milk'],
        'nuts'                   => ['nt', 'NT', 'Nuts'],
        'nut'                    => ['nt', 'NT', 'Nuts'],
        'celery'                 => ['ce', 'CE', 'Celery'],
        'mustard'                => ['ms', 'MS', 'Mustard'],
        'sesame'                 => ['se', 'SE', 'Sesame'],
        'sesame-seeds'           => ['se', 'SE', 'Sesame'],
        'sulphites'              => ['su', 'SU', 'Sulphites'],
        'sulphur-dioxide'        => ['su', 'SU', 'Sulphites'],
        'sulphur-dioxide-and-sulphites' => ['su', 'SU', 'Sulphites'],
        'lupin'                  => ['lu', 'LU', 'Lupin'],
        'molluscs'               => ['mo', 'MO', 'Molluscs'],
        'mollusc'                => ['mo', 'MO', 'Molluscs'],
    ];

    // Helper: get icon data for a tag key
    $getIcon = function (string $key) use ($allergenIconMap): array {
        return $allergenIconMap[$key] ?? ['xx', strtoupper(substr($key, 0, 2)), ucfirst($key)];
    };

    $extraLangDescs = $extraLangDescs ?? [];
    $usedAllergens  = $usedAllergens  ?? [];

    // Language label map for multi-lang badges
    $langLabels = ['en' => 'EN', 'it' => 'IT', 'de' => 'DE'];
    $primaryLang = $config['lang'] ?? 'en';
    $extraLangs  = array_slice($config['langs'] ?? [], 1);
@endphp
<body class="{{ $layoutClass }} {{ $colorClass }} {{ $paperClass }} {{ $pageWidthClass }} {{ $repeatHeader ? 'repeat-header' : '' }}">

{{-- Screen-only white card wrapper --}}
<div class="mp-doc">

@if($layout === 'elegant')
<div class="mp-page-wrapper">
@endif

{{-- ── HEADER ── --}}
<header class="mp-header">
    <h1>{{ $config['title'] }}</h1>
    @if(!empty($config['subtitle']))
        <p class="subtitle">{{ $config['subtitle'] }}</p>
    @endif
    @if(!empty($config['address']) || !empty($config['phone']))
        <p class="contact-line">
            @if(!empty($config['address'])){{ $config['address'] }}@endif
            @if(!empty($config['address']) && !empty($config['phone'])) &nbsp;·&nbsp; @endif
            @if(!empty($config['phone'])){{ $config['phone'] }}@endif
        </p>
    @endif
    @if(count($config['langs'] ?? []) > 1)
        <p class="mp-lang-badge" style="margin-top:0.35rem;">
            {{ implode(' · ', array_map(fn($l) => strtoupper($l), $config['langs'])) }}
        </p>
    @endif
</header>

{{-- ── MENU SECTIONS ── --}}
@if($autoOrganize)
    {{-- Single sections always rendered full-width --}}
    @foreach($singleSections ?? [] as $section)
        @include('menu-print._section', ['section' => $section, 'displayMode' => 'single'])
    @endforeach

    @if(!$isPdf)
        {{-- Browser preview: true side-by-side two-column table --}}
        @if(!empty($dualFoodSections) || !empty($dualDrinkSections))
        <table class="mp-dual-table"><tr>
            <td class="mp-dual-col">
                @foreach($dualFoodSections ?? [] as $section)
                    @include('menu-print._section', ['section' => $section, 'displayMode' => 'dual'])
                @endforeach
            </td>
            <td class="mp-dual-col">
                @foreach($dualDrinkSections ?? [] as $section)
                    @include('menu-print._section', ['section' => $section, 'displayMode' => 'dual'])
                @endforeach
            </td>
        </tr></table>
        @endif
    @else
        {{-- PDF: sequential — dompdf cannot reliably render multi-page side-by-side columns --}}
        @foreach($dualFoodSections ?? [] as $section)
            @include('menu-print._section', ['section' => $section, 'displayMode' => 'dual'])
        @endforeach
        @foreach($dualDrinkSections ?? [] as $section)
            @include('menu-print._section', ['section' => $section, 'displayMode' => 'dual'])
        @endforeach
    @endif

    @if(empty($singleSections) && empty($dualFoodSections) && empty($dualDrinkSections))
        <p style="text-align:center;color:#999;font-style:italic;margin-top:3rem;">
            No menu items found. Add items in the admin panel.
        </p>
    @endif
@else
    @forelse($sections as $section)
        @php $displayMode = $section['display_type'] ?? 'dual'; @endphp
        @include('menu-print._section', ['section' => $section, 'displayMode' => $displayMode])
    @empty
        <p style="text-align:center;color:#999;font-style:italic;margin-top:3rem;">
            No menu items found. Add items in the admin panel.
        </p>
    @endforelse
@endif

{{-- ── FOOTER ── --}}
@if(!empty($config['footer']) || ($showLegend && !empty($usedAllergens)))
    <footer class="mp-footer">
        @if(!empty($config['footer']))
            <p>{{ $config['footer'] }}</p>
        @endif

        @if($showLegend && !empty($usedAllergens))
            <div class="mp-allergen-legend">
                <div class="mp-legend-grid">
                    @foreach($usedAllergens as $key => $label)
                        @php [$mod, $code, $lbl] = $getIcon((string) $key); @endphp
                        <span class="mp-legend-item">
                            <span class="mp-ai mp-ai-{{ $mod }}">{{ $code }}</span>
                            {{ $lbl }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </footer>
@endif

@if($layout === 'elegant')
</div>{{-- /mp-page-wrapper --}}
@endif

</div>{{-- /mp-doc --}}
</body>
</html>
