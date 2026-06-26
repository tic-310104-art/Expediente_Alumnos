<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carreras';
    public $timestamps = false;

    protected $primaryKey = 'idCarreras';

    protected $fillable = [
        'Nombre',
        'Servicios_Escolares_id'
    ];

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'carreras_alumnos', 'Carreras_id', 'Alumnos_id');
    }

    public function servicioEscolar()
    {
        return $this->belongsTo(ServicioEscolar::class, 'Servicios_Escolares_id');
    }

    public function materias()
    {
        return $this->hasMany(Materia::class, 'idCarreras', 'idCarreras');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'idCarreras', 'idCarreras');
    }

    public function tutores()
    {
        return $this->hasMany(Tutor::class, 'idCarreras', 'idCarreras');
    }
}