<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioEscolar extends Model
{
    protected $table = 'servicios_escolares';

    public $timestamps = false;
    protected $primaryKey = 'idServicios_Escolares';

    protected $fillable = [
        'idServicios_Escolares',
        'Clave_Trabajador',
        'Correo',
        'Telefono',
        'Rol',
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

    public function tutores()
    {
        return $this->hasMany(Tutor::class, 'Servicios_Escolares_id');
    }

    public function alumnos()
    {
        return $this->hasMany(Alumno::class, 'Servicios_Escolares_id');
    }

    /**
     * Scope para buscar por correo electrónico (compatible con ambas columnas)
     */
    public function scopeByEmail($query, $email)
    {
        $table = $query->getModel()->getTable();

        $hasCorreo = \Illuminate\Support\Facades\Schema::hasColumn($table, 'Correo');
        $hasCorreoInst = \Illuminate\Support\Facades\Schema::hasColumn($table, 'Correo_inst');
        $hasEmail = \Illuminate\Support\Facades\Schema::hasColumn($table, 'Email');

        $query->where(function ($q) use ($email, $hasCorreo, $hasCorreoInst, $hasEmail) {
            if ($hasCorreoInst) {
                $q->orWhere('Correo_inst', $email);
            }
            if ($hasCorreo) {
                $q->orWhere('Correo', $email);
            }
            if ($hasEmail) {
                $q->orWhere('Email', $email);
            }
            if (!$hasCorreoInst && !$hasCorreo && !$hasEmail) {
                $q->whereRaw('0=1');
            }
        });

        return $query;
    }
}