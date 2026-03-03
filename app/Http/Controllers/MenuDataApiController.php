<?php

namespace App\Http\Controllers;

use App\Services\MenuCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Returns menu data in the exact shape expected by the JS
 * FoodMenuGenerator / DrinkMenuGenerator classes:
 *
 * [
 *   {
 *     "name": "Starters",
 *     "order": 1,
 *     "foodMenuItems": [
 *       {
 *         "name": "Samosa",
 *         "price": "€6.50",
 *         "descriptionDe": "...",
 *         "descriptionIt": "...",
 *         "descriptionEn": "...",
 *         "foodAllergies": [ { "key": "gluten" }, ... ]
 *       }
 *     ]
 *   }
 * ]
 *
 * The allergen keys are normalised to match the keys used in
 * FoodMenuGenerator.allergiesImageMap (e.g. "sulphurDioxide" not
 * "sulphur-dioxide").
 */
class MenuDataApiController extends Controller
{
    /**
     * Maps from slugged DB keys → the camelCase/exact key the JS allergiesImageMap uses.
     * Any key not in this map is passed through as-is.
     */
    private const ALLERGEN_KEY_MAP = [
        'sulphur-dioxide'                    => 'sulphurDioxide',
        'sulphur-dioxide-and-sulphites'      => 'sulphurDioxide',
        'sulphites'                          => 'sulphurDioxide',
        'crustacean'                         => 'crustaceans',
        'egg'                                => 'eggs',
        'peanut'                             => 'peanuts',
        'soya-beans'                         => 'soybeans',
        'soybean'                            => 'soybeans',
        'soy'                                => 'soybeans',
        'dairy'                              => 'milk',
        'nut'                                => 'nuts',
        'sesame-seeds'                       => 'sesame',
        'mollusc'                            => 'molluscs',
    ];

    public function __construct(private readonly MenuCatalogService $catalog) {}

    public function __invoke(Request $request): JsonResponse
    {
        $type = $request->query('type', 'food');

        if ($type === 'drink') {
            return response()->json($this->buildDrinksData());
        }

        return response()->json($this->buildFoodData());
    }

    // ── Food ──────────────────────────────────────────────────────────────

    /**
     * Load food sections in all 3 languages and merge descriptions into
     * descriptionDe / descriptionIt / descriptionEn per item.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildFoodData(): array
    {
        // Load primary structure (EN) — includes allergen keys
        $enSections = $this->filterSections(
            $this->catalog->getMenuByLocale('en'),
            'food'
        );

        // Build name-keyed description maps for DE and IT
        $descDe = $this->buildDescriptionMap('de');
        $descIt = $this->buildDescriptionMap('it');
        $descEn = $this->buildDescriptionMap('en');

        $result = [];

        foreach ($enSections as $section) {
            $items = [];

            foreach ($section['items'] ?? [] as $item) {
                $name = (string) ($item['name'] ?? '');
                $price = (string) ($item['price'] ?? '');

                $dDe = (string) ($descDe[$name] ?? '');
                $dIt = (string) ($descIt[$name] ?? '');
                $dEn = (string) ($descEn[$name] ?? '');

                // Suppress description when it is just the item name (fallback value)
                $dDe = ($dDe === $name) ? '' : $dDe;
                $dIt = ($dIt === $name) ? '' : $dIt;
                $dEn = ($dEn === $name) ? '' : $dEn;

                // Build allergen array in the JS-expected format
                $allergies = [];
                foreach (array_merge($item['allergies'] ?? [], $item['intolerances'] ?? []) as $tag) {
                    $rawKey = (string) ($tag['key'] ?? '');
                    if ($rawKey === '') {
                        continue;
                    }
                    $jsKey = self::ALLERGEN_KEY_MAP[$rawKey] ?? $rawKey;
                    // De-duplicate
                    if (! in_array($jsKey, array_column($allergies, 'key'), true)) {
                        $allergies[] = ['key' => $jsKey];
                    }
                }

                $items[] = [
                    'name'          => $name,
                    'price'         => $price,
                    'descriptionDe' => $dDe,
                    'descriptionIt' => $dIt,
                    'descriptionEn' => $dEn,
                    'foodAllergies' => $allergies,
                ];
            }

            if (empty($items)) {
                continue;
            }

            $result[] = [
                'name'          => (string) ($section['title'] ?? ''),
                'order'         => (int) ($section['order'] ?? 999),
                'foodMenuItems' => $items,
            ];
        }

        return $result;
    }

    // ── Drinks ────────────────────────────────────────────────────────────

    /**
     * Load drink sections in EN. No descriptions needed by DrinkMenuGenerator.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildDrinksData(): array
    {
        $sections = $this->filterSections(
            $this->catalog->getMenuByLocale('en'),
            'drink'
        );

        $result = [];

        foreach ($sections as $section) {
            $items = [];

            foreach ($section['items'] ?? [] as $item) {
                $name  = (string) ($item['name'] ?? '');
                $price = (string) ($item['price'] ?? '');

                if ($name === '') {
                    continue;
                }

                $items[] = [
                    'name'  => $name,
                    'price' => $price,
                    // DrinkMenuGenerator doesn't use descriptions or allergies
                    'foodAllergies' => [],
                ];
            }

            if (empty($items)) {
                continue;
            }

            $result[] = [
                'name'          => (string) ($section['title'] ?? ''),
                'order'         => (int) ($section['order'] ?? 999),
                'foodMenuItems' => $items,
            ];
        }

        return $result;
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * @return array<int, array<string, mixed>>
     */
    private function filterSections(array $sections, string $type): array
    {
        $filtered = array_values(array_filter(
            $sections,
            fn ($s) => ($s['type'] ?? '') === $type && ($s['display_type'] ?? 'dual') !== 'off'
        ));

        usort($filtered, fn ($a, $b) => (int) ($a['order'] ?? 999) <=> (int) ($b['order'] ?? 999));

        return $filtered;
    }

    /**
     * Build a map of [item name → description] for a given locale.
     *
     * @return array<string, string>
     */
    private function buildDescriptionMap(string $locale): array
    {
        $map = [];

        foreach ($this->catalog->getMenuByLocale($locale) as $section) {
            foreach ($section['items'] ?? [] as $item) {
                $name = (string) ($item['name'] ?? '');
                $desc = (string) ($item['description'] ?? '');
                if ($name !== '' && $desc !== '') {
                    $map[$name] = $desc;
                }
            }
        }

        return $map;
    }
}
