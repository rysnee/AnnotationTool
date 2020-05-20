<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use App\Video;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }
        return view('users.index',['users' => User::all()]); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }
        return view('users.create',['roles' => Role::all(), 'videos' => Video::all()]); 
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
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }

        $validated = $request->validate([
            'username' => 'required|max:25|unique:users',
            'password' => 'required|max:255',
            'email' => 'required|max:240|unique:users'
        ]);

        $user = new User;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        if ($request->admin == "on")
            $user->role_id = 1;
        else $user->role_id = 2;
        $user->save();

        if ($user->role->name != 'admin')
            if ($request->video_id != NULL)
            {
                foreach ($request->video_id as $id)
                {
                    $video = Video::find($id);
                    $video->user_id = $user->id;
                    $video->save();
                }
            }

        return redirect('users');   
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }

        return view('users.edit',['user' => $user, 'videos' => Video::all()]); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }

        $validated = $request->validate([
            'password' => 'max:255',
        ]);

        if ($request->password != null)
            $user->password = Hash::make($request->password);

        if ($request->admin == "on")
            $user->role_id = 1;
        else $user->role_id = 2;
        $user->save();

        $videos_user = $user->video;
        foreach ($videos_user as $video)
        {
            $video->user_id = null;
            $video->save();
        }

        if ($request->video_id != NULL)
        {
            foreach ($request->video_id as $id)
            {
                $video = Video::find($id);
                $video->user_id = $user->id;
                $video->save();
            }
        }
        return redirect('users');   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function delete($user_id)
    {
        if(Auth::user()->role->name != 'admin'){
            abort(403, 'You do not have permission');
        }

        $user = User::find($user_id);
        if ($user->role->name == "admin"){
            return redirect('users');       
        }

        $videos_user = $user->video;
        foreach ($videos_user as $video)
        {
            $video->user_id = null;
            $video->save();
        }

        $user->delete();
        return redirect('users'); 

    }
}
