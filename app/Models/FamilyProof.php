<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyProof extends Model
{
    use HasFactory;
    protected $fillable = ['jobId','member','nati','occup'];
}
