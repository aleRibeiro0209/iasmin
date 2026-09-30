<?php

namespace App\Http\Controllers;

use App\Models\GiftCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GiftCategoryController extends Controller
{
    /**
     * Admin screen to add and remove gift categories.
     */
    public function index(): Response
    {
        return Inertia::render('Categories', [
            'categories' => GiftCategory::query()
                ->select(['id', 'name'])
                ->withCount('items')
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:gift_categories,name'],
        ], [
            'name.required' => 'Informe o nome da categoria.',
            'name.max' => 'O nome pode ter no máximo 120 caracteres.',
            'name.unique' => 'Já existe uma categoria com esse nome.',
        ]);

        GiftCategory::create($validated);

        return back();
    }

    public function destroy(GiftCategory $giftCategory): RedirectResponse
    {
        if ($giftCategory->items()->exists()) {
            return back()->withErrors([
                'delete' => 'Remova os itens desta categoria antes de excluí-la.',
            ]);
        }

        $giftCategory->delete();

        return back();
    }
}
