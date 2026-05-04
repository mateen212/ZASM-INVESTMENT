<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PartnerDealService;
use App\Models\Deal;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
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
        $this->middleware(["auth:admin", "role:partner"]);
    }

    /**
     * Display the partner dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $pageTitle = "Partner Dashboard";
        $partnerId = Auth::guard("admin")->id();
        
        try {
            $deals = $this->partnerDealService->getPartnerDeals($partnerId);
            
            // Get counts for dashboard stats
            $totalDeals = $deals->count();
            $activeDeals = $deals->where("deal_stage", "active")->count();
            $pendingDeals = $deals->where("deal_stage", "pending")->count();
            
            return view("admin.partner.dashboard", compact(
                "deals", 
                "totalDeals", 
                "activeDeals", 
                "pendingDeals",
                "pageTitle"
            ));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error loading partner dashboard: ' . $e->getMessage());
            
            // Provide empty data to prevent errors
            $deals = collect([]);
            $totalDeals = 0;
            $activeDeals = 0;
            $pendingDeals = 0;
            
            return view("admin.partner.dashboard", compact(
                "deals", 
                "totalDeals", 
                "activeDeals", 
                "pendingDeals",
                "pageTitle"
            ));
        }
    }

    /**
     * Display a listing of the partner"s deals.
     *
     * @return \Illuminate\Http\Response
     */
    public function deals(Request $request)
    {
        $pageTitle = "Deals";
        $partnerId = Auth::guard("admin")->id();
        
        // Get partner deals and transform to a collection of Deal models
        $partnerDeals = $this->partnerDealService->getPartnerDeals($partnerId);
        $dealIds = $partnerDeals->pluck('deal_id')->toArray();
        
        // Start query builder for deals
        $dealsQuery = Deal::whereIn('id', $dealIds);
        
        // Handle sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['name', 'type', 'deal_stage', 'created_at'];
        if (in_array($sort, $allowedSortFields)) {
            $dealsQuery->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $dealsQuery->orderBy('created_at', 'desc'); // Default sorting
        }
        
        // Execute query with pagination
        $deals = $dealsQuery->paginate(10);
        
        // Append query parameters to pagination links
        if ($request->has('sort')) {
            $deals->appends(['sort' => $request->input('sort')]);
        }
        if ($request->has('direction')) {
            $deals->appends(['direction' => $request->input('direction')]);
        }
        
        // Use the same view as the admin deals page
        return view("admin.deals.index", compact("deals", "pageTitle"));
    }

    /**
     * Show the form for creating a new deal.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDeal()
    {
        return view("admin.partner.deals.create");
    }


    /**
     * Store a newly created deal in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDeal(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "type" => "required|string|max:255",
            "deal_stage" => "required|string|max:255",
            // Add other validation rules as needed
        ]);


        $request->merge(["user_id" => Auth::guard("admin")->id()]);
        // Create the deal
        $deal = Deal::create($request->all());
        
        // Associate the deal with the partner
        // $partnerId = Auth::guard("admin")->id();
        // $this->partnerDealService->associateDealWithPartner($deal->id, $partnerId, 'lead_partner');
        
        return redirect()->route("partner.deals.index")
            ->with("success", "Deal created successfully.");
    }

    /**
     * Display the specified deal.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDeal($id)
    {
        $partnerId = Auth::guard("admin")->id();
        
        // Check if the deal belongs to the partner
        if (!$this->partnerDealService->isDealOwnedByPartner($id, $partnerId)) {
            abort(403, "You do not have access to this deal.");
        }
        
        $deal = Deal::findOrFail($id);
        
        return view("admin.partner.deals.show", compact("deal"));
    }

    /**
     * Show the form for editing the specified deal.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editDeal($id)
    {
        $partnerId = Auth::guard("admin")->id();
        
        // Check if the deal belongs to the partner
        if (!$this->partnerDealService->isDealOwnedByPartner($id, $partnerId)) {
            abort(403, "You do not have access to this deal.");
        }
        
        $deal = Deal::findOrFail($id);
        
        return view("admin.partner.deals.edit", compact("deal"));
    }

    /**
     * Update the specified deal in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDeal(Request $request, $id)
    {
        $partnerId = Auth::guard("admin")->id();
        
        // Check if the deal belongs to the partner
        if (!$this->partnerDealService->isDealOwnedByPartner($id, $partnerId)) {
            abort(403, "You do not have access to this deal.");
        }
        
        $request->validate([
            "name" => "required|string|max:255",
            "type" => "required|string|max:255",
            "deal_stage" => "required|string|max:255",
            // Add other validation rules as needed
        ]);
        
        $deal = Deal::findOrFail($id);
        $deal->update($request->all());
        
        return redirect()->route("partner.deals.index")
            ->with("success", "Deal updated successfully.");
    }

    /**
     * Remove the specified deal from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyDeal($id)
    {
        $partnerId = Auth::guard("admin")->id();
        
        // Check if the deal belongs to the partner
        if (!$this->partnerDealService->isDealOwnedByPartner($id, $partnerId)) {
            abort(403, "You do not have access to this deal.");
        }
        
        // Remove the association first
        $this->partnerDealService->removeDealFromPartner($id);
        
        // Then delete the deal
        Deal::findOrFail($id)->delete();
        
        return redirect()->route("partner.deals.index")
            ->with("success", "Deal deleted successfully.");
    }

    /**
     * Update the partner"s company profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            "company_name" => "required|string|max:255",
            "company_description" => "nullable|string",
            "company_website" => "nullable|url",
            "company_logo" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);
        
        $partner = Auth::guard("admin")->user();
        
        // Handle logo upload if provided
        if ($request->hasFile("company_logo")) {
            $logoPath = $request->file("company_logo")->store("company_logos", "public");
            $partner->company_logo = $logoPath;
        }
        
        $partner->company_name = $request->company_name;
        $partner->company_description = $request->company_description;
        $partner->company_website = $request->company_website;
        $partner->save();
        
        return redirect()->route("partner.profile")
            ->with("success", "Company profile updated successfully.");
    }

    /**
     * Show the partner"s company profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfile()
    {
        $partner = Auth::guard("admin")->user();
        return view("admin.partner.profile", compact("partner"));
    }
}
