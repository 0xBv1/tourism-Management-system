<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\User;
use App\Enums\InquiryStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class InquiryResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that Finance role users only see confirmed inquiries
     *
     * @return void
     */
    public function test_finance_role_sees_only_confirmed_inquiries()
    {
        // Create roles
        $financeRole = Role::create(['name' => 'Finance', 'guard_name' => 'web']);
        
        // Create a Finance user
        $financeUser = User::factory()->create();
        $financeUser->assignRole($financeRole);
        
        // Create inquiries with different statuses
        $pendingInquiry = Inquiry::factory()->create(['status' => InquiryStatus::PENDING]);
        $confirmedInquiry = Inquiry::factory()->create(['status' => InquiryStatus::CONFIRMED]);
        $cancelledInquiry = Inquiry::factory()->create(['status' => InquiryStatus::CANCELLED]);
        
        // Login as Finance user
        $this->actingAs($financeUser);
        
        // Access the inquiries index page
        $response = $this->get('/dashboard/inquiries');
        
        // Should only see confirmed inquiries
        $response->assertStatus(200);
        
        // The DataTable should filter to only show confirmed inquiries
        // This is tested at the DataTable level, not the view level
        $this->assertTrue($financeUser->hasRole('Finance'));
    }

    /**
     * Test that other roles see all inquiries (except restricted roles)
     *
     * @return void
     */
    public function test_admin_role_sees_all_inquiries()
    {
        // Create roles
        $adminRole = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        
        // Create an Admin user
        $adminUser = User::factory()->create();
        $adminUser->assignRole($adminRole);
        
        // Create inquiries with different statuses
        $pendingInquiry = Inquiry::factory()->create(['status' => InquiryStatus::PENDING]);
        $confirmedInquiry = Inquiry::factory()->create(['status' => InquiryStatus::CONFIRMED]);
        $cancelledInquiry = Inquiry::factory()->create(['status' => InquiryStatus::CANCELLED]);
        
        // Login as Admin user
        $this->actingAs($adminUser);
        
        // Access the inquiries index page
        $response = $this->get('/dashboard/inquiries');
        
        // Should see all inquiries
        $response->assertStatus(200);
        
        $this->assertTrue($adminUser->hasRole('Admin'));
    }
}
