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
    <meta property="og:title" content="@yield('title', __('site.brand'))">
    <meta property="og:description" content="@yield('meta_description', __('site.brand'))">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('assets/images/dishes/image.png') }}">
    <meta name="theme-color" content="{{ $themeOptions[$currentTheme]['color'] }}">
    <link rel="icon" href="{{ asset($restaurantBrand['favicon_path'] ?? 'favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset($restaurantBrand['favicon_32_path'] ?? 'favicon-32x32.png') }}">
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

    @include('partials.cookie-consent')

    <script>
        (function () {
            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            /* ── Scroll reveal (fade-up + directional variants) ─────────────── */
            const revealSelectors = '.reveal, .reveal-left, .reveal-right, .reveal-scale';
            const revealEls = document.querySelectorAll(revealSelectors);

            if (reducedMotion) {
                revealEls.forEach((el) => el.classList.add('active'));
            } else {
                const revealObserver = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('active');
                                revealObserver.unobserve(entry.target);
                            }
                        });
                    },
                    { threshold: 0.12 }
                );
                revealEls.forEach((el) => revealObserver.observe(el));
            }

            if (reducedMotion) return; /* skip all motion below for a11y */

            /* ── Cursor glow ─────────────────────────────────────────────────── */
            function initCursorGlow() {
                const glow = document.createElement('div');
                glow.className = 'cursor-glow';
                document.body.appendChild(glow);

                let cx = -999, cy = -999;
                let rx = -999, ry = -999;
                let rafId;

                document.addEventListener('mousemove', (e) => {
                    cx = e.clientX;
                    cy = e.clientY;
                });

                function tick() {
                    rx += (cx - rx) * 0.10;
                    ry += (cy - ry) * 0.10;
                    glow.style.transform =
                        'translate(' + (rx - 260) + 'px, ' + (ry - 260) + 'px)';
                    rafId = requestAnimationFrame(tick);
                }
                tick();
            }

            /* ── 3D card tilt ────────────────────────────────────────────────── */
            function initCardTilt() {
                const cards = document.querySelectorAll('.menu-card');
                const MAX_TILT = 10; /* degrees */
                const PERSPECTIVE = 900;

                cards.forEach((card) => {
                    let resetTimer;

                    card.addEventListener('mousemove', (e) => {
                        clearTimeout(resetTimer);
                        const rect   = card.getBoundingClientRect();
                        const cx     = rect.left + rect.width  / 2;
                        const cy     = rect.top  + rect.height / 2;
                        const dx     = (e.clientX - cx) / (rect.width  / 2);
                        const dy     = (e.clientY - cy) / (rect.height / 2);
                        const rotateX = -dy * MAX_TILT;
                        const rotateY =  dx * MAX_TILT;

                        card.style.transition = 'transform 0.12s ease, border-color 0.3s ease, box-shadow 0.3s ease';
                        card.style.transform  =
                            'perspective(' + PERSPECTIVE + 'px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) scale(1.03)';
                        card.style.boxShadow  =
                            '0 30px 60px color-mix(in srgb, var(--brand-deep) 24%, transparent)';
                    });

                    function resetCard() {
                        card.style.transition = 'transform 0.55s cubic-bezier(0.25,0.46,0.45,0.94), border-color 0.3s ease, box-shadow 0.3s ease';
                        card.style.transform  = 'perspective(' + PERSPECTIVE + 'px) rotateX(0deg) rotateY(0deg) scale(1)';
                        card.style.boxShadow  = '';
                    }

                    card.addEventListener('mouseleave', () => {
                        resetTimer = setTimeout(resetCard, 40);
                    });
                });
            }

            /* ── Parallax hero media on scroll ───────────────────────────────── */
            function initParallax() {
                const heroMedia = document.querySelector('.hero-media');
                if (!heroMedia) return;

                let ticking = false;

                window.addEventListener('scroll', () => {
                    if (!ticking) {
                        requestAnimationFrame(() => {
                            const scrollY = window.scrollY;
                            /* Subtle upward drift — 20% scroll speed */
                            heroMedia.style.transform =
                                'translateY(calc(-10px + ' + (-scrollY * 0.05) + 'px))';
                            ticking = false;
                        });
                        ticking = true;
                    }
                }, { passive: true });
            }

            /* ── Smooth FAQ accordion ─────────────────────────────────────────── */
            function initFaqAnimation() {
                document.querySelectorAll('.faq-item').forEach((details) => {
                    const summary = details.querySelector('summary');
                    const rawP    = details.querySelector('p');
                    if (!summary || !rawP) return;

                    /* Wrap the paragraph in .faq-content for height animation */
                    const wrapper = document.createElement('div');
                    wrapper.className = 'faq-content';
                    rawP.parentNode.insertBefore(wrapper, rawP);
                    wrapper.appendChild(rawP);

                    /* Start collapsed */
                    wrapper.style.maxHeight = '0px';
                    wrapper.style.opacity   = '0';

                    summary.addEventListener('click', (e) => {
                        e.preventDefault();
                        const isOpen = details.hasAttribute('open');

                        if (isOpen) {
                            /* Close */
                            wrapper.style.maxHeight = '0px';
                            wrapper.style.opacity   = '0';
                            setTimeout(() => details.removeAttribute('open'), 400);
                        } else {
                            /* Open */
                            details.setAttribute('open', '');
                            wrapper.style.maxHeight = wrapper.scrollHeight + rawP.scrollHeight + 'px';
                            wrapper.style.opacity   = '1';
                        }
                    });
                });
            }

            /* ── Chip micro-tilt on hover ─────────────────────────────────────── */
            function initChipTilt() {
                document.querySelectorAll('.chip').forEach((chip) => {
                    chip.addEventListener('mousemove', (e) => {
                        const rect = chip.getBoundingClientRect();
                        const dx = (e.clientX - rect.left - rect.width  / 2) / (rect.width  / 2);
                        const dy = (e.clientY - rect.top  - rect.height / 2) / (rect.height / 2);
                        chip.style.transform =
                            'perspective(400px) rotateX(' + (-dy * 8) + 'deg) rotateY(' + (dx * 8) + 'deg) translateY(-3px)';
                    });
                    chip.addEventListener('mouseleave', () => {
                        chip.style.transform = '';
                    });
                });
            }

            /* ── Boot ─────────────────────────────────────────────────────────── */
            initCursorGlow();
            initCardTilt();
            initParallax();
            initFaqAnimation();
            initChipTilt();
        })();
    </script>
    @stack('scripts')
</body>
</html>
