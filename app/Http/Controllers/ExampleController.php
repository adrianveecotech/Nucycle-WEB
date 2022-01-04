<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function index(Request $request)
    {
        return view('activity.index', compact('activities'));
    }

    public function create()
    {
        return view('activity.create', compact('banners'));
    }

    public function edit($id)
    {
        return view('activity.create', compact('hub_recycles', 'id'));
    }

    public function insert(Request $request)
    {

        return redirect()->route('activity.index')->with('successMsg', 'Activity is created.');
    }

    public function edit_db(Request $request)
    {
        return redirect()->route('activity.index')->with('successMsg', 'Activity is edited.');
    }

    public function delete($id)
    {
        return redirect()->route('activity.index')->with('successMsg', 'Activity is deleted.');
    }
}
