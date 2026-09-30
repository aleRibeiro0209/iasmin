<?php

namespace App\Http\Controllers;

use App\Models\GiftCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
                ->orderBy('id')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        GiftCategory::create($this->validatedCategory($request));

        return back();
    }

    public function update(Request $request, GiftCategory $giftCategory): RedirectResponse
    {
        $giftCategory->update($this->validatedCategory($request, $giftCategory));

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

    /**
     * @return array{name: string}
     */
    private function validatedCategory(Request $request, ?GiftCategory $giftCategory = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('gift_categories', 'name')->ignore($giftCategory?->id),
            ],
        ], [
            'name.required' => 'Informe o nome da categoria.',
            'name.max' => 'O nome pode ter no máximo 120 caracteres.',
            'name.unique' => 'Já existe uma categoria com esse nome.',
        ]);
    }
}
