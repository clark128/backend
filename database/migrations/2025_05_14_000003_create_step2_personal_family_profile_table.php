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
        Schema::create('personal_family_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_request_id')->constrained()->onDelete('cascade');
            $table->string('contact_home_phone');
            $table->string('contact_office_phone');
            $table->string('contact_mobile_phone');
            $table->string('contact_email');
            $table->string('contact_spouse_name')->nullable();
            $table->string('contact_age')->nullable();
            $table->string('contact_dependents')->nullable();
            $table->string('contact_provincial_spouse')->nullable();
            $table->string('contact_mobile_no');
            $table->string('information_email');
            $table->json('dependents_info')->nullable();
            
            // Applicant's Parents
            $table->string('applicant_father_name')->nullable();
            $table->string('applicant_mother_name')->nullable();
            $table->string('applicant_occupation')->nullable();
            $table->string('applicant_mobile_no')->nullable();
            $table->string('applicant_address')->nullable();
            
            // Spouse's Parents
            $table->string('spouse_father_name')->nullable();
            $table->string('spouse_mother_name')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->string('spouse_mobile_no')->nullable();
            $table->string('spouse_address')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_family_profiles');
    }
}; 