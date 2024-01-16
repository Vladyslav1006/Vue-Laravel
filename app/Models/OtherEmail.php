<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherEmail extends Model
{
    use HasFactory;
    protected $fillable = ['jobId','email','label', 'is_primary'];
}
