<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $locale = app()->getLocale();

    $themeOptions = config('alpin_themes.options', []);
    $defaultTheme = config('alpin_themes.default', 'saffron-classic');
    $currentTheme = $defaultTheme;
    if (! array_key_exists($currentTheme, $themeOptions)) {
        $currentTheme = array_key_first($themeOptions) ?? 'saffron-classic';
    }

    $currentRoute = Route::currentRouteName() ?? 'home';

    // Hreflang alternate URLs for every supported locale
    $siteLocales  = ['en', 'it', 'de'];
    $hreflangs    = [];
    foreach ($siteLocales as $hl) {
        try {
            $hreflangs[$hl] = route($currentRoute, ['locale' => $hl]);
        } catch (\Throwable) {
            $hreflangs[$hl] = config('app.url') . '/' . $hl;
        }
    }

    // OG locale codes
    $ogLocaleMap = ['en' => 'en_US', 'it' => 'it_IT', 'de' => 'de_DE'];

    // Breadcrumb for non-home pages
    $breadcrumbRouteMap = [
        'menu'            => __('site.nav.menu'),
        'gallery'         => __('site.nav.gallery'),
        'faq'             => __('site.nav.faq'),
        'legal'           => 'Legal',
        'legal.privacy'   => 'Privacy Policy',
        'legal.cookies'   => 'Cookie Policy',
        'legal.impressum' => 'Impressum',
        'legal.terms'     => 'Terms',
    ];
    $breadcrumb = [['name' => __('site.brand'), 'url' => route('home', ['locale' => $locale])]];
    if (str_starts_with((string) $currentRoute, 'legal.')) {
        $breadcrumb[] = ['name' => 'Legal', 'url' => route('legal', ['locale' => $locale])];
    }
    if (isset($breadcrumbRouteMap[$currentRoute])) {
        $breadcrumb[] = ['name' => $breadcrumbRouteMap[$currentRoute], 'url' => url()->current()];
    }
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('site.brand'))</title>
    <meta name="description" content="@yield('meta_description', __('site.meta.home_description'))">
    <meta name="keywords" content="@yield('meta_keywords', __('site.meta.home_keywords'))">
    <meta name="theme-color" content="{{ $themeOptions[$currentTheme]['color'] }}">
    <meta name="robots" content="index, follow">

    {{-- Canonical & hreflang (critical for multilingual SEO) --}}
    <link rel="canonical" href="{{ url()->current() }}">
    @foreach ($hreflangs as $hl => $href)
        <link rel="alternate" hreflang="{{ $hl }}" href="{{ $href }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $hreflangs['en'] ?? url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type"              content="website">
    <meta property="og:site_name"         content="{{ __('site.brand') }}">
    <meta property="og:url"               content="{{ url()->current() }}">
    <meta property="og:title"             content="@yield('title', __('site.brand'))">
    <meta property="og:description"       content="@yield('meta_description', __('site.meta.home_description'))">
    <meta property="og:image"             content="@yield('og_image', asset('assets/images/dishes/image.png'))">
    <meta property="og:image:width"       content="1200">
    <meta property="og:image:height"      content="630">
    <meta property="og:image:alt"         content="@yield('title', __('site.brand'))">
    <meta property="og:locale"            content="{{ $ogLocaleMap[$locale] ?? 'en_US' }}">
    @foreach ($siteLocales as $altLocale)
        @if ($altLocale !== $locale)
            <meta property="og:locale:alternate" content="{{ $ogLocaleMap[$altLocale] }}">
        @endif
    @endforeach

    {{-- Twitter / X Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="@yield('title', __('site.brand'))">
    <meta name="twitter:description" content="@yield('meta_description', __('site.meta.home_description'))">
    <meta name="twitter:image"       content="@yield('og_image', asset('assets/images/dishes/image.png'))">

    {{-- Restaurant JSON-LD (all pages) --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Restaurant",
      "@@id": "{{ config('app.url') }}",
      "name": "{{ __('site.brand') }}",
      "image": "{{ asset('assets/images/dishes/rise-with-curries.png') }}",
      "url": "{{ config('app.url') }}",
      "telephone": "{{ config('restaurant.contact.phone_display') }}",
      "email": "{{ config('restaurant.contact.email') }}",
      "priceRange": "$$",
      "menu": "{{ route('menu', ['locale' => $locale]) }}",
      "servesCuisine": ["Indian", "International", "Vegetarian", "Vegan", "Healthy"],
      "acceptsReservations": "true",
      "currenciesAccepted": "EUR",
      "paymentAccepted": "Cash, Credit Card",
      "address": {
        "@@type": "PostalAddress",
        "streetAddress": "{{ config('restaurant.address.street') }} {{ config('restaurant.address.street_number') }}",
        "addressLocality": "{{ config('restaurant.address.city') }}",
        "postalCode": "{{ config('restaurant.address.postal_code') }}",
        "addressRegion": "{{ config('restaurant.address.region') }}",
        "addressCountry": "{{ config('restaurant.address.country_code') }}"
      },
      "geo": {
        "@@type": "GeoCoordinates",
        "latitude": 46.671,
        "longitude": 11.158
      },
      "hasMap": "https://www.google.com/maps/search/?api=1&query={{ urlencode(config('restaurant.map_query')) }}",
      "openingHoursSpecification": [
        {
          "@@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
          "opens": "11:00",
          "closes": "14:30"
        },
        {
          "@@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
          "opens": "17:30",
          "closes": "22:30"
        }
      ],
      "sameAs": [
        "https://www.facebook.com/alpincurry",
        "https://www.instagram.com/alpincurry"
      ],
      "potentialAction": {
        "@@type": "ReserveAction",
        "target": {
          "@@type": "EntryPoint",
          "urlTemplate": "{{ route('home', ['locale' => $locale]) }}#contact",
          "inLanguage": "{{ $locale }}",
          "actionPlatform": [
            "http://schema.org/DesktopWebPlatform",
            "http://schema.org/MobileWebPlatform"
          ]
        },
        "result": {
          "@@type": "Reservation",
          "name": "Book a Table"
        }
      },
      "description": "{{ __('site.meta.home_description') }}",
      "amenityFeature": [
        {"@@type": "LocationFeatureSpecification", "name": "Vegetarian options", "value": true},
        {"@@type": "LocationFeatureSpecification", "name": "Vegan options", "value": true},
        {"@@type": "LocationFeatureSpecification", "name": "Takeaway", "value": true},
        {"@@type": "LocationFeatureSpecification", "name": "Online Ordering", "value": true},
        {"@@type": "LocationFeatureSpecification", "name": "Accessible Entrance", "value": true},
        {"@@type": "LocationFeatureSpecification", "name": "Air Conditioned", "value": true},
        {"@@type": "LocationFeatureSpecification", "name": "Smoking Area", "value": true},
        {"@@type": "LocationFeatureSpecification", "name": "Pet Friendly", "value": true},
        {"@@type": "LocationFeatureSpecification", "name": "Central Location", "value": true},
        {"@@type": "LocationFeatureSpecification", "name": "Bar", "value": true}
      ]
    }
    </script>

    {{-- WebSite JSON-LD (enables Google Sitelinks Search Box) --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "WebSite",
      "name": "{{ __('site.brand') }}",
      "url": "{{ config('app.url') }}",
      "inLanguage": ["en", "it", "de"],
      "potentialAction": {
        "@@type": "SearchAction",
        "target": {
          "@@type": "EntryPoint",
          "urlTemplate": "{{ route('menu', ['locale' => $locale]) }}?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
      }
    }
    </script>

    @stack('head')

    <link rel="icon" href="{{ asset($restaurantBrand['favicon_path'] ?? 'favicon.ico') }}" sizes="any">    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset($restaurantBrand['favicon_32_path'] ?? 'favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($restaurantBrand['favicon_16_path'] ?? 'favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($restaurantBrand['apple_touch_icon_path'] ?? 'apple-touch-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/alpin-curry-site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/alpin-curry-animations.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-theme="{{ $currentTheme }}">
    <a class="skip-link" href="#main-content">{{ __('site.nav.skip_to_content') }}</a>

    @include('partials.header', [
        'locale' => $locale,
        'currentRoute' => $currentRoute,
    ])

    @yield('content')

    @include('partials.footer')

    @include('partials.whatsapp-fab')

    @include('partials.order-fabs')

    @include('partials.cookie-consent')

@if (count($breadcrumb) > 1)
    @php
        $breadcrumbSchema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => array_map(
                fn (int $i, array $b): array => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $b['name'], 'item' => $b['url']],
                array_keys($breadcrumb),
                $breadcrumb,
            ),
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endif

@stack('scripts')
</body>
</html>
