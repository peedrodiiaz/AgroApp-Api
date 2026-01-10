<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cronograma extends Model
{
    protected $fillable = [
        'fechaInicio',
        'fechaFin',
        'horaInicio',
        'horaFin',
        'color',
        'descripcion',
        'estado',
        'trabajador_id',
        'maquina_id'
    ];

    protected $casts = [
        'fechaInicio' => 'date',
        'fechaFin' => 'date',
        'horaInicio' => 'datetime:H:i',
        'horaFin' => 'datetime:H:i'
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
