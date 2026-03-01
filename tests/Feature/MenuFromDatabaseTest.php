<?php

namespace Tests\Feature;

use App\Models\FoodMenuCategory;
use App\Models\FoodMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuFromDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_page_uses_database_categories_and_items_grouped_by_type(): void
    {
        config()->set('services.menu.endpoint', '');

        $foodCategory = FoodMenuCategory::create([
            'name' => 'Chef Test Main',
            'type' => 'food',
            'status' => 'active',
            'order' => 1,
            'description_en' => 'Food category from database.',
        ]);

        FoodMenuItem::create([
            'food_menu_category_id' => $foodCategory->id,
            'name' => 'DB Test Curry',
            'description_en' => 'Slow-cooked test curry.',
            'price' => '€12.00',
            'status' => 'active',
            'order' => 1,
        ]);

        $drinkCategory = FoodMenuCategory::create([
            'name' => 'Chef Test Drinks',
            'type' => 'drink',
            'status' => 'active',
            'order' => 2,
            'description_en' => 'Drink category from database.',
        ]);

        FoodMenuItem::create([
            'food_menu_category_id' => $drinkCategory->id,
            'name' => 'DB Test Cola',
            'description_en' => 'Cold soft drink.',
            'price' => '€3.50',
            'status' => 'active',
            'order' => 1,
        ]);

        $response = $this->get('/en/menu');

        $response
            ->assertStatus(200)
            ->assertSeeText('Jump to category')
            ->assertSeeText('Food Categories')
            ->assertSeeText('Drink Categories')
            ->assertSeeText('Chef Test Main')
            ->assertSeeText('DB Test Curry')
            ->assertSeeText('Chef Test Drinks')
            ->assertSeeText('DB Test Cola')
            ->assertSee('href="#food-chef-test-main-1"', false)
            ->assertSee('href="#drink-chef-test-drinks-1"', false);
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
