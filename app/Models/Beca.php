<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beca extends Model
{
    protected $table = 'becas';
    protected $primaryKey = 'idBecas';

    protected $fillable = [
        'Nombre',
        'Descripcion',
        'Monto'
    ];

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumno_beca', 'Beca_id', 'Alumno_id')
                    ->withPivot('Fecha_Asignacion')
                    ->withTimestamps();
    }
}
