<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaPsicologia extends Model
{
    protected $table = 'citas_psicologia';
    public $timestamps = false;

    protected $primaryKey = 'idCita';

    protected $fillable = [
        'Fecha',
        'Asistencia',
        'Tutores_id'
    ];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'Tutores_id');
    }

    public function alumnos()
    {
        return $this->belongsToMany(
            Alumno::class,
            'citas_psicologia_alumnos',
            'Cita_id',
            'Alumno_id'
        );
    }
}
