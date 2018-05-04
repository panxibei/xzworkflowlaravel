<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

// class User extends Authenticatable
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'login_time', 'login_ip', 'login_counts',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
	
	// public function getAuthIdentifierName()
	// {
		// return 'id';
	// }
	
	// public function getAuthIdentifier()
	// {
		
	// }
	
	// public function getAuthPassword()
	// {
		
	// }
	
	// public function getRememberToken()
	// {
		
	// }

	// public function setRememberToken($value)
	// {
		
	// }
	// public function getRememberTokenName()
	// {
		
	// }
}
