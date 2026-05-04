<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Services\PartnerDealService;

class PartnerDealScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // Only apply this scope if the user is authenticated as admin and has the partner role
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('partner')) {
            $partnerId = Auth::guard('admin')->id();
            $partnerDealIds = app(PartnerDealService::class)->getPartnerDealIds($partnerId);
            
            // If the model is a Deal, filter by ID
            if ($model->getTable() === 'deals') {
                $builder->whereIn('id', $partnerDealIds);
            }
            // For other deal-related models, filter by deal_id
            elseif (in_array('deal_id', $model->getFillable()) || in_array('deal_id', array_keys($model->toArray()))) {
                $builder->whereIn('deal_id', $partnerDealIds);
            }
        }
    }
}
