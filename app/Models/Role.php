<?php 

namespace App\Models;

use Zizaco\Entrust\EntrustRole;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;

use Validator;

class Role extends EntrustRole
{
	protected $fillable = [
    	'name', 'display_name','description'
	];


	public static function validator(Request $request)
    {
    	return Validator::make($request->toArray(), [
            'name' => 'required|unique:roles|max:255',
            'display_name' => 'required',
            'description' => 'required',
        ]);
    }

    public static function updateValidator(Request $request)
    {
        return Validator::make($request->toArray(), [
            'name' => 'required|max:255',
            'display_name' => 'required',
            'description' => 'required',
        ]);
    }

    public function users()
    {
        return $this->belongsToMany('App\User','role_user');
    }
}   