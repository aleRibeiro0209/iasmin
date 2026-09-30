<?php

namespace Tests\Feature;

use App\Models\Confirmation;
use App\Models\GiftCategory;
use App\Models\GiftItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Confirmation::create([
            'name' => 'Ana',
            'attending' => true,
            'guests' => 3,
        ]);
        Confirmation::create([
            'name' => 'Bruno',
            'attending' => false,
            'guests' => 0,
        ]);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.total', 2)
            ->where('stats.attending', 1)
            ->where('stats.declined', 1)
            ->where('stats.guests', 3)
            ->where('confirmations.per_page', 15)
            ->has('confirmations.data', 2)
        );
    }

    public function test_dashboard_paginates_confirmations_by_fifteen()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        foreach (range(1, 16) as $index) {
            Confirmation::create([
                'name' => "Convidado {$index}",
                'attending' => true,
                'guests' => 1,
            ]);
        }

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('confirmations.per_page', 15)
                ->has('confirmations.data', 15)
                ->where('confirmations.total', 16)
                ->where('stats.guests', 16)
            );

        $this->get(route('dashboard', ['page' => 2]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('confirmations.data', 1)
                ->where('confirmations.current_page', 2)
            );
    }

    public function test_admin_pages_use_english_routes()
    {
        $this->get(route('categories.index'))->assertRedirect(route('login'));
        $this->get(route('gifts.index'))->assertRedirect(route('login'));

        $user = User::factory()->create();
        $category = GiftCategory::create(['name' => 'Livros']);

        $this->actingAs($user)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Categories')
                ->where('categories.per_page', 15)
            );

        $this->actingAs($user)
            ->get(route('gifts.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Gifts')
                ->where('categories.0.name', $category->name)
                ->where('gifts.per_page', 15)
            );
    }

    public function test_categories_and_gifts_paginate_fifteen_per_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        foreach (range(1, 16) as $index) {
            $category = GiftCategory::create(['name' => "Categoria {$index}"]);
            GiftItem::create([
                'name' => "Presente {$index}",
                'gift_category_id' => $category->id,
            ]);
        }

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('categories.data', 15)
                ->where('categories.total', 16)
                ->where('categories.per_page', 15)
            );

        $this->get(route('categories.index', ['page' => 2]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('categories.data', 1)
                ->where('categories.current_page', 2)
            );

        $this->get(route('gifts.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('gifts.data', 15)
                ->where('gifts.total', 16)
                ->where('gifts.per_page', 15)
                ->has('categories', 16)
            );

        $this->get(route('gifts.index', ['page' => 2]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('gifts.data', 1)
                ->where('gifts.current_page', 2)
            );
    }
}
