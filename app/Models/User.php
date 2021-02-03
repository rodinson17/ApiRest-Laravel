<?php

namespace App\Models;

use App\Transformers\UserTransformer;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    protected $table = 'users';
    protected $dates = [ 'deleted_at' ];

    const VERIFIED_USER = '1';
    const UNVERIFIED_USER = '0';

    const ADMINISTATOR = 'true';
    const REGULAR_USER = 'false';

    public $transformer = UserTransformer::class; // TODO: transformar las respuestas

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
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
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
