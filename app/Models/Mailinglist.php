<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mailinglist extends Model
{
	protected $fillable = [
        'name', 'template_id', 'isdefault', 'slot2user_id',
    ];
}
