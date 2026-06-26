<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitaTutoria extends Model
{
    protected $table = 'citas_tutorias';
    public $timestamps = false;

    protected $primaryKey = 'idCitas';

    protected $fillable = [
        'Fecha',
        'Motivo',
        'Tutores_id',
        'Alumnos_id'
    ];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'Tutores_id');
    }

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'Alumnos_id', 'idAlumnos');
    }
}