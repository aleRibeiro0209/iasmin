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
     * @return Collection<int, array{id: int, name: string, items: Collection<int, array{id: int, name: string}>}>
     */
    private function groupedCategories(): Collection
    {
        return GiftCategory::query()
            ->with(['items' => fn ($query) => $query->orderBy('name')->select(['id', 'gift_category_id', 'name'])])
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (GiftCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'items' => $category->items->map(fn (GiftItem $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
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
            'categories' => GiftCategory::query()->orderBy('name')->get(['id', 'name']),
            'gifts' => GiftItem::query()
                ->with('category:id,name')
                ->latest()
                ->paginate(15, ['id', 'name', 'gift_category_id', 'created_at'])
                ->withQueryString()
                ->through(fn (GiftItem $item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category' => $item->category?->name,
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'gift_category_id' => ['required', 'integer', 'exists:gift_categories,id'],
        ], [
            'name.required' => 'Informe o nome do item.',
            'name.max' => 'O nome pode ter no máximo 120 caracteres.',
            'gift_category_id.required' => 'Escolha uma categoria.',
            'gift_category_id.exists' => 'Escolha uma categoria válida.',
        ]);

        GiftItem::create($validated);

        return back();
    }

    public function destroy(GiftItem $giftItem): RedirectResponse
    {
        $giftItem->delete();

        return back();
    }
}
