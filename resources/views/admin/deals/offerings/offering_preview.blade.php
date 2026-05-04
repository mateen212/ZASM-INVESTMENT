@extends('admin.layouts.app') <!-- Assuming you have a main app layout -->
@section('panel')
    @if ($offering->status == 7)
        <div class="card" style="display: flex; align-items: center; justify-content: center;">
            <div class="card-body text-center" style="margin-top: 2rem">
                <img src="{{ asset('assets/images/danger.svg') }}"
                    style="max-height: 100%; max-width: 60%; object-fit: cover;" />
                <h4 class="mt-4">This page is private</h4>
                <p style="font-size:small;">If you believe this is an error, please contact your sponsor.</p>
            </div>
        </div>
    @else
        <div class="container my-4">
            <div class="row">
                <!-- Main Content Section -->
                <div class="col-md-8 pt-3">
                    <!-- Investment Section -->
                    <div class="mb-5">
                        <h3 class="fw-bold" style="font-size:30px;" >
                            {{ \Illuminate\Support\Str::title($offering->name) }}
                        </h3>

                        <div class="row g-3 mt-2">
                            <div class="col-md-9">
                                <div class="image-box " style="height: 45vh">
                                    <div class="responsive-image-container d-flex justify-content-center align-items-center bg-light position-relative"
                                        style="border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
                                        @if ($offering->assets->isNotEmpty() && $offering->assets->first()->assetMedia->isNotEmpty())
                                            <img id="mainImage"
                                                src="{{ asset($offering->assets->first()->assetMedia->first()->media_url) }}"
                                                alt="{{ $offering->assets->first()->name }}"
                                                style="height: 100%; width: 100%; object-fit: cover;" data-bs-toggle="modal"
                                                data-bs-target="#galleryModal">
                                        @else
                                            <img src="{{ asset('assets/images/default.png') }}" alt="Default Property Image"
                                                style="height: 100%; width: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row g-2" style="height: 100%">
                                    @php
                                        $thumbnails = collect();
                                        foreach ($offering->assets as $asset) {
                                            $thumbnails = $thumbnails->concat($asset->assetMedia);
                                        }
                                        $thumbnails = $thumbnails->skip(1)->take(3);
                                    @endphp
                                    @foreach ($thumbnails as $media)
                                        <div class="col-12">
                                            <div class="image-box"
                                                style="height:  @if ($thumbnails->count() < 3) 22vh @else 14.5vh @endif;">
                                                <div class="d-flex justify-content-center align-items-center bg-light position-relative"
                                                    style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; height: 100%;">
                                                    <img src="{{ asset($media->media_url) }}" alt="Property Image"
                                                        class="gallery-thumb"
                                                        style="height: 100%; width: 100%; object-fit: cover; cursor: pointer; transition: transform 0.3s ease;"
                                                        onmouseover="updateMainImage('{{ asset($media->media_url) }}'); this.style.transform='scale(1.05)';"
                                                        onmouseout="resetMainImage(); this.style.transform='scale(1)';"
                                                        data-bs-toggle="modal" data-bs-target="#galleryModal">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @if (
                                $offering->assets->sum(function ($asset) {
                                    return $asset->assetMedia->count();
                                }) > 4)
                                <div class="col-12">
                                    <button type="button" class="btn btn-light w-100 view-all-btn" data-bs-toggle="modal"
                                        data-bs-target="#galleryModal"
                                        style="background: rgba(255, 255, 255, 0.9); border: 1px solid #dee2e6; padding: 8px 16px; font-size: 14px;">
                                        <i class="fas fa-th me-2"></i> View all
                                    </button>
                                </div>
                            @endif
                        </div>

                    </div>
                    <hr>
                    <div class="mt-5 card">
                        <div class="card-body">
                            <!-- Meet the Team Section -->
                            <h5 class="mb-3 fw-bold">Meet the Team</h5>
                            @php
                                $investment = $offering->investments->first();
                            @endphp

                            @if ($investment)
                                <div class="d-flex align-items-center">
                                    <div
                                        class="bg-primary text-white text-center d-flex justify-content-center align-items-center first-letter">
                                        {{ substr($investment->primary_sponsor ?? '--', 0, 1) }}
                                    </div>
                                    <span class="ms-3">{{ $investment->primary_sponsor ?? 'N/A' }}</span>
                                </div>
                            @else
                                <p class="text-muted">No primary sponsor available.</p>
                            @endif

                            <hr>
                            <!-- Key metrics Section -->
                            @if (!empty($offering->key_metrics))
                                <h5 class="fw-bold mb-4 mt-4">Key metrics</h5>
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered mt-3" id="metrics-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Label</th>
                                                @foreach ($offering->classes as $class)
                                                    <th>{{ $class->equity_class_name }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($offering->key_metrics as $key_m)
                                                @php
                                                    $firstClass = $key_m->classes()->first();
                                                @endphp

                                                @if ($firstClass && isset($firstClass->pivot->value))
                                                    <tr>
                                                        <td>{{ $key_m->metric_label }}</td>

                                                        @foreach ($offering->classes as $class)
                                                            @php
                                                                $classMetric = $key_m
                                                                    ->classes()
                                                                    ->where('deal_class_id', $class->id)
                                                                    ->first();
                                                            @endphp

                                                            <td>
                                                                {{ $classMetric && isset($classMetric->pivot->value) ? $classMetric->pivot->value : 'N/A' }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>
                                <hr>
                            @endif
                            <!-- Overview Section -->
                            <h5 class="fw-bold mb-4 mt-4">Overview</h5>
                            @php
                                $videoUrl = $offering->video_url;
                                $embedUrl = '';

                                if (\Illuminate\Support\Str::contains($videoUrl, 'youtube.com')) {
                                    $embedUrl = \Illuminate\Support\Str::replace('watch?v=', 'embed/', $videoUrl);
                                } elseif (\Illuminate\Support\Str::contains($videoUrl, 'youtu.be')) {
                                    $videoId = \Illuminate\Support\Str::after($videoUrl, 'youtu.be/');
                                    $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                                } elseif (\Illuminate\Support\Str::contains($videoUrl, 'vimeo.com')) {
                                    $videoId = \Illuminate\Support\Str::afterLast($videoUrl, '/');
                                    $embedUrl = 'https://player.vimeo.com/video/' . $videoId;
                                }
                            @endphp

                            @if ($embedUrl)
                                <iframe width="630" height="400" src="{{ $embedUrl }}" frameborder="0"
                                    allowfullscreen
                                    style="border-radius: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);">
                                </iframe>
                            @endif
                            <hr>
                            <!-- About offering Section -->
                            @if (!empty($offering->summary))
                                <h5 class="fw-bold mb-4 mt-4">About offering</h5>
                                <p>{{ $offering->summary }}</p>
                                <hr>
                            @endif
                            <!-- Additional information Section -->
                            @if (!empty($offering->logged_summary))
                                <h5 class="fw-bold mb-4 mt-4">Additional information</h5>
                                <p>{{ $offering->logged_summary }}</p>
                                <hr>
                            @endif
                            <!-- Assets Section -->
                            @if (!empty($offering->assets))
                                <h5 class="fw-bold mb-4">Assets</h5>
                                @foreach ($offering->assets as $asset)
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1 fw-bold">{{ $asset->name }}</h6>
                                            <p class="mb-0 text-muted">{{ $asset->address }}</p>
                                        </div>
                                    </div>
                                    <div class="row ms-3 border-start ps-4 mb-4">
                                        <div class="col-md-4 mb-3">
                                            <p class="mb-1 text-muted">Property type</p>
                                            <p class="fw-bold">{{ $asset->property_type }}</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <p class="mb-1 text-muted">Property class</p>
                                            <p class="fw-bold">{{ $asset->property_class }}</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <p class="mb-1 text-muted">Number of units</p>
                                            <p class="fw-bold">{{ $asset->number_of_units }}</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <p class="mb-1 text-muted">Year built</p>
                                            <p class="fw-bold">{{ $asset->year_built }}</< /p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <p class="mb-1 text-muted">Acquisition date</p>
                                            <p class="fw-bold">{{ $asset->acquisition_date }}</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <p class="mb-1 text-muted">Acquisition price</p>
                                            <p class="fw-bold">{{ $asset->acquisition_price }}</p>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <p class="mb-1 text-muted">Current asset value</p>
                                            <p class="fw-bold">{{ $asset->net_asset_value }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                <hr>
                            @endif
                            <!-- Location Section -->
                            <h5 class="fw-bold mb-4 mt-4">Location</h5>
                            <hr>
                            <!-- Debt financing Section -->
                            @if (!empty($offering->insight))
                                <h5 class="fw-bold mb-4 mt-4">Debt financing</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Financing type</p>
                                        <p class="fw-bold">{{ ucfirst($offering->insight->financing_type) }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Loan-to-value</p>
                                        <p class="fw-bold">{{ $offering->insight->loan_to_value }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Interest rate</p>
                                        <p class="fw-bold">{{ $offering->insight->interest_rate }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Loan term</p>
                                        <p class="fw-bold">{{ $offering->insight->loan_term }} year(s)</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Loan assumption</p>
                                        <p class="fw-bold">
                                            {{ $offering->insight->loan_assumption ? 'Assumed' : 'Not Assumed' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Interest-only period</p>
                                        <p class="fw-bold">{{ $offering->insight->interest_only_period }} year(s)</p>
                                    </div>
                                </div>
                                <hr>
                                <!-- Terms and fees Section -->
                                <h5 class="fw-bold mb-4 mt-4">Terms and fees</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Acquisition fee</p>
                                        <p class="fw-bold">{{ $offering->insight->acquisition_fee }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Asset management fee</p>
                                        <p class="fw-bold">{{ $offering->insight->asset_management_fee }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Construction management fee</p>
                                        <p class="fw-bold">{{ $offering->insight->construction_management_fee }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Disposition fee</p>
                                        <p class="fw-bold">{{ $offering->insight->disposition_fee }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Refinance fee</p>
                                        <p class="fw-bold">{{ $offering->insight->refinance_fee }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">Profit sharing</p>
                                        <p class="fw-bold">{{ ucfirst($offering->insight->profit_sharing) }}</p>
                                    </div>
                                </div>
                                <hr>
                                <!-- Market details Section -->
                                <h5 class="fw-bold mb-4 mt-4">Market details</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">1-mile radius median income</p>
                                        <p class="fw-bold">{{ $offering->insight->one_mile_median_income }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <p class="mb-1 text-muted">3-mile radius median income</p>
                                        <p class="fw-bold">{{ $offering->insight->three_mile_median_income }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="side-bar col-md-4">

                    <!-- Test commitment and investment section -->
                    <div id="card2" class="card p-3">
                        @if ($offering->status == 1)
                            <button class="btn1 mb-2">Test commitment</button>
                            <button class="btn2 mb-2">Test investment</button>
                        @endif
                        @if ($offering->status == 2)
                            <button class="btn1 mb-2">Soft commit</button>
                            <button class="btn2 mb-2">Test investment</button>
                        @endif
                        @if ($offering->status == 3 || $offering->status == 4)
                            <button class="btn1 mb-2">Invest now</button>
                        @endif
                        @if ($offering->status == 5)
                            <button class="btn1 mb-2">Join waitlist</button>
                        @endif
                        @if ($offering->status == 6)
                        @endif
                        <h6 class="mt-3 mb-0">Offering size</h6>
                        <p style="font-size: larger;" class="fw-bold mb-3">{{ $offering->offering_size }}</p>
                        <h6 class="mb-0">Deal type</h6>
                        <p style="font-size: larger;" class="fw-bold mb-3">{{ $offering->deal->type }}</p>
                        <h6 class="mb-0">SEC type</h6>
                        <p style="font-size: larger;" class="fw-bold mb-3">{{ $offering->deal->sec_type }}</p>
                        <h6 class="mb-0">Investment type</h6>
                        <p style="font-size: larger;" class="fw-bold mb-3">
                            {{ $offering->classes->first()->investment_type }}</p>
                        <h6 class="mb-0">Close date</h6>
                        <p style="font-size: larger;" class="fw-bold mb-3">{{ $offering->deal->close_date }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
@push('style')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .row .header-box {
            white-space: nowrap;
            overflow: hidden;
        }

        .side-bar {
            margin-top: 4.3rem !important;
        }

        .btn-primary {
            background-color: #E55B13 !important;
            margin: 6px;
        }

        #card2 {
            position: sticky;
            top: 0;
            padding: 5px;
            box-shadow: -1px 0 5px rgba(0, 0, 0, 0.1);
            /* Adds a shadow to the left */
        }

        .btn1 {
            color: white;
            padding: 15px;
            background-color: #e85d0c;
            border-radius: 5px;
            border: 1px solid #e85d0c;
            font-size: large;
        }

        .btn1:hover {
            color: #e85d0c;
            padding: 15px;
            background-color: white;
            border: 1px solid #e85d0c;
            font-size: large;
        }

        .btn2 {
            color: #e85d0c;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            border: 1px solid #e85d0c;
            font-size: large;
        }

        .btn2:hover {
            color: white;
            padding: 15px;
            background-color: #e85d0c;
            border: 1px solid #e85d0c;
            border-radius: 5px;
            font-size: large;
        }

        .pin-icon {
            width: 30px;
            height: 30px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 50%;
        }

        .first-letter {
            width: 50px;
            height: 50px;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
@endpush
