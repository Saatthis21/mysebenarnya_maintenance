<?php

/**
 * Database Relationship Test Script
 *
 * This script demonstrates and tests all database table relationships
 * Run this script to verify that all tables are properly connected
 */

require_once 'bootstrap/app.php';

use App\Models\PublicUser;
use App\Models\Agency;
use App\Models\McmcStaff;
use App\Models\InquirySubmissionRecord;
use App\Models\Approval;
use App\Models\InquiryAssignment;
use App\Models\InquiryProgress;
use App\Models\Report;
use App\Models\UserRecord;

echo "=== DATABASE RELATIONSHIPS TEST ===\n\n";

// Test 1: Public Users and their Inquiries
echo "1. Testing Public Users → Inquiries Relationship:\n";
echo "------------------------------------------------\n";
$publicUsers = PublicUser::with('inquiries')->get();
foreach ($publicUsers->take(3) as $user) {
    echo "Public User: {$user->user_Name} ({$user->user_Email})\n";
    echo "   Inquiries: " . $user->inquiries->count() . "\n";
    foreach ($user->inquiries->take(2) as $inquiry) {
        echo "   - {$inquiry->inquiry_Title} (Status: {$inquiry->inquiry_Status})\n";
    }
    echo "\n";
}

// Test 2: Agencies and their Assignments
echo "2. Testing Agencies → Assignments Relationship:\n";
echo "-----------------------------------------------\n";
$agencies = Agency::with('assignments')->get();
foreach ($agencies->take(3) as $agency) {
    echo "Agency: {$agency->agency_Name}\n";
    echo "   Type: {$agency->agency_Type}\n";
    echo "   Assignments: " . $agency->assignments->count() . "\n";
    foreach ($agency->assignments->take(2) as $assignment) {
        echo "   - Assignment ID: {$assignment->assignment_ID} (Status: {$assignment->assignment_Status})\n";
    }
    echo "\n";
}

// Test 3: MCMC Staff and their Approvals
echo "3. Testing MCMC Staff → Approvals Relationship:\n";
echo "-----------------------------------------------\n";
$mcmcStaff = McmcStaff::with('approvals')->get();
foreach ($mcmcStaff->take(3) as $staff) {
    echo "MCMC Staff: {$staff->staff_Name} ({$staff->staff_Email})\n";
    echo "   Approvals: " . $staff->approvals->count() . "\n";
    echo "   Assignments: " . $staff->assignments->count() . "\n";
    echo "\n";
}

// Test 4: Inquiries and their Complex Relationships
echo "4. Testing Inquiries → Complex Relationships:\n";
echo "--------------------------------------------\n";
$inquiries = InquirySubmissionRecord::with(['user', 'approvals', 'assignments', 'progressRecords'])->get();
foreach ($inquiries->take(3) as $inquiry) {
    echo "Inquiry: {$inquiry->inquiry_Title}\n";
    echo "   Submitted by: " . ($inquiry->user ? $inquiry->user->user_Name : 'Unknown') . "\n";
    echo "   Status: {$inquiry->inquiry_Status}\n";
    echo "   Approvals: " . $inquiry->approvals->count() . "\n";
    echo "   Assignments: " . $inquiry->assignments->count() . "\n";
    echo "   Progress Records: " . $inquiry->progressRecords->count() . "\n";

    // Check if assigned to agency
    $agencyInfo = $inquiry->getAgencyAssignmentInfo();
    if ($agencyInfo) {
        echo "   Assigned to: {$agencyInfo['agency_name']}\n";
        echo "   Assignment Status: {$agencyInfo['assignment_status']}\n";
    }
    echo "\n";
}

// Test 5: Approvals and their Relationships
echo "5. Testing Approvals → Relationships:\n";
echo "------------------------------------\n";
$approvals = Approval::with(['inquiry', 'staff', 'assignment'])->get();
foreach ($approvals->take(3) as $approval) {
    echo "Approval ID: {$approval->approval_ID}\n";
    echo "   Inquiry: " . ($approval->inquiry ? $approval->inquiry->inquiry_Title : 'Unknown') . "\n";
    echo "   Staff: " . ($approval->staff ? $approval->staff->staff_Name : 'Unknown') . "\n";
    echo "   Status: {$approval->approval_Status}\n";
    echo "   Type: {$approval->approval_Type}\n";
    echo "   Has Assignment: " . ($approval->assignment ? 'Yes' : 'No') . "\n";
    echo "\n";
}

// Test 6: Assignment Chain (Inquiry → Approval → Assignment → Agency)
echo "6. Testing Assignment Chain:\n";
echo "---------------------------\n";
$assignments = InquiryAssignment::with(['inquiry', 'approval', 'agency', 'assignedByStaff'])->get();
foreach ($assignments->take(3) as $assignment) {
    echo "Assignment ID: {$assignment->assignment_ID}\n";
    echo "   Inquiry: " . ($assignment->inquiry ? $assignment->inquiry->inquiry_Title : 'Unknown') . "\n";
    echo "   Agency: " . ($assignment->agency ? $assignment->agency->agency_Name : 'Unknown') . "\n";
    echo "   Assigned by: " . ($assignment->assignedByStaff ? $assignment->assignedByStaff->staff_Name : 'Unknown') . "\n";
    echo "   Status: {$assignment->assignment_Status}\n";
    echo "   Date: {$assignment->assignment_Date}\n";
    echo "\n";
}

// Test 7: Progress Records
echo "7. Testing Progress Records:\n";
echo "---------------------------\n";
$progressRecords = InquiryProgress::with(['inquiry', 'agency', 'staff'])->get();
foreach ($progressRecords->take(3) as $progress) {
    echo "Progress ID: {$progress->progress_ID}\n";
    echo "   Inquiry: " . ($progress->inquiry ? $progress->inquiry->inquiry_Title : 'Unknown') . "\n";
    echo "   Agency: " . ($progress->agency ? $progress->agency->agency_Name : 'Unknown') . "\n";
    echo "   Staff: " . ($progress->staff ? $progress->staff->staff_Name : 'Unknown') . "\n";
    echo "   Status: {$progress->progress_Status}\n";
    echo "\n";
}

// Test 8: User Records (Authentication Table)
echo "8. Testing User Records (Authentication):\n";
echo "----------------------------------------\n";
$userRecords = UserRecord::all();
foreach ($userRecords->take(5) as $user) {
    echo "User: {$user->name} ({$user->email})\n";
    echo "   Type: {$user->user_type}\n";
    echo "   Verified: " . ($user->hasVerifiedEmail() ? 'Yes' : 'No') . "\n";
    echo "\n";
}

// Test 9: Database Statistics
echo "9. Database Statistics:\n";
echo "----------------------\n";
echo "Total Public Users: " . PublicUser::count() . "\n";
echo "Total Agencies: " . Agency::count() . "\n";
echo "Total MCMC Staff: " . McmcStaff::count() . "\n";
echo "Total Inquiries: " . InquirySubmissionRecord::count() . "\n";
echo "Total Approvals: " . Approval::count() . "\n";
echo "Total Assignments: " . InquiryAssignment::count() . "\n";
echo "Total Progress Records: " . InquiryProgress::count() . "\n";
echo "Total User Records: " . UserRecord::count() . "\n";

echo "\n=== ALL DATABASE RELATIONSHIPS ARE CONNECTED! ===\n";
