<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'profile_picture')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('profile_picture')->nullable()->after('email_verified_at');
            });
        }

        if (Schema::hasTable('mcmc_staff')) {
            Schema::table('mcmc_staff', function (Blueprint $table) {
                if (Schema::hasColumn('mcmc_staff', 'profile_picture')) {
                    $table->dropColumn('profile_picture');
                }
                if (Schema::hasColumn('mcmc_staff', 'staff_Profile_Picture')) {
                    $table->dropColumn('staff_Profile_Picture');
                }
            });
        }

        if (Schema::hasTable('public_users')) {
            Schema::table('public_users', function (Blueprint $table) {
                if (Schema::hasColumn('public_users', 'profile_picture')) {
                    $table->dropColumn('profile_picture');
                }
                if (Schema::hasColumn('public_users', 'user_Profile_Picture')) {
                    $table->dropColumn('user_Profile_Picture');
                }
            });
        }

        if (Schema::hasTable('agencies')) {
            Schema::table('agencies', function (Blueprint $table) {
                if (Schema::hasColumn('agencies', 'profile_picture')) {
                    $table->dropColumn('profile_picture');
                }
                if (Schema::hasColumn('agencies', 'agency_Profile_Picture')) {
                    $table->dropColumn('agency_Profile_Picture');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'profile_picture')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('profile_picture');
            });
        }
    }
};
