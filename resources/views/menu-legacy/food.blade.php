{{--
  resources/views/menu-legacy/food.blade.php
  Mirrors FoodMenuGenerator.ts:
  - Single column, full width
  - Header "Alpin Curry" in gold on every page
  - Bottom separator line on every page
  - Items: name + price, then DE / IT / EN descriptions, then allergen icon images
  - Allergen legend table in the footer (all used allergens)
--}}
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<style>

@page {
    size: A4 portrait;
    margin: 55pt 20pt 85pt 20pt;
}

body {
    font-family: 'DejaVu Serif', Georgia, serif;
    margin: 0;
    padding: 0;
    color: #111;
    font-size: 9pt;
}

/* ── Repeating page header (position:fixed → every page in dompdf) ── */
.pg-header {
    position: fixed;
    top: -44pt;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 20pt;
    font-weight: bold;
    color: #f8b233;
    line-height: 1.15;
}

/* ── Bottom separator line on every page ── */
.pg-footer-line {
    position: fixed;
    bottom: -68pt;
    left: 0;
    right: 0;
    border-top: 1pt solid #000;
}

/* ── Category ── */
.category {
    page-break-inside: avoid;
    margin-bottom: 8pt;
}

.category-title {
    font-size: 13pt;
    font-weight: bold;
    text-align: center;
    margin-bottom: 5pt;
    padding-bottom: 2pt;
    border-bottom: 0.5pt solid #ccc;
}

/* ── Item ── */
.item {
    page-break-inside: avoid;
    margin-bottom: 4pt;
}

.item-name {
    font-size: 10pt;
    font-weight: bold;
}

.item-price {
    font-size: 8pt;
    font-weight: bold;
    text-align: right;
    white-space: nowrap;
    width: 55pt;
}

.item-desc {
    font-size: 7.5pt;
    color: #444;
    margin-top: 1pt;
    margin-left: 4pt;
    line-height: 1.3;
}

/* ── Allergen images row ── */
.allergen-row {
    margin-top: 2pt;
    margin-left: 4pt;
}

.allergen-img {
    width: 13pt;
    height: 13pt;
    vertical-align: middle;
    margin-right: 3pt;
}

/* ── Footer allergen legend ── */
.legend {
    margin-top: 14pt;
    padding-top: 5pt;
    border-top: 0.5pt solid #aaa;
    page-break-inside: avoid;
}

.legend-title {
    font-size: 7pt;
    font-weight: bold;
    color: #666;
    text-align: center;
    margin-bottom: 4pt;
}

.legend-img {
    width: 11pt;
    height: 11pt;
    vertical-align: middle;
    margin-right: 2pt;
}

.legend-text {
    font-size: 6.5pt;
    color: #444;
    vertical-align: middle;
}

</style>
</head>
<body>

{{-- Fixed header — appears on every page --}}
<div class="pg-header">{{ $title }}</div>

{{-- Fixed bottom line — appears on every page --}}
<div class="pg-footer-line"></div>

{{-- ── Sections ── --}}
@foreach($sections as $section)
<div class="category">
    <div class="category-title">{{ $section['title'] ?? '' }}</div>

    @foreach($section['items'] ?? [] as $item)
        @php
            $name    = (string) ($item['name'] ?? '');
            $price   = (string) ($item['price'] ?? '');
            $allTags = array_merge($item['allergies'] ?? [], $item['intolerances'] ?? []);
            // DE → IT → EN order, matching FoodMenuGenerator.ts
            $descDe  = (string) ($descMap[$name]['de'] ?? '');
            $descIt  = (string) ($descMap[$name]['it'] ?? '');
            $descEn  = (string) ($descMap[$name]['en'] ?? '');
        @endphp
        <div class="item">
            <table style="width:100%;border-collapse:collapse;"><tr>
                <td class="item-name">{{ $name }}</td>
                <td class="item-price">{{ $price }}</td>
            </tr></table>

            @if($descDe !== '')
                <div class="item-desc">{{ $descDe }}</div>
            @endif
            @if($descIt !== '' && $descIt !== $descDe)
                <div class="item-desc">{{ $descIt }}</div>
            @endif
            @if($descEn !== '' && $descEn !== $descDe && $descEn !== $descIt)
                <div class="item-desc">{{ $descEn }}</div>
            @endif

            @if(count($allTags) > 0)
                <div class="allergen-row">
                    @foreach($allTags as $tag)
                        @php
                            $tKey = (string) ($tag['key'] ?? '');
                            $tImg = $allergenImageMap[$tKey] ?? '';
                        @endphp
                        @if($tImg !== '')
                            <img src="{{ $tImg }}" class="allergen-img" alt="{{ $tag['label'] ?? $tKey }}">
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
@endforeach

{{-- ── Allergen legend (mirrors addAllergiesImagesTableWithDescriptionFooter) ── --}}
@if(!empty($usedAllergens))
<div class="legend">
    <div class="legend-title">Allergeni &nbsp;·&nbsp; Allergene &nbsp;·&nbsp; Allergens</div>
    <table style="width:100%;border-collapse:collapse;">
        @foreach(array_chunk(array_keys($usedAllergens), 5) as $row)
        <tr>
            @foreach($row as $aKey)
                @php
                    $aImg = $allergenImageMap[$aKey] ?? '';
                    $aLbl = $usedAllergens[$aKey] ?? $aKey;
                @endphp
                <td style="padding:1pt 4pt;vertical-align:middle;white-space:nowrap;">
                    @if($aImg !== '')
                        <img src="{{ $aImg }}" class="legend-img">
                    @endif
                    <span class="legend-text">{{ $aLbl }}</span>
                </td>
            @endforeach
            @for($pad = count($row); $pad < 5; $pad++)
                <td></td>
            @endfor
        </tr>
        @endforeach
    </table>
</div>
@endif

</body>
</html>
