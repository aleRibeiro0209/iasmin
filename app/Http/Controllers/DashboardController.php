<?php

namespace App\Http\Controllers;

use App\Models\Confirmation;
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
}
