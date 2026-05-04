<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PermissionChecker;

class BaseController extends Controller
{
    use PermissionChecker;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // All admin controllers require authentication
        $this->middleware('auth:admin');
    }
}
