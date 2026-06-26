<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraAlumno extends Model
{
    protected $table = 'carreras_alumnos';

    protected $fillable = [
        'Carreras_id',
        'Alumnos_id'
    ];
}