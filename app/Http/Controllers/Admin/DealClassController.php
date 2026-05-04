<?php

namespace App\Http\Controllers\Admin;
use App\Models\Deal;
use App\Models\WaterFall;
use App\Models\Investor;
use App\Models\InvestorProfile;
use App\Models\Offering;
use App\Models\Asset;
use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Tag;
use App\Models\User;
use App\Models\DealClass;
class DealClassController extends Controller
{
    public function showClass(Deal $deal, DealClass $class)
    {
        $pageTitle = $deal->name;
        // dd($class);       
        $investors = Investor::with('investor_profiles')->get();
        $profiles = InvestorProfile::all();
        $investments = Investment::all();
        $pageTitle = $deal->name;
        $class->load(
            'investments',
        );
        $userId = auth('admin')->id();

        $investor_tags = Tag::investorTags($userId)->pluck('name');
        $investment_tags = Tag::investmentTags($userId)->pluck('name');

        return view('admin.deals.showClasses', compact('pageTitle', 'deal', 'class', 'investment_tags', 'investor_tags', 'investors', 'profiles', 'investments'));
    }
   
    
}