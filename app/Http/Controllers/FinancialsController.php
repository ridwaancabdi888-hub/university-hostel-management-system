<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FinancialsController extends Controller
{
    public function index(): View
    {
        return view('pages.coming-soon', [
            'title' => 'Financials',
            'description' => 'Track hostel fee payments, invoices, and monthly revenue.',
        ]);
    }
}
