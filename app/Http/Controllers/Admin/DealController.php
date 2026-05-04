<?php

namespace App\Http\Controllers\Admin;

use App\Models\Funding;
use App\Models\OfferingFundingInfo;
use App\Models\PersonalSetting;
use App\Models\StopWaterfallHurdle;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\WaterFall;
use App\Models\Investor;
use App\Models\InvestorProfile;
use App\Models\Offering;
use App\Models\Asset;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\KeyMetric;
use App\Models\DealSenderAddress;
use App\Models\DealBankAccount;
use App\Models\DealCheckSetting;
use App\Models\Investment;
use App\Models\Tag;
use App\Models\Manageoffering;
use App\Models\User;
use App\Models\GPProvision;
use App\Models\DealAchSetting;
use App\Traits\Utills;
use App\Models\DealAddress;
use App\Models\BeneficialOwnerDetail;
use App\Models\DealClass;
use App\Models\Member;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Services\StripeACHService;

class DealController extends Controller
{

    use Utills;

    protected $stripeService;

    public function __construct(StripeACHService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function index(Request $request)
    {
        $pageTitle = 'Deals';

        // Start with a base query
        $query = Deal::query();

        // Check if this is a partner accessing deals via the middleware
        if ($request->attributes->has('partner_deal_ids')) {
            $partnerDealIds = $request->attributes->get('partner_deal_ids');
            $query->whereIn('id', $partnerDealIds);
        }

        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Apply sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);

        // Get deals with pagination
        $deals = $query->paginate(10);

        // Get investor counts for each deal
        $investorCounts = [];
        foreach ($deals as $deal) {
            $investorCount = Investment::where('deal_id', $deal->id)
                ->distinct('investor_id')
                ->count('investor_id');
            $investorCounts[$deal->id] = $investorCount;
            // Debug achsettings for each deal
            \Log::info('Deal ID: ' . $deal->id, [
                'has_achsettings' => !is_null($deal->achsettings),
                'entity_name' => $deal->achsettings ? $deal->achsettings->entity_name : null,
            ]);
        }
        $query = Deal::with('achsettings');
        // Determine which view to use based on the URL
        if (str_starts_with($request->path(), 'partner')) {
            return view('partner.deals.index', compact('deals', 'pageTitle', 'investorCounts'));
        }

        // Return the admin view with the deals data
        return view('admin.deals.index', compact('deals', 'pageTitle', 'investorCounts'));
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'deal_stage' => 'required',
            'sec_type' => 'required',
            // 'owning_entity_name' => 'required',

        ], [
            'name' => 'The deal name field is required.',
            'deal_stage' => 'The deal stage field is required.',
            'sec_type' => 'The deal Sec type field is required',
            'owning_entity_name' => 'The entity name field is required.',
        ]);


        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        // Create a new deal
        $deal = Deal::create(array_merge($request->all(), ['user_id' => auth('admin')->user()->id]));

        $deal->owningEntityDetails()->create([
            'owning_entity_name' => $request->owning_entity_name,
            'executive_name' => $request->executive_name ?? '',
            'executive_title' => $request->executive_title ?? '',
            'jurisdiction' => $request->jurisdiction ?? '',
            'taxpayer_id' => $request->taxpayer_id ?? '',
            'email' => $request->email ?? '',
            'date_formed' => $request->date_formed ?? null,
            'address_1' => $request->address_1 ?? '',
            'address_2' => $request->address_2 ?? '',
            'city' => $request->city ?? '',
            'province' => $request->province ?? '',
            'postal_code' => $request->postal_code ?? '',
            'country' => $request->country ?? '',
        ]);

        $sections = [
            ['name' => 'Default', 'can_edit' => true],
            ['name' => 'Completed addendums', 'can_edit' => false],
            ['name' => 'Accreditation letters', 'can_edit' => false],
            ['name' => 'Offering documents', 'can_edit' => false],
            ['name' => 'Updates attachments', 'can_edit' => false],
            ['name' => 'FMV forms', 'can_edit' => false],
        ];

        foreach ($sections as $section) {
            $deal->document_sections()->create($section);
        }

        $loggedInUser = auth('admin')->user();
        $deal->members()->create([
            'first_name' => $loggedInUser->name,
            'last_name' => '',
            'email_address' => $loggedInUser->email,
            'role' => 'lead sponsor',
            'status' => 1,
        ]);

        return response()->json([
            'success' => 'Deal created successfully',
            'deal' => [
                'id' => $deal->id,
                'name' => $deal->name,
                'type' => $deal->type,
            ],
            'owning_entity_detail' => $deal->owningEntityDetails
        ], 200);
    }

    public function history()
    {
        // Retrieve all deals or filter as needed
        $deals = Deal::all();

        // Return the view with the deals data
        return view('admin.deal.history', compact('deals'));
    }

    public function showSummary(Deal $deal)
    {
        $investors = Investor::with('investor_profiles')->get();
        $profiles = InvestorProfile::all();
        $pageTitle = $deal->name;
        $deal->load(
            'classes',
            'assets',
            'offerings',
            'distributions',
            'investments',
            'investments.investor',
            'investments.profile',
            'investments.class',
            'investments.offering',
            'offerings.funding_info'
        );
        $deal->load([
            'document_sections' => function ($query) {
                $query->orderBy('can_edit', 'DESC');
            },
            'document_sections.documents'
        ]);

        $partners = $deal->getAllMembers();

        // return $partners;

        $userId = auth('admin')->id();

        $investor_tags = Tag::investorTags($userId)->pluck('name');
        $investment_tags = Tag::investmentTags($userId)->pluck('name');
        // Pass any necessary data to the view if needed
        return view('admin.deals.summary', compact('pageTitle', 'deal', 'investors', 'profiles', 'investment_tags', 'investor_tags', 'partners'));

    }

    public function destroy(\App\Models\Deal $deal)
    {
        try {
            $deal->delete();
            return response()->json(['success' => 'Deal deleted successfully.'], 200);
        } catch (\Exception $e) {
            \Log::error('Error deleting deal', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while deleting the deal'], 500);
        }
    }

    public function class(Deal $deal)
    {
        $pageTitle = $deal->name;
        $classes = $deal->classes()->with('hurdles')->get();
        $buckets = $deal->buckets()->with('classes', 'classes.hurdles')->get();
        $waterfalls = $deal->waterfalls()->with('hurdles')->get();
        $waterfall = $deal->waterfalls()->with('hurdles')->where('is_default', true)->first();
        if ($waterfalls->count() > 0 && $waterfall == null) {
            $waterfall = $waterfalls->first();
            $waterfall->is_default = true;
            $waterfall->save();
        } else if ($waterfalls->count() == 0) {
            $waterfall = $deal->waterfalls()->create(['waterfall_name' => 'Default Waterfall', 'is_default' => true]);
        }

        $waterfalls = $deal->waterfalls()->with('hurdles')->get();

        foreach ($waterfalls as $d_waterfall) {
            foreach ($d_waterfall->hurdles as $hurdle) {
                $this->processHurdles($hurdle);
            }
        }

        foreach ($waterfall->hurdles as $hurdle) {
            $this->processHurdles($hurdle);
        }
        // dd($deal);

        // Pass any necessary data to the view if needed
        // return $classes;
        return view('admin.deals.editclass', compact('pageTitle', 'deal', 'classes', 'buckets', 'waterfall', 'waterfalls'));
    }

    /**
     * Process Waterfall Hurdles
     * @param Request $request
     */
    protected function processHurdles($hurdle)
    {
        if ($hurdle->has_children) {
            $paths = [];
            foreach ($hurdle->splits as $sp_index => $split) {
                $childHurdles = $hurdle->children()->where('path', $sp_index + 1)->get();
                $paths[$sp_index]['hurdles'] = $childHurdles;

                // Process each child hurdle recursively
                foreach ($childHurdles as $childHurdle) {
                    $this->processHurdles($childHurdle);
                }
            }
            $hurdle->paths = $paths;
        }
    }

    public function storeClasses(Request $request, Deal $deal)
    {
        $validate = Validator::make($request->all(), [
            // 'class_type' => 'required',
            // 'preferred_return_type' => 'required_if:class_type,Mezzanine',
        ], [
            // 'class_type' => 'The class type field is required.',
            'preferred_return_type' => 'The preferred return type field is required.',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        $classes = $request->classes;

        $existingClasses = $deal->classes()->pluck('id')->toArray();


        foreach ($classes as $class) {
            $class['deal_id'] = $deal->id;


            if (isset($class['id'])) {

                // $class['raise_amount'] = $this->moneystrToDouble($class['raise_amount']);
                // dd($class,$this->moneystrToDouble($class['raise_amount_ownership']));
                $class = $this->formatClassData($class);

                $deal->classes()->find($class['id'])->update($class);
                $req_hurdles = $class['hurdles'];
                $existingHurdles = $deal->classes()->find($class['id'])->hurdles()->pluck('id')->toArray();

                foreach ($req_hurdles as $req_hurdle) {
                    if (isset($req_hurdle['id'])) {
                        $deal->classes()->find($class['id'])->hurdles()->find($req_hurdle['id'])->update($req_hurdle);
                        continue;
                    }
                    $deal->classes()->find($class['id'])->hurdles()->create($req_hurdle);
                    // Delete the hurdles

                }
                $requestHurdlesIds = array_column($req_hurdles, 'id');
                $hurdlesToDelete = array_diff($existingHurdles, $requestHurdlesIds);
                $deal->classes()->find($class['id'])->hurdles()->whereIn('id', $hurdlesToDelete)->delete();
                continue;
            }

            $class = $this->formatClassData($class);
            $newClass = $deal->classes()->create($class);
            $req_hurdles = $class['hurdles'];
            foreach ($req_hurdles as $req_hurdle) {
                $newClass->hurdles()->create($req_hurdle);
            }
        }
        $requestClassesIds = array_column($classes, 'id');
        $classesToDelete = array_diff($existingClasses, $requestClassesIds);
        $deal->classes()->whereIn('id', $classesToDelete)->delete();

        $existingBuckets = $deal->buckets()->pluck('id')->toArray();
        if (!empty($request->buckets)) {
            foreach ($request->buckets as $bucket) {
                if (isset($bucket['id'])) {
                    $bucket = $this->moneyToDouble($bucket);
                    $deal->buckets()->find($bucket['id'])->update($bucket);
                    $req_classes = $bucket['classes'];
                    // dd($req_classes);
                    $existingClasses = $deal->buckets()->find($bucket['id'])->classes()->pluck('id')->toArray();
                    foreach ($req_classes as $req_class) {
                        if (isset($req_class['id'])) {
                            $deal->buckets()->find($bucket['id'])->classes()->find($req_class['id'])->update($req_class);
                            // Add the hurdles or update
                            $req_hurdles = $req_class['hurdles'];
                            $existingHurdles = $deal->buckets()->find($bucket['id'])->classes()->find($req_class['id'])->hurdles()->pluck('id')->toArray();

                            foreach ($req_hurdles as $req_hurdle) {
                                if (isset($req_hurdle['id'])) {
                                    $deal->buckets()->find($bucket['id'])->classes()->find($req_class['id'])->hurdles()->find($req_hurdle['id'])->update($req_hurdle);
                                    continue;
                                }
                                $deal->buckets()->find($bucket['id'])->classes()->find($req_class['id'])->hurdles()->create($req_hurdle);
                                // Delete the hurdles

                            }
                            $requestHurdlesIds = array_column($req_hurdles, 'id');
                            $hurdlesToDelete = array_diff($existingHurdles, $requestHurdlesIds);
                            $deal->buckets()->find($bucket['id'])->classes()->find($req_class['id'])->hurdles()->whereIn('id', $hurdlesToDelete)->delete();
                            continue;
                        }
                        $deal->buckets()->find($bucket['id'])->classes()->create($req_class);
                    }
                    $requestClassesIds = array_column($req_classes, 'id');
                    $classesToDelete = array_diff($existingClasses, $requestClassesIds);
                    $deal->buckets()->find($bucket['id'])->classes()->whereIn('id', $classesToDelete)->delete();

                    continue;
                }
                $bucket = $this->moneyToDouble($bucket);
                $newBucket = $deal->buckets()->create($bucket);
                foreach ($bucket['classes'] as $bclass) {
                    $newBClass = $newBucket->classes()->create($bclass);
                    if (!empty($bclass['hurdles'])) {
                        foreach ($bclass['hurdles'] as $bchurdle) {
                            $newBClass->hurdles()->create($bchurdle);
                        }
                    }
                }
            }
        }

        $requestBucketsIds = array_column($request->buckets, 'id');
        $bucketsToDelete = array_diff($existingBuckets, $requestBucketsIds);
        foreach ($bucketsToDelete as $bucketId) {
            $deal->buckets()->find($bucketId)->classes()->delete();
        }
        $deal->buckets()->whereIn('id', $bucketsToDelete)->delete();

        if ($deal->classes()->count() > 0) {
            if (!$deal->waterfalls()->where('is_basic', true)->count()) {
                $basic_waterfall = $deal->waterfalls()->create([
                    'waterfall_name' => 'Basic Waterfall (based on class hurdles)',
                    'is_basic' => true,
                    'is_default' => true,
                ]);

                $this->createBasicWaterfall($deal);

            } else {
                $basic_waterfall = $deal->waterfalls()->where('is_basic', true)->first();
                $this->createBasicWaterfall($deal);
            }
        }

        $waterfalls = $deal->waterfalls()->with('hurdles')->get();

        foreach ($waterfalls as $d_waterfall) {
            foreach ($d_waterfall->hurdles as $hurdle) {
                $this->processHurdles($hurdle);
            }
        }

        // return $waterfalls;

        return response()->json([
            'success' => 'Deal created successfully',
            'deal' => [
                'id' => $deal->id,
                'name' => $deal->name,
                'type' => $deal->type,
                'waterfalls' => $waterfalls,
            ]
        ], 200);
    }

    /**
     * Crete Basic water fall hurdles from deal Classes
     * @param Deal $deal
     * @return $waterfall
     */
    protected function createBasicWaterfall(Deal $deal)
    {
        // Remove all hurdles from the basic waterfall
        $deal->waterfalls()->where('is_basic', true)->first()->hurdles()->delete();

        // get Waterfall
        $waterfall = $deal->waterfalls()->where('is_basic', true)->first();
        // Classes with hurdles and Classes from Buckets merge
        $classes = $deal->classes()->with('hurdles')->get();
        $buckets = $deal->buckets()->with('classes', 'classes.hurdles')->get();

        foreach ($buckets as $bucket) {
            $classes->merge($bucket->classes)->flatten();
        }

        $allClasses = $classes->merge($buckets->pluck('classes')->flatten());

        // Check if any mazanine class exists
        $mezzanine = $allClasses->where('class_type', 'Mezzanine');

        if ($mezzanine->count() > 0) {
            foreach ($mezzanine as $mezz) {
                $return_type = $mezz->preferred_return_type;
                if ($return_type == 'average_return') {
                    $return_type = 'cash_on_cash';
                }
                $hurd = $waterfall->hurdles()->create([
                    'split' => null,
                    'hurdle_type' => $return_type,
                    'cumulated_return_reach' => 0,
                    'day_count' => 0,
                    'compounding_frequency' => 'Monthly',
                    'start_date' => null,
                    'end_date' => null,
                    'duration' => 0,
                    'accrues_on' => 'capital_balance',
                    'payment_towards' => 'capital_balance',
                    'payment_type_towards' => 'capital_balance',
                    'split_unpayed' => false,
                    'accrual_cadence' => 'Monthly',
                    'notes' => 'Mezzanine hurdle',
                ]);
                $hurd->included_class = ["$mezz->id"];
                $hurd->classes_values = [
                    [
                        "id" => "$mezz->id",
                        "value" => $mezz->preferred_return ?? "0%",
                    ]
                ];
                $hurd->save();
            }
        }

        // Check for buckets have distribution shares
        $bucketSplits = $buckets->filter(function ($bucket) {
            return isset($bucket->distribution_share) && !empty($bucket->distribution_share)
                && preg_replace('/[^\d.]/', '', $bucket->distribution_share) != 0;
        });

        $b_classes = collect();

        foreach ($bucketSplits as $bucket) {
            $bucket_share = preg_replace('/[^\d.]/', '', $bucket->distribution_share);
            $b_classes = $bucket->classes;

            $share_per_class = $bucket_share / $b_classes->count();
            // Update classes with distribution share
            // make clone of object to avoid reference
            $b_classes = $b_classes->map(function ($class) use ($share_per_class) {
                $class->distribution_share = $share_per_class;
                return $class;
            });
        }

        // Check distribution share for classes and buckets
        $distributionSplitClasses = $classes->filter(function ($class) {
            return isset($class->distribution_share) && !empty($class->distribution_share)
                && preg_replace('/[^\d.]/', '', $class->distribution_share) != 0;
        })->merge($b_classes);

        // dd($distributionSplitClasses->pluck('distribution_share'));

        // dd($distributionSplitClasses->pluck('distribution_share'));

        if ($distributionSplitClasses->count() == 1 && preg_replace('/[^\d.]/', '', $distributionSplitClasses->first()->distribution_share) == 100) {
            // Only one class has 100% distribution share; no split paths needed.
            $this->createSinglePathwaterfall($allClasses, $waterfall);
            // dd('In Single Path');
        } else {
            $splits = [];
            $includedClasses = [];
            $classesValues = [];

            foreach ($distributionSplitClasses as $splitClass) {
                // Assuming distribution_share is stored as a percentage string, e.g., "50%"
                $shareValue = $splitClass->distribution_share;
                $numericShare = preg_replace('/[^\d.]/', '', $shareValue);
                $splits[] = [
                    'value' => $shareValue,
                ];
            }

            // Create a hurdle for split distribution shares in the basic waterfall
            $main_split_hurdle = $waterfall->hurdles()->create([
                'split' => 'Yes',
                'hurdle_type' => 'split',
                'splits' => $splits,
                'included_class' => $includedClasses,
                'classes_values' => $classesValues,
                'cumulated_return_reach' => 0,
                'day_count' => 0,
                'compounding_frequency' => 'Monthly',
                'start_date' => null,
                'end_date' => null,
                'duration' => 0,
                'accrues_on' => 'capital_balance',
                'payment_towards' => 'capital_balance',
                'payment_type_towards' => 'capital_balance',
                'split_unpayed' => false,
                'accrual_cadence' => 'Monthly',
                'notes' => 'Distribution share split hurdle',
            ]);

            // create child hurldes for each split
            $pathCount = 0;
            foreach ($distributionSplitClasses as $key => $splitClass) {
                $this->createSplitPathwaterfall($splitClass, $pathCount, $waterfall, $main_split_hurdle);
                $pathCount++;
            }
        }

        // Add Split Hurdles at the end
        // TODO : Add split hurdles at the end based on classes

        // Create a hurdle for each class
    }

    /**
     * Create Split Path hurdles for diffrent distribution share
     * @param Collection $allClasses
     * @param Waterfall $waterfall
     * @return $waterfall
     */
    protected function createSplitPathwaterfall($splitClass, $key, $waterfall, $parent)
    {
        $lp_classes = collect();
        if ($splitClass->hurdles->count()) {
            foreach ($splitClass->hurdles as $hurdle) {
                $lpCopy = clone $splitClass; // Clone the lp instance
                $lpCopy['hurdle'] = $hurdle;
                $lp_classes->push($lpCopy);
            }
        } else {
            $lpCopy = clone $splitClass; // Clone the lp instance
            $lpCopy['hurdle'] = null;
            $lp_classes->push($lpCopy);
        }
        $sort_order = 1;
        foreach ($lp_classes as $lp_class) {

            if ($lp_class['hurdle'] == null) {

                $splitClassId = $lp_class->id;
                $splitValue = preg_replace('/[^\d.]/', '', $lp_class->distribution_share);
                $splitValue = (int) $splitValue ?? 0;
                // dd($splitClass);
                $split_hurdle = $parent->children()->create([
                    'split' => 'No',
                    'hurdle_type' => 'cash_on_cash',
                    'path' => $key + 1,
                    'sort_order' => $sort_order,
                    'included_class' => [$splitClassId],
                    'classes_values' => [
                        [
                            "id" => $splitClassId,
                            "value" => $splitValue . '%',
                        ]
                    ],
                    'cumulated_return_reach' => 0,
                    'day_count' => 0,
                    'compounding_frequency' => 'Monthly',
                    'start_date' => null,
                    'end_date' => null,
                    'duration' => 0,
                    'accrues_on' => 'capital_balance',
                    'payment_towards' => 'capital_balance',
                    'payment_type_towards' => 'capital_balance',
                    'split_unpayed' => false,
                    'accrual_cadence' => 'Monthly',
                    'notes' => 'Distribution share split hurdle',
                ]);

                $split_hurdle->included_class = [$splitClassId];
                $split_hurdle->classes_values = [
                    [
                        "id" => "$splitClassId",
                        "value" => $lp_class->preferred_return . '%',
                    ]
                ];
                $split_hurdle->save();
            } else {
                $splitClassId = $lp_class->id;
                $splitValue = preg_replace('/[^\d.]/', '', $lp_class->distribution_share);
                $splitValue = (int) $splitValue ?? 0;
                // splits  --- [{"value": "60%"}, {"value": "50%"}]
                // included_class --- [3]
                // classes_values --- [{"id": 3, "value": "50%"}]
                $includedClassIds = [];
                $includedClassIds[] = (int) $lp_class->id;
                $includedClassIds[] = (int) $lp_class->deal->classes()->where('class_type', 'GP')->first()->id;
                // $includedClassIds = implode(',', $includedClassIds);

                $split_hurdle = $parent->children()->create([
                    'split' => 'No',
                    'hurdle_type' => 'split',
                    'path' => $key + 1,
                    'sort_order' => $sort_order,
                    'splits' => [],
                    'included_class' => $includedClassIds,
                    'classes_values' => [
                        [
                            "id" => $lp_class->id,
                            "value" => $lp_class->hurdle->upside_split ?? "0%",
                        ],
                        [
                            "id" => $lp_class->deal->classes()->where('class_type', 'GP')->first()->id,
                            "value" => $this->leftFrom100($lp_class->hurdle->upside_split) ?? "0%",
                        ]
                    ],
                    'cumulated_return_reach' => 0,
                    'day_count' => 0,
                    'compounding_frequency' => 'Monthly',
                    'start_date' => null,
                    'end_date' => null,
                    'duration' => 0,
                    'accrues_on' => 'capital_balance',
                    'payment_towards' => 'capital_balance',
                    'payment_type_towards' => 'capital_balance',
                    'split_unpayed' => false,
                    'accrual_cadence' => 'Monthly',
                    'notes' => 'Distribution share split hurdle',
                ]);
                // $hurd->included_class = ["$lp_class->id"];
                // $hurd->classes_values = [
                //     [
                //         "id" => "$lp_class->id",
                //         "value" => $lp_class->hurdle->upside_limit ?? "0%",
                //     ]
                // ];
                $split_hurdle->save();

                // dd($hurd->id);

                // $gp_provision = new GPProvision;
                // $gp_provision->waterfall_hurdle_id = $hurd->id;
                // $gp_provision->deal_class_id = $gps->first()->id;
                // $gp_provision->classes_catch_up = [$lp_class->id];
                // $catchup_splits = [
                //     "id" => $lp_class->id,
                //     "value" => $lp_class->hurdle->upside_split ?? "0%",
                //     "value1" => $lp_class->hurdle->upside_split ?? "0%",
                //     "value2" => $lp_class->hurdle->upside_limit ?? "0%",
                // ];
                // $gp_provision->catch_up_splits = $catchup_splits;
                // $gp_provision->classify_payment = 'capital_balance';
                // $gp_provision->notes = 'GP Provision';
                // $gp_provision->save();

                $includedClassIds = [];
                $includedClassIds[] = (int) $lp_class->id;

                // check if upside split is less than 100 and create a stop hurdle
                // replace string with actual value remove non numeric characters

                if ($lp_class->hurdle->upside_split !== null && $lp_class->hurdle->upside_split !== '') {

                    $upside_split = preg_replace('/[^\d.]/', '', $lp_class->hurdle->upside_split);
                    $upside_split = preg_replace('/\s+/', '', $upside_split);

                    $upside_split = (int) $upside_split ?? 0;

                    if ($upside_split < 100) {
                        $stopCondition = new StopWaterfallHurdle;
                        $stopCondition->waterfall_hurdle_id = $split_hurdle->id;
                        $stopCondition->preferred_return_type = $lp_class->hurdle->preferred_return_type ?? 'irr';
                        $stopCondition->included_class = $includedClassIds; // example array of class IDs
                        $stopCondition->classes_values = [
                            [
                                "id" => $lp_class->id,
                                "value" => $lp_class->hurdle->upside_limit ?? "0%",
                            ]
                        ];
                        $stopCondition->notes = 'Stop Waterfall Hurdle';
                        $stopCondition->save();
                    }
                }
            }
        }
    }

    /**
     * Create Single LP Path Waterfall for 100% distribution share
     * @param Collection $allClasses
     * @param Waterfall $waterfall
     * @return $waterfall
     */
    protected function createSinglePathwaterfall($allClasses, $waterfall)
    {
        // Iterate through each LP class
        $lps = $allClasses->where('class_type', 'LP');

        $gps = $allClasses->where('class_type', 'GP');

        // Find LP with coc hurdle
        $lp_classes = collect();
        foreach ($lps as $lp) {
            if ($lp->hurdles->count()) {
                foreach ($lp->hurdles as $hurdle) {
                    $lpCopy = clone $lp; // Clone the lp instance
                    $lpCopy['hurdle'] = $hurdle;
                    $lp_classes->push($lpCopy);
                }
            } else {
                $lpCopy = clone $lp; // Clone the lp instance
                $lpCopy['hurdle'] = null;
                $lp_classes->push($lpCopy);
            }
        }

        foreach ($lp_classes as $lp_class) {
            if ($lp_class['hurdle'] == null) {
                $hurd = $waterfall->hurdles()->create([
                    'split' => null,
                    'hurdle_type' => 'cash_on_cash',
                    'cumulated_return_reach' => 0,
                    'day_count' => 0,
                    'compounding_frequency' => 'Monthly',
                    'start_date' => null,
                    'end_date' => null,
                    'duration' => 0,
                    'accrues_on' => 'capital_balance',
                    'payment_towards' => 'capital_balance',
                    'payment_type_towards' => 'capital_balance',
                    'split_unpayed' => false,
                    'accrual_cadence' => 'Monthly',
                    'notes' => 'LP CoC hurdle',
                ]);
                $hurd->included_class = [$lp_class->id];
                $hurd->classes_values = [
                    [
                        "id" => "$lp_class->id",
                        "value" => $lp_class->preferred_return ?? "0%",
                    ]
                ];
                $hurd->save();
            } else {
                // splits  --- [{"value": "60%"}, {"value": "50%"}]
                // included_class --- [3]
                // classes_values --- [{"id": 3, "value": "50%"}]
                $includedClassIds = [];
                $includedClassIds[] = (int) $lp_class->id;
                $includedClassIds[] = (int) $gps->first()->id;
                // $includedClassIds = implode(',', $includedClassIds);

                $hurd = $waterfall->hurdles()->create([
                    'split' => 'No',
                    'hurdle_type' => 'split',
                    'splits' => [],
                    'included_class' => $includedClassIds,
                    'classes_values' => [
                        [
                            "id" => $lp_class->id,
                            "value" => $lp_class->hurdle->upside_split ?? "0%",
                        ],
                        [
                            "id" => $gps->first()->id,
                            "value" => $this->leftFrom100($lp_class->hurdle->upside_split) ?? "0%",
                        ]
                    ],
                    'cumulated_return_reach' => 0,
                    'day_count' => 0,
                    'compounding_frequency' => 'Monthly',
                    'start_date' => null,
                    'end_date' => null,
                    'duration' => 0,
                    'accrues_on' => 'capital_balance',
                    'payment_towards' => 'capital_balance',
                    'payment_type_towards' => 'capital_balance',
                    'split_unpayed' => false,
                    'accrual_cadence' => 'Monthly',
                    'notes' => 'LP CoC hurdle',
                ]);
                // $hurd->included_class = ["$lp_class->id"];
                // $hurd->classes_values = [
                //     [
                //         "id" => "$lp_class->id",
                //         "value" => $lp_class->hurdle->upside_limit ?? "0%",
                //     ]
                // ];
                $hurd->save();

                $includedClassIds = [];
                $includedClassIds[] = (int) $lp_class->id;

                // check if upside split is less than 100 and create a stop hurdle
                // replace string with actual value remove non numeric characters

                if ($lp_class->hurdle->upside_split !== null && $lp_class->hurdle->upside_split !== '') {

                    $upside_split = preg_replace('/[^\d.]/', '', $lp_class->hurdle->upside_split);
                    $upside_split = preg_replace('/\s+/', '', $upside_split);

                    $upside_split = (int) $upside_split ?? 0;

                    if ($upside_split < 100) {
                        $stopCondition = new StopWaterfallHurdle;
                        $stopCondition->waterfall_hurdle_id = $hurd->id;
                        $stopCondition->preferred_return_type = $lp_class->hurdle->preferred_return_type ?? 'irr';
                        $stopCondition->included_class = $includedClassIds; // example array of class IDs
                        $stopCondition->classes_values = [
                            [
                                "id" => $lp_class->id,
                                "value" => $lp_class->hurdle->upside_limit ?? "0%",
                            ]
                        ];
                        $stopCondition->notes = 'Stop Waterfall Hurdle';
                        $stopCondition->save();
                    }
                }
            }
        }
    }


    protected function leftFrom100($value)
    {
        // strip any non-numeric characters
        $value = preg_replace('/[^\d.]/', '', $value);
        return 100 - $value . '%';
    }


    /**
     * format data for class
     * @param array $class
     * @return array $class 
     */
    protected function formatClassData($class)
    {
        $class['raise_amount_ownership'] = $this->moneystrToDouble($class['raise_amount_ownership']);
        $class['raise_amount_distributions'] = $this->moneystrToDouble($class['raise_amount_distributions']);
        $class['raise_quota'] = $this->moneystrToDouble($class['raise_quota']);
        $class['minimum_investment'] = $this->moneystrToDouble($class['minimum_investment']);
        $class['price_per_unit'] = $this->moneystrToDouble($class['price_per_unit']);
        $class['target_irr'] = $this->moneystrToDouble($class['target_irr']);

        return $class;
    }

    /**
     * Create New Offering for deal
     * @param Request $request
     * @param Deal $deal
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOffering(Request $request, Deal $deal)
    {
        $validate = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'assets' => 'required',
                'offering_classes' => 'required',
                'verify_investor_accreditation' => 'required',
                // 'offering_media' => 'required',
            ],
            [
                'name' => 'Offering Name is required',
                'assets' => 'Assets are required',
                'offering_classes' => 'Offering Classes are required',
                'verify_investor_accreditation' => 'Investor Accreditation Verification is required',

            ]
        );

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        // Create a new Offering
        $data = $request->all();
        $data = $this->moneyToDouble($data);
        // generate uuid for offering
        $data['uuid'] = (string) Str::uuid();

        $offering = $deal->offerings()->create($data);

        // Attach assets to the offering
        if ($request->has('assets')) {
            $offering->assets()->attach($request->assets);
        }

        // Attach classes to the offering
        if ($request->has('offering_classes')) {
            $offering->classes()->attach($request->offering_classes);
        }

        // Attach media to the offering
        if ($request->has('offering_media') && $request->offering_media) {
            foreach ($request->offering_media as $media) {
                $path = $media->store('offerings/media', 'public');
                $offering->media()->create([
                    'media_url' => $path,
                    'media_type' => $media->getClientMimeType(),
                ]);
            }
        }


        $metrics = [
            [
                'metric_label' => 'Annualized return',
                'can_del' => false
            ],
            [
                'metric_label' => 'Average cash-on-cash',
                'can_del' => false
            ],
            [
                'metric_label' => 'Equity multiple',
                'can_del' => false
            ],
            [
                'metric_label' => 'IRR',
                'can_del' => false
            ],
        ];

        foreach ($metrics as $metric) {
            $offering->key_metrics()->create($metric);
        }


        return response()->json([
            'success' => 'Deal Offering created successfully',
            'deal' => [
                'id' => $deal->id,
                'name' => $deal->name,
                'type' => $deal->type,

            ]
        ], 200);
    }

    protected function moneystrToDouble($moneyString)
    {
        $cleanedString = preg_replace('/[^\d.]/', '', $moneyString);
        // Convert to a double value 
        $doubleValue = floatval($cleanedString);
        return $doubleValue;
    }

    public function showOfferingDetail(Deal $deal, Offering $offering)
    {
        $pageTitle = $deal->name;
        $offering->load('assets', 'classes', 'documents', 'funding_info', 'insight', 'investments', 'eSignTemplates');
        $template = $offering->eSignTemplates;
        $this->checkDefaultKeyMetrics($offering);
        // return $offering;
        if ($offering->insight == null) {
            $insight = $offering->insight()->create([
                'property_manager_name' => null
            ]);
        }
        if ($offering->funding_info == null) {
            $fundingInfo = new OfferingFundingInfo;
            $fundingInfo->offering_id = $offering->id;
            // dd($fundingInfo);
            $offering->new_funding_info = $fundingInfo;
            // return $offering;
        } else {
            $fundingInfo = $offering->funding_info;
            $fundingInfo->funding_methods = json_decode($fundingInfo->funding_methods, true);
        }
        $offering->load([
            'insight',
            'key_metrics' => function ($query) {
                $query->orderBy('can_del', 'ASC');
            },
            'key_metrics.classes:id,equity_class_name'
        ]);

        $assetImages = $offering->deal->assets->first()->assetMedia;

        $assetImages = $assetImages->map(function ($image) {
            return asset($image->media_url);
        });
        $existingTemplates = \App\Models\ESignTemplate::where('offering_id', $offering->id)
            ->pluck('template_type')
            ->toArray();

        // return $fundingInfo;
        // Pass any necessary data to the view if needed
        return view('admin.deals.offerings.offering_detail', compact('pageTitle', 'deal', 'offering', 'fundingInfo', 'assetImages', 'template', 'existingTemplates'));

    }

    public function showOfferingDetailPreview(Deal $deal, Offering $offering)
    {
        $pageTitle = 'Offering Detail Preview';
        $offering->load('assets', 'classes', 'documents', 'funding_info', 'insight', 'investments', 'key_metrics');
        $this->checkDefaultKeyMetrics($offering);
        // return $offering;
        if ($offering->insight == null) {
            $insight = $offering->insight()->create([
                'property_manager_name' => null
            ]);
        }
        if ($offering->funding_info == null) {
            $fundingInfo = new OfferingFundingInfo;
            $fundingInfo->offering_id = $offering->id;
            // dd($fundingInfo);
            $offering->new_funding_info = $fundingInfo;
            // return $offering;
        } else {
            $fundingInfo = $offering->funding_info;
            $fundingInfo->funding_methods = json_decode($fundingInfo->funding_methods, true);
        }
        $offering->load([
            'insight',
            'key_metrics' => function ($query) {
                $query->orderBy('can_del', 'ASC');
            },
            'key_metrics.classes:id,equity_class_name'
        ]);

        $assetImages = $offering->deal->assets->first()->assetMedia;

        $assetImages = $assetImages->map(function ($image) {
            return asset($image->media_url);
        });

        // return $fundingInfo;
        // Pass any necessary data to the view if needed
        return view('admin.deals.offerings.offering_preview', compact('pageTitle', 'deal', 'offering', 'fundingInfo', 'assetImages'));

    }

    public function showPublicPreview($encryptedId)
    {
        $pageTitle = 'Offering Detail';
        $offering = Offering::where('uuid', $encryptedId)->first();

        if (!$offering) {
            return redirect()->route('home');
        }

        $offering->load('assets', 'classes');

        // Check if user logged in
        if (Auth::check()) {
            return redirect()->route('user.offerings.offering', $offering->id);
        } else {
            // Check if offering is publicly accessible
            if ($offering->public_offering == 0) {
                return redirect()->route('home');
            }
        }

        if (!$offering) {
            return redirect()->route('home');
        }


        return view('templates.basic.publicOfferings', compact('offering', 'pageTitle'));
    }

    /**
     * Update offering for deal
     * @param Request $request
     * @param Deal $deal
     * @param Offering $offering
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOffering(Request $request, Deal $deal, Offering $offering)
    {
        $validate = Validator::make($request->all(), [
            // 'name' => 'required',
            // 'offering_classes' => 'required',
            'hard_committed_percent' => 'required_if:status,3',
            // 'offering_media' => 'required',
        ], [
            'name' => 'Offering Name is required',
            'offering_classes' => 'Offering Classes are required',
            'hard_committed_percent' => 'Hard Commitment Percent is required when status is 3',
            // 'offering_media' => 'Offering Media is required',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        // Update the Offering
        // dd($request->all());
        if ($request->has('overview_matrics')) {
            if (is_array($request->overview_matrics)) {
                $request->overview_matrics = json_encode($request->overview_matrics);
            }
        }

        $offering->update($request->all());

        // Attach classes to the offering
        $offering->classes()->sync($request->offering_classes);
        // Attach assets to the offering
        $offering->assets()->sync($request->offering_assets);
        // Attach media to the offering
        // if ($request->has('offering_media')) {
        //     // 
        //     foreach ($request->offering_media as $media) {
        //         $path = $media->store('offerings/media', 'public');
        //         $offering->media()->create([
        //             'media_url' => $path,
        //             'media_type' => $media->getClientMimeType(),
        //         ]);
        //     }
        // }

        return response()->json([
            'success' => 'Deal created successfully',
            'deal' => [
                'id' => $deal->id,
                'name' => $deal->name,
                'type' => $deal->type,
            ]
        ], 200);

    }


    /**
     * Check if Default key matrics exists
     * if not create default key metrics
     * @param Offering $offering
     * @return void
     * 
     * 
     */
    public function checkDefaultKeyMetrics(Offering $offering)
    {
        $metrics = [
            [
                'metric_label' => 'Equity multiple',
                'can_del' => false
            ],
            [
                'metric_label' => 'Annualized return',
                'can_del' => false
            ],
            [
                'metric_label' => 'Average cash-on-cash',
                'can_del' => false
            ],
            [
                'metric_label' => 'IRR',
                'can_del' => false
            ],
        ];

        foreach ($metrics as $metric) {
            $offering->key_metrics()->where('metric_label', $metric['metric_label'])->firstOrCreate($metric);
        }
    }

    /**
     * Create funding for offering
     * @param Request $request
     * @param Deal $deal
     * @param Offering $offering
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFundingInfo(Request $request, Deal $deal, Offering $offering)
    {
        $validate = Validator::make($request->all(), [
            'receiving_bank' => 'required',
            'bank_address' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 421);
        }

        $funding = OfferingFundingInfo::updateOrCreate([
            'offering_id' => $offering->id,
        ], [
            'receiving_bank' => $request->receiving_bank,
            'bank_address' => $request->bank_address,
            'routing_no' => $request->routing_no,
            'account_no' => $request->account_no,
            'account_type' => $request->account_type,
            'beneficiary_account_name' => $request->beneficiary_account_name,
            'beneficiary_address' => $request->beneficiary_address,
            'reference_info' => $request->reference_info,
            'instruction_info' => $request->instruction_info,
            'mail_address' => $request->mail_address,
            'mail_beneficiary' => $request->mail_beneficiary,
            'mail_beneficiary_address' => $request->mail_beneficiary_address,
            'mail_instructions' => $request->mail_instructions,
            'investment_fee_type' => $request->investment_fee_type,
            'investment_fee_method' => $request->investment_fee_method,
            'investment_fee_amount' => $this->moneystrToDouble($request->investment_fee_amount),
            'funding_methods' => json_encode($request->funding_methods, true),
        ]);


        // $funding->funding_methods = json_decode($funding->funding_methods);
        return response()->json(['success' => 'Funding created successfully', $funding], 200);

    }

    public function createKeySection(Request $request)
    {
        $request->validate([
            'offering_id' => 'required',
            'metric_label' => 'required|string|max:255',
        ]);

        $section = KeyMetric::create([
            'offering_id' => $request->offering_id,
            'metric_label' => $request->metric_label,
        ]);

        return response()->json(['success' => true, 'section' => $section]);
    }

    /**
     * Save the key metrics.
     *
     * @param Request $request
     * @param Deal $deal
     * @param Offering $offering
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveMetric(Request $request, Deal $deal, Offering $offering)
    {
        $validator = Validator::make($request->all(), [
            'metrics' => 'required|array',
            'metrics.*.label' => 'nullable|string|max:255',
            'metrics.*.*' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 421);
        }

        $offering->load('key_metrics.classes');
        $validatedData = $validator->validated();
        foreach ($validatedData['metrics'] as $keyMetricId => $metricData) {
            // Find or create a KeyMetric if not found
            $keyMetric = KeyMetric::find($keyMetricId);

            if (!$keyMetric) {
                $keyMetric = KeyMetric::create([
                    'offering_id' => $offering->id,
                    'metric_label' => $metricData['label'],
                ]);
            } else {
                // Update the existing KeyMetric
                $keyMetric->update([
                    'metric_label' => $metricData['label'],
                ]);
            }

            foreach ($offering->classes as $class) {
                $value = $metricData[$class->id] ?? null;

                $keyMetric->classes()->syncWithoutDetaching([
                    $class->id => ['value' => $value],
                ]);
            }
        }

        return response()->json(['message' => 'Metrics saved successfully.'], 200);
    }

    public function delKeyMetric(Request $request, Offering $offering, KeyMetric $id)
    {
        $keyMetric = $id;

        if (!$keyMetric) {
            return response()->json(['error' => 'Key Metric not found.'], 404);
        }

        $keyMetric->classes()->detach();

        $keyMetric->delete();

        return response()->json(['message' => 'Key Metric deleted successfully.'], 200);
    }

    public function showAssetDetail(Deal $deal, Asset $asset)
    {
        $pageTitle = $deal->name;
        $deal = Deal::findOrFail($deal->id);

        $asset = $deal->assets()->with('assetMedia')->where('id', $asset->id)->firstOrFail();
        // return $asset;
        // Pass any necessary data to the view if needed
        return view('admin.deals.assets.asset_detail', compact('pageTitle', 'deal', 'asset'));
    }

    public function updateOfferingInsight(Request $request, Offering $offering)
    {
        $data = $request->all();
        $data['loan_assumption'] = ($request->loan_assumption == 'true') ? true : false;
        $data['one_mile_median_income'] = $this->moneystrToDouble($request->one_mile_median_income);
        $data['three_mile_median_income'] = $this->moneystrToDouble($request->three_mile_median_income);
        $data['loan_to_value'] = $this->moneystrToDouble($request->loan_to_value);
        $data['interest_rate'] = $this->moneystrToDouble($request->interest_rate);
        $data['acquisition_fee'] = $this->moneystrToDouble($request->acquisition_fee);
        $data['asset_management_fee'] = $this->moneystrToDouble($request->asset_management_fee);
        $data['construction_management_fee'] = $this->moneystrToDouble($request->construction_management_fee);
        $data['disposition_fee'] = $this->moneystrToDouble($request->disposition_fee);
        $data['refinance_fee'] = $this->moneystrToDouble($request->refinance_fee);
        $offering->insight()->update($data);
        return response()->json(['message' => 'Offering Insights updated successfully.', 'success' => true], 200);
    }

    /**
     * View Edit Deal
     * @param Deal $deal
     * @return \Illuminate\View\View
     */
    public function edit(Deal $deal)
    {
        $pageTitle = $deal->name;
        $deal->load(
            'classes',
            'assets',
            'offerings',
            'distributions',
            'investments',
            'investments.investor',
            'investments.profile',
            'investments.class',
            'investments.offering',
            'offerings.funding_info',
            'admin_setting',
            'personal_setting',
            'senderaddresses',
            'bankaccounts',
            'settings',
            'beneficial_owner_details',
            'addresses',
            'achSettings'

        );
        // dd($deal->achSettings->verify_detail);
        $deal->load([
            'document_sections' => function ($query) {
                $query->orderBy('can_edit', 'DESC');
            },
            'document_sections.documents'
        ]);

        if ($deal->admin_setting == null) {
            $deal->admin_setting()->create([
                'equity_increase_class' => json_encode([], true),
            ]);
            $deal->admin_setting()->create([
                'equity_increase_class' => json_encode([], true),
            ]);
            $deal->load('admin_setting');
        }

        if ($deal->personal_setting == null) {
            $deal->personal_setting()->create([]);
            $deal->load('personal_setting');
        }

        if ($deal->settings == null) {
            $deal->settings()->create([]);
            $deal->load('settings');
        }

        if ($deal->settings == null) {
            $deal->settings()->create([]);
            $deal->load('settings');
        }
        $onboardingStatus = null;
        if ($deal->achSettings && $deal->achSettings->stripe_account_id) {
            try {
                $onboardingStatus = $this->stripeService->checkOnboardingStatus($deal->achSettings->stripe_account_id);
                // $statusForOnboarding = $onboardingStatus['onboarding_complete'];
            } catch (\Exception $e) {
                \Log::error('Failed to check onboarding status: ' . $e->getMessage());
                $onboardingStatus = (object)[
                    // 'onboarding_complete' => false,
                    'message' => 'Error checking onboarding status.',
                ];
            }
        }
        // return $deal;  
        // Pass any necessary data to the view if needed
        return view('admin.deals.edit', compact('pageTitle', 'deal','onboardingStatus'));
    }
    public function EntityDetail(Deal $deal)
    {
        $pageTitle = 'Edit Deal';
        $deals = Deal::paginate(10);

        return view('admin.deals.entitydetail', compact('pageTitle', 'deal'));
    }
    public function update(Request $request, Deal $deal)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            // 'type' => 'required',
        ], [
            'name' => 'The deal name field is required.',
            // 'type.required' => 'The deal type field is required.',
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        // Create a new deal
        $deal->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Deal updated successfully',
            'deal' => [
                'id' => $deal->id,
                'name' => $deal->name,
                'type' => $deal->type,

            ]
        ], 200);
    }
    public function setting(Request $request, Deal $deal)
    {
        $validate = Validator::make($request->all(), [
            'co_sponser_investor_info' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }
        // dd($request);

        $adminSetting = AdminSetting::updateOrCreate(
            ['deal_id' => $deal->id],
            [
                'co_sponser_investor_info' => ($request->co_sponser_investor_info == 'true') ? true : false,
                'co_sponser_member_tab' => ($request->co_sponser_member_tab == 'true') ? true : false,
                'lead_sponser_investment' => $request->lead_sponser_investment,
                'lead_sponser_distribution' => $request->lead_sponser_distribution,
                'lead_sponser_investment_update' => ($request->lead_sponser_investment_update == 'true') ? true : false,
                'co_sponser_portal' => ($request->co_sponser_portal == 'true') ? true : false,
                'sponsers_billing_notification' => $request->sponsers_billing_notification,
                'equity_increase_class' => json_encode($request->equity_increase_class, true),
                'equity_investment_increment' => ($request->equity_investment_increment == 'true') ? true : false,
                'equity_funds_recieved' => ($request->equity_funds_recieved == 'true') ? true : false,
                'equity_funds_instruction' => ($request->equity_funds_instruction == 'true') ? true : false,
                'equity_funds_show_instruction' => ($request->equity_funds_show_instruction == 'true') ? true : false,
                'equity_sponser_email' => $request->equity_sponser_email,
                'equity_ach_details' => ($request->equity_ach_details == 'true') ? true : false,
                'equity_gp_approval' => ($request->equity_gp_approval == 'true') ? true : false,
                'metric_ownership_percentage' => ($request->metric_ownership_percentage == 'true') ? true : false,
                'metric_investors_share' => ($request->metric_investors_share == 'true') ? true : false,
                'metric_show_coc' => ($request->metric_show_coc == 'true') ? true : false,
                'metric_investor_liquid' => ($request->metric_investor_liquid == 'true') ? true : false,
                'metric_capital_balance' => ($request->metric_capital_balance == 'true') ? true : false,
                'metric_investor_return' => ($request->metric_investor_return == 'true') ? true : false,
                'metric_investor_cash_balance' => ($request->metric_investor_cash_balance == 'true') ? true : false,
                'distribution_investment_return' => $request->distribution_investment_return,
                'distribution_reinvestment' => ($request->distribution_reinvestment == 'true') ? true : false,
                'distribution_tax_percentage' => ($request->distribution_tax_percentage == 'true') ? true : false,
                'min_amount' => $this->moneystrToDouble($request->min_amount),
                'max_amount' => $this->moneystrToDouble($request->max_amount),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Admin Settings updated successfully',
            $adminSetting,
            'deal' => [
                'id' => $deal->id,
                'name' => $deal->name,
                'type' => $deal->type,

            ]
        ], 200);
    }
    public function personal(Request $request, Deal $deal)
    {
        $validate = Validator::make($request->all(), [
            'email_privacy_investor' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 422);
        }

        $personalSetting = PersonalSetting::updateOrCreate(
            ['deal_id' => $deal->id],
            [
                'email_privacy_investor' => ($request->email_privacy_investor == 'true') ? true : false,
                'email_interception_review' => ($request->email_interception_review == 'true') ? true : false,
                'email_interception_sponser' => ($request->email_interception_sponser == 'true') ? true : false,
                'notification_selected_sponser' => $request->notification_selected_sponser,
                'document_visibility_investors' => ($request->document_visibility_investors == 'true') ? true : false,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Personal Settings updated successfully',
            $personalSetting,
            'deal' => [
                'id' => $deal->id,
                'name' => $deal->name,
                'type' => $deal->type,

            ]
        ], 200);
    }
    // Stores the sender address for a deal
    public function storesenderaddress(Request $request, Deal $deal)
    {

        $validate = Validator::make($request->all(), [
            'city' => 'required|string|max:100',
        ]);

        // Perform saving logic here, e.g.:
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        DealSenderAddress::updateOrCreate(['deal_id' => $deal->id], $request->all());


        return response()->json(['message' => 'Address created successfully', 'deal'], 200);
    }
    public function storebankaccount(Request $request, Deal $deal)
    {
        $validate = Validator::make($request->all(), [
            // 'email_privacy_investor' => 'required'
        ]);

        // Perform saving logic here, e.g.:
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        DealBankAccount::updateOrCreate(['deal_id' => $deal->id], $request->all());

        return response()->json(['message' => 'BankAccount created successfully'], 200);
    }

    public function StoreSetting(Request $request, Deal $deal)
    {

        //  dd($request->senderAddress);
        $validate = Validator::make($request->all(), [
            // 'city' => 'required|string|max:100',
        ]);

        // Perform saving logic here, e.g.:
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        // dd($request->all());
        DealCheckSetting::updateOrCreate(['deal_id' => $deal->id], $request->all());

        return response()->json(['message' => 'setting updated successfully'], 200);
    }

    public function showOfferingManage(Request $request, Deal $deal, Offering $offering)
    {
        $pageTitle = 'Offering Manage';
        $offering->load('manageoffering');

        // dd($offering);
        // dd($offering->manageOffering?->min_investment);

        return view('admin.deals.offerings.offering_manage', compact('pageTitle', 'offering', 'deal'));
    }
    public function storeManageOffering(Request $request, Offering $offering)
    {
        $validate = Validator::make($request->all(), [
            // 'name' => 'required|string|max:100',
            // 'description' => 'required|string',
            // 'price' => 'required|numeric',
            // 'quantity' => 'required|numeric',
        ]);
        // Perform saving logic here, e.g.:
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        ManageOffering::updateOrCreate(['offering_id' => $offering->id], [
            'offering_id' => $offering->id,
            'require_w9' => ($request->require_w9) ? 1 : 0,
            'min_investment' => ($request->min_investment) ? 1 : 0,
            'max_investment' => ($request->max_investment) ? 1 : 0,
            'account_creation' => ($request->account_creation) ? 1 : 0,
            'prompt_lp' => $request->prompt_lp,
            'ira_document' => ($request->ira_document) ? 1 : 0,
            'allowed_profile_types' => $request->allowed_profile_types,
            'questionnaire' => ($request->questionnaire) ? 1 : 0,
            'questionnaire_soft' => ($request->questionnaire_soft) ? 1 : 0,
            'signature_text' => $request->signature_text,
            'verify_investor' => ($request->verify_investor) ? 1 : 0,
            'verify_accreditation_soft' => ($request->verify_accreditation_soft) ? 1 : 0,
            'ait_cvl' => ($request->ait_cvl) ? 1 : 0,
            'rav_blp' => ($request->rav_blp) ? 1 : 0,
            'methods' => $request->methods,
            'verify_accreditation_identity' => ($request->verify_accreditation_identity) ? 1 : 0,
            'require_kyc' => ($request->require_kyc) ? 1 : 0,
            'display_offering' => ($request->display_offering) ? 1 : 0,
        ]);

        return response()->json(['message' => 'Offering created successfully'], 200);


    }
    public function destroyOffering(Deal $deal, Offering $offering)
    {

        if ($offering->deal_id !== $deal->id) {
            return response()->json(['error' => 'Offering does not belong to this deal'], 403);
        }

        $offering->delete();

        return response()->json(['success' => 'Offering deleted successfully'], 200);
    }
    public function storeAddress(Deal $deal, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'city' => 'required|string|max:100',
            'address_line_1' => 'required',
            'address_line_2' => 'required',
            'zip_code' => 'required',
            'country' => 'required|string',
            'state' => 'required',
        ], [
            'city' => 'City is required',
            'address_line_1' => 'Address Line 1 is required',
            'zip_code' => 'Zip Code is required',
            'country' => 'Country is required',
            'state' => 'State is required',
        ]);
        // Perform saving logic here, e.g.:
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        $address = DealAddress::create($request->all());

        $deal_addresses = DealAddress::where('deal_id', $deal->id)->get();

        return response()->json(
            [
                'message' => 'Address created successfully',
                'addresses' => $deal_addresses
            ],
            200
        );
    }
    public function storeAchSetting(Deal $deal, Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'entity_name' => 'required|string',
            'entity_type' => 'required|string',
            'state_registration' => 'required|string',
            'ein' => 'required|string',
            'ein_letter' => 'required|mimes:jpeg,png,pdf|max:2048',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'job_title' => 'required|string',
            'date_of_birth' => 'required|date',
            'ssn' => 'required|string',
            'address_id' => 'required',
            'controller_address' => 'required|string',
            'controller_id' => 'required|mimes:jpeg,png,pdf|max:2048',
            'document_label' => 'required|string',
            'does_individual' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $einLetterPath = $request->file('ein_letter')->store('verify_entity', 'public');
        $controllerIdPath = $request->file('controller_id')->store('verify_entity', 'public');

        $data = $request->all();
        $data['ein_letter'] = $einLetterPath;
        $data['controller_id'] = $controllerIdPath;
        $data['verify_detail'] = 1;
        $data['verify_confirmation'] = 'review';

        $verifyEntity = DealAchSetting::updateOrCreate([
            'deal_id' => $deal->id,
        ], $data);

        return response()->json(['message' => 'Address created successfully'], 200);
    }

    public function storeBeneficialOwnerDetail(Deal $deal, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
            'social_security_number' => ['required', 'regex:/^\d{3}-\d{2}-\d{4}$/'],
            'address_lookup' => 'required',
            'address_1' => 'required',
            'address_2' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => ['required', 'regex:/^\d{5}(-\d{4})?$/'],
        ], [
            'first_name.required' => 'First Name is required',
            'last_name.required' => 'Last Name is required',
            'dob.required' => 'Date of Birth is required',
            'dob.before_or_equal' => 'You must be at least 18 years old',
            'social_security_number.required' => 'Please enter the beneficial owner SSN!',
            'social_security_number.regex' => 'Please enter a valid SSN / EIN!',
            'address_lookup.required' => 'Address Lookup is required',
            'address_1.required' => 'Address 1 is required',
            'address_2.required' => 'Address 2 is required',
            'city.required' => 'City is required',
            'state.required' => 'State is required',
            'zipcode.required' => 'Zip Code is required',
            'zipcode.regex' => 'Invalid Zip Code!',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        // Perform saving logic here, e.g.:
        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }
        BeneficialOwnerDetail::create($request->all());

        $b_owners = BeneficialOwnerDetail::where('deal_id', $deal->id)->get();

        return response()->json([
            'message' => 'Address created successfully',
            'beneficial_owners' => $b_owners
        ], 200);
    }
    public function updateBeneficialOwnerDetail(Deal $deal, BeneficialOwnerDetail $beneficial, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
            'social_security_number' => ['required', 'regex:/^\d{3}-\d{2}-\d{4}$/'],
            'address_lookup' => 'required',
            'address_1' => 'required',
            'address_2' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => ['required', 'regex:/^\d{5}(-\d{4})?$/'],
        ], [
            // custom error messages...
            'first_name.required' => 'First Name is required',
            // etc...
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        // Update the existing beneficial record
        $beneficial->update($request->all());

        // Fetch all beneficial owners for the deal (to update your UI)
        $b_owners = BeneficialOwnerDetail::where('deal_id', $deal->id)->get();

        return response()->json([
            'message' => 'Beneficial owner updated successfully',
            'beneficial_owners' => $b_owners
        ], 200);
    }

    public function destroyBeneficial(Deal $deal, Request $request)
    {
        // Retrieve the beneficial id from the query parameters
        $beneficialId = $request->query('beneficial_id');

        if (!$beneficialId) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficial ID is missing.'
            ], 400);
        }

        $beneficial = $deal->beneficial_owner_details()->find($beneficialId);

        if (!$beneficial) {
            return response()->json([
                'success' => false,
                'message' => 'Beneficial not found.'
            ], 404);
        }

        try {
            $beneficial->delete();

            return response()->json([
                'success' => true,
                'message' => 'Beneficial deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the Beneficial.'
            ], 500);
        }
    }

    public function entityDetailStore(Deal $deal, Request $request)
    {
        $validated = $request->validate([
            'owning_entity_name' => 'required',
        ]);

        $deal->owningEntityDetails()->updateOrCreate(['deal_id' => $deal->id], $validated);

        return response()->json([
            'message' => 'Owning Entity Details saved successfully',
            'owningEntityDetail' => $deal->owningEntityDetail,
        ]);
    }
    public function storeMember(Deal $deal, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'role' => 'required',
            'email_address' => 'required|email',
        ], [
            'first_name.required' => 'First Name is required',
            'last_name.required' => 'Last Name is required',
            'role.required' => 'Role is required',
            'email_address.required' => 'Email Address is required',
            'email_address.email' => 'Invalid Email Address',
        ]);

        $partner = Admin::create([
            'email' => $request->email_address,
            'name' => $request->first_name . ' ' . $request->last_name,
            'username' => $request->email_address,
            'password' => bcrypt($request->email_address),
        ]);

        $partner->assignRole('partner');

        $deal->partners()->attach($partner->id, [
            'role' => $request->role ?? '',
            'activation_key' => Str::uuid(),
            'status' => 1,
            'invitation_email' => $request->email_address,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(
            [
                'deal_id' => $deal->id,
                'member' => $partner,
            ]
        );
    }
    public function updateMember(Deal $deal, $member, Request $request)
    {
        $validate = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'role' => 'required',
        ], [
            'first_name.required' => 'First Name is required',
            'last_name.required' => 'Last Name is required',
            'role.required' => 'Role is required',
        ]);

        if ($validate->fails()) {
            return response()->json(['errors' => $validate->errors()], 422);
        }

        $member = Admin::find($member);
        $member->update([
            'name' => $request->first_name . ' ' . $request->last_name,
        ]);
        $deal->partners()->updateExistingPivot($member->id, [
            'role' => $request->role,
            'updated_at' => now(),
        ]);

        return response()->json([
            'deal_id' => $deal->id,
            'member' => $member->fresh(),
        ], 200);
    }
    public function destroyMember(Deal $deal, $member, Request $request)
    {
        $member = Admin::find($member);
        $deal->partners()->detach($member->id);

        return response()->json([
            'success' => true,
            'message' => 'Member deleted successfully.'
        ]);
    }
    public function approveEntity(Request $request, Deal $deal)
    {
        try {
            if (!$deal->achsettings) {
                return response()->json(['message' => 'No ACH settings found for this deal.'], 404);
            }

            $deal->achsettings->update([
                'verify_confirmation' => 'pending'
            ]);

            return response()->json(['message' => 'Entity approved successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to approve entity: ' . $e->getMessage()], 500);
        }
    }
}
