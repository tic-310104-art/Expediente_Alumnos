<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';
    public $timestamps = false;

    protected $primaryKey = 'idGrupos';

    protected $fillable = [
        'Grupo',
        'Cantidad_Alumnos',
        'idCarreras',
        'idTutores'
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'idCarreras', 'idCarreras');
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'idTutores', 'idTutores');
    }

    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'grupo_materias', 'idGrupos', 'idMateria')
                    ->withPivot('Maestro', 'Horario')
                    ->withTimestamps();
    }

    public function grupoMaterias()
    {
        return $this->hasMany(GrupoMateria::class, 'idGrupos', 'idGrupos');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'Grupos_id');
    }
}