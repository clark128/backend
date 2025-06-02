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
        Schema::create('credit_inquiry_authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_request_id')->constrained()->onDelete('cascade');
            $table->string('signature_path')->nullable();
            $table->string('sketch_residence_path')->nullable();
            $table->string('sketch_residence_comaker_path')->nullable();
            $table->string('applicant_signature_path')->nullable();
            $table->string('spouse_signature_path')->nullable();
            $table->string('comaker_signature_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_inquiry_authorizations');
    }
}; 