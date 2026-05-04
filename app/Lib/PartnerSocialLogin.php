<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Admin;
use App\Models\UserLogin;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Socialite;

class PartnerSocialLogin
{
    private $provider;

    public function __construct($provider)
    {
        $this->provider = $provider;
        $this->configuration();
    }

    public function redirectDriver()
    {
        return Socialite::driver($this->provider)->redirect();
    }

    private function configuration()
    {
        $provider      = $this->provider;
        $configuration = gs('socialite_credentials')->$provider;

        Config::set('services.' . $provider, [
            'client_id'     => $configuration->client_id,
            'client_secret' => $configuration->client_secret,
            'redirect'      => route('partner.social.login.callback', $provider),
        ]);
    }

    public function login()
    {
        $provider = $this->provider;
        $user = Socialite::driver($provider)->user();

        $partnerData = Admin::where('provider_id', $user->id)->where('provider', $provider)->first();

        if (!$partnerData) {
            $emailExists = Admin::where('email', @$user->email)->exists();
            if ($emailExists) {
                throw new Exception('Email already exists');
            }
            $partnerData = $this->createPartner($user, $provider);
        }

        Auth::guard('admin')->login($partnerData);
        $this->loginLog($partnerData);
        
        return to_route('partner.dashboard');
    }

    private function createPartner($user, $provider)
    {
        // Generate a username from email
        $email = $user->email;
        $username = explode('@', $email)[0];
        $baseUsername = $username;
        $i = 1;
        
        // Ensure username is unique
        while (Admin::where('username', $username)->exists()) {
            $username = $baseUsername . $i;
            $i++;
        }

        $partner = new Admin();
        $partner->provider_id = $user->id;
        $partner->provider = $provider;
        $partner->email = $user->email;
        $partner->name = $user->name;
        $partner->username = $username;
        $partner->password = Hash::make(getTrx(8));
        $partner->status = 'pending'; // Partners need approval
        $partner->save();

        // Assign partner role
        $partner->assignRole('partner');

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $partner->id;
        $adminNotification->title = 'New partner registered via ' . ucfirst($provider);
        $adminNotification->click_url = urlPath('admin.partners.detail', $partner->id);
        $adminNotification->save();

        return $partner;
    }

    private function loginLog($partner)
    {
        //Login Log Create
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        //Check exist or not
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',', $info['long']);
            $userLogin->latitude =  @implode(',', $info['lat']);
            $userLogin->city =  @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $partner->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();
    }
}
