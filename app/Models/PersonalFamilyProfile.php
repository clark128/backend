<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalFamilyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_request_id',
        'contact_home_phone',
        'contact_office_phone',
        'contact_mobile_phone',
        'contact_email',
        'contact_spouse_name',
        'contact_age',
        'contact_dependents',
        'contact_provincial_spouse',
        'contact_mobile_no',
        'information_email',
        'dependents_info',
        // Applicant's Parents
        'applicant_father_name',
        'applicant_mother_name',
        'applicant_occupation',
        'applicant_mobile_no',
        'applicant_address',
        // Spouse's Parents
        'spouse_father_name',
        'spouse_mother_name',
        'spouse_occupation',
        'spouse_mobile_no',
        'spouse_address',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'dependents_info' => 'array',
    ];

    public function applicationRequest()
    {
        return $this->belongsTo(ApplicationRequest::class);
    }
} 