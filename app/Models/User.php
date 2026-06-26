<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con el perfil de Alumno
     */
    public function alumno()
    {
        return $this->hasOne(Alumno::class, 'user_id');
    }

    /**
     * Relación con el perfil de Tutor
     */
    public function tutor()
    {
        return $this->hasOne(Tutor::class, 'user_id');
    }

    /**
     * Relación con el perfil de Servicio Escolar (Admin)
     */
    public function servicioEscolar()
    {
        return $this->hasOne(ServicioEscolar::class, 'user_id');
    }
}
