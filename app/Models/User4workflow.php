<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User4workflow extends Model
{
	protected $fillable = [
        'user_id', 'rights', 'substitute_user_id', 'substitute_time',
    ];
}
