<?php

namespace App\Http\Controllers;

use App\Models\CollectionHub;
use App\Models\CollectionHubAdmin;
use App\Models\CollectionHubBin;
use App\Models\CollectionHubCollector;
use App\Models\CollectionHubRecycleType;
use App\Models\RecycleType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class CollectionHubRecycleTypeController extends Controller
{

    public function index(Request $request)
    {
        if (in_array(4, $request->user()->users_roles_id())) {
            $hub_id = Auth::user()->hub_admin->collection_hub_id;
            $hub = CollectionHub::find($hub_id);
            $hub_recycles1 = CollectionHub::find($hub_id)->collection_hub_recycle_type;
            $hub_recycles = array();
            foreach ($hub_recycles1 as $element) {
                $hub_recycles[$element['recycle_type_id']][] = $element;
            }
            return view('collection_hub_recycle_type.index', compact('hub_recycles','hub'));
        } elseif (in_array(1, $request->user()->users_roles_id())) {
            $hub_recycles1 = CollectionHubRecycleType::get();
            $hub_recycles = array();
            $hubs = array();
            foreach ($hub_recycles1 as $element) {
                $hub_recycles[$element['collection_hub_id']][$element['recycle_type_id']][] = $element;
            }
            foreach ($hub_recycles as $key => $value) {
                $hubs[] = CollectionHub::find($key)->toArray();
            }
            if(count($hubs) == 0)
            {
                $hubs = CollectionHub::get();
            }
            return view('collection_hub_recycle_type.index_admin', compact('hub_recycles', 'hubs'));
        }
    }

    public function create()
    {
        $hubs = CollectionHub::get();
        $recycle_types = RecycleType::get();
        return view('collection_hub_recycle_type.create', compact('hubs', 'recycle_types'));
    }

    public function edit($id, $hub_id)
    {
        $hub_recycles = CollectionHubRecycleType::where('recycle_type_id', $id)->where('collection_hub_id', $hub_id)->get();
        if (count($hub_recycles) == 0)
            abort('404');
        return view('collection_hub_recycle_type.edit', compact('hub_recycles', 'id'));
    }

    public function view($id, $hub_id)
    {
        $hub_recycles = CollectionHubRecycleType::where('recycle_type_id', $id)->where('collection_hub_id', $hub_id)->get();
        if (count($hub_recycles) == 0)
            abort('404');
        return view('collection_hub_recycle_type.view', compact('hub_recycles', 'id', 'hub_id'));
    }

    public function insert(Request $request)
    {
        $hub_id = '';
        if ($request->active_status == null)
        {
            $request->active_status = [''];
        }
        if (in_array(4, Auth::user()->users_roles_id()))
            $hub_id = Auth::user()->hub_admin->collection_hub_id;
        else if (in_array(1, Auth::user()->users_roles_id())) {
            $hub_id = $request->hub;
        }

        $this->validate($request, [
            'recycle_type' => 'required',
            'point' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);
        foreach ($request->point as $key => $value) {
            CollectionHubRecycleType::create([
                'recycle_type_id' => $request->recycle_type,
                'collection_hub_id' => $hub_id,
                'point' => $value,
                'start_date' => $request->start_date[$key],
                'end_date' => $request->end_date[$key],
                'is_active' => in_array("active" . $key, $request->active_status) ? 1 : 0
            ]);
        }
        CollectionHubBin::create([
            'recycle_type_id' => $request->recycle_type,
            'collection_hub_id' => $hub_id,
        ]);
        return redirect()->route('collection_hub_recycle_type.index')->with('successMsg', 'Collection Hub Recycle Type is created.');
    }

    public function edit_db(Request $request)
    {
        $hub_id = $request->hub_id;
        $hub_recycles = CollectionHubRecycleType::where('recycle_type_id', $request->id)->where('collection_hub_id', $hub_id)->get();
        foreach ($hub_recycles as $hub_recycle) {
            $found = false;
            foreach ($request->index as $key => $index) {
                if ($index == $hub_recycle['id']) {
                    // $recycle_type = CollectionHubRecycleType::where('id', '=', $index)->first();
                    // if (!empty($recycle_type)) {
                    $hub_recycle->point = $request->point[$key];
                    $hub_recycle->start_date = $request->start_date[$key];
                    $hub_recycle->end_date = $request->end_date[$key];
                    if ($request->has('active_status')) {
                        if (in_array($index, $request->active_status)) {
                            // $hub_recycle->is_active = $request->is_active = 1;
                            $hub_recycle->is_active = 1;
                        } else {
                            // $hub_recycle->is_active = $request->is_active = 0;
                            $hub_recycle->is_active = 0;
                        }
                    } else {
                        // $hub_recycle->is_active = $request->is_active = 0;
                        $hub_recycle->is_active = 0;
                    }

                    $hub_recycle->save();
                    $found = true;
                    break;
                    // }
                }
            }
            if ($found == false) {
                // $recycle_type = CollectionHubRecycleType::where('id', '=', $index)->first();
                // if (!empty($recycle_type))
                $hub_recycle->delete();
            }
        }
        if ($request->has('indexNew')) {
            foreach ($request->indexNew as $key => $value) {
                CollectionHubRecycleType::create([
                    'recycle_type_id' => $request->id,
                    'collection_hub_id' => $hub_id,
                    'point' => $request->pointNew[$key],
                    'start_date' => $request->start_dateNew[$key],
                    'end_date' => $request->end_dateNew[$key],
                    'is_active' => $request->has('active_statusNew') ? (in_array($value, $request->active_statusNew) ? 1 : 0) : 0
                ]);
            }
        }


        return redirect()->route('collection_hub_recycle_type.index')->with('successMsg', 'Collection Hub Recycle Type is edited.');
    }

    public function delete($id, $hub_id)
    {
        $hub_recycle = CollectionHubRecycleType::where('recycle_type_id', $id)->where('collection_hub_id', $hub_id)->get();
        $hub_recycle->each->delete();

        return redirect()->route('collection_hub_recycle_type.index')->with('successMsg', 'Collection Hub Recycle Type is deleted.');
    }

    public function get_hub_recycle(Request $request)
    {
        $hub_recycles1 = CollectionHubRecycleType::where('collection_hub_id', $request->hub_id)->get();
        $hub_recycles = array();
        foreach ($hub_recycles1 as $element) {
            $hub_recycles[$element['recycle_type_id']][] = $element;
        }



        $html = '';
        foreach ($hub_recycles as $hub_recycle) {
            $html .= '<tr>';
            $html .= '<td class="lalign">';
            $html .= $hub_recycle[0]->recycle_type->name;
            $html .= '</td> <td>';
            // foreach ($hub_recycle as $hub_recycle1)
            //     $html .= $hub_recycle1->collection_hub->hub_name . '<br>';
            // $html .= '</td><td>';
            foreach ($hub_recycle as $hub_recycle1)
                $html .= $hub_recycle1->point . '<br>';

            $html .= '</td><td>';
            foreach ($hub_recycle as $hub_recycle1)
                $html .= $hub_recycle1->start_date . '<br>';

            $html .= '</td><td>';
            foreach ($hub_recycle as $hub_recycle1)
                $html .= $hub_recycle1->end_date . '<br>';

            $html .= '</td><td>';

            foreach ($hub_recycle as $hub_recycle1) {
                $html .= $hub_recycle1->is_active == 0 ? 'Inactive' : 'Active';
                $html .= '<br>';
            }


            $html .= '</td>';
            $html .= '<td>';
            $html .= '<a href="' . route("collection_hub_recycle_type.view", ["id" => $hub_recycle[0]->recycle_type_id, "hub_id" => $hub_recycle[0]->collection_hub_id]) . '" class="btn btn-xs btn-success"><i class="nav-icon fa fa-eye"></i></a>';
            $html .= '<a href="' . route("collection_hub_recycle_type.edit", ["id" => $hub_recycle[0]->recycle_type_id, "hub_id" => $hub_recycle[0]->collection_hub_id]) . '" class="btn btn-xs btn-success"><i class="nav-icon fa fa-pencil"></i></a>';
            $html .= '<a href="' . route("collection_hub_recycle_type.delete", ["id" => $hub_recycle[0]->recycle_type_id, "hub_id" => $hub_recycle[0]->collection_hub_id]) . '" class="btn btn-xs btn-danger"><i class="nav-icon fa fa-trash"></i></a>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        return response()->json(['html' => $html]);
    }
}
