<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Dashboard\Services\DashboardService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function __invoke(Request $request)
    {
        $userId = $request->user()->id;

        $dashboardData = $this->dashboardService->getDashboardData($userId);
        dd($dashboardData);

        return Inertia::render('dashboard/home', $dashboardData);
    }
}
