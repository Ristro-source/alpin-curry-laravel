<?php

namespace App\Http\Controllers;

use App\Services\MenuCatalogService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class PageController extends Controller
{
    /**
     * Featured dish keyword order used on home page cards.
     *
     * @var array<int, string>
     */
    private const FEATURED_DISH_KEYWORDS = [
        'butter-chicken',
        'tandoori-chicken',
        'palak-paneer',
        'mango-lassi',
    ];

    public function __construct(private readonly MenuCatalogService $menuCatalogService)
    {
    }

    public function home(): View
    {
        $locale = app()->getLocale();
        $menuSections = $this->menuCatalogService->getMenuByLocale($locale);

        return view('pages.home', [
            'featuredMenuItems' => $this->buildFeaturedMenuItems($menuSections),
        ]);
    }

    public function menu(): View
    {
        return view('pages.menu', [
            'menuSections' => $this->menuCatalogService->getMenuByLocale(app()->getLocale()),
            'dietary' => $this->menuCatalogService->getDietaryByLocale(app()->getLocale()),
        ]);
    }

    public function gallery(): View
    {
        $basePath = public_path('assets/images/gallery');
        $paths = [];

        if (is_dir($basePath)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($basePath, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }

                $extension = strtolower((string) $file->getExtension());
                if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                    continue;
                }

                $paths[] = $file->getPathname();
            }
        }

        sort($paths, SORT_NATURAL | SORT_FLAG_CASE);

        $imagesByKey = [];

        foreach ($paths as $path) {
            $relative = str_replace(public_path().DIRECTORY_SEPARATOR, '', $path);
            $relative = str_replace('\\', '/', $relative);
            $key = Str::slug((string) pathinfo($path, PATHINFO_FILENAME));
            $priority = $this->galleryPriority($relative);
            $caption = $this->buildGalleryCaption($path);

            $candidate = [
                'path' => $relative,
                'caption' => $caption,
                'priority' => $priority,
            ];

            if (! isset($imagesByKey[$key])) {
                $imagesByKey[$key] = $candidate;
                continue;
            }

            $current = $imagesByKey[$key];
            if ($priority < $current['priority']) {
                $imagesByKey[$key] = $candidate;
            }
        }

        $images = array_values($imagesByKey);
        usort($images, fn (array $a, array $b): int => strcmp($a['caption'], $b['caption']));

        $images = array_map(fn (array $image): array => [
            'path' => $image['path'],
            'caption' => $image['caption'],
        ], $images);

        return view('pages.gallery', [
            'galleryImages' => $images,
        ]);
    }

    public function faq(): View
    {
        return view('pages.faq');
    }

    public function legal(): View
    {
        return view('pages.legal');
    }

    public function privacy(): View
    {
        return view('pages/legal/privacy');
    }

    public function cookies(): View
    {
        return view('pages/legal/cookies');
    }

    public function impressum(): View
    {
        return view('pages/legal/impressum');
    }

    public function terms(): View
    {
        return view('pages/legal/terms');
    }

    private function buildGalleryCaption(string $path): string
    {
        return Str::title(str_replace(['-', '_', '.'], ' ', (string) pathinfo($path, PATHINFO_FILENAME)));
    }

    private function galleryPriority(string $relativePath): int
    {
        return match (true) {
            str_contains($relativePath, '/original/') => 1,
            str_contains($relativePath, '/landscape/1920x1080/') => 2,
            str_contains($relativePath, '/landscape/1280x780/') => 3,
            str_contains($relativePath, '/portrait/1080x1350/') => 4,
            str_contains($relativePath, '/portrait/720x900/') => 5,
            str_contains($relativePath, '/angular-extra/menu/') => 6,
            str_contains($relativePath, '/angular-extra/section/') => 7,
            str_contains($relativePath, '/angular-extra/logos/') => 8,
            default => 9,
        };
    }

    /**
     * @param  array<int, array<string, mixed>>  $menuSections
     * @return array<int, array<string, string>>
     */
    private function buildFeaturedMenuItems(array $menuSections): array
    {
        $flattened = [];

        foreach ($menuSections as $section) {
            $sectionType = strtolower(trim((string) ($section['type'] ?? 'food')));
            if ($sectionType === 'drink') {
                continue;
            }

            $sectionTitle = trim((string) ($section['title'] ?? ''));
            $items = $section['items'] ?? [];

            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $name = trim((string) ($item['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $flattened[] = [
                    'name' => $name,
                    'description' => trim((string) ($item['description'] ?? '')),
                    'price' => trim((string) ($item['price'] ?? '')),
                    'section_title' => $sectionTitle,
                    'slug' => Str::slug($name),
                ];
            }
        }

        $featured = [];
        $usedIndexes = [];

        foreach (self::FEATURED_DISH_KEYWORDS as $keyword) {
            foreach ($flattened as $index => $item) {
                if (isset($usedIndexes[$index])) {
                    continue;
                }

                if (! str_contains((string) ($item['slug'] ?? ''), $keyword)) {
                    continue;
                }

                $featured[] = $item;
                $usedIndexes[$index] = true;
                break;
            }
        }

        if (count($featured) < 4) {
            foreach ($flattened as $index => $item) {
                if (isset($usedIndexes[$index])) {
                    continue;
                }

                $featured[] = $item;
                $usedIndexes[$index] = true;

                if (count($featured) >= 4) {
                    break;
                }
            }
        }

        return array_map(function (array $item, int $index): array {
            $slug = (string) ($item['slug'] ?? Str::slug((string) ($item['name'] ?? '')));

            return [
                'name' => $item['name'],
                'name_key' => $this->resolveFeaturedNameKey($slug),
                'description' => $item['description'],
                'price' => $item['price'],
                'section_title' => $item['section_title'],
                'image' => $this->resolveFeaturedImage($item['name'], $index),
            ];
        }, $featured, array_keys($featured));
    }

    private function resolveFeaturedImage(string $name, int $index): string
    {
        $slug = Str::slug($name);

        $mappedFile = match (true) {
            str_contains($slug, 'biryani') => 'biryani.png',
            str_contains($slug, 'palak-paneer') => 'palak-paneer.png',
            str_contains($slug, 'tandoori-chicken') => 'tandoori-chicken.png',
            str_contains($slug, 'mango-lassi') => 'mango-lassi.png',
            str_contains($slug, 'veg-samosa') || str_contains($slug, 'samosa') => 'veg-samosa.png',
            str_contains($slug, 'papadam') || str_contains($slug, 'papad') => 'papadam.png',
            str_contains($slug, 'butter-chicken') => 'buttur-chicken.png',
            default => null,
        };

        if (is_string($mappedFile) && file_exists(public_path('assets/images/dishes/'.$mappedFile))) {
            return 'assets/images/dishes/'.$mappedFile;
        }

        $fallbackFiles = [
            'image.png',
            'biryani.png',
            'palak-paneer.png',
            'tandoori-chicken.png',
            'mango-lassi.png',
        ];

        $file = $fallbackFiles[$index % count($fallbackFiles)];

        return 'assets/images/dishes/'.$file;
    }

    private function resolveFeaturedNameKey(string $slug): ?string
    {
        return match (true) {
            str_contains($slug, 'butter-chicken') => 'butter_chicken',
            str_contains($slug, 'tandoori-chicken') => 'tandoori_chicken',
            str_contains($slug, 'palak-paneer') => 'palak_paneer',
            str_contains($slug, 'mango-lassi') => 'mango_lassi',
            str_contains($slug, 'veg-samosa') || str_contains($slug, 'samosa') => 'veg_samosa',
            default => null,
        };
    }
}
