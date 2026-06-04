<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\UserRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class McmcManageUsersFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function createMcmcUser(): UserRecord
    {
        return UserRecord::create([
            'name' => 'MCMC Staff',
            'email' => 'mcmc.staff@example.com',
            'contact_number' => '+60111111111',
            'password' => Hash::make('password'),
            'user_type' => 'mcmc',
            'email_verified_at' => now(),
        ]);
    }

    public function test_mcmc_manage_users_page_loads(): void
    {
        $mcmc = $this->createMcmcUser();

        $response = $this->actingAs($mcmc, 'mcmc')
            ->get(route('mcmc.users'));

        $response->assertStatus(200);
        $response->assertViewIs('mcmc.manage-users');
    }

    public function test_public_users_filter_verified_only_works(): void
    {
        $mcmc = $this->createMcmcUser();

        UserRecord::create([
            'name' => 'Verified Public',
            'email' => 'verified.public@example.com',
            'contact_number' => '+60122222222',
            'password' => Hash::make('password'),
            'user_type' => 'public',
            'email_verified_at' => now(),
        ]);

        UserRecord::create([
            'name' => 'Unverified Public',
            'email' => 'unverified.public@example.com',
            'contact_number' => '+60133333333',
            'password' => Hash::make('password'),
            'user_type' => 'public',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($mcmc, 'mcmc')
            ->get(route('mcmc.users', ['public_filter' => 'verified']));

        $response->assertStatus(200);
        $response->assertSee('Verified Public');
        $response->assertDontSee('Unverified Public');
    }

    public function test_public_users_search_works(): void
    {
        $mcmc = $this->createMcmcUser();

        UserRecord::create([
            'name' => 'Ali Citizen',
            'email' => 'ali.citizen@example.com',
            'contact_number' => '+60144444444',
            'password' => Hash::make('password'),
            'user_type' => 'public',
            'email_verified_at' => now(),
        ]);

        UserRecord::create([
            'name' => 'Siti Citizen',
            'email' => 'siti.citizen@example.com',
            'contact_number' => '+60155555555',
            'password' => Hash::make('password'),
            'user_type' => 'public',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($mcmc, 'mcmc')
            ->get(route('mcmc.users', ['public_search' => 'Ali']));

        $response->assertStatus(200);
        $response->assertSee('Ali Citizen');
        $response->assertDontSee('Siti Citizen');
    }

    public function test_agency_filter_needs_reset_works(): void
    {
        $mcmc = $this->createMcmcUser();

        Agency::create([
            'agency_Name' => 'Needs Reset Agency',
            'agency_Type' => 'government_agency',
            'agency_Email' => 'needs.reset@agency.gov.my',
            'agency_Phone' => '+60166666666',
            'agency_Password' => Hash::make('password'),
            'agency_First_Time_Login' => true,
            'agency_Created_At' => now(),
            'agency_Updated_At' => now(),
        ]);

        Agency::create([
            'agency_Name' => 'Active Agency',
            'agency_Type' => 'government_agency',
            'agency_Email' => 'active@agency.gov.my',
            'agency_Phone' => '+60177777777',
            'agency_Password' => Hash::make('password'),
            'agency_First_Time_Login' => false,
            'agency_Created_At' => now(),
            'agency_Updated_At' => now(),
        ]);

        $response = $this->actingAs($mcmc, 'mcmc')
            ->get(route('mcmc.users', ['agency_filter' => 'needs-reset']));

        $response->assertStatus(200);
        $response->assertSee('Needs Reset Agency');
        $response->assertDontSee('Active Agency');
    }

    public function test_agency_search_works(): void
    {
        $mcmc = $this->createMcmcUser();

        Agency::create([
            'agency_Name' => 'Ministry of Testing',
            'agency_Type' => 'ministry',
            'agency_Email' => 'mot@agency.gov.my',
            'agency_Phone' => '+60188888888',
            'agency_Password' => Hash::make('password'),
            'agency_First_Time_Login' => true,
            'agency_Created_At' => now(),
            'agency_Updated_At' => now(),
        ]);

        Agency::create([
            'agency_Name' => 'Department of Samples',
            'agency_Type' => 'department',
            'agency_Email' => 'dos@agency.gov.my',
            'agency_Phone' => '+60199999999',
            'agency_Password' => Hash::make('password'),
            'agency_First_Time_Login' => true,
            'agency_Created_At' => now(),
            'agency_Updated_At' => now(),
        ]);

        $response = $this->actingAs($mcmc, 'mcmc')
            ->get(route('mcmc.users', ['agency_search' => 'Ministry']));

        $response->assertStatus(200);
        $response->assertSee('Ministry of Testing');
        $response->assertDontSee('Department of Samples');
    }

    public function test_cross_tab_filter_params_are_preserved_in_forms(): void
    {
        $mcmc = $this->createMcmcUser();

        $response = $this->actingAs($mcmc, 'mcmc')
            ->get(route('mcmc.users', [
                'public_search' => 'Ali',
                'public_filter' => 'verified',
                'agency_search' => 'Ministry',
                'agency_filter' => 'active',
            ]));

        $response->assertStatus(200);
        $response->assertSee('name="agency_search" value="Ministry"', false);
        $response->assertSee('name="agency_filter" value="active"', false);
        $response->assertSee('name="public_search" value="Ali"', false);
        $response->assertSee('name="public_filter" value="verified"', false);
    }
}
