<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaPsicologiaAlumno extends Model
{
    protected $table = 'citas_psicologia_alumnos';

    protected $fillable = [
        'Cita_id',
        'Alumno_id'
    ];
}
