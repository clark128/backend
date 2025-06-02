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
        Schema::table('application_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('application_requests', 'status')) {
                $table->text('status')->nullable()->after('signature_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
