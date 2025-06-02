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
        Schema::create('parental_credit_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_request_id')->constrained()->onDelete('cascade');
            
            // Credit References - individual fields
            $table->string('credit_store_bank')->nullable();
            $table->string('credit_item_loan_amount')->nullable();
            $table->string('credit_term')->nullable();
            $table->string('credit_date')->nullable();
            $table->string('credit_balance')->nullable();
            
            // Personal References - individual fields
            $table->string('references_full_name')->nullable();
            $table->string('references_relationship')->nullable();
            $table->string('references_tel_no')->nullable();
            $table->string('references_address')->nullable();
            
            // Source of Income - still as JSON
            $table->json('source_of_income')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parental_credit_infos');
    }
}; 