@extends('layouts.alpin-curry')

@section('title', __('site.menu_page.title').' | '.__('site.brand'))
@section('meta_description', __('site.menu_page.subtitle'))

@section('content')
    <main id="main-content">
        <section class="container page-hero reveal">
            <span class="eyebrow">{{ __('site.nav.menu') }}</span>
            <h1>{{ __('site.menu_page.title') }}</h1>
            <p>{{ __('site.menu_page.subtitle') }}</p>
            <div class="menu-pdf-actions">
                <x-ui.button href="#menu-pages">{{ __('site.menu_page.view_menu') }}</x-ui.button>
                <x-ui.button variant="ghost" href="#menu-desserts">{{ __('site.menu_page.view_desserts') }}</x-ui.button>
            </div>
            <p class="menu-pdf-link"><a href="{{ asset('menu/AlpinCurry_Menu.pdf') }}?v={{ filemtime(public_path('menu/AlpinCurry_Menu.pdf')) }}" target="_blank" rel="noopener">{{ __('site.menu_page.open_pdf') }}</a></p>
        </section>

        <section id="menu-pages" class="menu-pdf-pages">
            @foreach ($pages as $i => $page)
                <img @if (str_starts_with($page, 'desserts-1')) id="menu-desserts" @endif
                     src="{{ asset('menu/pages/'.$page) }}?v={{ filemtime(public_path('menu/pages/'.$page)) }}"
                     width="1080" height="1528" alt="{{ __('site.menu_page.page_alt') }} {{ $i + 1 }}"
                     loading="{{ $i < 2 ? 'eager' : 'lazy' }}" decoding="async">
            @endforeach
        </section>
    </main>
@endsection
