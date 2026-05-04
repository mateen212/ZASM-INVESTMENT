<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Investment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DealControllerUpdate extends Controller
{
    /**
     * Display a listing of the deals.
     * This method is enhanced to handle partner access via middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $pageTitle = 'Deals';
        
        // Start with a base query
        $query = Deal::query();
        
        // Check if this is a partner accessing deals via the middleware
        if ($request->attributes->has('partner_deal_ids')) {
            $partnerDealIds = $request->attributes->get('partner_deal_ids');
            $query->whereIn('id', $partnerDealIds);
        }
        
        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Apply sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);
        
        // Get deals with pagination
        $deals = $query->paginate(10);
        
        // Get investor counts for each deal
        $investorCounts = [];
        foreach ($deals as $deal) {
            $investorCount = Investment::where('deal_id', $deal->id)
                ->distinct('investor_id')
                ->count('investor_id');
            $investorCounts[$deal->id] = $investorCount;
        }
        
        // Determine which view to use based on the URL
        if (str_starts_with($request->path(), 'partner')) {
            return view('partner.deals.index', compact('deals', 'pageTitle', 'investorCounts'));
        }
        
        // Return the admin view with the deals data
        return view('admin.deals.index', compact('deals', 'pageTitle', 'investorCounts'));
    }
}
