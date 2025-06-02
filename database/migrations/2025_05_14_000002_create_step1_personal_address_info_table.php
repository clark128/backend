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
        Schema::create('personal_address_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_request_id')->constrained()->onDelete('cascade');
            $table->string('personal_first_name');
            $table->string('personal_middle_name')->nullable();
            $table->string('personal_last_name');
            $table->integer('personal_age');
            $table->string('personal_nb_rb')->nullable();
            $table->enum('personal_sex', ['Male', 'Female'])->nullable();
            $table->string('personal_citizenship')->nullable();
            $table->date('personal_birth_date')->nullable();
            $table->string('personal_religion')->nullable();
            $table->enum('personal_civil_status', ['Single', 'Married', 'Separated', 'Widowed'])->nullable();
            $table->string('personal_tin')->nullable();
            $table->string('personal_res_cert_no')->nullable();
            $table->date('personal_date_issued')->nullable();
            $table->string('personal_place_issued')->nullable();
            
            // Present Address
            $table->string('present_block_street')->nullable();
            $table->string('present_zone_purok')->nullable();
            $table->string('present_barangay')->nullable();
            $table->string('present_municipality_city')->nullable();
            $table->string('present_province')->nullable();
            $table->string('present_length_of_stay')->nullable();
            $table->enum('present_house_ownership', ['Owned', 'Rented', 'Mortgaged'])->nullable();
            $table->enum('present_lot_ownership', ['Owned', 'Rented', 'Mortgaged'])->nullable();
            $table->json('present_other_properties')->nullable();
            
            // Provincial Address
            $table->string('provincial_block_street')->nullable();
            $table->string('provincial_zone_purok')->nullable();
            $table->string('provincial_barangay')->nullable();
            $table->string('provincial_municipality_city')->nullable();
            $table->string('provincial_province')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_address_infos');
    }
}; 