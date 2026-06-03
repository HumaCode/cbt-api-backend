<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Category;
use App\Models\Question;
use App\Models\Assessment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Run Spatie roles & permissions seeder
        $this->call(RoleAndPermissionSeeder::class);

        // 2. Create Users
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@cbt.com',
            'password' => bcrypt('password123'),
            'is_active' => '1',
        ]);
        $admin->assignRole('Super Admin');

        $peserta = User::create([
            'name' => 'Peserta Ujian',
            'username' => 'peserta',
            'email' => 'peserta@cbt.com',
            'password' => bcrypt('password123'),
            'is_active' => '1',
        ]);
        $peserta->assignRole('Peserta');

        // 3. Create Groups
        $groupA = Group::create([
            'name' => 'Kelas X-A',
        ]);
        $groupB = Group::create([
            'name' => 'Kelas X-B',
        ]);

        // Attach Peserta to groupA
        $peserta->groups()->attach($groupA->id);

        // 4. Create Categories
        $catMath = Category::create([
            'name' => 'Matematika',
        ]);
        $catEnglish = Category::create([
            'name' => 'Bahasa Inggris',
        ]);

        // 5. Create Questions
        // Math PG Q1
        $q1 = Question::create([
            'category_id' => $catMath->id,
            'type' => 'pg',
            'difficulty' => 'easy',
            'content_text' => 'Berapakah hasil dari 5 * 6?',
            'created_by' => $admin->id,
        ]);
        $q1->options()->createMany([
            ['option_text' => '30', 'is_correct' => true, 'weight' => 20.00],
            ['option_text' => '25', 'is_correct' => false, 'weight' => 0.00],
            ['option_text' => '35', 'is_correct' => false, 'weight' => 0.00],
            ['option_text' => '20', 'is_correct' => false, 'weight' => 0.00],
        ]);

        // Math PG Q2
        $q2 = Question::create([
            'category_id' => $catMath->id,
            'type' => 'pg',
            'difficulty' => 'medium',
            'content_text' => 'Jika 2x + 5 = 15, berapakah nilai x?',
            'created_by' => $admin->id,
        ]);
        $q2->options()->createMany([
            ['option_text' => '5', 'is_correct' => true, 'weight' => 30.00],
            ['option_text' => '3', 'is_correct' => false, 'weight' => 0.00],
            ['option_text' => '4', 'is_correct' => false, 'weight' => 0.00],
            ['option_text' => '6', 'is_correct' => false, 'weight' => 0.00],
        ]);

        // Math Essay Q3
        $q3 = Question::create([
            'category_id' => $catMath->id,
            'type' => 'essay',
            'difficulty' => 'hard',
            'content_text' => 'Jelaskan perbedaan antara bilangan rasional dan irasional beserta contohnya.',
            'created_by' => $admin->id,
        ]);

        // English PG Q4
        $q4 = Question::create([
            'category_id' => $catEnglish->id,
            'type' => 'pg',
            'difficulty' => 'easy',
            'content_text' => 'What is the synonym of "Happy"?',
            'created_by' => $admin->id,
        ]);
        $q4->options()->createMany([
            ['option_text' => 'Joyful', 'is_correct' => true, 'weight' => 50.00],
            ['option_text' => 'Sad', 'is_correct' => false, 'weight' => 0.00],
            ['option_text' => 'Angry', 'is_correct' => false, 'weight' => 0.00],
            ['option_text' => 'Tired', 'is_correct' => false, 'weight' => 0.00],
        ]);

        // 6. Create Assessments
        $assessmentMath = Assessment::create([
            'title' => 'Ujian Tengah Semester Matematika',
            'start_date' => now()->subHours(2)->toDateTimeString(),
            'end_date' => now()->addDays(7)->toDateTimeString(),
            'duration_minutes' => 60,
            'max_attempts' => 2,
            'passing_grade' => 50.00,
        ]);

        // Assign to group A
        $assessmentMath->groups()->attach($groupA->id);

        // Assign questions 1, 2, and 3
        $assessmentMath->questions()->attach([
            $q1->id => ['order_no' => 1],
            $q2->id => ['order_no' => 2],
            $q3->id => ['order_no' => 3],
        ]);

        $assessmentEnglish = Assessment::create([
            'title' => 'Kuis Harian Bahasa Inggris',
            'start_date' => now()->subHours(1)->toDateTimeString(),
            'end_date' => now()->addDays(2)->toDateTimeString(),
            'duration_minutes' => 30,
            'max_attempts' => 3,
            'passing_grade' => 50.00,
        ]);

        // Assign to group A & B
        $assessmentEnglish->groups()->attach([$groupA->id, $groupB->id]);

        // Assign question 4
        $assessmentEnglish->questions()->attach([
            $q4->id => ['order_no' => 1]
        ]);
    }
}
