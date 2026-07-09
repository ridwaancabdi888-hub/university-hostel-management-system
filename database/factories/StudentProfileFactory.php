<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Enums\YearLevel;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentProfile>
 */
class StudentProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $courses = [
            'B.Sc. Computer Science',
            'Architecture & Design',
            'Medicine & Surgery',
            'International Law',
            'Business Administration',
            'Mechanical Engineering',
            'Economics',
            'Psychology',
        ];

        return [
            'user_id' => User::factory(),
            'student_id' => 'STU-'.fake()->unique()->numerify('####-####'),
            'course' => fake()->randomElement($courses),
            'year_level' => fake()->randomElement(YearLevel::cases()),
            'date_of_birth' => fake()->dateTimeBetween('-26 years', '-18 years'),
            'gender' => fake()->randomElement(Gender::cases()),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'guardian_name' => fake()->name(),
            'guardian_relationship' => fake()->randomElement(['Mother', 'Father', 'Aunt', 'Uncle', 'Guardian']),
            'guardian_phone' => fake()->phoneNumber(),
            'guardian_email' => fake()->safeEmail(),
            'guardian_address' => fake()->address(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_relationship' => fake()->randomElement(['Sibling', 'Friend', 'Relative']),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'status' => fake()->randomElement(StudentStatus::cases()),
        ];
    }
}
