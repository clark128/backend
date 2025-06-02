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
        Schema::create('employment_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_request_id')->constrained()->onDelete('cascade');
            
            // Applicant Employer Information
            $table->string('applicant_employer')->nullable();
            $table->string('applicant_position')->nullable();
            $table->string('applicant_block_street')->nullable();
            $table->string('applicant_zone_purok')->nullable();
            $table->string('applicant_barangay')->nullable();
            $table->string('applicant_municipality_city')->nullable();
            $table->string('applicant_province')->nullable();
            $table->string('applicant_telno')->nullable();
            $table->date('applicant_date_started')->nullable();
            $table->string('applicant_name_immediate')->nullable();
            $table->string('applicant_employer_mobile_no')->default('');
            $table->decimal('applicant_salary_gross', 15, 2)->nullable();
            
            // Spouse Employer Information
            $table->string('spouse_employer')->nullable();
            $table->string('spouse_position')->nullable();
            $table->string('spouse_block_street')->nullable();
            $table->string('spouse_zone_purok')->nullable();
            $table->string('spouse_barangay')->nullable();
            $table->string('spouse_municipality')->nullable();
            $table->string('spouse_province')->nullable();
            $table->string('spouse_telno')->nullable();
            $table->date('spouse_date_started')->nullable();
            $table->string('spouse_name_immediate')->nullable();
            $table->string('spouse_employer_mobile_no')->default('');
            $table->decimal('spouse_salary_gross', 15, 2)->nullable();
            
            // Unit to be Used For
            $table->boolean('personal_use')->default(false);
            $table->boolean('business_use')->default(false);
            $table->boolean('gift')->default(false);
            $table->boolean('use_by_relative')->default(false);
            
            // Mode of Payment
            $table->boolean('post_dated_checks')->default(false);
            $table->boolean('cash_paid_to_office')->default(false);
            $table->boolean('cash_for_collection')->default(false);
            $table->boolean('credit_card')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_payment_details');
    }
}; 