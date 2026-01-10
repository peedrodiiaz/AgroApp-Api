<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asignacion extends Model
{
    protected $table = 'asignacions';
    
    protected $fillable = [
        'fechaInicio',
        'fechaFin',
        'descripcion',
        'tipoAsignacion',
        'trabajador_id',
        'maquina_id'
    ];

    protected $casts = [
        'fechaInicio' => 'date',
        'fechaFin' => 'date'
    ];

    // Relaciones
    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquina::class);
    }
}
