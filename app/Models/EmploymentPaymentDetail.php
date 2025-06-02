<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentPaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_request_id',
        // Applicant Employer Information
        'applicant_employer',
        'applicant_position',
        'applicant_block_street',
        'applicant_zone_purok',
        'applicant_barangay',
        'applicant_municipality_city',
        'applicant_province',
        'applicant_telno',
        'applicant_date_started',
        'applicant_name_immediate',
        'applicant_employer_mobile_no',
        'applicant_salary_gross',
        
        // Spouse Employer Information
        'spouse_employer',
        'spouse_position',
        'spouse_block_street',
        'spouse_zone_purok',
        'spouse_barangay',
        'spouse_municipality',
        'spouse_province',
        'spouse_telno',
        'spouse_date_started',
        'spouse_name_immediate',
        'spouse_employer_mobile_no',
        'spouse_salary_gross',
        
        // Unit to be Used For
        'personal_use',
        'business_use',
        'gift',
        'use_by_relative',
        
        // Mode of Payment
        'post_dated_checks',
        'cash_paid_to_office',
        'cash_for_collection',
        'credit_card',
    ];

    protected $casts = [
        'applicant_date_started' => 'date',
        'spouse_date_started' => 'date',
        'applicant_salary_gross' => 'decimal:2',
        'spouse_salary_gross' => 'decimal:2',
        'personal_use' => 'boolean',
        'business_use' => 'boolean',
        'gift' => 'boolean',
        'use_by_relative' => 'boolean',
        'post_dated_checks' => 'boolean',
        'cash_paid_to_office' => 'boolean',
        'cash_for_collection' => 'boolean',
        'credit_card' => 'boolean',
        // Explicitly cast mobile number fields as strings
        'applicant_employer_mobile_no' => 'string',
        'spouse_employer_mobile_no' => 'string',
    ];

    public function setApplicantEmployerMobileNoAttribute($value)
    {
        $this->attributes['applicant_employer_mobile_no'] = ($value === null || $value === '') ? '' : (string)$value;
    }

    public function getApplicantEmployerMobileNoAttribute($value)
    {
        return ($value === null || $value === '') ? '' : (string)$value;
    }

    public function setSpouseEmployerMobileNoAttribute($value)
    {
        $this->attributes['spouse_employer_mobile_no'] = ($value === null || $value === '') ? '' : (string)$value;
    }

    public function getSpouseEmployerMobileNoAttribute($value)
    {
        return ($value === null || $value === '') ? '' : (string)$value;
    }

    public function applicationRequest()
    {
        return $this->belongsTo(ApplicationRequest::class);
    }
} 