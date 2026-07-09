<?php

namespace App\Exports;

use App\Models\StudentProfile;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return ['Student ID', 'Name', 'Email', 'Course', 'Year Level', 'Gender', 'Status', 'Phone'];
    }

    public function collection(): Collection
    {
        return StudentProfile::with('user')
            ->orderBy('student_id')
            ->get()
            ->map(fn (StudentProfile $student) => [
                $student->student_id,
                $student->user->name,
                $student->user->email,
                $student->course,
                $student->year_level->label(),
                $student->gender?->label(),
                $student->status->label(),
                $student->phone,
            ]);
    }
}
