<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(): View
    {
        return view('pages.coming-soon', [
            'title' => 'Maintenance',
            'description' => 'Submit and track maintenance requests across hostel blocks.',
        ]);
    }
}
