<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot2user extends Model
{
	protected $fillable = [
        'slot_id', 'user_id',
    ];
}
