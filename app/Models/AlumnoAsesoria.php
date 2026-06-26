<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoAsesoria extends Model
{
    protected $table = 'alumnos_asesoria';

    protected $fillable = [
        'Alumno_id',
        'Asesoria_id'
    ];
}
