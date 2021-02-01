<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';
    protected $dates = [ 'deleted_at' ];

    const VERIFIED_USER = '1';
    const UNVERIFIED_USER = '0';

    const ADMINISTATOR = 'true';
    const REGULAR_USER = 'false';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Guarda los datos en miniscula
    public function setNameAttribute( $value ) {
        $this->attributes['name'] = strtolower( $value );
    }

    // muestra los datos en capitalizados
    public function getNameAttribute( $value ) {
        return ucwords( $value );
    }

    public function setEmailAttribute( $value ) {
        $this->attributes['email'] = strtolower( $value );
    }

    public function isVerified() {
        return $this->verified === User::VERIFIED_USER;
    }

    public function isAdministator() {
        return $this->admin === User::ADMINISTATOR;
    }

    public static function generateVerificationToken() {
        return Str::random(40);
    }
}