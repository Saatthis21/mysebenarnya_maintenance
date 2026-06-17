<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Create UserRecords for legacy rows that have no match ─────
        // (idempotent – skips already-existing emails)
        $this->createMissingUserRecords();

        // ── Step 2: Drop all inbound FK constraints (idempotent) ─────────────
        // The partial run may have already dropped some of these.
        $this->dropInboundForeignKeys();

        // ── Step 3: mcmc_staff → shared PK ───────────────────────────────────
        if (!Schema::hasColumn('mcmc_staff', 'id')) {
            Schema::table('mcmc_staff', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->nullable()->after('staff_ID');
            });
        }

        DB::statement("
            UPDATE mcmc_staff ms
            INNER JOIN users u ON ms.staff_Email = u.email AND u.user_type = 'mcmc'
            SET ms.id = u.id
            WHERE ms.id IS NULL
        ");

        // Strip AUTO_INCREMENT so MySQL allows us to drop the PK
        DB::statement('ALTER TABLE mcmc_staff MODIFY staff_ID BIGINT UNSIGNED NOT NULL');
        Schema::table('mcmc_staff', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('staff_ID');
        });
        Schema::table('mcmc_staff', function (Blueprint $table) {
            $table->primary('id');
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });

        // ── Step 4: agencies → shared PK ─────────────────────────────────────
        if (!Schema::hasColumn('agencies', 'id')) {
            Schema::table('agencies', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->nullable()->after('agency_ID');
            });
        }

        DB::statement("
            UPDATE agencies a
            INNER JOIN users u ON a.agency_Email = u.email AND u.user_type = 'agency'
            SET a.id = u.id
            WHERE a.id IS NULL
        ");

        DB::statement('ALTER TABLE agencies MODIFY agency_ID BIGINT UNSIGNED NOT NULL');
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('agency_ID');
        });
        Schema::table('agencies', function (Blueprint $table) {
            $table->primary('id');
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });

        // ── Step 5: public_users → shared PK ─────────────────────────────────
        if (!Schema::hasColumn('public_users', 'id')) {
            Schema::table('public_users', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->nullable()->after('user_ID');
            });
        }

        DB::statement("
            UPDATE public_users pu
            INNER JOIN users u ON pu.user_Email = u.email AND u.user_type = 'public'
            SET pu.id = u.id
            WHERE pu.id IS NULL
        ");

        DB::statement('ALTER TABLE public_users MODIFY user_ID BIGINT UNSIGNED NOT NULL');
        Schema::table('public_users', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('user_ID');
        });
        Schema::table('public_users', function (Blueprint $table) {
            $table->primary('id');
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Drop new shared-PK setup
        foreach (['mcmc_staff', 'agencies', 'public_users'] as $tbl) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->dropForeign(['id']);
                $table->dropPrimary();
            });
        }

        // Re-add old auto-increment PKs
        Schema::table('mcmc_staff', function (Blueprint $table) {
            $table->bigIncrements('staff_ID')->first();
        });
        Schema::table('agencies', function (Blueprint $table) {
            $table->bigIncrements('agency_ID')->first();
        });
        Schema::table('public_users', function (Blueprint $table) {
            $table->bigIncrements('user_ID')->first();
        });

        // Note: inbound FK constraints from approvals, assignments, etc. are NOT
        // restored because restoring them would require matching the old PK values.
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function createMissingUserRecords(): void
    {
        // mcmc_staff
        foreach (DB::table('mcmc_staff')->get() as $row) {
            if (!DB::table('users')->where('email', $row->staff_Email)->exists()) {
                DB::table('users')->insert([
                    'name'       => $row->staff_Name,
                    'email'      => $row->staff_Email,
                    'password'   => $row->staff_Password,
                    'user_type'  => 'mcmc',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // agencies
        foreach (DB::table('agencies')->get() as $row) {
            if (!DB::table('users')->where('email', $row->agency_Email)->exists()) {
                DB::table('users')->insert([
                    'name'              => $row->agency_Name,
                    'email'             => $row->agency_Email,
                    'password'          => $row->agency_Password,
                    'user_type'         => 'agency',
                    'email_verified_at' => now(),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }

        // public_users
        foreach (DB::table('public_users')->get() as $row) {
            if (!DB::table('users')->where('email', $row->user_Email)->exists()) {
                DB::table('users')->insert([
                    'name'       => $row->user_Name,
                    'email'      => $row->user_Email,
                    'password'   => $row->user_Password,
                    'user_type'  => 'public',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function dropInboundForeignKeys(): void
    {
        $toDrop = [
            'approvals'            => ['approvals_staff_id_foreign'],
            'inquiry_assignments'  => ['inquiry_assignments_assigned_by_foreign', 'inquiry_assignments_agency_id_foreign'],
            'inquiry_progress'     => ['inquiry_progress_staff_id_foreign', 'inquiry_progress_agency_id_foreign', 'inquiry_progress_user_id_foreign'],
            'reports'              => ['reports_staff_id_foreign'],
            'inquiries'            => ['inquiries_user_id_foreign'],
            'users'                => ['users_agency_id_foreign'],
        ];

        foreach ($toDrop as $table => $constraints) {
            foreach ($constraints as $constraint) {
                try {
                    Schema::table($table, function (Blueprint $t) use ($constraint) {
                        $t->dropForeign($constraint);
                    });
                } catch (\Throwable) {
                    // Already dropped by a previous partial run — safe to skip
                }
            }
        }
    }
};
