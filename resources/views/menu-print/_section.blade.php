{{-- resources/views/menu-print/_section.blade.php
     Variables:
       $section     — section data array
       $displayMode — 'single' | 'dual'
     All other variables inherited from parent template scope.
--}}
@php
    $items = $section['items'] ?? [];
    $sectionExtraClass = $displayMode === 'single' ? 'mp-section-single' : 'mp-section-dual';

    $useDualTable  = $displayMode === 'dual' && count($items) > 0;
    $useModernGrid = !$useDualTable && $layout === 'modern';

    $leftItems  = [];
    $rightItems = [];
    if ($useDualTable) {
        $half       = (int) ceil(count($items) / 2);
        $leftItems  = array_slice($items, 0, $half);
        $rightItems = array_slice($items, $half);
    }
@endphp
<div class="mp-section {{ $sectionExtraClass }}">
    <h2 class="mp-section-title">{{ $section['title'] ?? '' }}</h2>

    @if($useDualTable)
        {{-- Two-column HTML table (dompdf-compatible) --}}
        <table style="width:100%;border-collapse:separate;border-spacing:1rem 0;"><tr>
            <td style="width:50%;vertical-align:top;">
                @foreach($leftItems as $item)
                    @php
                        $name         = $item['name']        ?? '';
                        $price        = $item['price']        ?? '';
                        $description  = $item['description']  ?? '';
                        $allTags      = array_merge($item['allergies'] ?? [], $item['intolerances'] ?? []);
                        $showThisDesc = $showDesc && $description !== '' && $description !== $name;
                    @endphp
                    <div class="mp-item">
                        @include('menu-print._item', compact('name','price','description','allTags','showThisDesc'))
                    </div>
                @endforeach
            </td>
            <td style="width:50%;vertical-align:top;">
                @foreach($rightItems as $item)
                    @php
                        $name         = $item['name']        ?? '';
                        $price        = $item['price']        ?? '';
                        $description  = $item['description']  ?? '';
                        $allTags      = array_merge($item['allergies'] ?? [], $item['intolerances'] ?? []);
                        $showThisDesc = $showDesc && $description !== '' && $description !== $name;
                    @endphp
                    <div class="mp-item">
                        @include('menu-print._item', compact('name','price','description','allTags','showThisDesc'))
                    </div>
                @endforeach
            </td>
        </tr></table>
    @endif

    @if(!$useDualTable)
        @if($useModernGrid)
            <div class="mp-items-grid">
        @endif

        @foreach($items as $item)
            @php
                $name         = $item['name']        ?? '';
                $price        = $item['price']        ?? '';
                $description  = $item['description']  ?? '';
                $allTags      = array_merge($item['allergies'] ?? [], $item['intolerances'] ?? []);
                $showThisDesc = $showDesc && $description !== '' && $description !== $name;
            @endphp
            <div class="mp-item">
                @include('menu-print._item', compact('name','price','description','allTags','showThisDesc'))
            </div>
        @endforeach

        @if($useModernGrid)
            </div>{{-- /mp-items-grid --}}
        @endif
    @endif

</div>
