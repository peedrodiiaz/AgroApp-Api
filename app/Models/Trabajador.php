<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadors';

    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'telefono',
        'email',
        'rol',
        'fechaAlta'
    ];

    protected $casts = [
        'fechaAlta' => 'date'
    ];
}
