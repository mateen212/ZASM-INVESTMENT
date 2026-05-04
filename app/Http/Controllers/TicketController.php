<?php

namespace App\Http\Controllers;

use App\Traits\SupportTicketManager;

class TicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        parent::__construct();
        $this->userType = 'user';
        $this->column = 'user_id';
        $this->user = auth()->user();
    }
}
