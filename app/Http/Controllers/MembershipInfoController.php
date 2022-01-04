<?php

namespace App\Http\Controllers;

use App\Models\MembershipInfo;
use Illuminate\Http\Request;

class MembershipInfoController extends Controller
{

    public function index()
    {
        $membership = MembershipInfo::first();
        return view('membership_info.index', compact('membership'));
    }

    public function edit()
    {
        $membership = MembershipInfo::first();
        return view('membership_info.edit', compact('membership'));
    }

    public function edit_db(Request $request)
    {
        $membership = MembershipInfo::first();
        $membership->content = $request->content;
        $membership->save();
        return redirect()->route('membership_info.index')->with('successMsg', 'Content is edited.');
    }
}
