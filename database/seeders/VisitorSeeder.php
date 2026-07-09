<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\VisitorStatus;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Visitor;
use App\Notifications\VisitorApproved;
use App\Notifications\VisitorRegistered;
use App\Notifications\VisitorRejected;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;

class VisitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = StudentProfile::with('user')->orderBy('id')->get();
        $staff = User::whereIn('role', [Role::Admin, Role::Warden])->get();
        $admin = User::where('email', 'admin@hostel.test')->first();

        $visitors = [
            ['Grace Muthoni', '+254701111111', 'Mother', 'Bringing groceries and checking in.'],
            ['Peter Otieno', '+254701222222', 'Father', 'Visiting during term break.'],
            ['Amina Yusuf', '+254701333333', 'Sister', 'Dropping off books.'],
            ['James Kariuki', '+254701444444', 'Friend', 'Studying together for exams.'],
            ['Fatima Noor', '+254701555555', 'Guardian', 'Discussing tuition arrangements.'],
            ['David Mwangi', '+254701666666', 'Cousin', 'Attending a campus event together.'],
            ['Halima Abdi', '+254701777777', 'Aunt', 'Bringing traditional food for the weekend.'],
            ['Samuel Kiptoo', '+254701888888', 'Brother', 'Picking up items for the family.'],
            ['Joyce Wambui', '+254701999999', 'Mother', 'Weekly check-in visit.'],
            ['Ahmed Hassan', '+254702000000', 'Friend', 'Project collaboration meeting.'],
            ['Lucy Chebet', '+254702111111', 'Sister', 'Celebrating a birthday together.'],
            ['Omar Farah', '+254702222222', 'Guardian', 'Reviewing academic progress.'],
        ];

        foreach ($visitors as $index => [$name, $phone, $relationship, $purpose]) {
            $student = $students[$index % $students->count()];
            $stage = $index % 3;

            $visitor = Visitor::create([
                'student_profile_id' => $student->id,
                'name' => $name,
                'phone' => $phone,
                'relationship' => $relationship,
                'purpose' => $purpose,
                'expected_at' => now()->addDays(fake()->numberBetween(-10, 10))->setTime(fake()->numberBetween(9, 18), 0),
                'status' => VisitorStatus::Pending,
            ]);

            if ($stage === 0) {
                // Left pending — awaiting staff review.
                Notification::send($staff, new VisitorRegistered($visitor));

                continue;
            }

            if ($stage === 1) {
                $visitor->update([
                    'status' => VisitorStatus::Approved,
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                ]);
                $student->user->notify(new VisitorApproved($visitor));

                continue;
            }

            $visitor->update([
                'status' => VisitorStatus::Rejected,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'rejection_reason' => 'Visiting hours conflict with scheduled hostel maintenance.',
            ]);
            $student->user->notify(new VisitorRejected($visitor));
        }
    }
}
