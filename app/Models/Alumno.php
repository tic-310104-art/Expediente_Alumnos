<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumnos';
    public $timestamps = false;

    protected $primaryKey = 'idAlumnos';

    protected $fillable = [
        'Nombre',
        'Apellido',
        'Cuatrimestre',
        'Matricula',
        'Correo_inst',
        'Telefono',
        'Estatus',
        'Rol',
        'Grupos_id',
        'Tutores_id',
        'Servicios_Escolares_id',
        'Password',
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

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'Grupos_id');
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'Tutores_id')->withDefault(function ($tutor, $alumno) {
            return $alumno->grupo ? $alumno->grupo->tutor : null;
        });
    }

    public function getCarreraAttribute()
    {
        return $this->carreras->first() ?? ($this->grupo ? $this->grupo->carrera : null);
    }

    public function getCargaAcademicaAttribute()
    {
        return $this->grupo ? $this->grupo->materias : collect();
    }

    public function servicioEscolar()
    {
        return $this->belongsTo(ServicioEscolar::class, 'Servicios_Escolares_id');
    }

    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'carreras_alumnos', 'Alumnos_id', 'Carreras_id');
    }

    public function asesorias()
    {
        return $this->belongsToMany(Asesoria::class, 'alumnos_asesoria', 'Alumno_id', 'Asesoria_id');
    }

    public function historialAcademico()
    {
        return $this->hasMany(HistorialAcademico::class, 'Alumno_id');
    }

    public function reportesDesempeno()
    {
        return $this->hasMany(ReporteDesempeno::class, 'Alumno_id', 'idAlumnos');
    }

    public function citasTutoria()
    {
        return $this->hasMany(CitaTutoria::class, 'Alumnos_id', 'idAlumnos');
    }

    public function citasPsicologia()
    {
        return $this->belongsToMany(CitaPsicologia::class, 'citas_psicologia_alumnos', 'Alumno_id', 'Cita_id');
    }

    public function becas()
    {
        return $this->belongsToMany(Beca::class, 'alumno_beca', 'Alumno_id', 'Beca_id')
                    ->withPivot('Fecha_Asignacion')
                    ->withTimestamps();
    }

    public function getPromedioAttribute()
    {
        $calificaciones = $this->historialAcademico()->pluck('Calificacion');
        $suma = 0;
        $count = 0;
        foreach ($calificaciones as $cal) {
            if (is_numeric($cal)) {
                $suma += (float) $cal;
                $count++;
            }
        }
        return $count > 0 ? round($suma / $count, 2) : 0;
    }

    /**
     * Obtiene el estatus de riesgo basado en una calificación o promedio
     */
    public static function getRiesgoStatus($promedio)
    {
        $val = (float) $promedio;
        if ($val <= 0) return 'N/A';
        if ($val < 8) return 'Riesgo Extremo';
        if ($val < 8.5) return 'Riesgo Medio';
        if ($val < 9.5) return 'Bien';
        return 'Excelente';
    }

    /**
     * Obtiene el color asociado al estatus de riesgo
     */
    public static function getRiesgoColor($promedio)
    {
        $val = (float) $promedio;
        if ($val <= 0) return '#4b5563'; 
        if ($val < 8) return '#dc2626'; 
        if ($val < 8.5) return '#f59e0b'; 
        if ($val < 9.5) return '#15803d'; 
        return '#059669'; 
    }
}
