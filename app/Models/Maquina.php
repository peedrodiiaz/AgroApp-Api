<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Maquina extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'imagen',
        'numSerie',
        'modelo',
        'tipo',
        'fechaCompra',
        'estado',
        'ubicacion',
        'descripcion',
        'potenciaCv',
        'tipoCombustible',
        'capacidadRemolque',
        'tipoCultivo',
        'anchoCorte',
        'capacidadTolva',
        'tipoBala',
        'capacidadEmpaque'
    ];

    protected $casts = [
        'fechaCompra' => 'date'
    ];

    // Relaciones
    public function cronogramas(): HasMany
    {
        return $this->hasMany(Cronograma::class);
    }

    public function incidencias(): HasMany
    {
        return $this->hasMany(Incidencia::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class);
    }
}
