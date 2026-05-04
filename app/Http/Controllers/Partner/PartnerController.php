<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PartnerDealService;
use App\Models\Deal;
use App\Models\Admin;
use App\Models\Asset;
use App\Models\Investment;
use App\Models\User;
use App\Models\Offering;
use App\Models\Distribution;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        // Apply the PartnerDealAccess middleware to ensure partners only see their own deals
        // No need to specify middleware here as it's already in the routes file
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
            
            // Get deal IDs to fetch related data
            $dealIds = $deals->pluck('id')->filter()->toArray();
            
            // Get recent deals for the My Deals section (limit to 5)
            $recentDeals = Deal::whereIn('id', $dealIds)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
                
            // Get investor counts for each deal
            $investorCounts = [];
            foreach ($recentDeals as $deal) {
                $investorCounts[$deal->id] = Investment::where('deal_id', $deal->id)
                    ->distinct('investor_id')
                    ->count('investor_id');
            }
            
            // Get offerings count
            $totalOfferings = 0;
            if (!empty($dealIds)) {
                $totalOfferings = Offering::whereIn('deal_id', $dealIds)->count();
            }
            
            // Get assets count
            $totalAssets = 0;
            if (!empty($dealIds)) {
                $totalAssets = Asset::whereIn('deal_id', $dealIds)->count();
            }
            
            // Get users count (investors in partner's deals)
            $totalUsers = 0;
            if (!empty($dealIds)) {
                // Using DB::raw to get distinct count properly in MySQL
                $totalUsers = Investment::whereIn('deal_id', $dealIds)
                    ->select(DB::raw('COUNT(DISTINCT investor_id) as user_count'))
                    ->first()
                    ->user_count ?? 0;
            }
            
            // Get active and pending deals count
            $activeDeals = $deals->where("deal_stage", "active")->count();
            $pendingDeals = $deals->where("deal_stage", "pending")->count();
            
            // Get investment data
            $totalInvested = 0;
            $pendingInvestments = 0;
            
            if (!empty($dealIds)) {
                // Total invested amount
                $totalInvested = Investment::whereIn('deal_id', $dealIds)
                    ->where('status', 'approved')
                    ->sum('amount');
                
                // Pending investments amount
                $pendingInvestments = Investment::whereIn('deal_id', $dealIds)
                    ->where('status', 'pending')
                    ->sum('amount');
            }
            
            // Get distribution data
            $totalDistributed = 0;
            $lastDistribution = null;
            
            if (!empty($dealIds)) {
                // Total distributed amount
                $totalDistributed = Distribution::whereIn('deal_id', $dealIds)
                    ->where('status', 'approved')
                    ->sum('amount');
                
                // Last distribution date
                $lastDistributionRecord = Distribution::whereIn('deal_id', $dealIds)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                $lastDistribution = $lastDistributionRecord ? $lastDistributionRecord->created_at : null;
            }
            
            // Get data for investment chart (last 6 months)
            $investmentChartData = [];
            $investmentChartLabels = [];
            
            if (!empty($dealIds)) {
                for ($i = 5; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i);
                    $monthLabel = $month->format('M Y');
                    $investmentChartLabels[] = $monthLabel;
                    
                    $monthlyInvestment = Investment::whereIn('deal_id', $dealIds)
                        ->where('status', 'approved')
                        ->whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->sum('amount');
                        
                    $investmentChartData[] = $monthlyInvestment;
                }
            }
            
            // Get data for distribution chart (last 6 months)
            $distributionChartData = [];
            $distributionChartLabels = [];
            
            if (!empty($dealIds)) {
                for ($i = 5; $i >= 0; $i--) {
                    $month = Carbon::now()->subMonths($i);
                    $monthLabel = $month->format('M Y');
                    $distributionChartLabels[] = $monthLabel;
                    
                    $monthlyDistribution = Distribution::whereIn('deal_id', $dealIds)
                        ->where('status', 'approved')
                        ->whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->sum('amount');
                        
                    $distributionChartData[] = $monthlyDistribution;
                }
            }
            
            // Get recent activities
            $activities = Activity::whereIn('deal_id', $dealIds)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            return view("partner.dashboard", compact(
                "deals", 
                "recentDeals",
                "investorCounts",
                "totalDeals", 
                "totalOfferings",
                "totalAssets",
                "totalUsers",
                "activeDeals",
                "pendingDeals",
                "totalInvested",
                "pendingInvestments",
                "totalDistributed",
                "lastDistribution",
                "investmentChartData",
                "investmentChartLabels",
                "distributionChartData",
                "distributionChartLabels",
                "activities",
                "pageTitle"
            ));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error loading partner dashboard: ' . $e->getMessage());
            
            // Provide empty data to prevent errors
            $deals = collect([]);
            $recentDeals = collect([]);
            $investorCounts = [];
            $totalDeals = 0;
            $totalOfferings = 0;
            $totalAssets = 0;
            $totalUsers = 0;
            $activeDeals = 0;
            $pendingDeals = 0;
            $totalInvested = 0;
            $pendingInvestments = 0;
            $totalDistributed = 0;
            $lastDistribution = null;
            $investmentChartData = [];
            $investmentChartLabels = [];
            $distributionChartData = [];
            $distributionChartLabels = [];
            $activities = collect([]);
            
            return view("partner.dashboard", compact(
                "deals", 
                "recentDeals",
                "investorCounts",
                "totalDeals", 
                "totalOfferings",
                "totalAssets",
                "totalUsers",
                "activeDeals",
                "pendingDeals",
                "totalInvested",
                "pendingInvestments",
                "totalDistributed",
                "lastDistribution",
                "investmentChartData",
                "investmentChartLabels",
                "distributionChartData",
                "distributionChartLabels",
                "activities",
                "pageTitle"
            ));
        }
    }

    /**
     * Display a listing of the partner's deals.
     *
     * @return \Illuminate\Http\Response
     */
    public function deals(Request $request)
    {
        $pageTitle = "Partner Deals";
        $partnerId = Auth::guard("admin")->id();
        
        // Get the partner's deal IDs
        // $partnerDealIds = $this->partnerDealService->getPartnerDealIds($partnerId);

        $deals = Deal::where('user_id', $partnerId)->orWhereHas('partners', function($q) use ($partnerId) {
            $q->where('admin_id', $partnerId);
        });

        
        // Apply sorting if requested
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        
        
        
        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $deals->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('deal_stage', 'like', "%$search%")
                    ->orWhere('sec_type', 'like', "%$search%")
                    ->orWhere('close_date', 'like', "%$search%")
                    ->orWhere('owning_entity_name', 'like', "%$search%");
            });
        }
        
        // Apply sorting
        if ($sort === 'investors') {
            // Special case for investors count
            $deals->withCount('investments')
                ->orderBy('investments_count', $direction);
        } else {
            $deals->orderBy($sort, $direction);
        }
        
        // Get the deals with pagination
        $deals = $deals->paginate(10);

        // Get investor counts for each deal
        $investorCounts = [];
        foreach ($deals as $deal) {
            // Using DB::raw to get distinct count properly in MySQL
            $investorCount = Investment::where('deal_id', $deal->id)
                ->select(DB::raw('COUNT(DISTINCT investor_id) as investor_count'))
                ->first();
            $investorCounts[$deal->id] = $investorCount ? $investorCount->investor_count : 0;
        }
        
        // Use the partner deals index view
        return view("admin.deals.index", compact("deals", "pageTitle", "investorCounts"));
    }

    /**
     * Display the specified deal.
     *
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function showDeal(Deal $deal)
    {
        $pageTitle = "Deal Details";
        $partnerId = Auth::guard("admin")->id();
        
        // Check if the deal belongs to this partner
        $hasAccess = $this->partnerDealService->checkPartnerHasDealAccess($partnerId, $deal->id);
        
        if (!$hasAccess) {
            $notify[] = ['error', 'You do not have access to this deal'];
            return redirect()->route('partner.deals.index')->withNotify($notify);
        }
        
        // Get additional deal data
        $offerings = $deal->offerings()->get();
        $assets = $deal->assets()->get();
        $classes = $deal->classes()->get();
        
        // Get investor count
        $investorCount = Investment::where('deal_id', $deal->id)
            ->select(DB::raw('COUNT(DISTINCT investor_id) as investor_count'))
            ->first();
        $investorCount = $investorCount ? $investorCount->investor_count : 0;
        
        // Return the view with all necessary data
        return view("partner.deals.show", compact(
            "deal", 
            "pageTitle", 
            "offerings", 
            "assets", 
            "classes", 
            "investorCount"
        ));
    }

    /**
     * Show the partner profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfile()
    {
        $pageTitle = "Partner Profile";
        $partner = Auth::guard("admin")->user();
        return view("partner.profile", compact("partner", "pageTitle"));
    }

    /**
     * Show the password change form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPasswordForm()
    {
        $pageTitle = "Change Password";
        return view("partner.password", compact("pageTitle"));
    }

    /**
     * Update the partner profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:admins,email," . Auth::guard("admin")->id(),
            "company_name" => "required|string|max:255",
            "company_description" => "nullable|string",
            "company_website" => "nullable|url",
            "company_logo" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);
        
        $partner = Auth::guard("admin")->user();
        $partner->name = $request->name;
        $partner->email = $request->email;
        $partner->company_name = $request->company_name;
        $partner->company_description = $request->company_description;
        $partner->company_website = $request->company_website;
        
        if ($request->hasFile('company_logo')) {
            // Handle logo upload
            $file = $request->file('company_logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/partner_logos'), $filename);
            $partner->company_logo = 'uploads/partner_logos/' . $filename;
        }
        
        $partner->save();
        
        $notify[] = ['success', 'Profile updated successfully'];
        return redirect()->route('partner.profile')->withNotify($notify);
    }

    /**
     * Update the partner password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            "current_password" => "required",
            "password" => "required|string|min:6|confirmed",
        ]);
        
        $partner = Auth::guard("admin")->user();
        
        if (!Hash::check($request->current_password, $partner->password)) {
            $notify[] = ['error', 'Current password is incorrect'];
            return back()->withNotify($notify);
        }
        
        $partner->password = Hash::make($request->password);
        $partner->save();
        
        $notify[] = ['success', 'Password updated successfully'];
        return redirect()->route('partner.password')->withNotify($notify);
    }
}
