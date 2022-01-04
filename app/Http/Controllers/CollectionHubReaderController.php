<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\CollectionHub;
use App\Models\CollectionHubAdmin;
use App\Models\CollectionHubReader;
use App\Models\User;
use App\Models\UsersRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionHubReaderController extends Controller
{
    public function index(Request $request)
    {
        $hub_id = DB::table('users')
            ->leftJoin('hub_admin', 'hub_admin.user_id', '=', 'users.id')
            ->where('users.id', '=', $request->user()->id)->first();
        $readers = DB::table('users')
            ->leftJoin('hub_reader', 'hub_reader.user_id', '=', 'users.id')
            ->leftJoin('collection_hub', 'hub_reader.collection_hub_id', '=', 'collection_hub.id')
            ->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')
            ->where('user_role.role_id', '=', 5)
            ->where('hub_reader.collection_hub_id', '=', $hub_id->collection_hub_id)
            ->get();
        return view('collection_hub_reader.index', compact('readers'));
    }

    public function create()
    {
        return view('collection_hub_reader.create');
    }

    public function edit($id)
    {
        $hub_reader = DB::table('users')
            ->leftJoin('hub_reader', 'hub_reader.user_id', '=', 'users.id')
            ->leftJoin('collection_hub', 'hub_reader.collection_hub_id', '=', 'collection_hub.id')
            ->where('users.id', '=', $id)
            ->first();
        return view('collection_hub_reader.edit', compact('hub_reader', 'id'));
    }

    public function view($id)
    {
        $hub_reader = DB::table('users')
            ->leftJoin('hub_reader', 'hub_reader.user_id', '=', 'users.id')
            ->leftJoin('collection_hub', 'hub_reader.collection_hub_id', '=', 'collection_hub.id')
            ->where('users.id', '=', $id)
            ->first();
        return view('collection_hub_reader.view', compact('hub_reader', 'id'));
    }

    public function insert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required|min:8|confirmed',
            'email' => 'required|email|unique:App\Models\User,email',
        ]);

        $user_id = User::create([
            'email' => $request->email,
            'password' => password_hash($request->password, PASSWORD_DEFAULT),
        ])->id;
        UsersRoles::create([
            'user_id' => $user_id,
            'role_id' => 5
        ]);

        $hub_id = Helper::getCollectionHubId();

        CollectionHubReader::create([
            'user_id' => $user_id,
            'collection_hub_id' => $hub_id,
            'name' => $request->name,
        ]);
        Helper::sendEmail($request->email, "User account created for you", "A NuCycle account under your email has been created. Please contact NuCycle for more info.");

        return redirect()->route('collection_hub_reader.index')->with('successMsg', 'Collection hub reader is created.');
    }

    public function edit_db(Request $request)
    {
        if ($request->password != null) {
            $user = User::find($request->user_id);
            $user->password = password_hash($request->password, PASSWORD_DEFAULT);
            $user->save();
        }

        $reader = CollectionHubReader::where('user_id', '=', $request->user_id)->first();
        $reader->name = $request->name;
        $reader->save();

        return redirect()->route('collection_hub_reader.index')->with('successMsg', 'Collection hub reader is edited.');
    }

    public function delete($id)
    {
        // $user = User::find($id);
        // $user->delete();
        $hub_id = Helper::getCollectionHubId();

        $user = CollectionHubReader::where('user_id', $id)->where('collection_hub_id', $hub_id)->first();
        $user->delete();
        return redirect()->route('collection_hub_reader.index')->with('successMsg', 'Collection hub reader is deleted.');
    }
}
