<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing null values to empty strings in mobile number columns
        DB::table('employment_payment_details')
            ->whereNull('applicant_employer_mobile_no')
            ->update(['applicant_employer_mobile_no' => '']);
            
        DB::table('employment_payment_details')
            ->whereNull('spouse_employer_mobile_no')
            ->update(['spouse_employer_mobile_no' => '']);
            
        // Modify columns to ensure they always default to empty string
        Schema::table('employment_payment_details', function (Blueprint $table) {
            $table->string('applicant_employer_mobile_no')->default('')->change();
            $table->string('spouse_employer_mobile_no')->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If needed, we could revert these changes by allowing nulls
        Schema::table('employment_payment_details', function (Blueprint $table) {
            $table->string('applicant_employer_mobile_no')->nullable()->change();
            $table->string('spouse_employer_mobile_no')->nullable()->change();
        });
    }
};
