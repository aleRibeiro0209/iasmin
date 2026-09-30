<?php

namespace App\Http\Controllers;

use App\Models\Confirmation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConfirmationController extends Controller
{
    /**
     * Save an RSVP coming from the public confirmation form.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'attending' => ['required', 'boolean'],
            'guests' => ['required', 'integer', 'min:0', 'max:10'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        // Quem não vai não ocupa lugares.
        if (! $validated['attending']) {
            $validated['guests'] = 0;
        }

        Confirmation::create($validated);

        return redirect()->route('presentes', ['origem' => 'confirmar'])->with('celebration', true);
    }
}
