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
        'Correo_inst', 
        'Email', 
        'Telefono',
        'Rol',
        'Password',
        'user_id',
        'foto_url'
    ];

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

        if ($hasCorreoInst) {
            return $query->where('Correo_inst', $email);
        }

        if ($hasCorreo && $hasEmail) {
            return $query->where('Correo', $email)->orWhere('Email', $email);
        }

        if ($hasCorreo) {
            return $query->where('Correo', $email);
        }

        if ($hasEmail) {
            return $query->where('Email', $email);
        }

        return $query->whereRaw('0=1');
    }
}