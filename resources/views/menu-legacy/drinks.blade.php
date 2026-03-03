{{--
  resources/views/menu-legacy/drinks.blade.php
  Mirrors DrinkMenuGenerator.ts (2 columns, drawLeftToRight):
  - Header "Alpin Curry" + "Drinks" in gold on every page
  - 2-column HTML table with vertical separator
  - Categories distributed left↔right (rotational, smallest first)
  - Compact: name + price only, no descriptions
--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>

@page {
    size: A4 portrait;
    margin: 75pt 15pt 50pt 15pt;
}

body {
    font-family: 'DejaVu Serif', Georgia, serif;
    margin: 0;
    padding: 0;
    color: #111;
    font-size: 9pt;
}

/* ── Repeating page header ── */
.pg-header {
    position: fixed;
    top: -62pt;
    left: 0;
    right: 0;
    text-align: center;
    line-height: 1.15;
}

.pg-title {
    font-size: 22pt;
    font-weight: bold;
    color: #f8b233;
}

.pg-subtitle {
    font-size: 16pt;
    font-weight: bold;
    color: #f8b233;
    margin-top: 1pt;
}

/* ── Category ── */
.category {
    page-break-inside: avoid;
    margin-bottom: 10pt;
}

.category-title {
    font-size: 12pt;
    font-weight: bold;
    text-align: center;
    margin-bottom: 4pt;
    padding-bottom: 2pt;
    border-bottom: 0.5pt solid #ccc;
}

/* ── Item ── */
.item {
    page-break-inside: avoid;
    margin-bottom: 2pt;
}

.item-name {
    font-size: 9.5pt;
}

.item-price {
    font-size: 8pt;
    font-weight: bold;
    text-align: right;
    white-space: nowrap;
    width: 48pt;
}

</style>
</head>
<body>

{{-- Fixed header — appears on every page --}}
<div class="pg-header">
    <div class="pg-title">{{ $title }}</div>
    <div class="pg-subtitle">Drinks</div>
</div>

{{-- ── 2-column layout (mirrors DrinkMenuGenerator columnCount=2) ── --}}
<table style="width:100%;border-collapse:collapse;"><tr>

    {{-- LEFT column --}}
    <td style="width:49%;vertical-align:top;padding-right:8pt;border-right:0.75pt solid #999;">
        @foreach($leftCol as $section)
        <div class="category">
            <div class="category-title">{{ $section['title'] ?? '' }}</div>
            @foreach($section['items'] ?? [] as $item)
            <div class="item">
                <table style="width:100%;border-collapse:collapse;"><tr>
                    <td class="item-name">{{ $item['name'] ?? '' }}</td>
                    <td class="item-price">{{ $item['price'] ?? '' }}</td>
                </tr></table>
            </div>
            @endforeach
        </div>
        @endforeach
    </td>

    {{-- RIGHT column --}}
    <td style="width:49%;vertical-align:top;padding-left:8pt;">
        @foreach($rightCol as $section)
        <div class="category">
            <div class="category-title">{{ $section['title'] ?? '' }}</div>
            @foreach($section['items'] ?? [] as $item)
            <div class="item">
                <table style="width:100%;border-collapse:collapse;"><tr>
                    <td class="item-name">{{ $item['name'] ?? '' }}</td>
                    <td class="item-price">{{ $item['price'] ?? '' }}</td>
                </tr></table>
            </div>
            @endforeach
        </div>
        @endforeach
    </td>

</tr></table>

</body>
</html>
