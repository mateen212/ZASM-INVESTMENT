<?php

namespace App\Http\Controllers\Admin;

use App\Models\StopWaterfallHurdle;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\WaterFall;
use App\Models\GpProvision;
use App\Http\Controllers\Controller;

class WaterFallController extends Controller
{
    public function storeWaterfall(Request $request, Deal $deal)
    {
        $validate = Validator::make($request->all(), [
            'waterfall' => 'required',
            'waterfall.waterfall_name' => 'required',
            'waterfall.hurdles' => 'required|array|min:1',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }

        if (array_key_exists('id', $request->waterfall)) {
            // Update existing waterfall
            $waterfall = $deal->waterfalls()->where('id', $request->waterfall['id'])->get()->first();
            $waterfall->update($request->waterfall);
            $waterfall->hurdles()->delete();
        } else {
            // Create a new waterfall
            $waterfall = $deal->waterfalls()->create($request->waterfall);
        }

        foreach ($request->waterfall['hurdles'] as $hurdle) {
            $this->storeHurdlesRecursively($waterfall, $hurdle);
        }


        return response()->json(['success' => 'Waterfall created successfully', 'waterfall' => $waterfall], 200);
    }

    /**
     * Store Hurdrles Recursively
     * @param $waterfall
     * @param $hurdle
     * @param $parent_id
     */
    public function storeHurdlesRecursively($waterfall, $hurdle, $parentId = null, $pathNumber = null, $sortOrder = 1)
    {
        if (isset($hurdle['included_class'])) {
            if (
                $hurdle['included_class'] == null
                || $hurdle['included_class'] == ''
                || $hurdle['included_class'] == []
                || $hurdle['included_class'] == 'null'
            ) {
                $hurdle['included_class'] = [];
            } else {
                $hurdle['included_class'] = $hurdle['included_class'];
            }
        } else {
            $hurdle['included_class'] = [];
        }

        $hurdle['parent_id'] = $parentId;
        $hurdle['path'] = $pathNumber;
        $hurdle['sort_order'] = $sortOrder;

        $createdHurdle = $waterfall->hurdles()->create($hurdle);
        $createdHurdle = $createdHurdle->toArray();

        if ($hurdle['stop_hurdle'] !== null) {
            $stopHurdle = $hurdle['stop_hurdle'];
            $stopHurdle['waterfall_hurdle_id'] = $createdHurdle['id'];
            $createdHurdle['stop_hurdle'] = StopWaterfallHurdle::create($stopHurdle);
        }
        // dd($stopHurdle);
        if ($hurdle['gp_provision'] !== null) {
            $gpProvision = $hurdle['gp_provision'];
            $gpProvision['waterfall_hurdle_id'] = $createdHurdle['id'];
            $createdHurdle['gp_provision'] = GpProvision::create($gpProvision);
        }


        if ($hurdle['hurdle_type'] == 'split' && isset($hurdle['paths']) && $hurdle['paths'] != null) {
            foreach ($hurdle['paths'] as $index => $path) {
                $pathNumber = $index + 1;
                $sort = 1;
                foreach ($path['hurdles'] as $pathHurdle) {
                    $this->storeHurdlesRecursively($waterfall, $pathHurdle, $createdHurdle['id'], $pathNumber, $sort);
                    $sort++;
                }
            }
        }
    }


    public function storeNewWaterfall(Request $request, Deal $deal)
    {
        $validate = Validator::make($request->all(), [
            'is_default' => 'required',
            'waterfall_name' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }

        if ($request->is_default == "1") {
            $deal->waterfalls()->update(['is_default' => 0]);
        }

        $waterfall = WaterFall::find($request->waterfall_template);

        if ($waterfall !== null) {
            if ($request->is_default == "1") {
                $deal->waterfalls()->update(['is_default' => 0]);
            }
            $newWaterfall = $deal->waterfalls()->create([
                'is_default' => ($request->is_default == "1") ? 1 : 0,
                'waterfall_name' => $request->waterfall_name,
            ]);

            foreach ($waterfall->hurdles as $hurdle) {
                $newWaterfall->hurdles()->create(Arr::except($hurdle, ['id', 'waterfall_id'])->toArray());
            }
        } else {
            if ($request->is_default == "1") {
                $deal->waterfalls()->update(['is_default' => 0]);
            }
            $newWaterfall = $deal->waterfalls()->create([
                'is_default' => ($request->is_default == "1") ? 1 : 0,
                'waterfall_name' => $request->waterfall_name,
            ]);
        }

        return response()->json(['success' => 'Waterfall created successfully'], 200);
    }

    /**
     * Set Default Waterfall
     * Deal $deal
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setDefaultWaterfall(Request $request, Deal $deal)
    {
        $validate = Validator::make($request->all(), [
            'waterfall_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }

        $deal->waterfalls()->update(['is_default' => 0]);
        $deal->waterfalls()->where('id', $request->waterfall_id)->update(['is_default' => 1]);

        return response()->json(['success' => 'Default Waterfall set successfully'], 200);
    }






    /**
     * Delete a waterfall and its associated hurdles
     *
     * @param Deal $deal
     * @param WaterFall $waterfall
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Deal $deal, WaterFall $waterfall)
    {
        // Verify the waterfall belongs to this deal
        if ($waterfall->deal_id !== $deal->id) {
            return response()->json(['error' => 'Waterfall does not belong to this deal'], 403);
        }



        // Delete all hurdles first (this will cascade delete stop_hurdles and gp_provisions)
        $waterfall->hurdles()->delete();



        // Delete the waterfall
        $waterfall->delete();



        return response()->json(['success' => 'Waterfall deleted successfully'], 200);
    }






}