<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing Manager to Lead Manager
        DB::statement("UPDATE user_rh SET role = 'Lead Manager' WHERE role = 'Manager'");

        // Modify the enum column
        DB::statement("ALTER TABLE user_rh MODIFY COLUMN role ENUM('Staff', 'Staff Manager', 'Lead Manager', 'Head Manager', 'Vice Director', 'Director') DEFAULT 'Staff'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Lead Manager back to Manager
        DB::statement("UPDATE user_rh SET role = 'Manager' WHERE role = 'Lead Manager'");

        // Revert Head Manager to Manager
        DB::statement("UPDATE user_rh SET role = 'Manager' WHERE role = 'Head Manager'");

        // Restore original enum
        DB::statement("ALTER TABLE user_rh MODIFY COLUMN role ENUM('Staff', 'Staff Manager', 'Manager', 'Vice Director', 'Director') DEFAULT 'Staff'");
    }
};
