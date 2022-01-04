<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\CollectionHub;
use App\Models\CollectionHubAdmin;
use App\Models\CollectionHubCollector;
use App\Models\User;
use App\Models\UsersRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionHubCollectorController extends Controller
{
    public function index(Request $request)
    {
        if (in_array(4, $request->user()->users_roles_id())) {
            $hub_id = DB::table('users')
                ->leftJoin('hub_admin', 'hub_admin.user_id', '=', 'users.id')
                ->where('users.id', '=', $request->user()->id)->first();
            $collectors = DB::table('users')
                ->leftJoin('collector', 'collector.user_id', '=', 'users.id')
                ->leftJoin('collection_hub', 'collector.collection_hub_id', '=', 'collection_hub.id')
                ->leftJoin('user_role', 'user_role.user_id', '=', 'users.id') 
                ->where('user_role.role_id', '=', 3)
                ->where('collector.collection_hub_id', '=', $hub_id->collection_hub_id)
                ->get();
        } elseif (in_array(1, $request->user()->users_roles_id())) {
            $collectors = DB::table('users')
                ->leftJoin('collector', 'collector.user_id', '=', 'users.id')
                ->leftJoin('collection_hub', 'collector.collection_hub_id', '=', 'collection_hub.id')
                ->leftJoin('user_role', 'user_role.user_id', '=', 'users.id') 
                ->where('user_role.role_id', '=', 3)->get();
        }
        return view('collection_hub_collector.index', compact('collectors'));
    }

    public function create()
    {
        return view('collection_hub_collector.create');
    }

    public function edit($id)
    {
        $hubs = CollectionHub::get();
        $collector = DB::table('users')
            ->leftJoin('collector', 'collector.user_id', '=', 'users.id')
            ->leftJoin('collection_hub', 'collector.collection_hub_id', '=', 'collection_hub.id')
            ->where('users.id', '=', $id)
            ->first();
        return view('collection_hub_collector.edit', compact('hubs', 'collector', 'id'));
    }

    public function view($id)
    {
        $hubs = CollectionHub::get();
        $collector = DB::table('users')
            ->leftJoin('collector', 'collector.user_id', '=', 'users.id')
            ->leftJoin('collection_hub', 'collector.collection_hub_id', '=', 'collection_hub.id')
            ->where('users.id', '=', $id)
            ->first();
        return view('collection_hub_collector.view', compact('hubs', 'collector', 'id'));
    }

    public function insert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required|min:8|confirmed',
            'email' => 'required|email|unique:App\Models\User,email',
            // 'hub' => 'required'
        ]);

        $user_id = User::create([
            'email' => $request->email,
            'password' => password_hash($request->password, PASSWORD_DEFAULT),
        ])->id;
        UsersRoles::create([
            'user_id' => $user_id,
            'role_id' => 3
        ]);
        CollectionHubCollector::create([
            'user_id' => $user_id,
            'collection_hub_id' => Helper::getCollectionHubId(),
            'name' => $request->name,
            'email' => $request->email,
        ]);

        Helper::sendEmail($request->email, "User account created for you", "A NuCycle account under your email has been created. Please contact NuCycle for more info.");

        return redirect()->route('collection_hub_collector.index')->with('successMsg', 'Collection Hub collector is created.');
    }

    public function edit_db(Request $request)
    {
        if ($request->password != null) {
            $user = User::find($request->user_id);
            $user->password = password_hash($request->password, PASSWORD_DEFAULT);
            $user->save();
        }

        $collector = CollectionHubCollector::where('user_id', '=', $request->user_id)->first();
        $collector->name = $request->name;
        $collector->collection_hub_id = $request->hub;
        $collector->save();

        return redirect()->route('collection_hub_collector.index')->with('successMsg', 'Collection Hub collector is edited.');
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();

        $user = CollectionHubCollector::where('user_id', '=', $id)->first();
        $user->delete();

        return redirect()->route('collection_hub_collector.index')->with('successMsg', 'Collection Hub collector is deleted.');
    }
}
