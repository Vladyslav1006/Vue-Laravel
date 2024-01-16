<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocatedBB extends Model
{
    use HasFactory;
    protected $fillable = ['jobId','baysitterId','rank','remark'];
}
