<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialAcademico extends Model
{
    protected $table = 'historial_academico';

    protected $primaryKey = 'idHistorial';
    public $timestamps = false;

    protected $fillable = [
        'Materia',
        'Profesor',
        'Calificacion',
        'Horario',
        'Ciclo',
        'Alumno_id',
        'idMateria'
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'idMateria', 'idMateria');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'Alumno_id', 'idAlumnos');
    }

    public function grupoMateria()
    {
        return $this->belongsTo(GrupoMateria::class, 'idMateria', 'idMateria');
    }
}