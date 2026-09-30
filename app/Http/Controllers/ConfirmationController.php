<?php

namespace App\Http\Controllers;

use App\Models\Confirmation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmationController extends Controller
{
    /**
     * Public confirmation page. After 23:59 on 20 October 2026 in Brasília the form stays closed.
     */
    public function create(): Response
    {
        return Inertia::render('Confirm', [
            'closed' => $this->rsvpsAreClosed(),
        ]);
    }

    /**
     * Save an RSVP coming from the public confirmation form.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($this->rsvpsAreClosed()) {
            return redirect()
                ->route('confirmar')
                ->withErrors(['closed' => 'As confirmações já encerraram.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'attending' => ['required', 'boolean'],
            'companions' => ['nullable', 'array', 'max:10'],
            'companions.*' => ['required', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Informe seu nome completo.',
            'companions.max' => 'Você pode levar no máximo 10 acompanhantes.',
            'companions.*.required' => 'Informe o nome do acompanhante.',
            'companions.*.max' => 'O nome do acompanhante pode ter no máximo 120 caracteres.',
        ]);

        $companions = $validated['attending']
            ? array_values(array_map(fn (string $name) => trim($name), $validated['companions'] ?? []))
            : [];

        Confirmation::create([
            'name' => trim($validated['name']),
            'attending' => $validated['attending'],
            'guests' => $validated['attending'] ? 1 + count($companions) : 0,
            'companions' => $companions,
            'message' => $validated['message'] ?? null,
        ]);

        return redirect()->route('presentes', ['origem' => 'confirmar'])->with('celebration', true);
    }

    /**
     * Confirmations stop at 2026-10-21 00:00 in America/Sao_Paulo, so 23:59 on 20 October is still accepted.
     */
    private function rsvpsAreClosed(): bool
    {
        $deadline = Date::parse('2026-10-21 00:00:00', 'America/Sao_Paulo');

        return Date::now('America/Sao_Paulo')->greaterThanOrEqualTo($deadline);
    }
}
