<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Http\Request;
use Validator;
use DB;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use EntrustUserTrait;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','email','password','contact','gender','dob','email_verified_at','is_active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
        return [
            'name' => $this->name,
            'roles'=>$this->roles
        ];
    }

    public static function validator(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required|max:255',
            'dob'=>'date|date_format:Y-m-d',
            'gender'=>'max:1'
        ]);
    }

    public static function updateValidator(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required',
            'dob'=>'date|date_format:Y-m-d',
            'gender'=>'max:1'
        ]);
    }

    
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_user');
    }
}
