<?php

namespace App\Http\Controllers;

use App\Services\MenuCatalogService;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Generates "legacy-style" food and drinks PDFs that mirror the original
 * Angular FoodMenuGenerator / DrinkMenuGenerator behaviour:
 *
 * Food  — single column, 3-language descriptions (DE → IT → EN),
 *          allergen PNG icons inline, allergen legend in footer.
 *
 * Drinks — 2-column compact layout, no descriptions, categories
 *          distributed left↔right in rotational order (smallest first),
 *          vertical separator between columns.
 */
class LegacyMenuController extends Controller
{
    public function __construct(private readonly MenuCatalogService $catalog) {}

    // ── Food ──────────────────────────────────────────────────────────────

    public function food(): \Symfony\Component\HttpFoundation\Response
    {
        // Build multi-language description map  [item name → [lang → desc]]
        // Order matches FoodMenuGenerator: De first, then It, then En
        $descMap = [];
        foreach (['de', 'it', 'en'] as $lang) {
            foreach ($this->catalog->getMenuByLocale($lang) as $section) {
                foreach ($section['items'] ?? [] as $item) {
                    $name = (string) ($item['name'] ?? '');
                    $desc = (string) ($item['description'] ?? '');
                    if ($name !== '' && $desc !== '' && $desc !== $name) {
                        $descMap[$name][$lang] = $desc;
                    }
                }
            }
        }

        // Primary data in EN (for allergen labels in English)
        $sections = $this->filterSections(
            $this->catalog->getMenuByLocale('en'),
            'food'
        );

        $allergenImageMap = $this->buildAllergenImageMap();

        $usedAllergens = [];
        foreach ($sections as $section) {
            foreach ($section['items'] ?? [] as $item) {
                foreach (array_merge($item['allergies'] ?? [], $item['intolerances'] ?? []) as $tag) {
                    $key = (string) ($tag['key'] ?? '');
                    if ($key !== '' && ! isset($usedAllergens[$key])) {
                        $usedAllergens[$key] = (string) ($tag['label'] ?? $key);
                    }
                }
            }
        }

        $pdf = Pdf::loadView('menu-legacy.food', [
            'sections'         => $sections,
            'descMap'          => $descMap,
            'allergenImageMap' => $allergenImageMap,
            'usedAllergens'    => $usedAllergens,
            'title'            => (string) config('restaurant.name', 'Alpin Curry'),
        ])->setPaper('a4', 'portrait')
          ->setOptions($this->dompdfOptions());

        return $pdf->download('food-menu.pdf');
    }

    // ── Drinks ────────────────────────────────────────────────────────────

    public function drinks(): \Symfony\Component\HttpFoundation\Response
    {
        $sections = $this->filterSections(
            $this->catalog->getMenuByLocale('en'),
            'drink'
        );

        // Mirror DrinkMenuGenerator.getReorderedCategories():
        // sort by item count ascending so smaller categories go first
        usort($sections, fn ($a, $b) => count($a['items'] ?? []) <=> count($b['items'] ?? []));

        // Mirror drawCategoriesLeftToRight(): rotational column assignment
        // col 0 → even index, col 1 → odd index
        $leftCol  = [];
        $rightCol = [];
        foreach ($sections as $i => $section) {
            if ($i % 2 === 0) {
                $leftCol[] = $section;
            } else {
                $rightCol[] = $section;
            }
        }

        $pdf = Pdf::loadView('menu-legacy.drinks', [
            'leftCol'  => $leftCol,
            'rightCol' => $rightCol,
            'title'    => (string) config('restaurant.name', 'Alpin Curry'),
        ])->setPaper('a4', 'portrait')
          ->setOptions($this->dompdfOptions());

        return $pdf->download('drinks-menu.pdf');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Filter sections by type, exclude display_type=off, sort by order.
     *
     * @return array<int, array<string, mixed>>
     */
    private function filterSections(array $allSections, string $type): array
    {
        $filtered = array_values(array_filter(
            $allSections,
            fn ($s) => ($s['type'] ?? '') === $type && ($s['display_type'] ?? 'dual') !== 'off'
        ));

        usort($filtered, fn ($a, $b) => (int) ($a['order'] ?? 999) <=> (int) ($b['order'] ?? 999));

        return $filtered;
    }

    /**
     * Load allergen PNG images as base64 data URIs (dompdf-safe, no remote).
     * Adds variant aliases so slugged DB keys all resolve correctly.
     *
     * @return array<string, string>
     */
    private function buildAllergenImageMap(): array
    {
        $map = [];
        $dir = public_path('images/allergies');

        if (is_dir($dir)) {
            foreach (glob($dir.'/*.png') ?: [] as $file) {
                $key        = basename($file, '.png');
                $raw        = file_get_contents($file);
                $map[$key]  = 'data:image/png;base64,'.base64_encode($raw !== false ? $raw : '');
            }
        }

        // Alias variants → canonical filename key
        $aliases = [
            'crustacean'                    => 'crustaceans',
            'egg'                           => 'eggs',
            'peanut'                        => 'peanuts',
            'soya-beans'                    => 'soybeans',
            'soybean'                       => 'soybeans',
            'soy'                           => 'soybeans',
            'dairy'                         => 'milk',
            'nut'                           => 'nuts',
            'sesame-seeds'                  => 'sesame',
            'sulphites'                     => 'sulphur-dioxide',
            'sulphur-dioxide-and-sulphites' => 'sulphur-dioxide',
            'mollusc'                       => 'molluscs',
        ];

        foreach ($aliases as $alias => $canonical) {
            if (isset($map[$canonical])) {
                $map[$alias] = $map[$canonical];
            }
        }

        return $map;
    }

    /**
     * @return array<string, mixed>
     */
    private function dompdfOptions(): array
    {
        return [
            'dpi'                  => 150,
            'defaultFont'          => 'serif',
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
            'defaultMediaType'     => 'print',
        ];
    }
}
