@props([
    'locale',
])

<section class="container hero">
    <x-ux.reveal>
        <span class="eyebrow">{{ __('site.hero.eyebrow') }}</span>
        <h1>{{ __('site.hero.title') }}</h1>
        <p>{{ __('site.hero.text') }}</p>
        <div class="hero-cta">
            <x-ui.button :href="'tel:'.$restaurantPhoneHref">
                {{ __('site.hero.cta_call') }}
            </x-ui.button>
            <x-ui.button variant="ghost" :href="route('menu', ['locale' => $locale])">
                {{ __('site.hero.cta_menu') }}
            </x-ui.button>
        </div>
        <div class="hero-cards">
            <x-ui.chip :title="__('site.highlights.fresh_title')" :text="__('site.highlights.fresh_text')" />
            <x-ui.chip :title="__('site.highlights.veg_title')" :text="__('site.highlights.veg_text')" />
            <x-ui.chip :title="__('site.highlights.lunch_title')" :text="__('site.highlights.lunch_text')" />
        </div>
    </x-ux.reveal>

    <x-ux.reveal class="hero-media" :delay="120">
        <video autoplay muted loop playsinline preload="none" poster="{{ asset('assets/images/dishes/image.png') }}">
            <source src="{{ asset('assets/videos/cooking-and-serving.mp4') }}" type="video/mp4">
            <img src="{{ asset('assets/images/dishes/image.png') }}" alt="{{ __('site.hero.video_alt') }}">
        </video>
        <div class="hero-badge">
            <div>
                <strong>{{ __('site.hero.badge_title') }}</strong>
                <p>{{ __('site.hero.badge_text') }}</p>
            </div>
            <x-ui.button variant="ghost" href="#contact">
                {{ __('site.nav.visit') }}
            </x-ui.button>
        </div>
    </x-ux.reveal>
</section>
