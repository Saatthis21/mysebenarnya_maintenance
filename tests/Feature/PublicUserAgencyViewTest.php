<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\UserRecord;
use App\Models\InquirySubmissionRecord;
use App\Models\Agency;
use App\Models\McmcStaff;
use App\Models\Approval;
use App\Models\InquiryAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class PublicUserAgencyViewTest extends TestCase
{
    use RefreshDatabase;

    protected $publicUser;
    protected $agency;
    protected $mcmcStaff;
    protected $inquiry;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->publicUser = UserRecord::create([
            'name' => 'Test Public User',
            'email' => 'testpublic@example.com',
            'password' => bcrypt('password'),
            'user_type' => 'public',
            'email_verified_at' => now(),
        ]);

        $this->agency = Agency::create([
            'agency_Name' => 'Test Government Agency',
            'agency_Type' => 'government',
            'agency_Email' => 'agency@example.com',
            'agency_Phone' => '123-456-7890',
            'agency_Password' => bcrypt('password'),
            'agency_First_Time_Login' => true,
            'agency_Created_At' => now(),
        ]);

        $this->mcmcStaff = McmcStaff::create([
            'staff_Name' => 'Test MCMC Staff',
            'staff_Email' => 'mcmc@example.com',
            'staff_Password' => bcrypt('password'),
            'staff_Role' => 'admin',
            'staff_Created_At' => now(),
        ]);

        $this->inquiry = InquirySubmissionRecord::create([
            'user_ID' => $this->publicUser->id,
            'inquiry_Title' => 'Test Inquiry',
            'inquiry_Description' => 'This is a test inquiry description.',
            'inquiry_Category' => 'general',
            'inquiry_Status' => 'assigned_to_agency',
            'inquiry_Created_At' => now(),
        ]);
    }

    public function test_public_user_can_see_agency_assignment_info()
    {
        // Create approval and assignment
        $approval = Approval::create([
            'inquiry_ID' => $this->inquiry->inquiry_ID,
            'staff_ID' => $this->mcmcStaff->staff_ID,
            'approval_Status' => 'approved',
            'approval_Comments' => 'Approved for agency review',
            'approval_Type' => 'mcmc_review',
            'approval_Date' => now(),
        ]);

        $assignment = InquiryAssignment::create([
            'agency_ID' => $this->agency->agency_ID,
            'approval_ID' => $approval->approval_ID,
            'assignment_Date' => now(),
            'assignment_Status' => 'in_progress',
            'assignment_Comments' => 'Assigned to agency for review',
            'assigned_By' => $this->mcmcStaff->staff_ID,
        ]);

        // Test the model method
        $agencyInfo = $this->inquiry->getAgencyAssignmentInfo();

        $this->assertNotNull($agencyInfo);
        $this->assertEquals('Test Government Agency', $agencyInfo['agency_name']);
        $this->assertEquals('in_progress', $agencyInfo['assignment_status']);
        $this->assertNotNull($agencyInfo['assigned_date']);
    }

    public function test_inquiry_without_assignment_returns_null()
    {
        // Test inquiry without any assignment
        $agencyInfo = $this->inquiry->getAgencyAssignmentInfo();

        $this->assertNull($agencyInfo);
        $this->assertFalse($this->inquiry->hasAgencyAssignment());
    }

    public function test_inquiry_with_assignment_returns_true()
    {
        // Create approval and assignment
        $approval = Approval::create([
            'inquiry_ID' => $this->inquiry->inquiry_ID,
            'staff_ID' => $this->mcmcStaff->staff_ID,
            'approval_Status' => 'approved',
            'approval_Comments' => 'Approved for agency review',
            'approval_Type' => 'mcmc_review',
            'approval_Date' => now(),
        ]);

        InquiryAssignment::create([
            'agency_ID' => $this->agency->agency_ID,
            'approval_ID' => $approval->approval_ID,
            'assignment_Date' => now(),
            'assignment_Status' => 'completed',
            'assignment_Comments' => 'Review completed',
            'assigned_By' => $this->mcmcStaff->staff_ID,
        ]);

        $this->assertTrue($this->inquiry->hasAgencyAssignment());
    }

    public function test_public_user_can_access_inquiry_history_page()
    {
        // Login as public user
        Auth::guard('public')->login($this->publicUser);

        $response = $this->get(route('inquiry.history'));

        $response->assertStatus(200);
        $response->assertSee('My Inquiry Submissions');
        $response->assertSee($this->inquiry->inquiry_Title);
    }
}
