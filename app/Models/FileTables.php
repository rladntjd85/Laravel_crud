<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileTables extends Model
{
    use HasFactory;

    protected $fillable = [
        'pid','code','userid','filename'
    ];
}
