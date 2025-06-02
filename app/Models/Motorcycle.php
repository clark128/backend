<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motorcycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'features',
        'description',
        'image_path',
        'specification_image_path',
    ];
}
