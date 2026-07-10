<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\MaintenanceStatus;
use App\Exports\BillingExport;
use App\Exports\HostelExport;
use App\Exports\OccupancyExport;
use App\Exports\PaymentExport;
use App\Exports\StudentExport;
use App\Models\Hostel;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\StudentProfile;
use App\Support\ReportAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Redirect to the first report the current role can see.
     */
    public function index(Request $request): RedirectResponse
    {
        $accessible = $this->accessibleReports($request);

        abort_if(empty($accessible), 403);

        return redirect()->route("reports.{$accessible[0]}");
    }

    public function occupancy(Request $request): View
    {
        $this->authorizeReport($request, 'occupancy');

        return view('reports.occupancy', $this->occupancyData());
    }

    public function billing(Request $request): View
    {
        $this->authorizeReport($request, 'billing');

        return view('reports.billing', $this->billingData());
    }

    public function payments(Request $request): View
    {
        $this->authorizeReport($request, 'payments');

        return view('reports.payments', $this->paymentsData($request));
    }

    public function students(Request $request): View
    {
        $this->authorizeReport($request, 'students');

        return view('reports.students', $this->studentsData());
    }

    public function hostels(Request $request): View
    {
        $this->authorizeReport($request, 'hostels');

        return view('reports.hostels', $this->hostelsData());
    }

    /**
     * Download the given report as a PDF.
     */
    public function exportPdf(Request $request, string $type)
    {
        $this->authorizeReport($request, $type);

        $data = match ($type) {
            'occupancy' => $this->occupancyData(),
            'billing' => $this->billingData(),
            'payments' => $this->paymentsData($request),
            'students' => $this->studentsData(),
            'hostels' => $this->hostelsData(),
            default => abort(404),
        };

        return Pdf::loadView("reports.pdf.{$type}", $data)->download("{$type}-report.pdf");
    }

    /**
     * Download the given report as an Excel workbook.
     */
    public function exportExcel(Request $request, string $type): BinaryFileResponse|StreamedResponse
    {
        $this->authorizeReport($request, $type);

        $export = match ($type) {
            'occupancy' => new OccupancyExport,
            'billing' => new BillingExport,
            'payments' => new PaymentExport($request),
            'students' => new StudentExport,
            'hostels' => new HostelExport,
            default => abort(404),
        };

        return Excel::download($export, "{$type}-report.xlsx");
    }

    /**
     * @return list<string>
     */
    private function accessibleReports(Request $request): array
    {
        return ReportAccess::for($request->user()->role);
    }

    private function authorizeReport(Request $request, string $type): void
    {
        abort_unless(in_array($type, $this->accessibleReports($request), true), 403);
    }

    /**
     * @return array<string, mixed>
     */
    private function occupancyData(): array
    {
        return Cache::remember('reports.occupancy', now()->addMinutes(5), function () {
            $rooms = Room::all();
            $totalRooms = $rooms->count();
            $totalCapacity = (int) $rooms->sum('capacity');
            $totalOccupied = (int) $rooms->sum('occupied_beds');
            $occupancyRate = $totalCapacity > 0 ? round($totalOccupied / $totalCapacity * 100, 1) : 0;

            $byHostel = Hostel::with('blocks.floors.rooms')->get()->map(function (Hostel $hostel) {
                $rooms = $hostel->blocks->flatMap->floors->flatMap->rooms;
                $capacity = (int) $rooms->sum('capacity');
                $occupied = (int) $rooms->sum('occupied_beds');

                return [
                    'name' => $hostel->name,
                    'rooms' => $rooms->count(),
                    'capacity' => $capacity,
                    'occupied' => $occupied,
                    'rate' => $capacity > 0 ? round($occupied / $capacity * 100, 1) : 0,
                ];
            })->values();

            $byRoomType = RoomType::with('rooms')->get()->map(function (RoomType $type) {
                $capacity = (int) $type->rooms->sum('capacity');
                $occupied = (int) $type->rooms->sum('occupied_beds');

                return [
                    'name' => $type->name,
                    'rooms' => $type->rooms->count(),
                    'capacity' => $capacity,
                    'occupied' => $occupied,
                    'rate' => $capacity > 0 ? round($occupied / $capacity * 100, 1) : 0,
                ];
            })->values();

            return compact('totalRooms', 'totalCapacity', 'totalOccupied', 'occupancyRate', 'byHostel', 'byRoomType');
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function billingData(): array
    {
        $totalBilled = Invoice::where('status', '!=', InvoiceStatus::Cancelled)->sum('total_amount');
        $totalCollected = Payment::sum('amount');
        $totalOutstanding = Invoice::where('status', '!=', InvoiceStatus::Cancelled)
            ->withSum('payments', 'amount')
            ->get()
            ->sum(fn (Invoice $invoice) => $invoice->balance());

        $byStatus = DB::table('invoices')
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('status')
            ->get();

        $monthlyBreakdown = collect(range(11, 0))->map(function (int $offset) {
            $month = now()->copy()->subMonths($offset)->startOfMonth();

            return [
                'label' => $month->format('M Y'),
                'billed' => (float) Invoice::whereYear('billing_month', $month->year)
                    ->whereMonth('billing_month', $month->month)
                    ->where('status', '!=', InvoiceStatus::Cancelled)
                    ->sum('total_amount'),
                'collected' => (float) Payment::whereYear('paid_at', $month->year)
                    ->whereMonth('paid_at', $month->month)
                    ->sum('amount'),
            ];
        })->values();

        return compact('totalBilled', 'totalCollected', 'totalOutstanding', 'byStatus', 'monthlyBreakdown');
    }

    /**
     * @return array<string, mixed>
     */
    private function paymentsData(Request $request): array
    {
        $dateFrom = $request->date('date_from')?->startOfDay() ?? now()->startOfMonth();
        $dateTo = $request->date('date_to')?->endOfDay() ?? now()->endOfMonth();

        $todayIncome = Payment::whereDate('paid_at', today())->sum('amount');
        $monthIncome = Payment::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');

        $pendingBalance = Invoice::where('status', '!=', InvoiceStatus::Cancelled)
            ->withSum('payments', 'amount')
            ->get()
            ->sum(fn (Invoice $invoice) => $invoice->balance());

        // Grouped in PHP (rather than a DATE_FORMAT(...) raw query) so this
        // works identically on MySQL and SQLite (the test suite's driver).
        $monthlyIncome = Payment::select('paid_at', 'amount')
            ->where('paid_at', '>=', now()->subMonths(11)->startOfMonth())
            ->get()
            ->groupBy(fn (Payment $payment) => $payment->paid_at->format('Y-m'))
            ->map(fn ($payments, $month) => (object) [
                'month' => $month,
                'total' => $payments->sum('amount'),
                'payment_count' => $payments->count(),
            ])
            ->sortByDesc('month')
            ->values();

        $dailyIncome = Payment::selectRaw('paid_at as day, SUM(amount) as total, COUNT(*) as payment_count')
            ->whereBetween('paid_at', [$dateFrom, $dateTo])
            ->groupBy('paid_at')
            ->orderByDesc('paid_at')
            ->get();

        $byMethod = DB::table('payments')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        $payments = Payment::with(['invoice.studentProfile.user'])
            ->whereBetween('paid_at', [$dateFrom, $dateTo])
            ->latest('paid_at')
            ->paginate(20)
            ->withQueryString();

        return [
            'todayIncome' => $todayIncome,
            'monthIncome' => $monthIncome,
            'pendingBalance' => $pendingBalance,
            'monthlyIncome' => $monthlyIncome,
            'dailyIncome' => $dailyIncome,
            'byMethod' => $byMethod,
            'payments' => $payments,
            'filters' => [
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function studentsData(): array
    {
        return Cache::remember('reports.students', now()->addMinutes(5), function () {
            $total = StudentProfile::count();

            $byStatus = DB::table('student_profiles')->select('status', DB::raw('COUNT(*) as count'))->groupBy('status')->get();
            $byYearLevel = DB::table('student_profiles')->select('year_level', DB::raw('COUNT(*) as count'))->groupBy('year_level')->get();
            $byGender = DB::table('student_profiles')->select('gender', DB::raw('COUNT(*) as count'))->groupBy('gender')->get();
            $byCourse = DB::table('student_profiles')->select('course', DB::raw('COUNT(*) as count'))->groupBy('course')->orderByDesc('count')->get();

            return compact('total', 'byStatus', 'byYearLevel', 'byGender', 'byCourse');
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function hostelsData(): array
    {
        return Cache::remember('reports.hostels', now()->addMinutes(5), function () {
            $hostels = Hostel::with('blocks.floors.rooms')->get();

            // A room -> hostel lookup built once from the already-eager-loaded
            // tree above, so revenue/maintenance can be aggregated in a single
            // query each below instead of two extra queries per hostel.
            $roomHostelMap = $hostels->flatMap(
                fn (Hostel $hostel) => $hostel->blocks->flatMap->floors->flatMap->rooms
                    ->map(fn (Room $room) => ['room_id' => $room->id, 'hostel_id' => $hostel->id])
            )->pluck('hostel_id', 'room_id');

            $revenueByHostel = Invoice::where('status', '!=', InvoiceStatus::Cancelled)
                ->whereHas('roomAllocation')
                ->with('roomAllocation:id,room_id')
                ->get(['id', 'total_amount', 'room_allocation_id'])
                ->groupBy(fn (Invoice $invoice) => $roomHostelMap->get($invoice->roomAllocation?->room_id))
                ->map(fn ($invoices) => (float) $invoices->sum('total_amount'));

            $maintenanceByHostel = MaintenanceRequest::where('status', '!=', MaintenanceStatus::Completed)
                ->whereNotNull('room_id')
                ->get(['id', 'room_id'])
                ->groupBy(fn (MaintenanceRequest $request) => $roomHostelMap->get($request->room_id))
                ->map->count();

            $result = $hostels->map(function (Hostel $hostel) use ($revenueByHostel, $maintenanceByHostel) {
                $rooms = $hostel->blocks->flatMap->floors->flatMap->rooms;
                $capacity = (int) $rooms->sum('capacity');
                $occupied = (int) $rooms->sum('occupied_beds');

                return [
                    'name' => $hostel->name,
                    'blocks' => $hostel->blocks->count(),
                    'rooms' => $rooms->count(),
                    'capacity' => $capacity,
                    'occupied' => $occupied,
                    'rate' => $capacity > 0 ? round($occupied / $capacity * 100, 1) : 0,
                    'revenue' => (float) ($revenueByHostel->get($hostel->id) ?? 0),
                    'activeMaintenance' => (int) ($maintenanceByHostel->get($hostel->id) ?? 0),
                ];
            })->values();

            return ['hostels' => $result];
        });
    }
}
