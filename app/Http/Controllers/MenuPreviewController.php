<?php

namespace App\Http\Controllers;

use App\Services\MenuCatalogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuPreviewController extends Controller
{
    public function __construct(private readonly MenuCatalogService $catalog) {}

    public function html(Request $request): Response
    {
        $params = $this->parseParams($request);
        $data = $this->buildTemplateData($params);

        $html = view('menu-print.template', $data)->render();

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }

    public function pdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $params = $this->parseParams($request);
        $data = $this->buildTemplateData($params);
        $data['isPdf'] = true;

        $pdf = Pdf::loadView('menu-print.template', $data)
            ->setPaper(strtolower($params['paper']), 'portrait')
            ->setOptions([
                'dpi'               => 150,
                'defaultFont'       => 'serif',
                'isRemoteEnabled'   => false,
                'isHtml5ParserEnabled' => true,
                'defaultMediaType'  => 'print',   // ignore @media screen — fixes blank overflow pages
            ]);

        $filename = 'menu-' . implode('-', $params['langs']) . '-' . $params['layout'] . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * @return array<string, mixed>
     */
    private function parseParams(Request $request): array
    {
        // Parse multi-language selection (comma-separated, e.g. "en,it")
        $rawLangs = (string) $request->query('langs', $request->query('lang', 'en'));
        $langs = array_values(array_unique(array_intersect(
            array_map('trim', explode(',', $rawLangs)),
            ['en', 'it', 'de']
        )));
        if (empty($langs)) {
            $langs = ['en'];
        }

        $sort = $request->query('sort', 'default');
        if (! in_array($sort, ['default', 'alpha-asc', 'alpha-desc', 'price-asc', 'price-desc'], true)) {
            $sort = 'default';
        }

        $pageWidth = $request->query('pageWidth', 'standard');
        if (! in_array($pageWidth, ['standard', 'full', 'compact', 'zigzag'], true)) {
            $pageWidth = 'standard';
        }

        return [
            'langs'          => $langs,
            'lang'           => $langs[0],
            'type'           => $request->query('type', 'all'),
            'descriptions'   => $request->query('descriptions', '1') === '1',
            'allergens'      => $request->query('allergens', '1') === '1',
            'allergenIcons'  => $request->query('allergenIcons', '1') === '1',
            'allergenLegend' => $request->query('allergenLegend', '1') === '1',
            'layout'         => in_array($request->query('layout'), ['classic', 'elegant', 'modern'], true)
                                     ? $request->query('layout')
                                     : 'classic',
            'colors'         => $request->query('colors', 'brand') === 'neutral' ? 'neutral' : 'brand',
            'title'          => strip_tags((string) $request->query('title', config('restaurant.name', 'Alpin Curry'))),
            'subtitle'       => strip_tags((string) $request->query('subtitle', 'Authentic Indian Cuisine')),
            'address'        => $request->query('address', '1') === '1',
            'phone'          => $request->query('phone', '1') === '1',
            'footer'         => strip_tags((string) $request->query('footer', 'All prices include VAT. Please inform our staff of any allergies.')),
            'paper'          => in_array($request->query('paper'), ['A4', 'A5', 'Letter'], true)
                                     ? $request->query('paper')
                                     : 'A4',
            'sort'           => $sort,
            'pageWidth'      => $pageWidth,
            'repeatHeader'   => $request->query('repeatHeader', '0') === '1',
            'autoOrganize'   => $request->query('autoOrganize', '0') === '1',
        ];
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function buildTemplateData(array $params): array
    {
        $primaryLang = $params['lang'];
        $allSections = $this->catalog->getMenuByLocale($primaryLang);

        $type = (string) $params['type'];
        if ($type === 'food') {
            $sections = array_values(array_filter($allSections, fn ($s) => ($s['type'] ?? '') === 'food'));
        } elseif ($type === 'drink') {
            $sections = array_values(array_filter($allSections, fn ($s) => ($s['type'] ?? '') === 'drink'));
        } else {
            $sections = $allSections;
        }

        // Apply sort ordering
        $sort = $params['sort'] ?? 'default';
        if ($sort !== 'default') {
            foreach ($sections as &$section) {
                $items = $section['items'] ?? [];
                usort($items, function (array $a, array $b) use ($sort): int {
                    return match ($sort) {
                        'alpha-asc'  => strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? '')),
                        'alpha-desc' => strcmp((string) ($b['name'] ?? ''), (string) ($a['name'] ?? '')),
                        'price-asc'  => $this->comparePrice((string) ($a['price'] ?? ''), (string) ($b['price'] ?? '')),
                        'price-desc' => $this->comparePrice((string) ($b['price'] ?? ''), (string) ($a['price'] ?? '')),
                        default      => 0,
                    };
                });
                $section['items'] = $items;
            }
            unset($section);
        }

        // Filter out categories with display_type = 'off'
        $sections = array_values(array_filter($sections, fn ($s) => ($s['display_type'] ?? 'dual') !== 'off'));

        // Build auto-organize splits (single full-width, dual split food|drink)
        $autoOrganize = (bool) ($params['autoOrganize'] ?? false);
        if ($autoOrganize) {
            $singleSections    = array_values(array_filter($sections, fn ($s) => ($s['display_type'] ?? 'dual') === 'single'));
            $dualFoodSections  = array_values(array_filter($sections, fn ($s) => ($s['display_type'] ?? 'dual') === 'dual' && ($s['type'] ?? '') === 'food'));
            $dualDrinkSections = array_values(array_filter($sections, fn ($s) => ($s['display_type'] ?? 'dual') === 'dual' && ($s['type'] ?? '') === 'drink'));
        } else {
            $singleSections    = [];
            $dualFoodSections  = [];
            $dualDrinkSections = [];
        }

        // Build extra-language description map (item name → translated description)
        $extraLangs = array_slice($params['langs'], 1);
        $extraLangDescs = [];
        foreach ($extraLangs as $lang) {
            $langSections = $this->catalog->getMenuByLocale((string) $lang);
            $map = [];
            foreach ($langSections as $sec) {
                foreach ($sec['items'] ?? [] as $item) {
                    $name = (string) ($item['name'] ?? '');
                    $desc = (string) ($item['description'] ?? '');
                    if ($name !== '' && $desc !== '') {
                        $map[$name] = $desc;
                    }
                }
            }
            $extraLangDescs[(string) $lang] = $map;
        }

        // Collect unique allergens used (for the legend)
        $usedAllergens = [];
        foreach ($sections as $section) {
            foreach ($section['items'] ?? [] as $item) {
                $tags = array_merge($item['allergies'] ?? [], $item['intolerances'] ?? []);
                foreach ($tags as $tag) {
                    $key = (string) ($tag['key'] ?? '');
                    if ($key !== '' && ! isset($usedAllergens[$key])) {
                        $usedAllergens[$key] = (string) ($tag['label'] ?? $key);
                    }
                }
            }
        }

        $restaurantConfig = [
            'title'          => $params['title'],
            'subtitle'       => $params['subtitle'],
            'address'        => $params['address'] ? $this->formatAddress() : null,
            'phone'          => $params['phone'] ? config('restaurant.contact.phone_display', '') : null,
            'footer'         => $params['footer'],
            'layout'         => $params['layout'],
            'colors'         => $params['colors'],
            'paper'          => $params['paper'],
            'descriptions'   => $params['descriptions'],
            'allergens'      => $params['allergens'],
            'allergenIcons'  => $params['allergenIcons'],
            'allergenLegend' => $params['allergenLegend'],
            'pageWidth'      => $params['pageWidth'],
            'langs'          => $params['langs'],
            'lang'           => $params['lang'],
            'repeatHeader'   => $params['repeatHeader'],
            'autoOrganize'   => $params['autoOrganize'],
        ];

        return [
            'sections'          => $sections,
            'config'            => $restaurantConfig,
            'extraLangDescs'    => $extraLangDescs,
            'usedAllergens'     => $usedAllergens,
            'singleSections'    => $singleSections,
            'dualFoodSections'  => $dualFoodSections,
            'dualDrinkSections' => $dualDrinkSections,
        ];
    }

    private function comparePrice(string $a, string $b): int
    {
        $parse = fn (string $p): float => (float) preg_replace('/[^0-9.]/', '', str_replace(',', '.', $p));

        return $parse($a) <=> $parse($b);
    }

    private function formatAddress(): string
    {
        $street = config('restaurant.address.street', '');
        $number = config('restaurant.address.street_number', '');
        $postal = config('restaurant.address.postal_code', '');
        $city   = config('restaurant.address.city', '');

        $line1 = trim($street . ' ' . $number);
        $line2 = trim($postal . ' ' . $city);

        return implode(', ', array_filter([$line1, $line2]));
    }
}
