<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Offering;
use App\Models\Deal;

use App\Http\Controllers\Controller;

class AssetsController extends Controller
{
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
          //  'name' => 'required',
           // 'property_type' => 'required',
           // 'deal_id' => 'required',
           // 'property_images' => 'nullable',
        //    'property_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }
    
        // Create a new asset
        $asset = Asset::create($request->all());
    
        // Upload the images
        if ($request->hasFile('property_images')) {
            foreach ($request->file('property_images') as $image) {
                $name = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('assets_images/'.$asset->id), $name);
                $asset->assetMedia()->create([
                    'media_url' => 'assets_images/'.$asset->id.'/'.$name,
                    'media_type' => 'image',
                    'media_description' => 'Property Image',
                ]);
            }
        }
    
        return response()->json(['success' => 'Asset created successfully', 
            'asset' => $asset
        ], 200);
    }

    public function index()
    {
        $pageTitle = 'Assets';
        // Retrieve all deals or filter as needed
        $deals = Deal::paginate(10);

        // Return the view with the deals data
        return view('Template::user.assets.index', compact('deals', 'pageTitle'));
    }


    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset deleted successfully.'
        ]);
    }

    /**
     * UPDATE ASSETS for offering
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOfferingAssets(Request $request, Offering $offering)
    {
        $validate = Validator::make($request->all(), [
            'assets' => 'required|array',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 421);
        }

        $existingAssets = $offering->assets()->pluck('assets.id')->toArray();
        $newAssets = array_column($request->assets, 'id');

        // Delete Assets not in the new list
        $deleteAssets = array_diff($existingAssets, $newAssets);
        if(count($deleteAssets) > 0){
            $offering->assets()->detach($deleteAssets);
            Asset::destroy($deleteAssets);
        }
        // Update Assets by id
        foreach ($request->assets as $asset) {
            if(isset($asset['id'])){
                Asset::find($asset['id'])->update($asset);

            }else{
                // Create new Asset
                $newAsset = Asset::create(array_merge($asset, ['deal_id' => $offering->deal_id]));
                $offering->assets()->attach($newAsset->id);
            }
        }
        $assets = $offering->assets()->get();
        return response()->json(['success' => 'Assets updated successfully', 'assets' => $assets], 200);

    }

    /**
     * UPdate Asset
     * @param Request $request
     * @param Asset $asset
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateAsset(Request $request, Asset $asset)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'country' => 'required',
            'property_type' => 'required',
            'property_class' => 'required',
            'number_of_units' => 'required',
            'type_of_units' => 'required',
            'deal_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $asset->update($request->all());

        return response()->json(['success' => 'Asset updated successfully', 'asset' => $asset], 200);
    }

    /**
     * Uplaod Asset Media
     * @param Request $request
     * @param Asset $asset
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function uploadAssetMedia(Request $request, Asset $asset)
    {
        $validator = Validator::make($request->all(), [
            'media' => 'required|array',
            'media.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        foreach ($request->file('media') as $media) {
            $name = time() . '_' . $media->getClientOriginalName();
            $media->move(public_path('assets_images/'.$asset->id), $name);
            $asset->assetMedia()->create([
                'media_url' => 'assets_images/'.$asset->id.'/'.$name,
                'media_type' => 'image',
                'media_description' => 'Property Image',
            ]);
        }

        $asset_media = $asset->assetMedia()->get();

        return response()->json([
            'success' => 'Asset media uploaded successfully',
            'asset_media' => $asset_media
        ], 200);
    }

    /**
     * Delete Asset Media
     * @param Asset $asset
     * @param $media_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAssetMedia(Request $request, Asset $asset)
    {
        $validate = Validator::make($request->all(), [
            'media_id' => 'required|exists:asset_media,id',
        ]);
        $assetMedia = $asset->assetMedia()->find($request->media_id);
        if($assetMedia){
            // remove file from storage
            $file_path = public_path($assetMedia->media_url);
            if(file_exists($file_path)){
                unlink($file_path);
            }
            $assetMedia->delete();
            $asset_media = $asset->assetMedia()->get();
            return response()->json([
                'success' => 'Asset media deleted successfully',
                'asset_media' => $asset_media
            ], 200);
        }
        return response()->json(['error' => 'Asset media not found'], 404);
    }

}
