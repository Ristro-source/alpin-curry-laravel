{{-- Style tag lives inside the single Livewire root (x-filament-panels::page).
     Placing it outside causes Livewire's multiple-root-element detection to throw. --}}
<x-filament-panels::page>
<style>
/* ================================================================
   Menu Generator — page-specific styles
   Uses Filament's CSS custom properties for colour consistency.
================================================================ */

/* ── Two-panel layout ───────────────────────────────────────── */
.mg-wrap {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    align-items: flex-start;
}
@media (min-width: 1024px) {
    .mg-wrap { flex-direction: row; }
}

.mg-sidebar {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
@media (min-width: 1024px) {
    .mg-sidebar { width: 22rem; flex-shrink: 0; }
}

.mg-main {
    flex: 1;
    min-width: 0;
}

/* ── Field label ────────────────────────────────────────────── */
.mg-label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 500;
    color: rgb(75 85 99);
    margin-bottom: 0.375rem;
    line-height: 1.4;
}
.dark .mg-label { color: rgb(156 163 175); }

/* ── Field spacing ──────────────────────────────────────────── */
.mg-field { margin-bottom: 0.875rem; }
.mg-field:last-child { margin-bottom: 0; }

.mg-divider {
    height: 1px;
    background: rgb(243 244 246);
    margin: 0.75rem 0;
}
.dark .mg-divider { background: rgb(31 41 55); }

/* ── Segmented pill button group ────────────────────────────── */
.mg-pills {
    display: flex;
    gap: 0.1875rem;
    background: rgb(243 244 246);
    border-radius: 0.625rem;
    padding: 0.1875rem;
}
.dark .mg-pills { background: rgb(31 41 55); }

.mg-pill {
    flex: 1;
    border: none;
    background: transparent;
    border-radius: 0.4375rem;
    padding: 0.3rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.12s, color 0.12s, box-shadow 0.12s;
    color: rgb(107 114 128);
    line-height: 1.4;
}
.dark .mg-pill { color: rgb(156 163 175); }

.mg-pill.active {
    background: #fff;
    color: rgb(var(--color-primary-600, 180 83 9));
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.12), 0 1px 2px -1px rgb(0 0 0 / 0.08);
}
.dark .mg-pill.active {
    background: rgb(17 24 39);
    color: rgb(var(--color-primary-400, 251 191 36));
}

/* ── Language checkbox chips ────────────────────────────────── */
.mg-lang-chips {
    display: flex;
    gap: 0.375rem;
    flex-wrap: wrap;
}

.mg-lang-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.65rem;
    border-radius: 9999px;
    border: 1.5px solid rgb(209 213 219);
    font-size: 0.75rem;
    font-weight: 700;
    cursor: pointer;
    transition: border-color 0.12s, background 0.12s, color 0.12s;
    color: rgb(107 114 128);
    background: transparent;
    user-select: none;
}
.dark .mg-lang-chip { border-color: rgb(55 65 81); color: rgb(156 163 175); }

.mg-lang-chip.active {
    border-color: rgb(var(--color-primary-500, 245 158 11));
    background: rgb(var(--color-primary-50, 255 251 235));
    color: rgb(var(--color-primary-700, 146 64 14));
}
.dark .mg-lang-chip.active {
    background: rgb(var(--color-primary-950, 69 26 3) / 0.3);
    color: rgb(var(--color-primary-400, 251 191 36));
}

.mg-lang-chip-dot {
    width: 0.45rem;
    height: 0.45rem;
    border-radius: 50%;
    background: currentColor;
    opacity: 0;
    transition: opacity 0.12s;
}
.mg-lang-chip.active .mg-lang-chip-dot { opacity: 1; }

/* ── Toggle row ─────────────────────────────────────────────── */
.mg-toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.1875rem 0;
}

.mg-toggle-label {
    font-size: 0.8125rem;
    color: rgb(55 65 81);
    line-height: 1.4;
}
.dark .mg-toggle-label { color: rgb(209 213 219); }

.mg-toggle {
    position: relative;
    display: inline-flex;
    align-items: center;
    width: 2.25rem;
    height: 1.25rem;
    border-radius: 9999px;
    border: none;
    cursor: pointer;
    flex-shrink: 0;
    transition: background-color 0.2s;
    outline: none;
}
.mg-toggle:focus-visible {
    box-shadow: 0 0 0 3px rgb(var(--color-primary-500, 245 158 11) / 0.4);
}
.mg-toggle[aria-checked="true"]  { background-color: rgb(var(--color-primary-500, 245 158 11)); }
.mg-toggle[aria-checked="false"] { background-color: rgb(209 213 219); }
.dark .mg-toggle[aria-checked="false"] { background-color: rgb(55 65 81); }

.mg-toggle-knob {
    position: absolute;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgb(0 0 0 / 0.25);
    transition: transform 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    top: 0.125rem;
}
.mg-toggle[aria-checked="true"]  .mg-toggle-knob { transform: translateX(1.125rem); }
.mg-toggle[aria-checked="false"] .mg-toggle-knob { transform: translateX(0.125rem); }

/* ── Layout style radio cards ───────────────────────────────── */
.mg-style-opts { display: flex; flex-direction: column; gap: 0.375rem; }

.mg-style-opt {
    display: flex;
    align-items: flex-start;
    gap: 0.625rem;
    padding: 0.625rem 0.75rem;
    border-radius: 0.625rem;
    border: 1.5px solid rgb(229 231 235);
    cursor: pointer;
    transition: border-color 0.12s, background 0.12s;
    background: transparent;
    text-align: left;
    width: 100%;
}
.dark .mg-style-opt { border-color: rgb(55 65 81); }

.mg-style-opt.active {
    border-color: rgb(var(--color-primary-500, 245 158 11));
    background: rgb(var(--color-primary-50, 255 251 235));
}
.dark .mg-style-opt.active {
    background: rgb(var(--color-primary-950, 69 26 3) / 0.25);
}

.mg-style-radio {
    width: 0.9375rem;
    height: 0.9375rem;
    border-radius: 50%;
    border: 2px solid rgb(209 213 219);
    flex-shrink: 0;
    margin-top: 0.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: border-color 0.12s;
}
.mg-style-opt.active .mg-style-radio {
    border-color: rgb(var(--color-primary-500, 245 158 11));
}

.mg-style-dot {
    width: 0.4375rem;
    height: 0.4375rem;
    border-radius: 50%;
    background: rgb(var(--color-primary-500, 245 158 11));
    transition: transform 0.12s;
}
.mg-style-opt:not(.active) .mg-style-dot { transform: scale(0); }
.mg-style-opt.active        .mg-style-dot { transform: scale(1); }

.mg-style-name {
    font-size: 0.8125rem;
    font-weight: 600;
    color: rgb(17 24 39);
    line-height: 1.4;
}
.dark .mg-style-name { color: rgb(243 244 246); }

.mg-style-desc {
    font-size: 0.7rem;
    color: rgb(107 114 128);
    margin-top: 0.125rem;
    line-height: 1.35;
}
.dark .mg-style-desc { color: rgb(107 114 128); }

/* ── Preview iframe ─────────────────────────────────────────── */
.mg-iframe-shell {
    border-radius: 0.5rem;
    overflow: hidden;
    border: 1px solid rgb(229 231 235);
    background: #edf2f7;
}
.dark .mg-iframe-shell { border-color: rgb(55 65 81); background: rgb(15 23 42); }

.mg-iframe {
    width: 100%;
    height: calc(100vh - 18rem);
    min-height: 520px;
    display: block;
    border: none;
}

/* ── Action bar ─────────────────────────────────────────────── */
.mg-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.625rem;
    padding-top: 0.875rem;
    border-top: 1px solid rgb(243 244 246);
    margin-top: 0.875rem;
}
.dark .mg-actions { border-color: rgb(31 41 55); }

.mg-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border-radius: 0.5rem;
    padding: 0.45rem 0.9rem;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.12s, border-color 0.12s, box-shadow 0.12s;
    line-height: 1;
}

.mg-btn-outline {
    background: transparent;
    border: 1px solid rgb(209 213 219);
    color: rgb(55 65 81);
}
.mg-btn-outline:hover { background: rgb(249 250 251); }
.dark .mg-btn-outline {
    border-color: rgb(75 85 99);
    color: rgb(209 213 219);
}
.dark .mg-btn-outline:hover { background: rgb(31 41 55); }

.mg-btn-filled {
    background: rgb(var(--color-primary-500, 245 158 11));
    border: 1px solid rgb(var(--color-primary-500, 245 158 11));
    color: #fff;
}
.mg-btn-filled:hover {
    background: rgb(var(--color-primary-600, 180 83 9));
    border-color: rgb(var(--color-primary-600, 180 83 9));
}
</style>
<div
    x-data="{
        menuType:        'all',
        langs:           ['en'],
        showDesc:        true,
        showAlrg:        true,
        showAlrgIcons:   true,
        showAlrgLegend:  true,
        layoutStyle:     'classic',
        colorScheme:     'brand',
        pageWidth:       'standard',
        sortOrder:       'default',
        headerTitle:     @js($this->restaurantName),
        headerSubtitle:  @js($this->restaurantSubtitle),
        showAddress:     true,
        showPhone:       true,
        footerNote:      'All prices include VAT. Please inform our staff of any allergies.',
        paperSize:       'A4',
        repeatHeader:    false,
        autoOrganize:    false,

        toggleLang(lang) {
            const idx = this.langs.indexOf(lang);
            if (idx === -1) {
                this.langs.push(lang);
            } else if (this.langs.length > 1) {
                // Always keep at least one language selected
                this.langs.splice(idx, 1);
            }
        },

        get previewUrl() {
            return '/admin/menu-preview?' + new URLSearchParams({
                type:          this.menuType,
                langs:         this.langs.join(','),
                descriptions:  this.showDesc        ? '1' : '0',
                allergens:     this.showAlrg        ? '1' : '0',
                allergenIcons: this.showAlrgIcons   ? '1' : '0',
                allergenLegend:this.showAlrgLegend  ? '1' : '0',
                layout:        this.layoutStyle,
                colors:        this.colorScheme,
                pageWidth:     this.pageWidth,
                sort:          this.sortOrder,
                title:         this.headerTitle,
                subtitle:      this.headerSubtitle,
                address:       this.showAddress ? '1' : '0',
                phone:         this.showPhone   ? '1' : '0',
                footer:        this.footerNote,
                paper:         this.paperSize,
                repeatHeader:  this.repeatHeader ? '1' : '0',
                autoOrganize:  this.autoOrganize ? '1' : '0',
                _t:            Date.now(),
            }).toString();
        },

        get downloadUrl() {
            return '/admin/menu-download?' + new URLSearchParams({
                type:          this.menuType,
                langs:         this.langs.join(','),
                descriptions:  this.showDesc        ? '1' : '0',
                allergens:     this.showAlrg        ? '1' : '0',
                allergenIcons: this.showAlrgIcons   ? '1' : '0',
                allergenLegend:this.showAlrgLegend  ? '1' : '0',
                layout:        this.layoutStyle,
                colors:        this.colorScheme,
                pageWidth:     this.pageWidth,
                sort:          this.sortOrder,
                title:         this.headerTitle,
                subtitle:      this.headerSubtitle,
                address:       this.showAddress ? '1' : '0',
                phone:         this.showPhone   ? '1' : '0',
                footer:        this.footerNote,
                paper:         this.paperSize,
                repeatHeader:  this.repeatHeader ? '1' : '0',
                autoOrganize:  this.autoOrganize ? '1' : '0',
            }).toString();
        },

        printMenu() {
            const f = document.getElementById('mg-iframe');
            if (f?.contentWindow) { f.contentWindow.focus(); f.contentWindow.print(); }
        },
    }"
    class="mg-wrap"
>

    {{-- ══════════════════════════════════════
         LEFT — Configuration sidebar
    ══════════════════════════════════════ --}}
    <aside class="mg-sidebar">

        {{-- Content ──────────────────────── --}}
        <x-filament::section
            heading="Content"
            icon="heroicon-o-document-text"
            :compact="true"
        >
            {{-- Menu Type --}}
            <div class="mg-field">
                <p class="mg-label">Menu Type</p>
                <div class="mg-pills" role="group">
                    @foreach(['all' => 'All', 'food' => 'Food', 'drink' => 'Drinks'] as $v => $l)
                        <button type="button"
                            class="mg-pill"
                            :class="menuType === '{{ $v }}' ? 'active' : ''"
                            @click="menuType = '{{ $v }}'"
                        >{{ $l }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Language — multi-select chips --}}
            <div class="mg-field">
                <p class="mg-label">Languages <span style="font-weight:400;color:rgb(107 114 128);font-size:0.7rem;">(select one or more)</span></p>
                <div class="mg-lang-chips" role="group">
                    @foreach(['en' => 'English', 'it' => 'Italiano', 'de' => 'Deutsch'] as $v => $l)
                        <button type="button"
                            class="mg-lang-chip"
                            :class="langs.includes('{{ $v }}') ? 'active' : ''"
                            @click="toggleLang('{{ $v }}')"
                        >
                            <span class="mg-lang-chip-dot"></span>
                            {{ $v === 'en' ? 'EN' : ($v === 'it' ? 'IT' : 'DE') }}
                            <span style="font-weight:400;font-size:0.68rem;opacity:0.75;">{{ $l }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mg-divider"></div>

            {{-- Toggles --}}
            <div class="mg-toggle-row">
                <span class="mg-toggle-label">Show descriptions</span>
                <button type="button" role="switch" class="mg-toggle"
                    :aria-checked="showDesc.toString()" @click="showDesc = !showDesc">
                    <span class="mg-toggle-knob"></span>
                </button>
            </div>
            <div class="mg-toggle-row">
                <span class="mg-toggle-label">Show allergen tags</span>
                <button type="button" role="switch" class="mg-toggle"
                    :aria-checked="showAlrg.toString()" @click="showAlrg = !showAlrg">
                    <span class="mg-toggle-knob"></span>
                </button>
            </div>
            <div class="mg-toggle-row" x-show="showAlrg" style="padding-left:0.75rem;">
                <span class="mg-toggle-label" style="font-size:0.75rem;">Use icon badges</span>
                <button type="button" role="switch" class="mg-toggle"
                    :aria-checked="showAlrgIcons.toString()" @click="showAlrgIcons = !showAlrgIcons">
                    <span class="mg-toggle-knob"></span>
                </button>
            </div>
            <div class="mg-toggle-row" x-show="showAlrg" style="padding-left:0.75rem;">
                <span class="mg-toggle-label" style="font-size:0.75rem;">Allergen legend in footer</span>
                <button type="button" role="switch" class="mg-toggle"
                    :aria-checked="showAlrgLegend.toString()" @click="showAlrgLegend = !showAlrgLegend">
                    <span class="mg-toggle-knob"></span>
                </button>
            </div>
        </x-filament::section>

        {{-- Sort Order ────────────────────── --}}
        <x-filament::section
            heading="Item Order"
            icon="heroicon-o-bars-arrow-down"
            :compact="true"
            collapsible
        >
            <div class="mg-style-opts mg-field">
                @foreach([
                    ['default',    'Default',      'Preserve the admin-defined display order'],
                    ['alpha-asc',  'A → Z',        'Sort items alphabetically ascending'],
                    ['alpha-desc', 'Z → A',        'Sort items alphabetically descending'],
                    ['price-asc',  'Price ↑',      'Cheapest items first'],
                    ['price-desc', 'Price ↓',      'Most expensive items first'],
                ] as [$v, $name, $desc])
                    <button type="button"
                        class="mg-style-opt"
                        :class="sortOrder === '{{ $v }}' ? 'active' : ''"
                        @click="sortOrder = '{{ $v }}'"
                    >
                        <div class="mg-style-radio">
                            <div class="mg-style-dot"></div>
                        </div>
                        <div>
                            <p class="mg-style-name">{{ $name }}</p>
                            <p class="mg-style-desc">{{ $desc }}</p>
                        </div>
                    </button>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Layout Style ─────────────────── --}}
        <x-filament::section
            heading="Layout Style"
            icon="heroicon-o-paint-brush"
            :compact="true"
            collapsible
        >
            <div class="mg-style-opts mg-field">
                @foreach([
                    ['classic', 'Classic',  'Traditional list with dotted price leaders'],
                    ['elegant', 'Elegant',  'Fine-dining with decorative border & generous spacing'],
                    ['modern',  'Modern',   'Two-column grid with bold category headings'],
                ] as [$v, $name, $desc])
                    <button type="button"
                        class="mg-style-opt"
                        :class="layoutStyle === '{{ $v }}' ? 'active' : ''"
                        @click="layoutStyle = '{{ $v }}'"
                    >
                        <div class="mg-style-radio">
                            <div class="mg-style-dot"></div>
                        </div>
                        <div>
                            <p class="mg-style-name">{{ $name }}</p>
                            <p class="mg-style-desc">{{ $desc }}</p>
                        </div>
                    </button>
                @endforeach
            </div>

            <div class="mg-divider"></div>

            {{-- Page Width --}}
            <div class="mg-field">
                <p class="mg-label">Page Width <span style="font-weight:400;color:rgb(107 114 128);font-size:0.7rem;">(preview only)</span></p>
                <div class="mg-pills" role="group">
                    @foreach(['standard' => 'Standard', 'full' => 'Full', 'compact' => 'Compact', 'zigzag' => 'Zig-Zag'] as $v => $l)
                        <button type="button"
                            class="mg-pill"
                            :class="pageWidth === '{{ $v }}' ? 'active' : ''"
                            @click="pageWidth = '{{ $v }}'"
                        >{{ $l }}</button>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="mg-label">Color Scheme</p>
                <div class="mg-pills" role="group">
                    @foreach(['brand' => 'Brand Colors', 'neutral' => 'Neutral'] as $v => $l)
                        <button type="button"
                            class="mg-pill"
                            :class="colorScheme === '{{ $v }}' ? 'active' : ''"
                            @click="colorScheme = '{{ $v }}'"
                        >{{ $l }}</button>
                    @endforeach
                </div>
            </div>
        </x-filament::section>

        {{-- Branding ─────────────────────── --}}
        <x-filament::section
            heading="Branding"
            icon="heroicon-o-building-storefront"
            :compact="true"
            collapsible
            :collapsed="true"
        >
            <div class="mg-field">
                <label class="mg-label">Header Title</label>
                <x-filament::input type="text" x-model="headerTitle" />
            </div>
            <div class="mg-field">
                <label class="mg-label">Header Subtitle</label>
                <x-filament::input type="text" x-model="headerSubtitle" />
            </div>

            <div class="mg-divider"></div>

            <div class="mg-toggle-row">
                <span class="mg-toggle-label">Show address</span>
                <button type="button" role="switch" class="mg-toggle"
                    :aria-checked="showAddress.toString()" @click="showAddress = !showAddress">
                    <span class="mg-toggle-knob"></span>
                </button>
            </div>
            <div class="mg-toggle-row">
                <span class="mg-toggle-label">Show phone number</span>
                <button type="button" role="switch" class="mg-toggle"
                    :aria-checked="showPhone.toString()" @click="showPhone = !showPhone">
                    <span class="mg-toggle-knob"></span>
                </button>
            </div>
        </x-filament::section>

        {{-- Footer Note ──────────────────── --}}
        <x-filament::section
            heading="Footer Note"
            icon="heroicon-o-chat-bubble-bottom-center-text"
            :compact="true"
            collapsible
            :collapsed="true"
        >
            <textarea
                x-model="footerNote"
                rows="3"
                class="fi-input w-full"
                style="resize:vertical;"
            ></textarea>
        </x-filament::section>

        {{-- Export ───────────────────────── --}}
        <x-filament::section
            heading="Export"
            icon="heroicon-o-arrow-down-tray"
            :compact="true"
            collapsible
            :collapsed="true"
        >
            <label class="mg-label">Paper Size</label>
            <x-filament::input.select x-model="paperSize">
                <option value="A4">A4 — 210 × 297 mm</option>
                <option value="A5">A5 — 148 × 210 mm</option>
                <option value="Letter">Letter — 8.5 × 11 in</option>
            </x-filament::input.select>

            <div class="mg-divider" style="margin-top:0.875rem;"></div>

            <div class="mg-toggle-row">
                <span class="mg-toggle-label">Repeat header on all PDF pages</span>
                <button type="button" role="switch" class="mg-toggle"
                    :aria-checked="repeatHeader.toString()" @click="repeatHeader = !repeatHeader">
                    <span class="mg-toggle-knob"></span>
                </button>
            </div>
            <div class="mg-toggle-row" style="align-items:flex-start;">
                <div>
                    <span class="mg-toggle-label">Auto-organize categories</span>
                    <p style="font-size:0.7rem;color:rgb(107 114 128);margin-top:0.1rem;line-height:1.35;">Groups dual-view categories side by side (food | drink)</p>
                </div>
                <button type="button" role="switch" class="mg-toggle" style="margin-top:0.15rem;"
                    :aria-checked="autoOrganize.toString()" @click="autoOrganize = !autoOrganize">
                    <span class="mg-toggle-knob"></span>
                </button>
            </div>
        </x-filament::section>

    </aside>

    {{-- ══════════════════════════════════════
         RIGHT — Live Preview
    ══════════════════════════════════════ --}}
    <div class="mg-main">
        <x-filament::section
            heading="Live Preview"
            icon="heroicon-o-eye"
        >
            <x-slot name="afterHeader">
                <x-filament::badge color="success" size="sm">
                    Auto-refresh
                </x-filament::badge>
            </x-slot>

            {{-- iframe --}}
            <div class="mg-iframe-shell">
                <iframe
                    id="mg-iframe"
                    :src="previewUrl"
                    class="mg-iframe"
                    title="Menu Preview"
                ></iframe>
            </div>

            {{-- Actions --}}
            <div class="mg-actions">
                <button type="button" @click="printMenu()" class="mg-btn mg-btn-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" style="width:1rem;height:1rem;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 0 0 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                    </svg>
                    Print
                </button>

                <a :href="downloadUrl" download class="mg-btn mg-btn-filled">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" style="width:1rem;height:1rem;">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Download PDF
                </a>
            </div>
        </x-filament::section>
    </div>

</div>
</x-filament-panels::page>
