<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Anamnesis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'anamnesis';
    
    protected $fillable = [
        'client_id', 'has_allergy', 'eye_disease', 'pregnant_or_lactating', 
        'uses_contact_lenses', 'thyroid_problem', 'sleeps_on_stomach', 
        'observations', 'preferred_style', 'mapping_details', 'physical_file', 'signature'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relação SaaS: Este registro pertence a um Estúdio
    public function studio(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Studio::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}