<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'personal_first_name',
        'personal_age',
        'status'
    ];

    protected $casts = [
        'other_properties' => 'array',
        'co_makers' => 'array',
    ];
    
    protected $appends = [
        'applicant_employer_mobile_no',
        'spouse_employer_mobile_no',
    ];
    
    // Add accessors for the mobile number fields
    public function getApplicantEmployerMobileNoAttribute()
    {        
        // Always return a string, never null
        if (!$this->employmentPaymentDetail) return '';
        
        $value = $this->employmentPaymentDetail->applicant_employer_mobile_no;
        return $value === null || $value === '' ? '' : (string)$value;
    }
    
    public function getSpouseEmployerMobileNoAttribute()
    {

        
        // Always return a string, never null
        if (!$this->employmentPaymentDetail) return '';
        
        $value = $this->employmentPaymentDetail->spouse_employer_mobile_no;
        return $value === null || $value === '' ? '' : (string)$value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function personalAddressInfo()
    {
        return $this->hasOne(PersonalAddressInfo::class);
    }

    public function personalFamilyProfile()
    {
        return $this->hasOne(PersonalFamilyProfile::class);
    }

    public function parentalCreditInfo()
    {
        return $this->hasOne(ParentalCreditInfo::class);
    }

    public function employmentPaymentDetail()
    {
        return $this->hasOne(EmploymentPaymentDetail::class);
    }

    public function coMakerEmploymentDetail()
    {
        return $this->hasOne(CoMakerEmploymentDetail::class);
    }

    public function creditInquiryAuthorization()
    {
        return $this->hasOne(CreditInquiryAuthorization::class);
    }
}
