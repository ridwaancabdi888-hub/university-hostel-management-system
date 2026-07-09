<?php

namespace Database\Seeders;

use App\Enums\StudentStatus;
use App\Enums\YearLevel;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoStudent = User::where('email', 'student@hostel.test')->first();

        if ($demoStudent && ! $demoStudent->studentProfile) {
            StudentProfile::factory()->create([
                'user_id' => $demoStudent->id,
                'student_id' => 'STU-2026-0001',
                'course' => 'B.Sc. Computer Science',
                'year_level' => YearLevel::Sophomore,
                'status' => StudentStatus::Active,
            ]);
        }

        StudentProfile::factory(14)->create();
    }
}
