<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Department;
use App\Models\Institute;
use App\Models\InterviewRecord;
use App\Models\Lead;
use App\Models\LeaveRequest;
use App\Models\Payment;
use App\Models\Petition;
use App\Models\Report;
use App\Models\Subject;
use App\Models\Title;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_priority_factories_can_create_valid_records(): void
    {
        $department = Department::factory()->create();
        $title = Title::factory()->create(['department_id' => $department->id]);
        $institute = Institute::factory()->create();
        $course = Course::factory()->create();
        $subject = Subject::factory()->create(['course_id' => $course->id]);
        $classroom = Classroom::factory()->create();
        $lead = Lead::factory()->create();
        $interviewRecord = InterviewRecord::factory()->create(['lead_id' => $lead->id]);
        $leaveRequest = LeaveRequest::factory()->create();
        $petition = Petition::factory()->create();
        $announcement = Announcement::factory()->create();
        $payment = Payment::factory()->create();
        $report = Report::factory()->create();

        $this->assertDatabaseHas('departments', ['id' => $department->id]);
        $this->assertDatabaseHas('titles', ['id' => $title->id, 'department_id' => $department->id]);
        $this->assertDatabaseHas('institutes', ['id' => $institute->id]);
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'course_id' => $course->id]);
        $this->assertDatabaseHas('classrooms', ['id' => $classroom->id]);
        $this->assertDatabaseHas('leads', ['id' => $lead->id]);
        $this->assertDatabaseHas('interview_records', ['id' => $interviewRecord->id, 'lead_id' => $lead->id]);
        $this->assertDatabaseHas('leave_requests', ['id' => $leaveRequest->id]);
        $this->assertDatabaseHas('petitions', ['id' => $petition->id]);
        $this->assertDatabaseHas('announcements', ['id' => $announcement->id]);
        $this->assertDatabaseHas('payments', ['id' => $payment->id]);
        $this->assertDatabaseHas('reports', ['id' => $report->id]);
    }

    public function test_database_seeder_loads_development_data_in_local_environment(): void
    {
        config(['app.env' => 'local']);

        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@cskm.com')->first();

        $this->assertNotNull($admin);
        $this->assertSame(Role::Admin, $admin->role);
        $this->assertTrue(Hash::check('password123', $admin->password));
        $this->assertDatabaseCount('regions', 3);
        $this->assertDatabaseCount('departments', 5);
        $this->assertDatabaseCount('titles', 5);
    }

    public function test_database_seeder_skips_development_data_in_production_environment(): void
    {
        config(['app.env' => 'production']);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('regions', 0);
        $this->assertDatabaseCount('departments', 0);
        $this->assertDatabaseCount('titles', 0);
        $this->assertDatabaseMissing('users', ['email' => 'admin@cskm.com']);
    }
}
