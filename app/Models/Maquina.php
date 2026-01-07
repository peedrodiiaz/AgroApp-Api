<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
