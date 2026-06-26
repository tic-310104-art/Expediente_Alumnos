<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoMateria extends Model
{
    protected $table = 'grupo_materias';

    protected $fillable = [
        'idGrupos',
        'idMateria',
        'Maestro',
        'Horario'
    ];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupos', 'idGrupos');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'idMateria', 'idMateria');
    }

    public function historialAcademico()
    {
        return $this->hasMany(HistorialAcademico::class, 'idMateria', 'idMateria');
    }

    /**
     * Obtiene la calificación de un alumno para esta materia específica
     */
    public function historialCalificacion($alumnoId)
    {
        $historial = \App\Models\HistorialAcademico::where('Alumno_id', $alumnoId)
            ->where('idMateria', $this->idMateria)
            ->first();

        return $historial ? $historial->Calificacion : null;
    }
}
