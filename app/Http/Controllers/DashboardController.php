<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(): Response|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $dashboardData = $this->dashboardService->getDashboardData($user);
        if ($dashboardData === null) {
            return redirect()->route('family.setup');
        }

        return Inertia::render('Dashboard', $dashboardData);
    }
}
