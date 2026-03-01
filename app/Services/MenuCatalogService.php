<?php

namespace App\Services;

use App\Models\FoodAllergy;
use App\Models\FoodIngredient;
use App\Models\FoodIntolerance;
use App\Models\FoodMenuCategory;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class MenuCatalogService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getMenuByLocale(string $locale): array
    {
        $sections = $this->loadMenuFromDatabase($locale);

        if (! empty($sections)) {
            return $sections;
        }

        $sections = $this->loadMenuFromLocalData($locale);

        if (! empty($sections)) {
            return $sections;
        }

        $sections = $this->fetchFromApi($locale);

        if (! empty($sections)) {
            return $sections;
        }

        return $this->fallbackMenu($locale);
    }

    /**
     * @return array<string, array<int, array<string, string>>>
     */
    public function getDietaryByLocale(string $locale): array
    {
        $fromDatabase = $this->loadDietaryFromDatabase($locale);
        $hasDatabaseDietary = ! empty($fromDatabase['allergies'])
            || ! empty($fromDatabase['intolerances'])
            || ! empty($fromDatabase['ingredients']);

        if ($hasDatabaseDietary) {
            return $fromDatabase;
        }

        return [
            'allergies' => $this->readDietaryGroup(
                $this->resolveDataPath(['allergies.json']),
                'foodAllergies',
                $locale
            ),
            'intolerances' => $this->readDietaryGroup(
                $this->resolveDataPath(['intolerances.json']),
                'foodIntolerances',
                $locale
            ),
            'ingredients' => $this->readDietaryGroup(
                $this->resolveDataPath(['Ingredients.json', 'ingredients.json']),
                'foodIngredients',
                $locale
            ),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadMenuFromDatabase(string $locale): array
    {
        if (! Schema::hasTable('food_menu_categories') || ! Schema::hasTable('food_menu_items')) {
            return [];
        }

        $descriptionColumn = $this->databaseDescriptionColumnForLocale($locale);

        try {
            $categories = FoodMenuCategory::query()
                ->where('status', 'active')
                ->orderBy('type')
                ->orderBy('order')
                ->with([
                    'foodMenuItems' => function ($query): void {
                        $query->where('status', 'active')
                            ->orderBy('order')
                            ->with([
                                'foodAllergies' => fn ($relation) => $relation->where('status', 'active')->orderBy('order'),
                                'foodIntolerances' => fn ($relation) => $relation->where('status', 'active')->orderBy('order'),
                            ]);
                    },
                ])
                ->get();
        } catch (QueryException) {
            return [];
        }

        if ($categories->isEmpty()) {
            return [];
        }

        $sections = [];

        foreach ($categories as $category) {
            $items = [];

            foreach ($category->foodMenuItems as $item) {
                $name = trim((string) $item->name);

                if ($name === '') {
                    continue;
                }

                $items[] = [
                    'name' => $name,
                    'price' => $this->normalizePrice((string) $item->price),
                    'description' => $this->resolveDatabaseLocalizedValue($item, $descriptionColumn),
                    'order' => (int) $item->order,
                    'allergies' => $this->extractDietaryTagsFromModels($item->foodAllergies, $descriptionColumn),
                    'intolerances' => $this->extractDietaryTagsFromModels($item->foodIntolerances, $descriptionColumn),
                ];
            }

            if (empty($items)) {
                continue;
            }

            usort($items, fn (array $a, array $b): int => (int) ($a['order'] ?? 999) <=> (int) ($b['order'] ?? 999));

            $sections[] = [
                'title' => (string) $category->name,
                'description' => $this->resolveDatabaseLocalizedValue($category, $descriptionColumn),
                'type' => $this->normalizeSectionType((string) $category->type),
                'order' => (int) $category->order,
                'items' => $items,
            ];
        }

        return $sections;
    }

    /**
     * @return array<string, array<int, array<string, string>>>
     */
    private function loadDietaryFromDatabase(string $locale): array
    {
        $descriptionColumn = $this->databaseDescriptionColumnForLocale($locale);

        return [
            'allergies' => $this->readDietaryGroupFromDatabase(FoodAllergy::class, $descriptionColumn),
            'intolerances' => $this->readDietaryGroupFromDatabase(FoodIntolerance::class, $descriptionColumn),
            'ingredients' => $this->readDietaryGroupFromDatabase(FoodIngredient::class, $descriptionColumn),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchFromApi(string $locale): array
    {
        $endpoint = (string) config('services.menu.endpoint', '');

        if ($endpoint === '') {
            return [];
        }

        $query = <<<'GRAPHQL'
query {
  foodMenus {
    name
    menuCategory {
      name
      order
      foodMenuItems {
        name
        price
        status
        order
        descriptionEn
        descriptionIt
        descriptionDe
      }
    }
  }
}
GRAPHQL;

        try {
            $response = Http::timeout((int) config('services.menu.timeout', 8))
                ->asJson()
                ->post($endpoint, ['query' => $query]);

            if (! $response->successful()) {
                return [];
            }

            $payload = $response->json();
            $foodMenus = $payload['data']['foodMenus'] ?? null;

            if (! is_array($foodMenus)) {
                return [];
            }

            $descriptionField = match ($locale) {
                'it' => 'descriptionIt',
                'de' => 'descriptionDe',
                default => 'descriptionEn',
            };

            $sections = [];

            foreach ($foodMenus as $foodMenu) {
                $menuName = (string) ($foodMenu['name'] ?? '');
                $categories = $foodMenu['menuCategory'] ?? [];
                if (! is_array($categories)) {
                    continue;
                }

                usort($categories, fn (array $a, array $b): int => (int) ($a['order'] ?? 999) <=> (int) ($b['order'] ?? 999));

                foreach ($categories as $category) {
                    $itemsRaw = $category['foodMenuItems'] ?? [];
                    if (! is_array($itemsRaw)) {
                        continue;
                    }

                    $items = [];

                    foreach ($itemsRaw as $item) {
                        $status = strtolower((string) ($item['status'] ?? 'active'));
                        if (! in_array($status, ['active', '1', 'true'], true)) {
                            continue;
                        }

                        $items[] = [
                            'name' => (string) ($item['name'] ?? ''),
                            'price' => $this->normalizePrice((string) ($item['price'] ?? '')),
                            'description' => trim((string) (($item[$descriptionField] ?? '')
                                ?: ($item['descriptionEn'] ?? '')
                                ?: ($item['descriptionIt'] ?? '')
                                ?: ($item['descriptionDe'] ?? ''))),
                            'order' => (int) ($item['order'] ?? 999),
                        ];
                    }

                    usort($items, fn (array $a, array $b): int => (int) $a['order'] <=> (int) $b['order']);

                    if (empty($items)) {
                        continue;
                    }

                    $sections[] = [
                        'title' => (string) ($category['name'] ?? ''),
                        'type' => $this->inferSectionType($menuName, (string) ($category['name'] ?? '')),
                        'items' => $items,
                    ];
                }
            }

            return $sections;
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function loadMenuFromLocalData(string $locale): array
    {
        $payload = $this->readJsonFile($this->resolveDataPath(['menu_data.json', 'menu-data.json']));
        $foodMenus = $payload['data']['foodMenus'] ?? null;

        if (! is_array($foodMenus)) {
            return [];
        }

        $descriptionField = $this->descriptionFieldForLocale($locale);
        $sections = [];

        foreach ($foodMenus as $foodMenu) {
            $menuName = (string) ($foodMenu['name'] ?? '');
            $categories = $foodMenu['menuCategory'] ?? [];

            if (! is_array($categories)) {
                continue;
            }

            usort($categories, fn (array $a, array $b): int => (int) ($a['order'] ?? 999) <=> (int) ($b['order'] ?? 999));

            foreach ($categories as $category) {
                $categoryName = (string) ($category['name'] ?? '');
                $itemsRaw = $category['foodMenuItems'] ?? [];

                if (! is_array($itemsRaw)) {
                    continue;
                }

                $items = [];

                foreach ($itemsRaw as $item) {
                    $status = strtolower((string) ($item['status'] ?? 'active'));
                    if (! in_array($status, ['active', '1', 'true'], true)) {
                        continue;
                    }

                    $items[] = [
                        'name' => (string) ($item['name'] ?? ''),
                        'price' => $this->normalizePrice((string) ($item['price'] ?? '')),
                        'description' => trim((string) (($item[$descriptionField] ?? '')
                            ?: ($item['descriptionEn'] ?? '')
                            ?: ($item['descriptionIt'] ?? '')
                            ?: ($item['descriptionDe'] ?? ''))),
                        'order' => (int) ($item['order'] ?? 999),
                        'allergies' => $this->extractDietaryTags($item['foodAllergies'] ?? [], $descriptionField),
                        'intolerances' => $this->extractDietaryTags($item['foodIntolerances'] ?? [], $descriptionField),
                    ];
                }

                usort($items, fn (array $a, array $b): int => (int) $a['order'] <=> (int) $b['order']);

                if (empty($items)) {
                    continue;
                }

                $sections[] = [
                    'title' => $menuName !== '' ? $menuName.' - '.$categoryName : $categoryName,
                    'type' => $this->inferSectionType($menuName, $categoryName),
                    'items' => $items,
                ];
            }
        }

        return $sections;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fallbackMenu(string $locale): array
    {
        $menu = config('alpin_menu.sections', []);

        if (! is_array($menu)) {
            return [];
        }

        $sections = [];

        foreach ($menu as $section) {
            $title = $section['title'][$locale] ?? $section['title']['en'] ?? '';
            $items = [];

            foreach (($section['items'] ?? []) as $item) {
                $items[] = [
                    'name' => $item['name'][$locale] ?? $item['name']['en'] ?? '',
                    'description' => $item['description'][$locale] ?? $item['description']['en'] ?? '',
                    'price' => $item['price'] ?? '',
                ];
            }

            $sections[] = [
                'title' => $title,
                'type' => $this->inferSectionType('', $title),
                'items' => $items,
            ];
        }

        return $sections;
    }

    private function descriptionFieldForLocale(string $locale): string
    {
        return match ($locale) {
            'it' => 'descriptionIt',
            'de' => 'descriptionDe',
            default => 'descriptionEn',
        };
    }

    private function normalizePrice(string $price): string
    {
        return trim($price);
    }

    private function databaseDescriptionColumnForLocale(string $locale): string
    {
        return match ($locale) {
            'it' => 'description_it',
            'de' => 'description_de',
            default => 'description_en',
        };
    }

    private function normalizeSectionType(string $type): string
    {
        $normalized = strtolower(trim($type));

        return in_array($normalized, ['food', 'drink'], true) ? $normalized : 'food';
    }

    private function inferSectionType(string $menuName, string $categoryName): string
    {
        $haystack = Str::lower(trim($menuName.' '.$categoryName));

        $drinkKeywords = [
            'drink',
            'beverage',
            'cocktail',
            'mocktail',
            'wine',
            'beer',
            'water',
            'coffee',
            'coffe',
            'tea',
            'prosecco',
            'grappa',
            'aperitif',
            'aperitifs',
            'aperitivo',
            'digestivo',
            'soft',
            'juice',
            'spritz',
        ];

        foreach ($drinkKeywords as $keyword) {
            if (str_contains($haystack, $keyword)) {
                return 'drink';
            }
        }

        return 'food';
    }

    /**
     * @param  object  $model
     */
    private function resolveDatabaseLocalizedValue(object $model, string $preferredColumn): string
    {
        $preferred = trim((string) data_get($model, $preferredColumn, ''));
        if ($preferred !== '') {
            return $preferred;
        }

        $fallback = [
            'description_en',
            'description_it',
            'description_de',
            'name',
        ];

        foreach ($fallback as $column) {
            $value = trim((string) data_get($model, $column, ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    /**
     * @param  EloquentCollection<int, object>  $tags
     * @return array<int, array<string, string>>
     */
    private function extractDietaryTagsFromModels(EloquentCollection $tags, string $preferredColumn): array
    {
        $rows = [];

        foreach ($tags as $tag) {
            $label = $this->resolveDatabaseLocalizedValue($tag, $preferredColumn);
            $key = Str::slug(strtolower(trim((string) data_get($tag, 'key', data_get($tag, 'name', '')))));

            if ($label === '' || $key === '') {
                continue;
            }

            $rows[] = [
                'key' => $key,
                'label' => $label,
            ];
        }

        return $rows;
    }

    /**
     * @param  mixed  $tags
     * @return array<int, array<string, string>>
     */
    private function extractDietaryTags(mixed $tags, string $descriptionField): array
    {
        if (! is_array($tags)) {
            return [];
        }

        $normalized = [];

        foreach ($tags as $tag) {
            if (! is_array($tag)) {
                continue;
            }

            $status = strtolower((string) ($tag['status'] ?? 'active'));
            if (! in_array($status, ['active', '1', 'true'], true)) {
                continue;
            }

            $label = (string) ($tag[$descriptionField] ?? $tag['name'] ?? '');
            $key = strtolower((string) ($tag['key'] ?? $tag['name'] ?? ''));

            $normalized[] = [
                'key' => Str::slug($key),
                'label' => $label,
            ];
        }

        return $normalized;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function readDietaryGroup(string $path, string $groupKey, string $locale): array
    {
        $payload = $this->readJsonFile($path);
        $list = $payload['data'][$groupKey] ?? null;

        if (! is_array($list)) {
            return [];
        }

        $descriptionField = $this->descriptionFieldForLocale($locale);
        $rows = [];

        foreach ($list as $item) {
            if (! is_array($item)) {
                continue;
            }

            $status = strtolower((string) ($item['status'] ?? 'active'));
            if (! in_array($status, ['active', '1', 'true'], true)) {
                continue;
            }

            $label = (string) ($item[$descriptionField] ?? $item['name'] ?? '');
            $key = strtolower((string) ($item['key'] ?? $item['name'] ?? ''));

            $rows[] = [
                'key' => Str::slug($key),
                'label' => $label,
            ];
        }

        usort($rows, fn (array $a, array $b): int => strcmp($a['label'], $b['label']));

        return $rows;
    }

    /**
     * @param  class-string  $modelClass
     * @return array<int, array<string, string>>
     */
    private function readDietaryGroupFromDatabase(string $modelClass, string $descriptionColumn): array
    {
        if (! class_exists($modelClass)) {
            return [];
        }

        $table = (new $modelClass)->getTable();
        if (! Schema::hasTable($table)) {
            return [];
        }

        /** @var EloquentCollection<int, object> $rows */
        try {
            $rows = $modelClass::query()
                ->where('status', 'active')
                ->orderBy('order')
                ->get();
        } catch (QueryException) {
            return [];
        }

        $normalized = [];

        foreach ($rows as $row) {
            $label = $this->resolveDatabaseLocalizedValue($row, $descriptionColumn);
            $rawKey = trim((string) data_get($row, 'key', data_get($row, 'name', '')));
            $key = Str::slug(strtolower($rawKey));

            if ($label === '' || $key === '') {
                continue;
            }

            $normalized[] = [
                'key' => $key,
                'label' => $label,
            ];
        }

        usort($normalized, fn (array $a, array $b): int => strcmp($a['label'], $b['label']));

        return $normalized;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readJsonFile(string $path): ?array
    {
        if (! is_file($path)) {
            return null;
        }

        $raw = file_get_contents($path);

        if ($raw === false || trim($raw) === '') {
            return null;
        }

        $normalized = $this->normalizeJson($raw);
        $decoded = json_decode($normalized, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function normalizeJson(string $raw): string
    {
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw) ?? $raw;

        $firstObject = strpos($raw, '{');
        $firstArray = strpos($raw, '[');

        if ($firstObject === false && $firstArray === false) {
            return $raw;
        }

        $start = 0;

        if ($firstObject === false) {
            $start = $firstArray;
        } elseif ($firstArray === false) {
            $start = $firstObject;
        } else {
            $start = min($firstObject, $firstArray);
        }

        return substr($raw, $start);
    }

    private function resolveDataPath(array $candidates): string
    {
        $base = storage_path('app/private/data');

        foreach ($candidates as $candidate) {
            $path = $base.DIRECTORY_SEPARATOR.$candidate;
            if (is_file($path)) {
                return $path;
            }
        }

        return $base.DIRECTORY_SEPARATOR.$candidates[0];
    }
}
