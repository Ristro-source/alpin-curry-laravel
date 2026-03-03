@extends('layouts.alpin-curry')

@section('title', __('site.menu_page.title').' | '.__('site.brand'))
@section('meta_description', __('site.menu_page.subtitle'))

@section('content')
    <main id="main-content">
        @php
            $foodSections = array_values(array_filter($menuSections, static fn (array $section): bool => ($section['type'] ?? 'food') !== 'drink'));
            $drinkSections = array_values(array_filter($menuSections, static fn (array $section): bool => ($section['type'] ?? 'food') === 'drink'));

            $attachAnchors = static function (array $sections, string $prefix): array {
                $anchors = [];

                foreach ($sections as $index => $section) {
                    $title = trim((string) ($section['title'] ?? ''));
                    $slug = \Illuminate\Support\Str::slug($title);
                    $anchorId = $prefix.'-'.($slug !== '' ? $slug : 'section').'-'.($index + 1);

                    $sections[$index]['anchor_id'] = $anchorId;
                    $anchors[] = [
                        'id' => $anchorId,
                        'label' => $title !== '' ? $title : __('site.menu_page.title'),
                    ];
                }

                return [$sections, $anchors];
            };

            [$foodSections, $foodAnchors] = $attachAnchors($foodSections, 'food');
            [$drinkSections, $drinkAnchors] = $attachAnchors($drinkSections, 'drink');
            $quickLinks = array_merge($foodAnchors, $drinkAnchors);
        @endphp

        <section class="container page-hero reveal">
            <span class="eyebrow">{{ __('site.nav.menu') }}</span>
            <h1>{{ __('site.menu_page.title') }}</h1>
            <p>{{ __('site.menu_page.subtitle') }}</p>
        </section>

        @if (! empty($quickLinks))
            <section class="container reveal">
                <nav class="menu-quick-nav" aria-label="{{ __('site.menu_page.quick_nav_aria') }}">
                    <p class="menu-quick-nav-label">{{ __('site.menu_page.quick_nav_title') }}</p>
                    <div class="menu-quick-nav-list">
                        @foreach ($quickLinks as $quickLink)
                            <a class="menu-quick-link" href="#{{ $quickLink['id'] }}">{{ $quickLink['label'] }}</a>
                        @endforeach
                    </div>
                </nav>
            </section>
        @endif

        <section class="section">
            <div class="container">
                <div class="section-head reveal">
                    <h2>{{ __('site.menu_page.food_title') }}</h2>
                    <p>{{ __('site.menu_page.food_subtitle') }}</p>
                </div>
                <div class="menu-section-grid">
                    @forelse ($foodSections as $section)
                        <article id="{{ $section['anchor_id'] }}" class="menu-table reveal">
                            <h3>{{ $section['title'] }}</h3>
                            @if (! empty($section['description']))
                                <p class="menu-section-description">{{ $section['description'] }}</p>
                            @endif
                            @foreach ($section['items'] as $item)
                                <div class="menu-row">
                                    <div class="menu-row-top">
                                        <span>{{ $item['name'] }}</span>
                                        <span>{{ $item['price'] }}</span>
                                    </div>
                                    <p>{{ $item['description'] }}</p>
                                    @if (! empty($item['allergies']) || ! empty($item['intolerances']))
                                        <div class="menu-tags">
                                            @foreach (($item['allergies'] ?? []) as $allergy)
                                                <span class="menu-tag">
                                                    @if (file_exists(public_path('assets/images/allergies/'.$allergy['key'].'.png')))
                                                        <img src="{{ asset('assets/images/allergies/'.$allergy['key'].'.png') }}" alt="{{ $allergy['label'] }}">
                                                    @endif
                                                    {{ $allergy['label'] }}
                                                </span>
                                            @endforeach
                                            @foreach (($item['intolerances'] ?? []) as $intolerance)
                                                <span class="menu-tag menu-tag-intolerance">{{ $intolerance['label'] }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </article>
                    @empty
                        <article class="menu-table">
                            <h3>{{ __('site.menu_page.title') }}</h3>
                            <p>{{ __('site.menu_page.empty') }}</p>
                        </article>
                    @endforelse
                </div>

                @if (! empty($drinkSections))
                    <div class="section-head reveal" style="margin-top: 1.4rem;">
                        <h2>{{ __('site.menu_page.drinks_title') }}</h2>
                        <p>{{ __('site.menu_page.drinks_subtitle') }}</p>
                    </div>
                    <div class="menu-section-grid">
                        @foreach ($drinkSections as $section)
                            <article id="{{ $section['anchor_id'] }}" class="menu-table reveal">
                                <h3>{{ $section['title'] }}</h3>
                                @if (! empty($section['description']))
                                    <p class="menu-section-description">{{ $section['description'] }}</p>
                                @endif
                                @foreach ($section['items'] as $item)
                                    <div class="menu-row">
                                        <div class="menu-row-top">
                                            <span>{{ $item['name'] }}</span>
                                            <span>{{ $item['price'] }}</span>
                                        </div>
                                        @if ($item['description'] !== '')
                                            <p>{{ $item['description'] }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="section">
            <div class="container legal-grid">
                <article class="legal-card reveal">
                    <h3>{{ __('site.menu_page.allergies_title') }}</h3>
                    <div class="dietary-legend">
                        @foreach (($dietary['allergies'] ?? []) as $allergy)
                            <div class="dietary-item">
                                @if (file_exists(public_path('assets/images/allergies/'.$allergy['key'].'.png')))
                                    <img src="{{ asset('assets/images/allergies/'.$allergy['key'].'.png') }}" alt="{{ $allergy['label'] }}">
                                @endif
                                <span>{{ $allergy['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
@php
    $menuSchema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Menu',
        'name'     => __('site.menu_page.title'),
        'url'      => url()->current(),
        'inLanguage' => app()->getLocale(),
        'hasMenuSection' => array_map(static function (array $section): array {
            return [
                '@type' => 'MenuSection',
                'name'  => $section['title'] ?? '',
                'hasMenuItem' => array_map(static function (array $item): array {
                    $entry = [
                        '@type' => 'MenuItem',
                        'name'  => $item['name'] ?? '',
                    ];
                    if (!empty($item['description'])) {
                        $entry['description'] = $item['description'];
                    }
                    if (!empty($item['price'])) {
                        $entry['offers'] = [
                            '@type'         => 'Offer',
                            'price'         => preg_replace('/[^\d.,]/', '', (string) $item['price']),
                            'priceCurrency' => 'EUR',
                        ];
                    }
                    return $entry;
                }, $section['items'] ?? []),
            ];
        }, $menuSections),
    ];
@endphp
<script type="application/ld+json">
    {!! json_encode($menuSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
