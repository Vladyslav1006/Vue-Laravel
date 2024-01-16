<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherEty extends Model
{
    use HasFactory;
    protected $table='other_etys';
    protected $fillable = ['jobId','ety','label', 'is_primary'];
}
