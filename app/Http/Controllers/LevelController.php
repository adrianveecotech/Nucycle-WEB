<?php

namespace App\Http\Controllers;

use App\Models\BannerTag;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LevelController extends Controller
{
    public function index(Request $request)
    {
        $levels = Level::orderBy('points_from', 'asc')->get();
        return view('level.index', compact('levels'));
    }

    public function create()
    {
        return view('level.create');
    }

    public function edit($id)
    {
        $level = level::find($id);
        return view('level.edit', compact('level', 'id'));
    }

    public function view($id)
    {
        $level = level::find($id);
        return view('level.view', compact('level', 'id'));
    }

    public function insert(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
            'points_from' => 'required|integer',
            'points_to' => 'required|integer',
            'free_monthly_voucher' => 'required|numeric',
            'multiplier' => 'required|numeric',
        ]);
        if (!$request->hasFile('image')) {
            return redirect()->back()->with('image', 'Image is empty.')->withInput();
        }

        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/avatar', $image);
        }

        level::create([
            'name' =>  $request->name,
            'description' => $request->description,
            'image' => $image,
            'points_from' => $request->points_from,
            'points_to' => $request->points_to,
            'free_monthly_voucher' => $request->free_monthly_voucher,
            'multiplier' => $request->multiplier,
        ]);

        return redirect()->route('level.index')->with('successMsg', 'Level is created.');
    }

    public function edit_db(Request $request)
    {
        // return redirect()->back()->with('image', 'Image is empty.');

        $this->validate($request, [
            'name' => 'required',
            'points_from' => 'required|integer',
            'points_to' => 'required|integer',
            'free_monthly_voucher' => 'required|numeric',
            'multiplier' => 'required|numeric'
        ]);

        $level = level::find($request->level_id);
        if (!$level->image && !$request->hasFile('image')) {
            return redirect()->back()->with('image', 'Image is empty.')->withInput();
        }


        $image = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $image = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('nucycle-admin/images/avatar', $image);
            File::delete('nucycle-admin/images/avatar/' . $level->image);
        } else {
            $image = $level->image;
        }

        $level->name = $request->name;
        $level->description = $request->description;
        $level->image = $image;
        $level->points_from = $request->points_from;
        $level->points_to = $request->points_to;
        $level->free_monthly_voucher = $request->free_monthly_voucher;
        $level->multiplier = $request->multiplier;
        $level->save();

        return redirect()->route('level.index')->with('successMsg', 'Level is edited.');
    }

    public function delete($id)
    {
        $level = level::find($id);
        File::delete('nucycle-admin/images/avatar/' . $level->image);
        $level->delete();
        return redirect()->route('level.index')->with('successMsg', 'Level is deleted.');
    }
}
