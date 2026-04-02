<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create student role
        $studentRole = DB::table('roles')->select('id')->first();
        if (!$studentRole) {
            DB::table('roles')->insert([
                'name' => 'students',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $studentRoleId = DB::getPdo()->lastInsertId();
        } else {
            $studentRoleId = $studentRole->id;
        }

        // Get or create teacher role
        $teacherRole = DB::table('roles')->select('id')->where('name', 'teachers')->first();
        if (!$teacherRole) {
            DB::table('roles')->insert([
                'name' => 'teachers',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $teacherRoleId = DB::getPdo()->lastInsertId();
        } else {
            $teacherRoleId = $teacherRole->id;
        }

        // Get or create student user
        $studentUser = DB::table('users')->select('id')->where('email', 'student@example.com')->first();
        if (!$studentUser) {
            DB::table('users')->insert([
                'name' => 'Student User',
                'email' => 'student@example.com',
                'password' => Hash::make('password'),
                'role_id' => $studentRoleId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $studentUserId = DB::getPdo()->lastInsertId();
        } else {
            $studentUserId = $studentUser->id;
        }

        // Get or create teacher user
        $teacherUser = DB::table('users')->select('id')->where('email', 'teacher@example.com')->first();
        if (!$teacherUser) {
            DB::table('users')->insert([
                'name' => 'Teacher User',
                'email' => 'teacher@example.com',
                'password' => Hash::make('password'),
                'role_id' => $teacherRoleId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $teacherUserId = DB::getPdo()->lastInsertId();
        } else {
            $teacherUserId = $teacherUser->id;
        }

        // Get or create company
        $company = DB::table('companies')->select('id')->first();
        if (!$company) {
            DB::table('companies')->insert([
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $companyId = DB::getPdo()->lastInsertId();
        } else {
            $companyId = $company->id;
        }

        // Get or create group
        $group = DB::table('groups')->select('id')->first();
        if (!$group) {
            DB::table('groups')->insert([
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $groupId = DB::getPdo()->lastInsertId();
        } else {
            $groupId = $group->id;
        }

        // Get or create assessment type
        $assessment = DB::table('assesment_type')->select('id')->first();
        if (!$assessment) {
            DB::table('assesment_type')->insert([
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $assessmentId = DB::getPdo()->lastInsertId();
        } else {
            $assessmentId = $assessment->id;
        }

        // Get or create internship
        $internship = DB::table('internships')->select('id')->first();
        if (!$internship) {
            DB::table('internships')->insert([
                'company_id' => $companyId,
                'group_id' => $groupId,
                'title' => 'Test Internship',
                'description' => 'A test internship for testing',
                'goals' => 'Learn Laravel',
                'start_at' => Carbon::now()->subMonth(),
                'end_at' => Carbon::now()->addMonth(),
                'assesment_id' => $assessmentId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $internshipId = DB::getPdo()->lastInsertId();
        } else {
            $internshipId = $internship->id;
        }

        $this->command->info('Test data seeded successfully!');
        $this->command->info("Student User ID: {$studentUserId}");
        $this->command->info("Teacher User ID: {$teacherUserId}");
        $this->command->info("Internship ID: {$internshipId}");
    }
}
