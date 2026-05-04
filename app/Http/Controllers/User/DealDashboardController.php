<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;

class DealDashboardController extends Controller
{
    public function dashboard2()
    {
        $pageTitle = 'My Deal Dashboard';
        // Retrieve all deals or filter as needed
        $deals = Deal::all();
        // Return the view with the deals data
        return view('Template::user.deals.dashboards.dashboard2', compact('deals', 'pageTitle'));
    }
}
