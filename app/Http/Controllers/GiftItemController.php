<?php

namespace App\Http\Controllers;

use App\Models\GiftCategory;
use App\Models\GiftItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class GiftItemController extends Controller
{
    /**
     * Public gift list, same audience as the invitation.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Presentes', [
            'categories' => $this->groupedCategories(),
            'celebration' => (bool) $request->session()->get('celebration'),
            'backHref' => $request->query('origem') === 'confirmar' ? '/confirmar' : '/',
        ]);
    }

    /**
     * Gift list used while the public page is pulled in from the side.
     */
    public function data(): JsonResponse
    {
        return response()->json([
            'categories' => $this->groupedCategories(),
        ]);
    }

    /**
     * @return Collection<int, array{id: int, name: string, items: Collection<int, array{id: int, name: string, description: string|null}>}>
     */
    private function groupedCategories(): Collection
    {
        return GiftCategory::query()
            ->with(['items' => fn ($query) => $query->orderBy('name')->orderBy('id')->select(['id', 'gift_category_id', 'name', 'description'])])
            ->orderBy('name')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->map(fn (GiftCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'items' => $category->items->map(fn (GiftItem $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                ])->values(),
            ])
            ->filter(fn (array $category) => $category['items']->isNotEmpty())
            ->values();
    }

    /**
     * Admin screen to add and remove gift names.
     */
    public function admin(): Response
    {
        return Inertia::render('Gifts', [
            'categories' => GiftCategory::query()->orderBy('name')->orderBy('id')->get(['id', 'name']),
            'gifts' => GiftItem::query()
                ->with('category:id,name')
                ->orderBy('name')
                ->orderBy('id')
                ->paginate(15, ['id', 'name', 'description', 'gift_category_id'])
                ->withQueryString()
                ->through(fn (GiftItem $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'gift_category_id' => $item->gift_category_id,
                    'category' => $item->category?->name,
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        GiftItem::create($this->validatedGift($request));

        return back();
    }

    public function update(Request $request, GiftItem $giftItem): RedirectResponse
    {
        $giftItem->update($this->validatedGift($request));

        return back();
    }

    public function destroy(GiftItem $giftItem): RedirectResponse
    {
        $giftItem->delete();

        return back();
    }

    /**
     * @return array{name: string, description: string|null, gift_category_id: int}
     */
    private function validatedGift(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'gift_category_id' => ['required', 'integer', 'exists:gift_categories,id'],
        ], [
            'name.required' => 'Informe o nome do item.',
            'name.max' => 'O nome pode ter no máximo 120 caracteres.',
            'description.max' => 'A descrição pode ter no máximo 500 caracteres.',
            'gift_category_id.required' => 'Escolha uma categoria.',
            'gift_category_id.exists' => 'Escolha uma categoria válida.',
        ]);

        $description = trim((string) ($validated['description'] ?? ''));
        $validated['description'] = $description !== '' ? $description : null;

        return $validated;
    }
}
