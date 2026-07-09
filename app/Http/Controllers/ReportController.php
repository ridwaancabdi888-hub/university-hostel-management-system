<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display payment reports: daily/monthly income, outstanding balance,
     * and a filterable list of recorded payments.
     */
    public function index(Request $request): View
    {
        $dateFrom = $request->date('date_from')?->startOfDay() ?? now()->startOfMonth();
        $dateTo = $request->date('date_to')?->endOfDay() ?? now()->endOfMonth();

        $todayIncome = Payment::whereDate('paid_at', today())->sum('amount');
        $monthIncome = Payment::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');

        $pendingBalance = Invoice::where('status', '!=', InvoiceStatus::Cancelled)
            ->withSum('payments', 'amount')
            ->get()
            ->sum(fn (Invoice $invoice) => $invoice->balance());

        $monthlyIncome = Payment::selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month, SUM(amount) as total, COUNT(*) as payment_count")
            ->groupBy('month')
            ->orderByDesc('month')
            ->limit(12)
            ->get();

        $dailyIncome = Payment::selectRaw('paid_at as day, SUM(amount) as total, COUNT(*) as payment_count')
            ->whereBetween('paid_at', [$dateFrom, $dateTo])
            ->groupBy('paid_at')
            ->orderByDesc('paid_at')
            ->get();

        $payments = Payment::with(['invoice.studentProfile.user'])
            ->whereBetween('paid_at', [$dateFrom, $dateTo])
            ->latest('paid_at')
            ->paginate(20)
            ->withQueryString();

        return view('reports.index', [
            'todayIncome' => $todayIncome,
            'monthIncome' => $monthIncome,
            'pendingBalance' => $pendingBalance,
            'monthlyIncome' => $monthlyIncome,
            'dailyIncome' => $dailyIncome,
            'payments' => $payments,
            'filters' => [
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
            ],
        ]);
    }
}
