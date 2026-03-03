@extends('layouts.alpin-curry')

@section('title', __('site.meta.home_title'))
@section('meta_description', __('site.meta.home_description'))
@section('meta_keywords', __('site.meta.home_keywords'))

@section('content')
    <main id="main-content">
        <x-ui.hero :locale="app()->getLocale()" />

        {{-- New: Features Section for High Quality, Veg/Vegan, Bar --}}
        <section class="section section-features" style="background: var(--brand-white); padding-bottom: 2rem; padding-top: 0;">
            <div class="container">
                <div class="feature-grid">
                    <div class="feature-item reveal" style="transition-delay: 50ms;">
                        <div class="feature-icon">✨</div>
                        <h3>{{ __('site.features.quality_title') }}</h3>
                        <p>{{ __('site.features.quality_text') }}</p>
                    </div>
                    <div class="feature-item reveal" style="transition-delay: 150ms;">
                        <div class="feature-icon">🥦</div>
                        <h3>{{ __('site.features.veg_vegan_title') }}</h3>
                        <p>{{ __('site.features.veg_vegan_text') }}</p>
                    </div>
                    <div class="feature-item reveal" style="transition-delay: 250ms;">
                        <div class="feature-icon">🍷</div>
                        <h3>{{ __('site.features.bar_drinks_title') }}</h3>
                        <p>{{ __('site.features.bar_drinks_text') }}</p>
                    </div>
                </div>

                {{-- Amenities Icons --}}
                <div class="amenity-grid reveal" style="transition-delay: 350ms; margin-top: 4rem;">
                    <div class="amenity-item">
                        <span class="amenity-icon">♿</span>
                        <span>{{ __('site.amenities.accessible') }}</span>
                    </div>
                    <div class="amenity-item">
                        <span class="amenity-icon">❄️</span>
                        <span>{{ __('site.amenities.ac') }}</span>
                    </div>
                    <div class="amenity-item">
                        <span class="amenity-icon">🚬</span>
                        <span>{{ __('site.amenities.smoking') }}</span>
                    </div>
                    <div class="amenity-item">
                        <span class="amenity-icon">🥡</span>
                        <span>{{ __('site.amenities.takeaway') }}</span>
                    </div>
                    <div class="amenity-item">
                        <span class="amenity-icon">📱</span>
                        <span>{{ __('site.amenities.online_order') }}</span>
                    </div>
                    <div class="amenity-item">
                        <span class="amenity-icon">🐾</span>
                        <span>{{ __('site.amenities.pets') }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-head reveal">
                    <h2>{{ __('site.sections.signature_title') }}</h2>
                    <p>{{ __('site.sections.signature_text') }}</p>
                </div>
                <div class="menu-grid">
                    @forelse (($featuredMenuItems ?? []) as $index => $item)
                        @php
                            $itemName = ! empty($item['name_key'])
                                ? __('site.home.featured_names.'.$item['name_key'])
                                : $item['name'];
                        @endphp
                        <article class="menu-card reveal" style="transition-delay: {{ ($index + 1) * 70 }}ms;">
                            <img src="{{ asset($item['image']) }}" alt="{{ __('site.home.signature_image_alt', ['name' => $itemName]) }} - Alpin Curry Merano">
                            <div class="menu-card-body">
                                <h3>{{ $itemName }}</h3>
                                @if (! empty($item['section_title']))
                                    <p class="menu-card-category">{{ $item['section_title'] }}</p>
                                @endif
                                <p>{{ $item['description'] !== '' ? $item['description'] : __('site.home.signature_fallback_description') }}</p>
                                <div class="meta">
                                    <span class="pill pill-spice">{{ __('site.home.signature_badge') }}</span>
                                    <strong>{{ $item['price'] !== '' ? $item['price'] : __('site.home.price_on_request') }}</strong>
                                </div>
                            </div>
                        </article>
                    @empty
                        <article class="menu-card reveal">
                            <img src="{{ asset('assets/images/dishes/image.png') }}" alt="{{ __('site.brand') }} - Indian Cuisine Merano">
                            <div class="menu-card-body">
                                <h3>{{ __('site.brand') }}</h3>
                                <p>{{ __('site.menu_page.empty') }}</p>
                            </div>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="section" style="background: var(--brand-white-alt);">
            <div class="container story">
                <div class="story-media reveal-left">
                    <img src="{{ asset('assets/images/dishes/rise-with-curries.png') }}" alt="{{ __('site.home.story_image_alt') }} - Authentic Indian Food Merano">
                </div>
                <article class="story-card reveal-right" style="transition-delay: 120ms;">
                    <div class="section-head" style="margin-bottom: 0.9rem;">
                        <h2>{{ __('site.sections.story_title') }}</h2>
                        <p>{{ __('site.sections.story_text') }}</p>
                    </div>
                    <div class="story-copy">
                        <p>{{ __('site.home.story_paragraph_1') }}</p>
                        <p>{{ __('site.home.story_paragraph_2') }}</p>
                    </div>
                    <ul class="story-points">
                        <li>{{ __('site.home.story_point_1') }}</li>
                        <li>{{ __('site.home.story_point_2') }}</li>
                        <li>{{ __('site.home.story_point_3') }}</li>
                    </ul>
                </article>
            </div>
        </section>

        {{-- New: Lunch Section for Students & Workers --}}
        <section class="section section-lunch">
            <div class="container">
                <div class="section-head reveal">
                    <h2>{{ __('site.lunch.title') }}</h2>
                    <p>{{ __('site.lunch.subtitle') }}</p>
                </div>

                <div class="lunch-box">
                    <div class="lunch-card reveal-left">
                        <span class="lunch-badge">Nutrient Rich</span>
                        <h3>{{ __('site.lunch.student_title') }}</h3>
                        <p>{{ __('site.lunch.student_text') }}</p>
                    </div>
                    <div class="lunch-card reveal-right">
                        <span class="lunch-badge">Quick Service</span>
                        <h3>{{ __('site.lunch.worker_title') }}</h3>
                        <p>{{ __('site.lunch.worker_text') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="section-head reveal">
                    <h2>{{ __('site.faq_page.title') }}</h2>
                    <p>{{ __('site.faq_page.subtitle') }}</p>
                </div>

                <div class="faq-list">
                    @foreach (['q1', 'q2', 'q3', 'q4'] as $faqKey)
                        <details class="faq-item reveal">
                            <summary>{{ __('site.faq.items.'.$faqKey.'.question') }}</summary>
                            <p>{{ __('site.faq.items.'.$faqKey.'.answer') }}</p>
                        </details>
                    @endforeach
                </div>

                <div class="hero-cta reveal">
                    <x-ui.button variant="ghost" :href="route('faq', ['locale' => app()->getLocale()])">
                        {{ __('site.faq_page.cta') }}
                    </x-ui.button>
                </div>
            </div>
        </section>

        <section id="contact" class="section">
            <div class="container contact-wrap reveal">
                <div class="contact-grid">
                    <article class="contact-main">
                        @php
                            $reservationErrors = $errors->getBag('reservation');
                            $contactErrors = $errors->getBag('contact');
                        @endphp

                        <h2 id="reservation-title">{{ __('site.sections.visit_title') }}</h2>
                        <p>{{ __('site.sections.visit_text') }}</p>
                        <p class="required-note">{{ __('site.reservation.required_note') }}</p>

                        @if (session('reservation_success'))
                            <div class="alert alert-success" role="status" aria-live="polite">{{ __('site.reservation.success') }}</div>
                        @endif
                        @if ($reservationErrors->has('form'))
                            <div class="alert alert-error" role="alert">{{ $reservationErrors->first('form') }}</div>
                        @endif

                        <form
                            id="reservation-form"
                            class="reservation-form"
                            method="POST"
                            action="{{ route('reservations.store', ['locale' => app()->getLocale()]) }}"
                            aria-labelledby="reservation-title"
                        >
                            @csrf

                            <div class="honeypot" aria-hidden="true">
                                <label for="reservation_website">{{ __('site.forms.honeypot_label') }}</label>
                                <input
                                    id="reservation_website"
                                    type="text"
                                    name="reservation_website"
                                    tabindex="-1"
                                    autocomplete="off"
                                    value="{{ old('reservation_website') }}"
                                >
                            </div>
                            <input type="hidden" name="reservation_started_at" value="{{ old('reservation_started_at', now()->timestamp) }}">

                            <div class="field-grid">
                                <x-ui.input
                                    :label="__('site.reservation.name')"
                                    id="reservation_name"
                                    name="name"
                                    :value="old('name')"
                                    :placeholder="__('site.reservation.name')"
                                    autocomplete="name"
                                    errorBag="reservation"
                                    required
                                />
                                <x-ui.input
                                    :label="__('site.reservation.phone')"
                                    id="reservation_phone"
                                    name="phone"
                                    :value="old('phone')"
                                    placeholder="+39 ..."
                                    autocomplete="tel"
                                    inputmode="tel"
                                    errorBag="reservation"
                                    required
                                />
                            </div>
                            <div class="field-grid">
                                <x-ui.input
                                    :label="__('site.reservation.email')"
                                    id="reservation_email"
                                    name="email"
                                    type="email"
                                    :value="old('email')"
                                    placeholder="name@example.com"
                                    autocomplete="email"
                                    errorBag="reservation"
                                    required
                                />
                                <label class="field">
                                    <span>{{ __('site.reservation.guests') }} <span aria-hidden="true">*</span></span>
                                    <select
                                        id="reservation_guests"
                                        name="guests"
                                        required
                                        aria-required="true"
                                        aria-invalid="{{ $reservationErrors->has('guests') ? 'true' : 'false' }}"
                                        @if ($reservationErrors->has('guests')) aria-describedby="reservation_guests-error" @endif
                                    >
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" @selected(old('guests', 2) == $i)>{{ $i }}</option>
                                        @endfor
                                    </select>

                                    @if ($reservationErrors->has('guests'))
                                        <small id="reservation_guests-error" class="field-error" role="alert">{{ $reservationErrors->first('guests') }}</small>
                                    @endif
                                </label>
                            </div>
                            <div class="field-grid">
                                <x-ui.input
                                    :label="__('site.reservation.date')"
                                    id="reservation_date"
                                    name="reservation_date"
                                    type="date"
                                    :value="old('reservation_date')"
                                    autocomplete="off"
                                    errorBag="reservation"
                                    required
                                />
                                <x-ui.input
                                    :label="__('site.reservation.time')"
                                    id="reservation_time"
                                    name="reservation_time"
                                    type="time"
                                    :value="old('reservation_time')"
                                    autocomplete="off"
                                    errorBag="reservation"
                                    required
                                />
                            </div>
                            <x-ui.textarea
                                :label="__('site.reservation.notes')"
                                id="reservation_message"
                                name="message"
                                :placeholder="__('site.reservation.notes')"
                                errorBag="reservation"
                            >{{ old('message') }}</x-ui.textarea>
                            <div class="hero-cta">
                                <x-ui.button type="submit">
                                    {{ __('site.reservation.submit') }}
                                </x-ui.button>
                                <x-ui.button
                                    variant="ghost"
                                    :href="$restaurantWhatsappUrl"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    {{ __('site.reservation.whatsapp') }}
                                </x-ui.button>
                            </div>
                        </form>

                        <section class="contact-form-block" aria-labelledby="contact-form-title">
                            <h3 id="contact-form-title">{{ __('site.contact.form_title') }}</h3>
                            <p>{{ __('site.contact.form_text') }}</p>

                            @if (session('contact_success'))
                                <div class="alert alert-success" role="status" aria-live="polite">{{ __('site.contact.success') }}</div>
                            @endif
                            @if ($contactErrors->has('form'))
                                <div class="alert alert-error" role="alert">{{ $contactErrors->first('form') }}</div>
                            @endif

                            <form
                                id="contact-form"
                                class="contact-form"
                                method="POST"
                                action="{{ route('contact.store', ['locale' => app()->getLocale()]) }}"
                                aria-labelledby="contact-form-title"
                            >
                                @csrf

                                <div class="honeypot" aria-hidden="true">
                                    <label for="contact_website">{{ __('site.forms.honeypot_label') }}</label>
                                    <input
                                        id="contact_website"
                                        type="text"
                                        name="contact_website"
                                        tabindex="-1"
                                        autocomplete="off"
                                        value="{{ old('contact_website') }}"
                                    >
                                </div>
                                <input type="hidden" name="contact_started_at" value="{{ old('contact_started_at', now()->timestamp) }}">

                                <div class="field-grid">
                                    <x-ui.input
                                        :label="__('site.contact.name')"
                                        id="contact_name"
                                        name="contact_name"
                                        :value="old('contact_name')"
                                        :placeholder="__('site.contact.name')"
                                        autocomplete="name"
                                        errorBag="contact"
                                        required
                                    />
                                    <x-ui.input
                                        :label="__('site.contact.email')"
                                        id="contact_email"
                                        name="contact_email"
                                        type="email"
                                        :value="old('contact_email')"
                                        placeholder="name@example.com"
                                        autocomplete="email"
                                        errorBag="contact"
                                        required
                                    />
                                </div>

                                <x-ui.input
                                    :label="__('site.contact.phone')"
                                    id="contact_phone"
                                    name="contact_phone"
                                    :value="old('contact_phone')"
                                    placeholder="+39 ..."
                                    autocomplete="tel"
                                    inputmode="tel"
                                    errorBag="contact"
                                />

                                <x-ui.textarea
                                    :label="__('site.contact.message')"
                                    id="contact_message"
                                    name="contact_message"
                                    :placeholder="__('site.contact.message')"
                                    errorBag="contact"
                                    required
                                >{{ old('contact_message') }}</x-ui.textarea>

                                <div class="hero-cta">
                                    <x-ui.button type="submit">
                                        {{ __('site.contact.submit') }}
                                    </x-ui.button>
                                </div>
                            </form>
                        </section>

                        <div class="details">
                            <div><strong>{{ __('site.contact.address_label') }}:</strong> {{ $restaurantAddressLine }}</div>
                            <div><strong>{{ __('site.contact.phone_label') }}:</strong> <a href="tel:{{ $restaurantPhoneHref }}">{{ $restaurantContact['phone_display'] ?? '' }}</a></div>
                            <div><strong>{{ __('site.contact.email_label') }}:</strong> <a href="mailto:{{ $restaurantContact['email'] ?? '' }}">{{ $restaurantContact['email'] ?? '' }}</a></div>
                        </div>
                        <div class="hours">
                            <div><strong>{{ __('site.hours.mon') }}</strong></div>
                            <div><strong>{{ __('site.hours.week') }}</strong></div>
                        </div>
                    </article>
                    <div class="contact-map">
                        <div class="map-panel">
                            <iframe
                                title="{{ __('site.home.map_iframe_title') }}"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                src="{{ $restaurantMapEmbedUrl }}">
                            </iframe>
                            <div class="map-note">{{ __('site.contact.map_note') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
