<?php

namespace App\Http\Controllers\Partner\Auth;

use App\Http\Controllers\Controller;
use App\Lib\PartnerSocialLogin;
use Exception;

class SocialiteController extends Controller
{
    public function socialLogin($provider)
    {
        $socialLogin = new PartnerSocialLogin($provider);
        return $socialLogin->redirectDriver();
    }

    public function callback($provider)
    {
        try {
            $socialLogin = new PartnerSocialLogin($provider);
            return $socialLogin->login();
        } catch (Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return to_route('partner.login')->withNotify($notify);
        }
    }
}
