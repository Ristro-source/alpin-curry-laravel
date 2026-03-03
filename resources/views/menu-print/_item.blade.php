{{-- resources/views/menu-print/_item.blade.php
     Variables (compact from _section):
       $name, $price, $description, $allTags, $showThisDesc
     Inherited from template scope:
       $layout, $showAlrg, $showAlrgIcons, $extraLangs, $extraLangDescs, $getIcon
--}}
@if($layout === 'classic')
    {{-- ── Classic ── --}}
    <div class="mp-item-row">
        <span class="mp-item-name">{{ $name }}</span>
        <span class="mp-item-dots"></span>
        <span class="mp-item-price">{{ $price }}</span>
    </div>

    @if($showThisDesc)
        <p class="mp-item-description">
            {{ $description }}
            @if($showAlrg && !$showAlrgIcons && count($allTags))
                <span class="mp-item-allergy-inline">({{ implode(', ', array_column($allTags, 'key')) }})</span>
            @endif
        </p>
    @elseif($showAlrg && !$showAlrgIcons && count($allTags))
        <p class="mp-item-description">
            <span class="mp-item-allergy-inline">({{ implode(', ', array_column($allTags, 'key')) }})</span>
        </p>
    @endif

    {{-- Allergen icons row (classic) --}}
    @if($showAlrg && $showAlrgIcons && count($allTags))
        <div class="mp-ai-row">
            @foreach($allTags as $tag)
                @php [$mod, $code, $lbl] = $getIcon($tag['key'] ?? ''); @endphp
                <span class="mp-ai mp-ai-{{ $mod }}" title="{{ $lbl }}">{{ $code }}</span>
            @endforeach
        </div>
    @endif

    {{-- Extra language descriptions (skip if identical to name or primary) --}}
    @foreach($extraLangs as $eLang)
        @php $eDesc = $extraLangDescs[$eLang][$name] ?? ''; @endphp
        @if($showDesc && $eDesc !== '' && $eDesc !== $name && $eDesc !== $description)
            <p class="mp-item-description mp-lang-desc">
                <span class="mp-lang-badge">{{ strtoupper($eLang) }}</span>
                {{ $eDesc }}
            </p>
        @endif
    @endforeach

@else
    {{-- ── Elegant & Modern ── --}}
    <div class="mp-item-row">
        <span class="mp-item-name">{{ $name }}</span>
        <span class="mp-item-price">{{ $price }}</span>
    </div>

    @if($showThisDesc)
        <p class="mp-item-description">{{ $description }}</p>
    @endif

    {{-- Extra language descriptions (skip if identical to name or primary) --}}
    @foreach($extraLangs as $eLang)
        @php $eDesc = $extraLangDescs[$eLang][$name] ?? ''; @endphp
        @if($showDesc && $eDesc !== '' && $eDesc !== $name && $eDesc !== $description)
            <p class="mp-item-description mp-lang-desc">
                <span class="mp-lang-badge">{{ strtoupper($eLang) }}</span>
                {{ $eDesc }}
            </p>
        @endif
    @endforeach

    {{-- Allergen icons (elegant/modern) --}}
    @if($showAlrg && count($allTags))
        @if($showAlrgIcons)
            <div class="mp-ai-row">
                @foreach($allTags as $tag)
                    @php [$mod, $code, $lbl] = $getIcon($tag['key'] ?? ''); @endphp
                    <span class="mp-ai mp-ai-{{ $mod }}" title="{{ $lbl }}">{{ $code }}</span>
                @endforeach
            </div>
        @else
            <div class="mp-tags">
                @foreach($allTags as $tag)
                    <span class="mp-tag">{{ $tag['key'] }}</span>
                @endforeach
            </div>
        @endif
    @endif
@endif
