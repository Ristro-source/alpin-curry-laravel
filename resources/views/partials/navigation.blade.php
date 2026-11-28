<nav class="nav" aria-label="{{ __('site.nav.aria_main') }}">
    <div class="nav-links">
        <a href="{{ route('home', ['locale' => $locale]) }}" @if ($currentRoute === 'home') aria-current="page" @endif>{{ __('site.nav.home') }}</a>
        <a href="{{ route('menu', ['locale' => $locale]) }}" @if ($currentRoute === 'menu') aria-current="page" @endif>{{ __('site.nav.menu') }}</a>
        <a href="{{ route('gallery', ['locale' => $locale]) }}" @if ($currentRoute === 'gallery') aria-current="page" @endif>{{ __('site.nav.gallery') }}</a>
        <a href="{{ route('faq', ['locale' => $locale]) }}" @if ($currentRoute === 'faq') aria-current="page" @endif>{{ __('site.nav.faq') }}</a>
    </div>
    <a class="btn btn-primary" href="{{ route('home', ['locale' => $locale]) }}#contact">{{ __('site.nav.reserve') }}</a>

    <div class="lang-dropdown">
        <label class="sr-only" for="lang-select">{{ __('site.nav.aria_language') }}</label>
        <select
            id="lang-select"
            class="lang-select"
            aria-label="{{ __('site.nav.aria_language') }}"
            onchange="if (this.value) window.location.href = this.value;"
        >
            @foreach (['en', 'it', 'de'] as $lang)
                <option
                    value="{{ route($currentRoute, ['locale' => $lang]) }}"
                    @selected($locale === $lang)
                >
                    {{ strtoupper($lang) }}
                </option>
            @endforeach
        </select>
    </div>
</nav>
