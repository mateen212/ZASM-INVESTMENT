<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Show the partner registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPartnerRegistrationForm()
    {
        $pageTitle = "Partner Registration";
        return view('admin.auth.partner_register', compact('pageTitle'));
    }

    /**
     * Handle a partner registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerPartner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6|confirmed',
            'company_name' => 'required|string|max:255',
            'company_website' => 'nullable|url',
            'contact_number' => 'required|string|max:20',
            'invited_by' => 'nullable|string',
            'agree' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if(!verifyCaptcha()){
            $notify[] = ['error','Invalid captcha provided'];
            return back()->withNotify($notify)->withInput();
        }

        // Check if this is an invited partner
        $status = 'pending';
        if ($request->invited_by) {
            // In the future, we'll implement invitation validation here
            // For now, all registrations will be pending
        }

        // Generate a username from email
        $username = explode('@', $request->email)[0];
        $baseUsername = $username;
        $i = 1;
        
        // Ensure username is unique
        while (Admin::where('username', $username)->exists()) {
            $username = $baseUsername . $i;
            $i++;
        }

        $partner = new Admin();
        $partner->name = $request->name;
        $partner->username = $username;
        $partner->email = $request->email;
        $partner->password = Hash::make($request->password);
        $partner->company_name = $request->company_name;
        $partner->company_website = $request->company_website;
        $partner->contact_number = $request->contact_number;
        $partner->invited_by = $request->invited_by;
        $partner->status = $status;
        $partner->save();

        // Assign partner role
        $partner->assignRole('partner');

        $notify[] = ['success', 'Your partner account has been created successfully. An administrator will review your application.'];
        return redirect()->route('partner.login')->withNotify($notify);
    }
}
