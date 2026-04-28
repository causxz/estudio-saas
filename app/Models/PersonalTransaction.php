<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalTransaction extends Model
{
    protected $fillable = ['user_id', 'description', 'amount', 'type', 'category', 'date'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];
}
