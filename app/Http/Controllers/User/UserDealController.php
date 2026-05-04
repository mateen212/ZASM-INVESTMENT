<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;


use App\Models\Deal;
use Illuminate\Http\Request;

class UserDealController extends Controller
{
    public function mydeals()
    {
        $pageTitle = 'My Deals';
        // Retrieve all deals or filter as needed
        $user = auth()->user();
        $investor = $user->investor;

        if($investor !== null){
            $investments = $user->investor->investments()->with('class')->get();
        }else{
            $investments = [];
        }
        
        $deals = [];
        foreach ($investments as $investment) {
            $deal = $investment->deal;
            $invesors = [];
            foreach ($deal->investments as $dealInvestment) {
                $invesors[] = $dealInvestment->investor->investor_fname . ' ' . $dealInvestment->investor->investor_lname;
            }
            $deal->investors = implode(', ', $invesors);

            $deal->total_investment_amount = $deal->investments->sum(function ($investment) {
                return (float) preg_replace('/[^\d.]/', '', $investment->investment_amount);
            });
            
            $deal->total_distribution_amount = $deal->distributions->sum(function ($distribution) {
                return (float) preg_replace('/[^\d.]/', '', $distribution->amount);
            });

            $deals[] = $deal->id;
        }

        $deals = array_unique($deals);
        // Fetch the deals based on the unique IDs
        $deals = Deal::whereIn('id', $deals)
            ->with(['classes', 'buckets', 'assets', 'offerings', 'waterfalls'])
            ->get();
        // Return the view with the deals data
        return view('Template::user.deals.mydeals', compact(
            'deals',
            'pageTitle',
            'investor',
            'investments'
        ));
    }
}
