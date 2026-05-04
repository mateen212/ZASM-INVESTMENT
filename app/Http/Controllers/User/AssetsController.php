<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Asset;

use App\Http\Controllers\Controller;

class AssetsController extends Controller
{

    public function index()
    {
        $pageTitle = 'Assets';
        // Retrieve all deals or filter as needed
        $deals = Deal::paginate(10);

        // Return the view with the deals data
        return view('Template::admin.assets.index', compact('deals', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'property_type' => 'required',
            'deal_id' => 'required',

        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }

        // Create a new deal
        $asset = Asset::create($request->all());

        return response()->json(['success' => 'Asset created successfully', 
            'asset' => $asset
        ], 200);
    }


}
