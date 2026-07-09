<?php

namespace Database\Seeders;

use App\Enums\MaintenanceCategory;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRequest;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = StudentProfile::with('user')->orderBy('id')->get();
        $admin = User::where('email', 'admin@hostel.test')->first();
        $warden = User::where('email', 'warden@hostel.test')->first();
        $staff = [$admin->id, $warden->id];

        $tickets = [
            [MaintenanceCategory::Maintenance, 'Leaking faucet in bathroom', 'The bathroom sink faucet drips constantly and is wasting water.', MaintenancePriority::Medium],
            [MaintenanceCategory::Maintenance, 'AC not cooling', 'The air conditioning unit runs but no longer cools the room.', MaintenancePriority::High],
            [MaintenanceCategory::Maintenance, 'Broken window latch', 'The window in my room does not lock properly, which is a security concern.', MaintenancePriority::Urgent],
            [MaintenanceCategory::Maintenance, 'Flickering hallway light', 'The light outside my room flickers constantly at night.', MaintenancePriority::Low],
            [MaintenanceCategory::Maintenance, 'Wi-Fi router offline', 'No internet connection in the room since yesterday evening.', MaintenancePriority::High],
            [MaintenanceCategory::Maintenance, 'Clogged shower drain', 'Water pools in the shower and drains very slowly.', MaintenancePriority::Medium],
            [MaintenanceCategory::Maintenance, 'Door hinge squeaks loudly', 'The room door hinge needs oiling, it is very noisy.', MaintenancePriority::Low],
            [MaintenanceCategory::Maintenance, 'Electrical outlet not working', 'One of the wall outlets has stopped supplying power.', MaintenancePriority::Urgent],
            [MaintenanceCategory::Complaint, 'Noise from neighboring room', 'Loud music late at night is disrupting my studying and sleep.', MaintenancePriority::Medium],
            [MaintenanceCategory::Complaint, 'Common area left uncleaned', 'The shared kitchen has not been cleaned in over a week.', MaintenancePriority::Medium],
            [MaintenanceCategory::Complaint, 'Unresponsive laundry service', 'My laundry slot has not been serviced for two cycles.', MaintenancePriority::Low],
            [MaintenanceCategory::Complaint, 'Pest sighting in corridor', 'Saw cockroaches in the hallway near the stairwell.', MaintenancePriority::High],
            [MaintenanceCategory::Complaint, 'Roommate conflict over schedule', 'Requesting mediation over conflicting sleep/study schedules.', MaintenancePriority::Medium],
            [MaintenanceCategory::Complaint, 'Overflowing trash bins', 'Bins on the ground floor have not been emptied in days.', MaintenancePriority::Medium],
            [MaintenanceCategory::Maintenance, 'Ceiling stain from leak', 'A brown water stain is spreading on the ceiling above my desk.', MaintenancePriority::High],
        ];

        foreach ($tickets as $index => [$category, $title, $description, $priority]) {
            $student = $students[$index % $students->count()];

            $ticket = MaintenanceRequest::create([
                'student_profile_id' => $student->id,
                'room_id' => $student->activeAllocation?->room_id,
                'category' => $category,
                'title' => $title,
                'description' => $description,
                'priority' => $priority,
                'status' => MaintenanceStatus::Pending,
            ]);

            $stage = $index % 4;

            if ($stage === 0) {
                // Left pending, unassigned — a fresh submission.
                continue;
            }

            $assignee = $staff[$index % 2];
            $ticket->update(['assigned_to' => $assignee]);

            $ticket->comments()->create([
                'user_id' => $student->user_id,
                'body' => 'Any update on this? It\'s still an issue.',
            ]);

            if ($stage === 1) {
                $this->progress($ticket, MaintenanceStatus::InProgress, $assignee, 'Assigned and investigating.');
                $ticket->comments()->create([
                    'user_id' => $assignee,
                    'body' => 'Looking into this now, will update shortly.',
                ]);

                continue;
            }

            $this->progress($ticket, MaintenanceStatus::InProgress, $assignee, 'Assigned and investigating.');
            $this->progress($ticket, MaintenanceStatus::Verification, $assignee, 'Repair completed, awaiting student confirmation.');
            $ticket->comments()->create([
                'user_id' => $assignee,
                'body' => 'This has been fixed — please confirm on your end.',
            ]);

            if ($stage === 2) {
                continue;
            }

            $this->progress($ticket, MaintenanceStatus::Completed, $student->user_id, 'Confirmed fixed, thank you.');
            $ticket->comments()->create([
                'user_id' => $student->user_id,
                'body' => 'Confirmed, working fine now. Thanks!',
            ]);
        }
    }

    /**
     * Transition a ticket's status and record the change in its history,
     * mirroring what MaintenanceRequestController::updateStatus() does.
     */
    private function progress(MaintenanceRequest $ticket, MaintenanceStatus $status, int $changedBy, ?string $note = null): void
    {
        $from = $ticket->status;

        $ticket->update([
            'status' => $status,
            'resolution_notes' => $status === MaintenanceStatus::Completed ? ($note ?? $ticket->resolution_notes) : $ticket->resolution_notes,
        ]);

        $ticket->statusLogs()->create([
            'changed_by' => $changedBy,
            'from_status' => $from,
            'to_status' => $status,
            'note' => $note,
        ]);
    }
}
