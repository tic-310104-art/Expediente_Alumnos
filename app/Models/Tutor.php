<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    protected $table = 'tutores';
    public $timestamps = false;

    protected $primaryKey = 'idTutores';

    protected $fillable = [
        'Clave_Trabajador',
        'Nombre',
        'Apellido',
        'Correo_inst',
        'Password',
        'Telefono',
        'Rol',
        'idCarreras',
        'user_id',
        'foto_url'
    ];

    public function getFotoUrlAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        $path = public_path(ltrim($value, '/'));
        if (!file_exists($path)) return null;
        return asset($value);
    }

    public function servicioEscolar()
    {
        return $this->belongsTo(ServicioEscolar::class, 'Servicios_Escolares_id');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'idCarreras', 'idCarreras');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'Tutores_id', 'idTutores');
    }

    public function citasTutorias()
    {
        return $this->hasMany(CitaTutoria::class, 'Tutores_id', 'idTutores');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'idTutores', 'idTutores');
    }
}
