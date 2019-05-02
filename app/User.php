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
            'role'=>$this->roles[0]->name
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

    public function packages()
    {
        return $this->belongsToMany('App\Models\Package', 'package_users')->withPivot('enrollment_date', 'expire_date');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group', 'group_users');
    }

    public function activePackages()
    {
        return $this->belongsToMany('App\Models\Package', 'package_users')->where('expire_date', '>=', date("Y-m-d"))->withPivot('enrollment_date', 'expire_date');
    }

    public function isEnrolledCourse($course_id)
    {
        $query = "select count(*) row_count from courses where id = ".$course_id." and id in (
                select course_id from package_courses where package_id in (select package_id from package_users where user_id = '".$this->id."' and expire_date >= '".date("Y-m-d H:i:s")."')
        ) and id in (select course_id from user_courses where course_id = ".$course_id." and user_id = '".$this->id."' )";
        $result = DB::select($query);
        return $result[0]->row_count;
    }
    public function isAllowedCourse($course_id)
    {
        
        $query = "select count(*) row_count from courses where id = ".$course_id." and id in (
                select course_id from package_courses where package_id in (select package_id from package_users where user_id = '".$this->id."' and expire_date >= '".date("Y-m-d H:i:s")."')
        )";
        $result = DB::select($query);
        return $result[0]->row_count;
    }
}
