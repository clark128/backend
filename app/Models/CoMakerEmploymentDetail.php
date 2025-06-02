<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoMakerEmploymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_request_id',
        'co_makers',
    ];

    protected $casts = [
        'co_makers' => 'array',
    ];

    public function applicationRequest()
    {
        return $this->belongsTo(ApplicationRequest::class);
    }
} 