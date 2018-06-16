<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Circulation extends Model
{
	protected $fillable = [
        'guid', 'name', 'template_id', 'mailinglist_id', 'slot2user_id', 'slot_id', 'user_id', 'current_station', 'creator', 'todo_time', 'progress', 'description', 'is_archived',
    ];
}
