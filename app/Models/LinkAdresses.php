<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkAdresses extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coin',
        'address',
        'label'
    ];
}
