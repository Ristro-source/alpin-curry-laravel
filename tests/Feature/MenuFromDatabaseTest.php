<?php

namespace Tests\Feature;

use App\Models\FoodMenuCategory;
use App\Models\FoodMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuFromDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_page_shows_pdf_pages_and_download_links(): void
    {
        $this->get('/en/menu')
            ->assertStatus(200)
            ->assertSee('menu/AlpinCurry_Menu.pdf?v=', false)
            ->assertSee('menu/AlpinCurry_Desserts.pdf?v=', false)
            ->assertSee('menu/pages/menu-01.jpg', false)
            ->assertSee('menu/pages/menu-12.jpg', false)
            ->assertSee('menu/pages/desserts-1.jpg', false)
            ->assertSeeText('Download menu (PDF)');
    }

    public function test_home_featured_menu_excludes_drink_only_items_when_food_exists(): void
    {
        config()->set('services.menu.endpoint', '');

        $drinkCategory = FoodMenuCategory::create([
            'name' => 'Only Drinks',
            'type' => 'drink',
            'status' => 'active',
            'order' => 1,
        ]);

        FoodMenuItem::create([
            'food_menu_category_id' => $drinkCategory->id,
            'name' => 'Only Drink Item',
            'description_en' => 'Drink test item.',
            'price' => '€4.00',
            'status' => 'active',
            'order' => 1,
        ]);

        $foodCategory = FoodMenuCategory::create([
            'name' => 'Only Food',
            'type' => 'food',
            'status' => 'active',
            'order' => 2,
        ]);

        FoodMenuItem::create([
            'food_menu_category_id' => $foodCategory->id,
            'name' => 'Only Food Item',
            'description_en' => 'Food test item.',
            'price' => '€10.00',
            'status' => 'active',
            'order' => 1,
        ]);

        $response = $this->get('/en');

        $response
            ->assertStatus(200)
            ->assertSeeText('Only Food Item')
            ->assertDontSeeText('Only Drink Item');
    }
}
