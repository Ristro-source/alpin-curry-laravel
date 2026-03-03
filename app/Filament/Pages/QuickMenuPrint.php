<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * Hidden admin page with two one-click buttons to download
 * the legacy-style food and drinks PDFs.
 *
 * Not shown in the navigation sidebar.
 * Accessible at: /admin/quick-menu-print
 */
class QuickMenuPrint extends Page
{
    protected string $view = 'filament.pages.quick-menu-print';

    protected static ?string $title = 'Quick Menu Print';

    /** Hide from the navigation sidebar while keeping the page accessible. */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
