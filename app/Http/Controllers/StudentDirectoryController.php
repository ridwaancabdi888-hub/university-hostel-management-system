<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StudentDirectoryController extends Controller
{
    public function index(): View
    {
        return view('pages.coming-soon', [
            'title' => 'Student Directory',
            'description' => 'Browse and manage student records, enrollment, and payment status.',
        ]);
    }
}
