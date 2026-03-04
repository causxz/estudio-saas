<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'whatsapp', 'birth_date'];

    public function anamneses()
    {
        return $this->hasMany(Anamnesis::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

}

