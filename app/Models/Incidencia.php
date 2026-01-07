<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    public function maquina()
    {
        return $this->belongsTo(Maquina::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }
}
