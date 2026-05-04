<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\PartnerDealService;
use Illuminate\Support\Facades\Auth;
use App\Models\Deal;

class PartnerDealAccess
{
    /**
     * The partner deal service instance.
     *
     * @var \App\Services\PartnerDealService
     */
    protected $partnerDealService;

    /**
     * Create a new middleware instance.
     *
     * @param  \App\Services\PartnerDealService  $partnerDealService
     * @return void
     */
    public function __construct(PartnerDealService $partnerDealService)
    {
        $this->partnerDealService = $partnerDealService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip this middleware if the user is not a partner (has other admin roles)
        if (!Auth::guard("admin")->check() || !Auth::guard("admin")->user()->hasRole("partner")) {
            return $next($request);
        }

        $partnerId = Auth::guard("admin")->id();
        
        // Handle deal listing routes - both admin.deals.index and partner.deals.index
        if (in_array($request->route()->getName(), ['admin.deals.index', 'partner.deals.index'])) {
            // Get the partner's deal IDs
            $partnerDealIds = $this->partnerDealService->getPartnerDealIds($partnerId);
            
            // Share the partner's deal IDs with the controller
            $request->attributes->add(['partner_deal_ids' => $partnerDealIds]);
            
            return $next($request);
        }
        
        // For routes that involve a specific deal
        $dealId = null;
        
        // Try to get the deal ID from the route parameter
        if ($request->route("deal")) {
            // Handle both Deal model instances and direct IDs
            if ($request->route("deal") instanceof Deal) {
                $dealId = $request->route("deal")->id;
            } else {
                $dealId = $request->route("deal");
            }
        } else if ($request->input("deal_id")) {
            $dealId = $request->input("deal_id");
        }
        
        // If no deal ID is found in the request, proceed
        if (!$dealId) {
            return $next($request);
        }
        
        // Check if the deal belongs to the partner
        if (!$this->partnerDealService->checkPartnerHasDealAccess($partnerId, $dealId)) {
            abort(403, "You do not have access to this deal.");
        }
        
        return $next($request);
    }
}
