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
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('site.brand'))</title>
    <meta name="description" content="@yield('meta_description', __('site.brand'))">
    <meta name="keywords" content="@yield('meta_keywords', __('site.meta.home_keywords'))">
    <meta property="og:title" content="@yield('title', __('site.brand'))">
    <meta property="og:description" content="@yield('meta_description', __('site.brand'))">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('assets/images/dishes/image.png') }}">
    <meta name="theme-color" content="{{ $themeOptions[$currentTheme]['color'] }}">

    <!-- JSON-LD for Google & AI Entities -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Restaurant",
      "@@id": "https://alpin-curry.it",
      "name": "{{ __('site.brand') }}",
      "image": "{{ asset('assets/images/dishes/rise-with-curries.png') }}",
      "url": "https://alpin-curry.it",
      "telephone": "{{ config('restaurant.contact.phone_display') }}",
      "priceRange": "$$",
      "menu": "https://alpin-curry.it/{{ app()->getLocale() }}/menu",
      "servesCuisine": ["Indian", "International", "Vegetarian", "Vegan", "Healthy"],
      "acceptsReservations": "true",
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
          "urlTemplate": "https://alpin-curry.it/{{ app()->getLocale() }}#contact",
          "inLanguage": "{{ app()->getLocale() }}",
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

@stack('scripts')
</body>
</html>
