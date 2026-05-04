<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\Extension;
use Illuminate\Support\Facades\Schema;

class Captcha{

    /*
    |--------------------------------------------------------------------------
    | Captcha
    |--------------------------------------------------------------------------
    |
    | This class is using verify and show captcha. Here is currently available
    | custom captcha and google recaptcha2. Developer can use verify method
    | to verify all captcha or can use separately if required
    |
    */

    /**
    * Google recaptcha2 script
    *
    * @return string
    */
    public static function reCaptcha(){
        try {
            if (!Schema::hasTable('extensions')) {
                return null;
            }
            $reCaptcha = Extension::where('act', 'google-recaptcha2')->where('status', Status::ENABLE)->first();
            return $reCaptcha ? (method_exists($reCaptcha, 'generateScript') ? $reCaptcha->generateScript() : null) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
    * Custom captcha script
    *
    * @return string
    */
    public static function customCaptcha($width = '100%', $height = 46, $bgColor = '#003'){
        try {
            if (!Schema::hasTable('extensions')) {
                return 0;
            }

            $textColor = '#'.gs('base_color');
            $captcha = Extension::where('act', 'custom-captcha')->where('status', Status::ENABLE)->first();
            if (!$captcha) {
                return 0;
            }
        } catch (\Exception $e) {
            return 0;
        }
        $code = rand(100000, 999999);
        $char = str_split($code);
        $ret = '<link href="https://fonts.googleapis.com/css?family=Henny+Penny&display=swap" rel="stylesheet">';
        $ret .= '<div style="height: ' . $height . 'px; line-height: ' . $height . 'px; width:' . $width . '; text-align: center; background-color: ' . $bgColor . '; color: ' . $textColor . '; font-size: ' . ($height - 20) . 'px; font-weight: bold; letter-spacing: 20px; font-family: \'Henny Penny\', cursive;  -webkit-user-select: none; -moz-user-select: none;-ms-user-select: none;user-select: none;  display: flex; justify-content: center;">';
        foreach ($char as $value) {
            $ret .= '<span style="    float:left;     -webkit-transform: rotate(' . rand(-60, 60) . 'deg);">' . $value . '</span>';
        }
        $ret .= '</div>';
        $captchaSecret = '';
        try {
            $captchaSecret = hash_hmac('sha256', $code, optional($captcha->shortcode->random_key)->value ?? '');
        } catch (\Exception $e) {
            $captchaSecret = '';
        }
        $ret .= '<input type="hidden" name="captcha_secret" value="' . $captchaSecret . '">';
        return $ret;

    }

    /**
    * Verify all captcha
    *
    * @return boolean
    */
    public static function verify(){
        $gCaptchaPass = self::verifyGoogleCaptcha();
        $cCaptchaPass = self::verifyCustomCaptcha();
        if ($gCaptchaPass && $cCaptchaPass) {
            return true;
        }
        return false;
    }

    /**
    * Verify google recaptcha2
    *
    * @return boolean
    */
    public static function verifyGoogleCaptcha(){
        $pass = true;
        try {
            if (!Schema::hasTable('extensions')) {
                return true;
            }
            $googleCaptcha = Extension::where('act', 'google-recaptcha2')->where('status', Status::ENABLE)->first();
            if ($googleCaptcha) {
                $secret = optional($googleCaptcha->shortcode->secret_key)->value ?? '';
                $response = request()['g-recaptcha-response'] ?? '';
                $resp = json_decode(@file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$response."&remoteip=".getRealIP()), true);
                if (!($resp['success'] ?? false)) {
                    $pass = false;
                }
            }
        } catch (\Exception $e) {
            return true;
        }
        return $pass;
    }

    /**
    * Verify custom captcha
    *
    * @return boolean
    */
    public static function verifyCustomCaptcha(){
        $pass = true;
        try {
            if (!Schema::hasTable('extensions')) {
                return true;
            }
            $customCaptcha = Extension::where('act', 'custom-captcha')->where('status', Status::ENABLE)->first();
            if ($customCaptcha) {
                $secretKey = optional($customCaptcha->shortcode->random_key)->value ?? '';
                $captchaSecret = hash_hmac('sha256', request()->captcha ?? '', $secretKey);
                if ($captchaSecret != (request()->captcha_secret ?? '')) {
                    $pass = false;
                }
            }
        } catch (\Exception $e) {
            return true;
        }
        return $pass;
    }

}
