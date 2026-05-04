<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiIntegration;
use Illuminate\Http\Request;

class ApiIntegrationsController extends Controller
{
    public function index()
    {
        $pageTitle = 'API Integrations';
        $apis = ApiIntegration::all();
        return view('admin.apis.index', compact('pageTitle', 'apis'));
    }
    
    public function edit($code)
    {
        $pageTitle = 'Edit API Integration';
        $api = ApiIntegration::where('code', $code)->firstOrFail();
        return view('admin.apis.edit', compact('pageTitle', 'api'));
    }
    
    public function update(Request $request, $code)
    {
        $api = ApiIntegration::where('code', $code)->firstOrFail();
        
        $credentials = json_decode(json_encode($api->credentials), true);
        
        foreach ($credentials as $key => $credential) {
            if ($request->has($key)) {
                $credentials[$key]['value'] = $request->$key;
            }
        }
        
        $api->credentials = $credentials;
        $api->status = $request->status ? 1 : 0;
        $api->save();
        
        $notify[] = ['success', $api->name . ' settings updated successfully'];
        return back()->withNotify($notify);
    }
}
