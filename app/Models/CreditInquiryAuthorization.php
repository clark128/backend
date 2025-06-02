<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditInquiryAuthorization extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_request_id',
        'signature_path',
        'sketch_residence_path',
        'sketch_residence_comaker_path',
        'applicant_signature_path',
        'spouse_signature_path',
        'comaker_signature_path',
    ];

    public function applicationRequest()
    {
        return $this->belongsTo(ApplicationRequest::class);
    }
} 