<?php

namespace App\Http\Controllers;

use App\Models\CollectionHubBin;
use App\Models\CollectionHubBinActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionHubBinActivityController extends Controller
{
    public function index()
    {
        $activities = CollectionHubBinActivity::get();

        return view('collection_hub_bin_activity.index', compact('activities'));
    }
}
