<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentalCreditInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_request_id',
        // Credit References - individual fields
        'credit_store_bank',
        'credit_item_loan_amount',
        'credit_term',
        'credit_date',
        'credit_balance',
        // Personal References - individual fields
        'references_full_name',
        'references_relationship',
        'references_tel_no',
        'references_address',
        // Source of Income - still as JSON
        'source_of_income',
    ];

    protected $casts = [
        'source_of_income' => 'array',
    ];

    public function applicationRequest()
    {
        return $this->belongsTo(ApplicationRequest::class);
    }
} 