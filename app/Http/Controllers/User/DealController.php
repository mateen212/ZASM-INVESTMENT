<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Deal;

use App\Http\Controllers\Controller;

class DealController extends Controller
{

    public function index()
    {
        $pageTitle = 'Deals';
        // Retrieve all deals or filter as needed
        $deals = Deal::paginate(10);

        // Return the view with the deals data
        return view('Template::user.deals.index', compact('deals', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }

        // Create a new deal
        $deal = Deal::create(array_merge($request->all(), ['user_id' => auth()->id()]));

        return response()->json(['success' => 'Deal created successfully', 'deal' => [
            'id' => $deal->id,
            'name' => $deal->name,
            'type' => $deal->type, 
            
        ]], 200);
    }

    public function history()
    {
        // Retrieve all deals or filter as needed
        $deals = Deal::all();

        // Return the view with the deals data
        return view('user.deal.history', compact('deals'));
    }
}
