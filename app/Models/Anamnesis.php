<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anamnesis extends Model
{
    protected $table = 'anamnesis';
    
    protected $fillable = [
        'client_id', 'has_allergy', 'eye_disease', 'pregnant_or_lactating', 
        'uses_contact_lenses', 'thyroid_problem', 'sleeps_on_stomach', 
        'observations', 'preferred_style', 'mapping_details', 'physical_file'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}