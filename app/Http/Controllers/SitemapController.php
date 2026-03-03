<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    private const LOCALES = ['en', 'it', 'de'];

    /** Route name → [priority, changefreq] */
    private const ROUTES = [
        'home'            => [1.0, 'weekly'],
        'menu'            => [0.9, 'weekly'],
        'gallery'         => [0.7, 'monthly'],
        'faq'             => [0.7, 'monthly'],
        'legal'           => [0.3, 'yearly'],
        'legal.privacy'   => [0.2, 'yearly'],
        'legal.cookies'   => [0.2, 'yearly'],
        'legal.impressum' => [0.2, 'yearly'],
        'legal.terms'     => [0.2, 'yearly'],
    ];

    public function __invoke(): Response
    {
        $urls = [];

        foreach (self::ROUTES as $routeName => [$priority, $changefreq]) {
            foreach (self::LOCALES as $locale) {
                $alternates = [];
                foreach (self::LOCALES as $altLocale) {
                    $alternates[$altLocale] = route($routeName, ['locale' => $altLocale]);
                }

                $urls[] = [
                    'loc'        => route($routeName, ['locale' => $locale]),
                    'priority'   => number_format($priority, 1),
                    'changefreq' => $changefreq,
                    'alternates' => $alternates,
                ];
            }
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
