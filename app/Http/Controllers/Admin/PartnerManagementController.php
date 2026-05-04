<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Deal;
use App\Models\PartnerDeal;
use App\Services\PartnerDealService;
use Spatie\Permission\Models\Role;

class PartnerManagementController extends Controller
{
    /**
     * The partner deal service instance.
     *
     * @var \App\Services\PartnerDealService
     */
    protected $partnerDealService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\PartnerDealService  $partnerDealService
     * @return void
     */
    public function __construct(PartnerDealService $partnerDealService)
    {
        $this->partnerDealService = $partnerDealService;
        $this->middleware(["auth:admin"]);
        // Temporarily removed role:admin middleware to allow access during development
    }

    /**
     * Display a listing of the partners.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTitle = 'All Partners';
        $partnerRole = Role::where("name", "partner")->first();
        
        // Get pagination size from request or use default
        $perPage = $request->input('per_page', 40);
        // Ensure per_page is one of the allowed values
        if (!in_array($perPage, [40, 80, 160])) {
            $perPage = 40;
        }
        
        $partners = Admin::role("partner")->paginate($perPage);
        $emptyMessage = 'No partners found';
        
        return view("admin.partner-management.index", compact("partners", "emptyMessage", "pageTitle", "perPage"));
    }

    /**
     * Show the form for creating a new partner.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = 'Create Partner';
        return view("admin.partner-management.create", compact("pageTitle"));
    }

    /**
     * Store a newly created partner in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:admins",
            "password" => "required|string|min:8|confirmed",
            "company_name" => "required|string|max:255",
            "company_description" => "nullable|string",
            "company_website" => "nullable|url",
            "company_logo" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);
        
        // Create the admin user
        $partner = new Admin();
        $partner->name = $request->name;
        $partner->email = $request->email;
        $partner->password = bcrypt($request->password);
        $partner->company_name = $request->company_name;
        $partner->company_description = $request->company_description;
        $partner->company_website = $request->company_website;
        
        // Handle logo upload if provided
        if ($request->hasFile("company_logo")) {
            $location = imagePath()['profile']['partner']['path'];
            
            // Create directory if it doesn't exist
            if (!file_exists($location)) {
                mkdir($location, 0755, true);
            }
            
            // Remove old image if exists
            if ($partner->company_logo && file_exists($location . '/' . $partner->company_logo)) {
                @unlink($location . '/' . $partner->company_logo);
            }
            
            // Generate a unique filename
            $filename = time() . '_' . $request->file('company_logo')->getClientOriginalName();
            
            // Move the uploaded file to the destination
            $request->file('company_logo')->move($location, $filename);
            
            // Save the filename to the database
            $partner->company_logo = $filename;
        }
        
        $partner->save();
        
        // Assign partner role
        $partner->assignRole("partner");
        
        return redirect()->route("admin.partner-management.index")
            ->with("success", "Partner created successfully.");
    }

    /**
     * Display the specified partner.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = 'Partner Details';
        $partner = Admin::findOrFail($id);
        $partnerDeals = $this->partnerDealService->getPartnerDeals($id);
        
        return view("admin.partner-management.show", compact("partner", "partnerDeals", "pageTitle"));
    }

    /**
     * Show the form for editing the specified partner.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = 'Edit Partner';
        $partner = Admin::findOrFail($id);
        return view("admin.partner-management.edit", compact("partner", "pageTitle"));
    }

    /**
     * Update the specified partner in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:admins,email," . $id,
            "company_name" => "required|string|max:255",
            "company_description" => "nullable|string",
            "company_website" => "nullable|url",
            "company_logo" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);
        
        $partner = Admin::findOrFail($id);
        $partner->name = $request->name;
        $partner->email = $request->email;
        $partner->company_name = $request->company_name;
        $partner->company_description = $request->company_description;
        $partner->company_website = $request->company_website;
        
        // Handle logo upload if provided
        if ($request->hasFile("company_logo")) {
            $location = imagePath()['profile']['partner']['path'];
            
            // Create directory if it doesn't exist
            if (!file_exists($location)) {
                mkdir($location, 0755, true);
            }
            
            // Remove old image if exists
            if ($partner->company_logo && file_exists($location . '/' . $partner->company_logo)) {
                @unlink($location . '/' . $partner->company_logo);
            }
            
            // Generate a unique filename
            $filename = time() . '_' . $request->file('company_logo')->getClientOriginalName();
            
            // Move the uploaded file to the destination
            $request->file('company_logo')->move($location, $filename);
            
            // Save the filename to the database
            $partner->company_logo = $filename;
        }
        
        // Update password if provided
        if ($request->filled("password")) {
            $request->validate([
                "password" => "required|string|min:8|confirmed",
            ]);
            
            $partner->password = bcrypt($request->password);
        }
        
        $partner->save();
        
        return redirect()->route("admin.partner-management.index")
            ->with("success", "Partner updated successfully.");
    }

    /**
     * Remove the specified partner from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $partner = Admin::findOrFail($id);
            
            // Check if this is a partner role
            if (!$partner->hasRole('partner')) {
                if (request()->ajax()) {
                    return response()->json(['error' => 'This user is not a partner.'], 403);
                }
                return redirect()->route("admin.partner-management.index")
                    ->with("error", "This user is not a partner.");
            }
            
            // Remove all deal associations first
            PartnerDeal::where("admin_id", $id)->delete();
            
            // Then delete the partner
            $partner->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => 'Partner deleted successfully.']);
            }
            
            return redirect()->route("admin.partner-management.index")
                ->with("success", "Partner deleted successfully.");
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => 'An error occurred while deleting the partner.'], 500);
            }
            
            return redirect()->route("admin.partner-management.index")
                ->with("error", "An error occurred while deleting the partner.");
        }
    }

    /**
     * Show the form for assigning deals to a partner.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignDealsForm($id)
    {
        $pageTitle = 'Assign Deals to Partner';
        $partner = Admin::findOrFail($id);
        $partnerDeals = $this->partnerDealService->getPartnerDeals($id);
        $assignedDealIds = $partnerDeals->pluck("deal_id")->toArray();
        
        // Get all deals
        $availableDeals = Deal::where('user_id', '!=', $id)->get();

        return view("admin.partner-management.assign-deals", compact(
            "partner", 
            "partnerDeals", 
            "availableDeals",
            "assignedDealIds",
            "pageTitle"
        ));
    }

    /**
     * Assign deals to a partner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignDeals(Request $request, $id)
    {
        $request->validate([
            "deal_ids" => "required|array",
            "deal_ids.*" => "exists:deals,id",
        ]);
        
        $partner = Admin::findOrFail($id);
        
        foreach ($request->deal_ids as $dealId) {
            $this->partnerDealService->associateDealWithPartner($dealId, $id, 'admin_sponsor');
            // if member exists in deal for this partner email
            $deal = Deal::where('id', $dealId)->
            whereHas('members', function($query) use ($partner) {
                $query->where('email_address', $partner->email);
            })->first();

            // dd($deal);
            if ($deal == null) {
                $deal = Deal::findOrFail($dealId);
                $deal->members()->create([
                    'email_address' => $partner->email,
                    'first_name' => $partner->name,
                    'last_name' => null,
                    'role' => null,
                    'status' => 1,
                ]);

            }
        }
        
        return redirect()->route("admin.partner-management.show", $id)
            ->with("success", "Deals assigned to partner successfully.");
    }

    /**
     * Remove a deal from a partner.
     *
     * @param  int  $partnerId
     * @param  int  $dealId
     * @return \Illuminate\Http\Response
     */
    public function removeDeal($partnerId, $dealId)
    {
        $partner = Admin::findOrFail($partnerId);
        $this->partnerDealService->removeDealFromPartner($dealId);
        
        return redirect()->route("admin.partner-management.show", $partnerId)
            ->with("success", "Deal removed from partner successfully.");
    }

    /**
     * Toggle the status of a partner.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id, Request $request)
    {
        $partner = Admin::findOrFail($id);
        $newStatus = $request->input('status', null);
        
        // If no specific status is provided, toggle between active and inactive
        if ($newStatus === null) {
            $partner->status = $partner->status == 1 ? 0 : 1;
        } else {
            // Set to the specified status
            $partner->status = $newStatus;
        }
        
        $partner->save();
        
        $statusMessages = [
            0 => 'deactivated',
            1 => 'activated',
            2 => 'paused',
            3 => 'terminated'
        ];
        
        $statusText = $statusMessages[$partner->status] ?? 'updated';
        
        return redirect()->route('admin.partner-management.index')
            ->with('success', "Partner has been {$statusText} successfully.");
    }
}
