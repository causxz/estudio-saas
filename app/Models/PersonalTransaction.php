<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'description', 'amount', 'type', 'category', 'date'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];
}
