<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asesoria extends Model
{
    protected $table = 'asesoria';

    protected $primaryKey = 'idAsesoria';
    public $timestamps = false;

    protected $fillable = [
        'Motivo',
        'Fecha'
    ];

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumnos_asesoria', 'Asesoria_id', 'Alumno_id');
    }
}