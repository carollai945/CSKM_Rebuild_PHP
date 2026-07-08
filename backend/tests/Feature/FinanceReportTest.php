<?php
namespace Tests\Feature;

use App\Enums\Role;
use App\Models\InvoiceRequest;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FinanceReportTest extends TestCase
{
    use RefreshDatabase;

    private function financeUser(): User { return User::factory()->create(['role' => Role::Finance]); }
    private function adminUser(): User   { return User::factory()->create(['role' => Role::Admin]); }
    private function ceoUser(): User     { return User::factory()->create(['role' => Role::CEO]); }

    private function makeStaffWithInvoice(array $invoiceAttrs = []): InvoiceRequest
    {
        $staff = Staff::factory()->create();
        return InvoiceRequest::create(array_merge([
            'staff_id'    => $staff->id,
            'title'       => 'Test Invoice',
            'amount'      => 1000,
            'status'      => 'PENDING',
        ], $invoiceAttrs));
    }

    private function makeStudentWithPayment(array $paymentAttrs = []): Payment
    {
        $student = Student::create(['student_no' => 'ST' . rand(100, 999), 'name' => 'Test', 'status' => 'ACTIVE']);
        return Payment::create(array_merge([
            'student_id'   => $student->id,
            'amount'       => 500,
            'status'       => 'PENDING',
            'payment_date' => '2026-07-01',
        ], $paymentAttrs));
    }

    // ─── /invoices ───────────────────────────────────────────────────

    public function test_finance_can_view_invoices_report(): void
    {
        Sanctum::actingAs($this->financeUser());
        $this->makeStaffWithInvoice();
        $this->getJson('/api/v1/finance-reports/invoices')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_admin_can_view_invoices_report(): void
    {
        Sanctum::actingAs($this->adminUser());
        $this->getJson('/api/v1/finance-reports/invoices')->assertStatus(200);
    }

    public function test_ceo_can_view_invoices_report(): void
    {
        Sanctum::actingAs($this->ceoUser());
        $this->getJson('/api/v1/finance-reports/invoices')->assertStatus(200);
    }

    public function test_invoices_aggregates_by_status(): void
    {
        Sanctum::actingAs($this->financeUser());
        $this->makeStaffWithInvoice(['status' => 'PENDING', 'amount' => 300]);
        $this->makeStaffWithInvoice(['status' => 'PENDING', 'amount' => 700]);
        $this->makeStaffWithInvoice(['status' => 'APPROVED', 'amount' => 500]);

        $response = $this->getJson('/api/v1/finance-reports/invoices')->assertStatus(200);
        $data = collect($response->json('data'));

        $pending = $data->firstWhere('status', 'PENDING');
        $this->assertEquals(2, $pending['count']);
        $this->assertEquals('1000.00', $pending['total']);

        $approved = $data->firstWhere('status', 'APPROVED');
        $this->assertEquals(1, $approved['count']);
    }

    public function test_invoices_filters_by_date_range(): void
    {
        Sanctum::actingAs($this->financeUser());
        $this->getJson('/api/v1/finance-reports/invoices?from_date=2026-07-01&to_date=2026-07-31')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_staff_cannot_access_invoices_report(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => Role::Staff]));
        $this->getJson('/api/v1/finance-reports/invoices')->assertStatus(403);
    }

    public function test_unauthenticated_cannot_access_invoices_report(): void
    {
        auth()->forgetGuards();
        $this->getJson('/api/v1/finance-reports/invoices')->assertStatus(401);
    }

    // ─── /payments ───────────────────────────────────────────────────

    public function test_finance_can_view_payments_report(): void
    {
        Sanctum::actingAs($this->financeUser());
        $this->makeStudentWithPayment();
        $this->getJson('/api/v1/finance-reports/payments')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_admin_can_view_payments_report(): void
    {
        Sanctum::actingAs($this->adminUser());
        $this->getJson('/api/v1/finance-reports/payments')->assertStatus(200);
    }

    public function test_ceo_can_view_payments_report(): void
    {
        Sanctum::actingAs($this->ceoUser());
        $this->getJson('/api/v1/finance-reports/payments')->assertStatus(200);
    }

    public function test_payments_aggregates_by_status(): void
    {
        Sanctum::actingAs($this->financeUser());
        $this->makeStudentWithPayment(['status' => 'PENDING', 'amount' => 200]);
        $this->makeStudentWithPayment(['status' => 'PENDING', 'amount' => 300]);
        $this->makeStudentWithPayment(['status' => 'ACADEMIC_CONFIRMED', 'amount' => 1000]);

        $response = $this->getJson('/api/v1/finance-reports/payments')->assertStatus(200);
        $data = collect($response->json('data'));

        $pending = $data->firstWhere('status', 'PENDING');
        $this->assertEquals(2, $pending['count']);
        $this->assertEquals('500.00', $pending['total']);

        $confirmed = $data->firstWhere('status', 'ACADEMIC_CONFIRMED');
        $this->assertEquals(1, $confirmed['count']);
    }

    public function test_payments_filters_by_date_range(): void
    {
        Sanctum::actingAs($this->financeUser());
        $this->getJson('/api/v1/finance-reports/payments?from_date=2026-07-01&to_date=2026-07-31')
            ->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    public function test_payments_filters_by_student(): void
    {
        Sanctum::actingAs($this->financeUser());
        $student = Student::create(['student_no' => 'ST999', 'name' => 'Specific', 'status' => 'ACTIVE']);
        Payment::create(['student_id' => $student->id, 'amount' => 800, 'status' => 'PENDING', 'payment_date' => '2026-07-01']);

        $response = $this->getJson('/api/v1/finance-reports/payments?student_id=' . $student->id)->assertStatus(200);
        $data = collect($response->json('data'));
        $this->assertEquals(1, $data->sum('count'));
    }

    public function test_staff_cannot_access_payments_report(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => Role::Staff]));
        $this->getJson('/api/v1/finance-reports/payments')->assertStatus(403);
    }

    public function test_unauthenticated_cannot_access_payments_report(): void
    {
        auth()->forgetGuards();
        $this->getJson('/api/v1/finance-reports/payments')->assertStatus(401);
    }
}
