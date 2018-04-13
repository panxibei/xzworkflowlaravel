<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Config;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		// $config = Config::select('cfg_id', 'cfg_name', 'cfg_value', 'created_at')
            // ->orderBy('created_at', 'desc');

		$site_title = Config::where('cfg_name', 'SITE_TITLE')->first();
		$site_version = Config::where('cfg_name', 'SITE_VERSION')->first();
		$site_copyright = Config::where('cfg_name', 'SITE_COPYRIGHT')->first();
		
        // $assign = [
            // 'config' => $config,
            // 'head' => $head,
            // 'tagName' => ''
        // ];

        return view('home.login')
			->with('site_title',$site_title['cfg_value'])
			->with('site_version',$site_version['cfg_value'])
			->with('site_copyright',$site_copyright['cfg_value']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
