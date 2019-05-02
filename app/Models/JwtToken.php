<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JwtToken extends Model
{
    protected $table = 'jwt_tokens';

    protected $fillable = [
        'user_id','token','browser','platform','device','desktop','phone',
    ];
}
