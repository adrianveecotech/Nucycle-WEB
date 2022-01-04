<?php

namespace App\Http\Controllers;

use App\Models\CollectionHubBin;
use App\Models\CollectionHubBinActivity;
use Illuminate\Http\Request;
use Auth;

class CollectionHubBinController extends Controller
{
    public function index()
    {
        $bins = CollectionHubBin::get();

        if (in_array(4, Auth::user()->users_roles_id())) {
            $hub_id = (Auth::user()->hub_admin->collection_hub_id);
            $bins = CollectionHubBin::where('collection_hub_id', $hub_id)->get();
        } elseif (in_array(1, Auth::user()->users_roles_id())) {
            $bins = CollectionHubBin::get();
        }
        return view('collection_hub_bin.index', compact('bins'));
    }

    public function edit($id)
    {
        $bin = CollectionHubBin::find($id);
        return view('collection_hub_bin.edit', compact('bin', 'id'));
    }

    public function edit_db(Request $request)
    {
        $bin = CollectionHubBin::find($request->bin_id);

        if (in_array(4, Auth::user()->users_roles_id())) {
            $this->validate($request, [
                'current_weight' => 'required',
            ]);
        } elseif (in_array(1, Auth::user()->users_roles_id())) {
            $this->validate($request, [
                'capacity_weight' => 'required',
                'current_weight' => 'required',
            ]);
            $bin = CollectionHubBin::find($request->bin_id);
            $bin->capacity_weight = $request->capacity_weight;
        }
        $bin->current_weight = $request->current_weight;
        $bin->save();



        return redirect()->route('collection_hub_bin.index')->with('successMsg', 'Bin is edited.');
    }

    public function reset($id)
    {
        $bin = CollectionHubBin::find($id);
        $bin->current_weight = 0;
        $bin->save();

        CollectionHubBinActivity::create([
            'description' => $bin->recycle_type->name. ' bin in '. $bin->collection_hub->hub_name . ' has been reset to 0.'
        ]);

        return redirect()->route('collection_hub_bin.index')->with('successMsg', 'Current weight reset to 0.');
    }
}
