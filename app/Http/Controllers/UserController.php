<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $users = User::orderBy('created_at')->paginate(15);

        $data = [
            'users' => $users,
        ];
        return view('users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function levelup(User $user)
    {
        $user->level = 0;
        $user->update();
        return redirect()->back();
    }
    public function leveldown(User $user)
    {
        $user->level = 1;
        $user->update();
        return redirect()->back();
    }
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|string|same:password',
            'level' => 'required',
        ]);
        unset($request['password_confirmation']);
        $user = User::create($request->all());
        $user->password = Hash::make($user->password);
        $user->update();

        return redirect()->route('users.index');
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
    public function edit(User $user)
    {
        //
        $data = [
            'user'=>$user,
        ];

        return view('users.edit',$data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,User $user)
    {
        //
        $this->validate($request, [
            'name' => 'required',
            'password' => 'nullable|min:6|confirmed',
            'password_confirmation' => 'nullable|string|same:password',
        ]);

        $user->name = $request->input('name');
        if($request->has('password')){
            $newpwd = Hash::make($request->input('password'));
            $user->password = $newpwd;
        }

        if($request->has('level')){
            $user->level = $request->input('level');
        }
        if($request->has('is_left')){
            $user->is_left = $request->input('is_left');
        }
        if($request->has('phone_number')){
            $user->phone_number = $request->input('phone_number');
        }
        if($request->has('extension_number')){
            $user->extension_number = $request->input('extension_number');
        }

        $user->updated_at = now();
        $user->update();

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
