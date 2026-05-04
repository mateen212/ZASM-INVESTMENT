<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\InvestorProfile;
use App\Models\Investment;
use App\Models\User;
use Spatie\Tags\Tag;



use Validator;

class InvestorController extends Controller
{
    public function storeInvestor(Request $request)
    {
        // Add validation rules for required fields
        $validate = Validator::make($request->all(), [
            'investor_fname' => 'required|string|max:255',
            'investor_lname' => 'required|string|max:255',
            'investor_email' => 'required|email|unique:investors,investor_email',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        // Check if the user already exists or create a new one
        $user = User::where('email', $request->investor_email)->first();
        if (!$user) {
            $user = User::create([
                'firstname' => $request->investor_fname,
                'lastname' => $request->investor_lname,
                'username' => strtolower(str_replace(' ', '-', $request->investor_fname)) . '-' . strtolower(str_replace(' ', '-', $request->investor_lname)),
                'email' => $request->investor_email,
                'password' => bcrypt('password'),
            ]);
        }

        $request->merge(['user_id' => $user->id]);

        // Create the investor entry
        $investor = Investor::create($request->all());

        $userId = auth('admin')->id();
        $tags = explode(',', $request->investor_tags);
        $investor->attachTagsForUser($tags, $userId);
        // Retrieve all investors or filter as needed
        $investors = Investor::with('investor_profiles')->get();

        // Return a success response
        return response()->json([
            'success' => 'Investor created successfully',
            'investors' => $investors,
            'investor' => $investor,
        ], 200);
    }

    public function storeInvestorTag(Request $request)
    {
        // Add validation rules for required fields
        $validate = Validator::make($request->all(), [
            'tag' => 'required|string|max:255',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        // Create a new tag
        $tag = Tag::create(['name' => $request->tag]);

        // Return a success response
        return response()->json([
           'success' => 'Tag created successfully',
            'tag' => $tag,
        ], 200);    
    }
     
    // store investor profile
    public function storeInvestorProfile(Request $request)
    {
        // Add validation rules for required fields
        $validate = Validator::make($request->all(), [
            'profile_type' => 'required|string|max:255',
            'profile_fname' => 'required_if:profile_type,indivisual,join_tenancy',
            'profile_lname' => 'required_if:profile_type,indivisual,join_tenancy',
            'profile_ira_name' => 'required_if:profile_type,custodian',
            'profile_fname2' => 'required_if:profile_type,join_tenancy',
            'profile_lname2' => 'required_if:profile_type,join_tenancy',
            'profile_email2' => 'required_if:profile_type,join_tenancy',
          
        ], [
            'profile_fname' => 'The profile first name field is required',
            'profile_lname' => 'The profile last name field is required',
            'profile_ira_name' => 'The profile ira name field is required',
            'profile_fname2' => 'The profile first name field is required',
            'profile_lname2' => 'The profile last name field is required',
            'profile_email2' => 'The profile email field is required',
        ]); 
    
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
    
        // Create the investment entry
        $investorprofile = InvestorProfile::create($request->all());
        $profiles = InvestorProfile::where('investor_id', $request->investor_id)->get();
        // Return a success response
        return response()->json(['success' => 'profile created successfully', 'investorprofile'  => $investorprofile ,'profiles'=> $profiles ], 200);
        
    }
    // public function show($id)
    // {
        
    //     $investment = Investment::with('investors')->find($id);
       

    //     // Check if the investment exists
    //     if (!$investment) {
    //         abort(404, 'Investment not found');
    //     }

    //     // Pass the investment data to the Blade view
    //     return view('admin.deals.summary', compact(''));
    // }

}
