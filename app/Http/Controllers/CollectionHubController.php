<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\CollectionHub;
use App\Models\CollectionHubBin;
use App\Models\RecycleType;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CollectionHubController extends Controller
{
    public function index(Request $request)
    {
        $hubs = DB::table('collection_hub')->leftJoin('state', 'collection_hub.hub_state_id', '=', 'state.id')->get(['collection_hub.id', 'hub_name', 'hub_address', 'hub_postcode',  DB::raw("state.name as state_name"), 'contact_number', 'operating_hours', 'is_active', 'read_only', 'operating_day', 'type']);
        return view('collection_hub.index', compact('hubs'));
    }

    public function create()
    {
        $states = State::get();
        $cities = City::get();
        return view('collection_hub.create', compact('states', 'cities'));
    }

    public function insert(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'postcode' => 'required|integer|digits:5',
            'contact_number' => 'required|numeric',
            'operating_day' => 'required',
            'operating_hour_start' => 'required',
            'operating_hour_end' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'image' => 'required',
        ]);

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/hub_logo', $image);
        }
        $operating_hours = $request->operating_hour_start . ' - ' . $request->operating_hour_end;
        $active = '';
        $readonly = '';
        $request->has('active_status') ? $active = 1 : $active = 0;
        $request->has('read_only') ? $readonly = 1 : $readonly = 0;
        $hub_id = CollectionHub::create([
            'hub_name' => $request->name,
            'hub_address' => $request->address,
            'hub_postcode' => $request->postcode,
            'hub_state_id' => $request->state_id,
            'hub_city_id' => $request->city_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'contact_number' => $request->contact_number,
            'operating_day' => $request->operating_day,
            'operating_hours' => $operating_hours,
            'is_active' => $active,
            'read_only' => $readonly,
            'type' => $request->type,
            'image' => $image,
        ])->id;

        // $recycle_type = RecycleType::get();
        // foreach ($recycle_type as $value) {
        //     CollectionHubBin::create([
        //         'recycle_type_id' => $value->id,
        //         'collection_hub_id' => $hub_id,
        //     ]);
        // }

        return redirect()->route('collection_hub.index')->with('successMsg', 'Collection hub is created.');
    }

    public function edit($id)
    {
        $states = State::get();
        $cities = City::get();
        $hub = DB::table('collection_hub')->where('collection_hub.id', '=', $id)->leftJoin('state', 'collection_hub.hub_state_id', '=', 'state.id')->get(['collection_hub.id', 'hub_name', 'hub_address', 'hub_postcode',  DB::raw("state.name as state_name"), 'hub_state_id', 'hub_city_id', 'contact_number', 'operating_hours', 'is_active', 'read_only', 'latitude', 'longitude', 'operating_day', 'type'])->first();
        $hub = CollectionHub::find($id);

        return view('collection_hub.edit', compact('states', 'hub', 'id', 'cities'));
    }

    public function view($id)
    {
        $hub = DB::table('collection_hub')->where('collection_hub.id', '=', $id)->leftJoin('state', 'collection_hub.hub_state_id', '=', 'state.id')->get(['collection_hub.id', 'hub_name', 'hub_address', 'hub_postcode',  DB::raw("state.name as state_name"), 'hub_state_id', 'hub_city_id', 'contact_number', 'operating_hours', 'is_active', 'read_only', 'latitude', 'longitude', 'operating_day'])->first();
        $hub = CollectionHub::find($id);
        return view('collection_hub.view', compact('hub', 'id'));
    }

    public function edit_db(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'postcode' => 'required|integer|digits:5',
            'contact_number' => 'required|numeric',
            'operating_day' => 'required',
            'operating_hour_start' => 'required',
            'operating_hour_end' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',

        ]);

        $hub = CollectionHub::find($request->hub_id);

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/hub_logo', $image);
            File::delete('nucycle-admin/images/hub_logo/' . $hub->image);
        } else {
            $image = $hub->image;
        }


        $hub->hub_name = $request->name;
        $hub->hub_address = $request->address;
        $hub->hub_postcode = $request->postcode;
        $hub->hub_state_id = $request->state_id;
        $hub->hub_city_id = $request->city_id;
        $hub->contact_number = $request->contact_number;
        $hub->operating_day = $request->operating_day;
        $hub->operating_hours = $request->operating_hour_start . ' - ' . $request->operating_hour_end;
        $hub->is_active = $request->active_status == 'active' ? 1 : 0;
        $hub->read_only = $request->read_only == 'readonly' ? 1 : 0;
        $hub->latitude = $request->latitude;
        $hub->longitude = $request->longitude;
        $hub->type = $request->type;
        $hub->image = $image;
        $hub->save();

        return redirect()->route('collection_hub.index')->with('successMsg', 'Collection hub is edited.');
    }

    // public function delete($id)
    // {
    //     $hub = CollectionHub::find($id);
    //     $hub->delete();

    // }
}
