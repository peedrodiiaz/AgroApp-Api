<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'prioridad',
        'fechaApertura',
        'fechaCierre',
        'maquina_id',
        'trabajador_id'
    ];

    protected $casts = [
        'fechaApertura' => 'datetime',
        'fechaCierre' => 'datetime'
    ];

    // Relaciones
    public function maquina(): BelongsTo
    {
        return $this->belongsTo(Maquina::class);
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }
}
