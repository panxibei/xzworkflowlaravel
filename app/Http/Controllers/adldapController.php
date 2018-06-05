<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Adldap\Laravel\Facades\Adldap;

class adldapController extends Controller
{
    public function adLdap () {
		
	// Finding a user:
	// $user = Adldap::search()->users()->find('ca071215958');		
	// dd($user);
	// dd($user['mail'][0]);
	// dd($user['displayname'][0]);
	
	// Searching for a user:
	// $search = Adldap::search()->where('cn', '=', 'user01')->get();
	// dd($search);
	
	// Running an operation under a different connection:
	// $users = Adldap::getProvider('other-connection')->search()->users()->get();
	// $users = Adldap::search()->users()->get()->toArray();
// dd($users);
// dd($users[0]['samaccountname']);
	
	// Creating a user:
	// $user = Adldap::make()->user([
		// 'cn' => 'John Doe',
		// 'cn' => 'user02',
	// ]);
	
	// Modifying Attributes:
	// $user->cn = 'Jane Doe';

	// Saving a user:
	// $user->save();

	// dd(env('JWT_TTL', 60));

try {
	$mm = Adldap::auth()->attempt('ca071215958', 'Aota12345678');
	dd($mm);
}
catch (Exception $e) {
	echo 'Message: ' .$e->getMessage();
}
	
	
	}
}
