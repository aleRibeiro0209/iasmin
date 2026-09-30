<?php

namespace Tests\Feature;

use App\Models\Confirmation;
use App\Models\GiftCategory;
use App\Models\GiftItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InvitationUpdatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_gift_items_store_a_description_and_lists_are_alphabetical(): void
    {
        $user = User::factory()->create();
        $books = GiftCategory::create(['name' => 'Livros']);
        $accessories = GiftCategory::create(['name' => 'Acessórios']);

        GiftItem::create([
            'name' => 'Pulseira',
            'description' => 'Dourada',
            'gift_category_id' => $accessories->id,
        ]);
        GiftItem::create([
            'name' => 'Anel',
            'gift_category_id' => $accessories->id,
        ]);
        GiftItem::create([
            'name' => 'Romance',
            'gift_category_id' => $books->id,
        ]);

        $this->actingAs($user)
            ->post(route('gifts.store'), [
                'name' => 'Caderno',
                'description' => 'Capa roxa',
                'gift_category_id' => $books->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('gift_items', [
            'name' => 'Caderno',
            'description' => 'Capa roxa',
        ]);

        $this->actingAs($user)
            ->get(route('gifts.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('gifts.data.0.name', 'Anel')
                ->where('gifts.data.1.name', 'Caderno')
                ->where('gifts.data.1.description', 'Capa roxa')
                ->where('categories.0.name', 'Acessórios')
                ->where('categories.1.name', 'Livros')
            );

        $this->get(route('presentes'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('categories.0.name', 'Acessórios')
                ->where('categories.0.items.0.name', 'Anel')
                ->where('categories.0.items.1.name', 'Pulseira')
                ->where('categories.0.items.1.description', 'Dourada')
                ->where('categories.1.name', 'Livros')
                ->where('categories.1.items.0.name', 'Caderno')
            );
    }

    public function test_rsvp_counts_the_guest_plus_named_companions(): void
    {
        $this->post(route('confirmar.store'), [
            'name' => 'Maria Souza',
            'attending' => true,
            'companions' => ['Ana Souza', 'João Souza'],
            'message' => 'Até lá',
        ])->assertRedirect(route('presentes', ['origem' => 'confirmar']));

        $confirmation = Confirmation::query()->first();

        $this->assertNotNull($confirmation);
        $this->assertSame(3, $confirmation->guests);
        $this->assertSame(['Ana Souza', 'João Souza'], $confirmation->companions);

        $this->post(route('confirmar.store'), [
            'name' => 'Paulo Lima',
            'attending' => true,
            'companions' => [],
        ])->assertRedirect();

        $this->assertDatabaseHas('confirmations', [
            'name' => 'Paulo Lima',
            'attending' => true,
            'guests' => 1,
        ]);

        $this->post(route('confirmar.store'), [
            'name' => 'Clara Dias',
            'attending' => false,
            'companions' => ['Ignorado'],
        ])->assertRedirect();

        $declined = Confirmation::query()->where('name', 'Clara Dias')->first();

        $this->assertNotNull($declined);
        $this->assertFalse($declined->attending);
        $this->assertSame(0, $declined->guests);
        $this->assertSame([], $declined->companions);
    }

    public function test_admin_can_edit_a_category_and_a_gift(): void
    {
        $user = User::factory()->create();
        $category = GiftCategory::create(['name' => 'Livros']);
        $other = GiftCategory::create(['name' => 'Acessórios']);
        $item = GiftItem::create([
            'name' => 'Romance',
            'gift_category_id' => $category->id,
        ]);

        $this->actingAs($user)
            ->put(route('categories.update', $category), ['name' => 'Livros'])
            ->assertRedirect();

        $this->actingAs($user)
            ->put(route('categories.update', $category), ['name' => 'Leitura'])
            ->assertRedirect();

        $this->actingAs($user)
            ->put(route('gifts.update', $item), [
                'name' => 'Anel',
                'description' => 'Dourado',
                'gift_category_id' => $other->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('gift_categories', [
            'id' => $category->id,
            'name' => 'Leitura',
        ]);
        $this->assertDatabaseHas('gift_items', [
            'id' => $item->id,
            'name' => 'Anel',
            'description' => 'Dourado',
            'gift_category_id' => $other->id,
        ]);
    }
}
