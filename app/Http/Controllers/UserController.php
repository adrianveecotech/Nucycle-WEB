<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\User;
use App\Models\State;
use App\Models\UserRole;
use App\Models\UsersRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = UsersRoles::get();
        return view('user.index', compact('users'));
    }

    public function edit($id)
    {
        $user = UsersRoles::find($id);
        $roles = UserRole::get();
        return view('user.edit', compact('user', 'id', 'roles'));
    }

    public function edit_db(Request $request)
    {
        $this->validate($request, [
            'role' => 'required',
        ]);
        $user = UsersRoles::find($request->id);
        $user->role_id = $request->role;
        $user->save();

        return redirect()->route('user.index')->with('successMsg', 'User role is edited.');
    }

    public function create()
    {
        $roles = UserRole::get();
        $users = User::get();
        return view('user.create', compact('roles', 'users'));
    }

    public function insert(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'role' => 'required'
        ]);
        if (UsersRoles::where('user_id', $request->user_id)->where('role_id', $request->role)->exists()) {
            return redirect()->route('user.index')->with('warningMsg', 'User role is already exist.');
        }
        UsersRoles::create([
            'user_id' => $request->user_id,
            'role_id' => $request->role

        ]);
        $message = "User role is created.";
        if ($request->role == 2)
            $message = "User role is created. Please proceed to user page to create user profile.";
        else if ($request->role == 3)
            $message = "User role is created. Please proceed to collector page to create collector profile.";
        return redirect()->route('user.index')->with('successMsg', $message);
    }

    public function delete($id)
    {
        $user_role = UsersRoles::find($id);
        $user_role->delete();

        return redirect()->route('user.index')->with('successMsg', 'User role is deleted.');
    }
}
