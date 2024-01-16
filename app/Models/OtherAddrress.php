<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherAddrress extends Model
{
    use HasFactory;
    protected $fillable = ['jobId','address','aunit','rank','prime','label'];
}
