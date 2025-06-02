<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAddressInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_request_id',
        'personal_first_name',
        'personal_middle_name',
        'personal_last_name',
        'personal_age',
        'personal_nb_rb',
        'personal_sex',
        'personal_citizenship',
        'personal_birth_date',
        'personal_religion',
        'personal_civil_status',
        'personal_tin',
        'personal_res_cert_no',
        'personal_date_issued',
        'personal_place_issued',
        // Present Address
        'present_block_street',
        'present_zone_purok',
        'present_barangay',
        'present_municipality_city',
        'present_province',
        'present_length_of_stay',
        'present_house_ownership',
        'present_lot_ownership',
        'present_other_properties',
        // Provincial Address
        'provincial_block_street',
        'provincial_zone_purok',
        'provincial_barangay',
        'provincial_municipality_city',
        'provincial_province',
    ];

    protected $casts = [
        'personal_birth_date' => 'date',
        'personal_date_issued' => 'date',
        'present_other_properties' => 'array',
    ];

    public function applicationRequest()
    {
        return $this->belongsTo(ApplicationRequest::class);
    }
} 