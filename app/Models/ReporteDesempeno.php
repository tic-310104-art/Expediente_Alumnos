<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteDesempeno extends Model
{
    protected $table = 'reportes_desempeno';
    protected $primaryKey = 'idReporte';

    protected $fillable = [
        'Alumno_id',
        'Tutor_id',
        'Fecha',
        'Nivel_Riesgo',
        'Observaciones',
        'Recomendaciones'
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'Alumno_id', 'idAlumnos');
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'Tutor_id', 'idTutores');
    }
}
