<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\CollectionHub;
use App\Models\CollectionHubAdmin;
use App\Models\User;
use App\Models\UsersRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionHubAdminController extends Controller
{
    public function index(Request $request)
    {
        $hub_admins = DB::table('users')->leftJoin('hub_admin', 'users.id', '=', 'hub_admin.user_id')->leftJoin('collection_hub', 'hub_admin.collection_hub_id', '=', 'collection_hub.id')->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')->where('user_role.role_id', '=', 4)->get();
        return view('collection_hub_admin.index', compact('hub_admins'));
    }

    public function create()
    {
        $hubs = CollectionHub::get();
        return view('collection_hub_admin.create', compact('hubs'));
    }

    public function edit($id)
    {
        $hubs = CollectionHub::get();
        $hub_admins = DB::table('users')->where('users.id', '=', $id)->leftJoin('hub_admin', 'users.id', '=', 'hub_admin.user_id')->leftJoin('collection_hub', 'hub_admin.collection_hub_id', '=', 'collection_hub.id')->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')->where('user_role.role_id', '=', 4)->first();
        return view('collection_hub_admin.edit', compact('hubs', 'hub_admins', 'id'));
    }

    public function view($id)
    {
        $hubs = CollectionHub::get();
        $hub_admins = DB::table('users')->where('users.id', '=', $id)->leftJoin('hub_admin', 'users.id', '=', 'hub_admin.user_id')->leftJoin('collection_hub', 'hub_admin.collection_hub_id', '=', 'collection_hub.id')->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')->where('user_role.role_id', '=', 4)->first();
        return view('collection_hub_admin.view', compact('hubs', 'hub_admins', 'id'));
    }

    public function insert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required|min:8|confirmed',
            'email' => 'required|email|unique:App\Models\User,email',
            'hub' => 'required'

        ]);

        $user_id = User::create([
            'email' => $request->email,
            'password' => password_hash($request->password, PASSWORD_DEFAULT),
        ])->id;
        UsersRoles::create([
            'user_id' => $user_id,
            'role_id' => 4,
        ]);
        CollectionHubAdmin::create([
            'user_id' => $user_id,
            'collection_hub_id' => $request->hub,
            'name' => $request->name
        ]);

        Helper::sendEmail($request->email, "NuCycle account created for you", "A NuCycle account under your email has been created. Please contact NuCycle for more info.");

        return redirect()->route('collection_hub_admin.index')->with('successMsg', 'Collection Hub admin is created.');
    }

    public function edit_db(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'nullable|min:8|confirmed',
            'hub' => 'required'

        ]);

        if ($request->password != null) {
            $user = User::find($request->user_id);
            $user->password = password_hash($request->password, PASSWORD_DEFAULT);
            $user->save();
        }

        $hub_admin = CollectionHubAdmin::where('user_id', '=', $request->user_id)->first();
        $hub_admin->name = $request->name;
        $hub_admin->collection_hub_id = $request->hub;
        $hub_admin->save();

        return redirect()->route('collection_hub_admin.index')->with('successMsg', 'Collection Hub admin is edited.');
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();

        $user = CollectionHubAdmin::where('user_id', '=', $id)->first();
        $user->delete();

        return redirect()->route('collection_hub_admin.index')->with('successMsg', 'Collection Hub admin is deleted.');
    }
}
