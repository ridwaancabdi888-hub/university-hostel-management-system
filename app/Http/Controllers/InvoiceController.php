<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Http\Requests\GenerateBillsRequest;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\StudentProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * The flat amount added when applying a late fee to an overdue invoice.
     */
    private const LATE_FEE_INCREMENT = 25.00;

    /**
     * Display the billing history.
     */
    public function index(Request $request): View
    {
        $query = Invoice::with(['studentProfile.user'])->withSum('payments', 'amount');

        if ($request->user()->hasRole(Role::Student)) {
            $query->whereHas('studentProfile', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($query) use ($search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('studentProfile', fn ($q) => $q->where('student_id', 'like', "%{$search}%"))
                    ->orWhereHas('studentProfile.user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Raw correlated subqueries (rather than HAVING on the withSum
        // alias) because MySQL's ONLY_FULL_GROUP_BY mode rejects a HAVING
        // clause that references a non-aggregated, non-grouped column.
        $paidSubquery = '(select coalesce(sum(amount), 0) from payments where payments.invoice_id = invoices.id)';

        match ($request->string('status')->toString()) {
            'overdue' => $query->overdue(),
            'pending' => $query->where('status', '!=', InvoiceStatus::Cancelled)->whereRaw("{$paidSubquery} < total_amount"),
            'unpaid' => $query->where('status', InvoiceStatus::Unpaid)->whereRaw("{$paidSubquery} = 0"),
            'partial' => $query->where('status', InvoiceStatus::Unpaid)->whereRaw("{$paidSubquery} > 0"),
            'paid' => $query->where('status', InvoiceStatus::Paid),
            'cancelled' => $query->where('status', InvoiceStatus::Cancelled),
            default => null,
        };

        $invoices = $query->latest('billing_month')->paginate(20)->withQueryString();

        return view('invoices.index', [
            'invoices' => $invoices,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a one-off invoice.
     */
    public function create(): View
    {
        return view('invoices.create', [
            'students' => StudentProfile::with('user')->orderBy('student_id')->get(),
        ]);
    }

    /**
     * Store a newly created invoice.
     */
    public function store(InvoiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $invoice = DB::transaction(function () use ($data) {
            $student = StudentProfile::findOrFail($data['student_profile_id']);

            return Invoice::create([
                ...$data,
                'billing_month' => Carbon::parse($data['billing_month'])->startOfMonth(),
                'room_allocation_id' => $student->activeAllocation?->id,
                'status' => InvoiceStatus::Unpaid,
            ]);
        });

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice created.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load([
            'studentProfile.user',
            'roomAllocation.room.floor.block.hostel',
            'payments' => fn ($query) => $query->latest('paid_at'),
            'payments.recordedBy',
        ]);

        return view('invoices.show', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice): View
    {
        $invoice->load('studentProfile.user');

        return view('invoices.edit', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Update the specified invoice's charges, due date, status, and notes.
     */
    public function update(InvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update($request->validated());

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice updated.');
    }

    /**
     * Remove the specified invoice, unless it has recorded payments.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        if ($invoice->payments()->exists()) {
            return redirect()->route('invoices.index')
                ->with('error', "Cannot delete invoice \"{$invoice->invoice_number}\" because it has recorded payments.");
        }

        $invoice->delete();

        return redirect()->route('invoices.index')->with('status', 'Invoice deleted.');
    }

    /**
     * Show the form for generating monthly bills.
     */
    public function generateForm(): View
    {
        return view('invoices.generate');
    }

    /**
     * Generate one invoice per actively-housed student for the given month,
     * skipping any student who already has a bill for that month.
     */
    public function generate(GenerateBillsRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $billingMonth = Carbon::createFromFormat('Y-m', $data['billing_month'])->startOfMonth();

        [$generated, $skipped] = DB::transaction(function () use ($data, $billingMonth) {
            $students = StudentProfile::with('activeAllocation.room.roomType')
                ->whereHas('activeAllocation')
                ->get();

            // whereYear/whereMonth (rather than a plain string-equality
            // where()) because the "date"-cast billing_month column can be
            // stored with a time component depending on the DB driver
            // (e.g. SQLite, unlike MySQL, doesn't truncate DATE columns).
            $alreadyBilledIds = Invoice::whereYear('billing_month', $billingMonth->year)
                ->whereMonth('billing_month', $billingMonth->month)
                ->pluck('student_profile_id');

            $generated = 0;
            $skipped = 0;

            foreach ($students as $student) {
                if ($alreadyBilledIds->contains($student->id)) {
                    $skipped++;

                    continue;
                }

                Invoice::create([
                    'student_profile_id' => $student->id,
                    'room_allocation_id' => $student->activeAllocation->id,
                    'billing_month' => $billingMonth,
                    'rent_amount' => $student->activeAllocation->room->roomType->monthly_rate,
                    'utility_amount' => $data['utility_amount'] ?? 0,
                    'due_date' => $data['due_date'],
                    'status' => InvoiceStatus::Unpaid,
                ]);

                $generated++;
            }

            return [$generated, $skipped];
        });

        return redirect()->route('invoices.index')
            ->with('status', "Generated {$generated} invoice(s) for {$billingMonth->format('F Y')}.".($skipped ? " Skipped {$skipped} student(s) already billed." : ''));
    }

    /**
     * Apply a flat late fee to an overdue invoice.
     */
    public function applyLateFee(Invoice $invoice): RedirectResponse
    {
        if (! $invoice->isOverdue()) {
            return redirect()->route('invoices.show', $invoice)->with('error', 'Late fees can only be applied to overdue invoices.');
        }

        $invoice->update([
            'late_fee_amount' => $invoice->late_fee_amount + self::LATE_FEE_INCREMENT,
        ]);

        return redirect()->route('invoices.show', $invoice)->with('status', 'Late fee applied.');
    }

    /**
     * Download the invoice as a PDF.
     */
    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['studentProfile.user', 'roomAllocation.room.floor.block.hostel']);

        return Pdf::loadView('invoices.pdf', ['invoice' => $invoice])
            ->download("{$invoice->invoice_number}.pdf");
    }
}
