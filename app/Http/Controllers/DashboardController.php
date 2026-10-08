<?php

namespace App\Http\Controllers;

use App\Models\Confirmation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Admin home: RSVP totals and a paginated list of every response.
     */
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'confirmations' => Confirmation::query()
                ->latest()
                ->paginate(15, ['id', 'name', 'attending', 'guests', 'companions', 'message', 'created_at'])
                ->withQueryString(),
            'stats' => [
                'total' => Confirmation::query()->count(),
                'attending' => Confirmation::query()->where('attending', true)->count(),
                'declined' => Confirmation::query()->where('attending', false)->count(),
                'guests' => (int) Confirmation::query()->where('attending', true)->sum('guests'),
            ],
        ]);
    }

    /**
     * Correct a guest name and split companions into one name each.
     */
    public function update(Request $request, Confirmation $confirmation): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'companions' => ['nullable', 'array', 'max:10'],
            'companions.*' => ['required', 'string', 'max:120'],
        ], [
            'name.required' => 'Informe o nome completo.',
            'companions.max' => 'Uma confirmação pode ter no máximo 10 acompanhantes.',
            'companions.*.required' => 'Informe o nome do acompanhante.',
            'companions.*.max' => 'O nome do acompanhante pode ter no máximo 120 caracteres.',
        ]);

        $companions = $confirmation->attending
            ? array_values(array_map(fn (string $name) => trim($name), $validated['companions'] ?? []))
            : [];

        $confirmation->update([
            'name' => trim($validated['name']),
            'companions' => $companions,
            'guests' => $confirmation->attending ? 1 + count($companions) : 0,
        ]);

        return back();
    }

    /**
     * Remove one RSVP from the admin list.
     */
    public function destroy(Confirmation $confirmation): RedirectResponse
    {
        $confirmation->delete();

        return back();
    }
}
