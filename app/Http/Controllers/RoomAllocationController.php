<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class RoomAllocationController extends Controller
{
    public function index(): View
    {
        return view('pages.coming-soon', [
            'title' => 'Room Allocation',
            'description' => 'Manage room allocations, track occupancy, and assign students to available beds.',
        ]);
    }
}
