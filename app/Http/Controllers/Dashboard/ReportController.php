<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Reporting\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date')) 
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date')) 
            : Carbon::now()->endOfMonth();

        $overview = $this->reportService->getFinancialOverview($startDate, $endDate);
        $cashFlow = $this->reportService->getCashFlow($startDate, $endDate);
        $expensesByCategory = $this->reportService->getExpensesByCategory($startDate, $endDate);
        $incomeByCategory = $this->reportService->getIncomeByCategory($startDate, $endDate);

        return Inertia::render('Reports/Index', [
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'overview' => $overview,
            'cashFlow' => $cashFlow,
            'expensesByCategory' => $expensesByCategory,
            'incomeByCategory' => $incomeByCategory,
        ]);
    }
}
