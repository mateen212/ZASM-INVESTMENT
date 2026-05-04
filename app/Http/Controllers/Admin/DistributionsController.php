<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Models\Distribution;


class DistributionsController extends Controller
{
    
    public function index()
    {
        $pageTitle = 'Distributions';
        // Retrieve all distributions or filter as needed
        $distributions = Distribution::paginate(10);

        // Return the view with the distributions data
        return view('admin.distributions.index', compact('distributions', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'source' => 'required',
            'distribution_waterfall'=> 'required_if:water_fall,1',
            // 'included_classes'=> 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'distribution_date' => 'required',
            'compounding_period' => 'required_if:invested_payment,1|required_if:preferred_return,1',
        ],[
            'source' => 'The  source field is required',
            'distribution_waterfall' => 'The distribution waterfall field is required ',
            // 'included_classes' => 'The included classes field is required',
            'start_date' => 'The calculation method field is required',
            'amount' => 'The amount field is required when calculation method is not preferred return or invested payment',
            'distribution_date' => 'The distribution date field is required',
            'compounding_period' => 'The distribution type field is required',   
        ]
        );


        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        Distribution::create($request->all());

        return response()->json(['message' => 'Distribution created successfully'], 200);
    }

    public function toggleVisibility(Request $request, $id)
    {
        $distribution = Distribution::findOrFail($id);
        $distribution->is_visible = $request->is_visible;
        $distribution->save();

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $distribution = Distribution::find($id);
        if ($distribution) {
            $distribution->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Distribution not found.']);
    }

    
}
