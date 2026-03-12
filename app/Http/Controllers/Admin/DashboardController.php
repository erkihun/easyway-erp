<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardMetricsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardMetricsService $dashboardMetricsService)
    {
    }

    public function __invoke(): View
    {
        return view('admin.dashboard', $this->dashboardMetricsService->build());
    }
}

