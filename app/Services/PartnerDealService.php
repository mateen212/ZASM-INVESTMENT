<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Admin;
use App\Models\PartnerDeal;
use App\Models\DealClass;
use App\Models\DealDocument;
use App\Models\Distribution;
use App\Models\Investment;
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class PartnerDealService
{
    /**
     * Get all deals associated with a partner
     *
     * @param int|null $partnerId The ID of the partner (admin with partner role)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPartnerDeals($partnerId = null)
    {
        try {
            // Check if the partner_deals table exists
            if (!Schema::hasTable('partner_deals')) {
                // If table doesn't exist, return empty collection
                return collect([]);
            }
            
            return PartnerDeal::where('admin_id', $partnerId)
                ->with('deal')
                ->get();
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching partner deals: ' . $e->getMessage());
            
            // Return empty collection on error
            return collect([]);
        }
    }
    
    /**
     * Get all deal IDs associated with a partner
     *
     * @param int|null $partnerId The ID of the partner (admin with partner role)
     * @return array
     */
    public function getPartnerDealIds($partnerId = null)
    {
        try {
            // Check if the partner_deals table exists
            if (!Schema::hasTable('partner_deals')) {
                // If table doesn't exist, return empty array
                return [];
            }
            
            return PartnerDeal::where('admin_id', $partnerId)
                ->pluck('deal_id')
                ->toArray();
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching partner deal IDs: ' . $e->getMessage());
            
            // Return empty array on error
            return [];
        }
    }
    
    /**
     * Associate a deal with a partner
     *
     * @param int $dealId The ID of the deal
     * @param int $partnerId The ID of the partner (admin with partner role)
     * @return bool
     */
    public function associateDealWithPartner($dealId, $partnerId, $role = null)
    {
        try {
            PartnerDeal::create([
                'deal_id' => $dealId,
                'admin_id' => $partnerId,
                'role' => $role
            ]);
            
            return true;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to associate deal with partner: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove association between a deal and a partner
     *
     * @param int $dealId The ID of the deal
     * @param int|null $partnerId The ID of the partner (admin with partner role)
     * @return bool
     */
    public function removeDealFromPartner($dealId, $partnerId = null)
    {
        try {
            $query = PartnerDeal::where('deal_id', $dealId);
            
            if ($partnerId) {
                $query->where('admin_id', $partnerId);
            }
            
            $query->delete();
            
            return true;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to remove deal from partner: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all deals that can be assigned to a partner
     *
     * @param int $partnerId The ID of the partner (admin with partner role)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAssignableDeals($partnerId)
    {
        $assignedDealIds = PartnerDeal::where('admin_id', $partnerId)
            ->pluck('deal_id')
            ->toArray();
        
        return Deal::whereNotIn('id', $assignedDealIds)->get();
    }
    
    /**
     * Check if a deal belongs to a specific partner
     *
     * @param int $dealId The ID of the deal
     * @param int $partnerId The ID of the partner (admin with partner role)
     * @return bool
     */
    public function isDealOwnedByPartner($dealId, $partnerId)
    {
        return PartnerDeal::where('deal_id', $dealId)
            ->where('admin_id', $partnerId)
            ->exists();
    }
    
    /**
     * Get the partner ID associated with a deal
     *
     * @param int $dealId The ID of the deal
     * @return int|null The partner ID or null if not found
     */
    public function getPartnerIdForDeal($dealId)
    {
        $partnerDeal = PartnerDeal::where('deal_id', $dealId)->first();
        return $partnerDeal ? $partnerDeal->admin_id : null;
    }
    
    /**
     * Check if a partner has access to a specific deal
     *
     * @param int $partnerId
     * @param int $dealId
     * @return bool
     */
    public function checkPartnerHasDealAccess($partnerId = null, $dealId = null)
    {
        try {
            if (!Schema::hasTable('partner_deals')) {
                return false;
            }
            
            $partnerDeal = PartnerDeal::where('admin_id', $partnerId)
                ->where('deal_id', $dealId)
                ->first();
                
            return $partnerDeal ? true : false;
        } catch (\Exception $e) {
            Log::error('Error checking partner deal access: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a partner has access to a specific deal class
     *
     * @param int $partnerId
     * @param int $classId
     * @return bool
     */
    public function checkPartnerHasClassAccess($partnerId, $classId)
    {
        try {
            $class = DealClass::find($classId);
            if (!$class) {
                return false;
            }
            
            return $this->checkPartnerHasDealAccess($partnerId, $class->deal_id);
        } catch (\Exception $e) {
            Log::error('Error checking partner class access: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a partner has access to a specific deal document
     *
     * @param int $partnerId
     * @param int $documentId
     * @return bool
     */
    public function checkPartnerHasDocumentAccess($partnerId, $documentId)
    {
        try {
            $document = DealDocument::find($documentId);
            if (!$document) {
                return false;
            }
            
            return $this->checkPartnerHasDealAccess($partnerId, $document->deal_id);
        } catch (\Exception $e) {
            Log::error('Error checking partner document access: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a partner has access to a specific distribution
     *
     * @param int $partnerId
     * @param int $distributionId
     * @return bool
     */
    public function checkPartnerHasDistributionAccess($partnerId, $distributionId)
    {
        try {
            $distribution = Distribution::find($distributionId);
            if (!$distribution) {
                return false;
            }
            
            return $this->checkPartnerHasDealAccess($partnerId, $distribution->deal_id);
        } catch (\Exception $e) {
            Log::error('Error checking partner distribution access: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a partner has access to a specific investment
     *
     * @param int $partnerId
     * @param int $investmentId
     * @return bool
     */
    public function checkPartnerHasInvestmentAccess($partnerId, $investmentId)
    {
        try {
            $investment = Investment::find($investmentId);
            if (!$investment) {
                return false;
            }
            
            return $this->checkPartnerHasDealAccess($partnerId, $investment->deal_id);
        } catch (\Exception $e) {
            Log::error('Error checking partner investment access: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all deal classes associated with a partner
     *
     * @param int $partnerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPartnerDealClasses($partnerId)
    {
        try {
            $dealIds = $this->getPartnerDealIds($partnerId);
            return DealClass::whereIn('deal_id', $dealIds)->get();
        } catch (\Exception $e) {
            Log::error('Error fetching partner deal classes: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get all documents associated with a partner's deals
     *
     * @param int $partnerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPartnerDealDocuments($partnerId)
    {
        try {
            $dealIds = $this->getPartnerDealIds($partnerId);
            return DealDocument::whereIn('deal_id', $dealIds)->get();
        } catch (\Exception $e) {
            Log::error('Error fetching partner deal documents: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get all distributions associated with a partner's deals
     *
     * @param int $partnerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPartnerDistributions($partnerId)
    {
        try {
            $dealIds = $this->getPartnerDealIds($partnerId);
            return Distribution::whereIn('deal_id', $dealIds)->get();
        } catch (\Exception $e) {
            Log::error('Error fetching partner distributions: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get all investments associated with a partner's deals
     *
     * @param int $partnerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPartnerInvestments($partnerId)
    {
        try {
            $dealIds = $this->getPartnerDealIds($partnerId);
            return Investment::whereIn('deal_id', $dealIds)->get();
        } catch (\Exception $e) {
            Log::error('Error fetching partner investments: ' . $e->getMessage());
            return collect([]);
        }
    }
}
