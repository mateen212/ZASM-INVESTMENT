@extends(auth()->check() ? $activeTemplate . 'layouts.frontend' : $activeTemplate . 'layouts.frontend')

@section('content')
    @if ($offering->status == 7 || $offering->public_offering == 0)
        <div class="card" style="display: flex; align-items: center; justify-content: center;">
            <div class="card-body text-center" style="margin-top: 2rem">
                <img src="{{ asset('assets/images/danger.png') }}"
                    style="max-height: 100%; max-width: 60%; object-fit: cover;" />
                <h4 class="mt-4">This page is private</h4>
                <p style="font-size:small;">If you believe this is an error, please contact your sponsor.</p>
            </div>
        </div>
    @else
        <div class="container-fluid ">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <!-- Main Content Section -->
                        <div class="col-md-8 pt-4">
                            <!-- Investment Section -->
                            <div class="mb-5">
                                <h3 class="fw-bold" style="font-size:30px;">
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
                                                        style="height: 100%; width: 100%; object-fit: cover;"
                                                        data-bs-toggle="modal" data-bs-target="#galleryModal">
                                                @else
                                                    <img src="{{ asset('assets/images/default.png') }}"
                                                        alt="Default Property Image"
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
                                            <button type="button" class="btn btn-light w-100 view-all-btn"
                                                data-bs-toggle="modal" data-bs-target="#galleryModal"
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
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="bg-primary text-white text-center d-flex justify-content-center align-items-center first-letter">
                                            {{ substr(optional($offering->investments->first())->primary_sponsor ?? 'N/A', 0, 1) }}
                                        </div>
                                        <span
                                            class="ms-3">{{ optional($offering->investments->first())->primary_sponsor ?? 'N/A' }}</span>
                                    </div>
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
                                                        @if ($key_m->classes()->first()?->pivot->value)
                                                            <tr>
                                                                <td>
                                                                    {{ $key_m->metric_label }}
                                                                </td>
                                                                @foreach ($offering->classes as $class)
                                                                    <td>
                                                                        {{ $key_m->classes()->where('deal_class_id', $class->id)->first()?->pivot->value }}
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
                                            $embedUrl = \Illuminate\Support\Str::replace(
                                                'watch?v=',
                                                'embed/',
                                                $videoUrl,
                                            );
                                        } elseif (\Illuminate\Support\Str::contains($videoUrl, 'youtu.be')) {
                                            $videoId = \Illuminate\Support\Str::after($videoUrl, 'youtu.be/');
                                            $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                                        } elseif (\Illuminate\Support\Str::contains($videoUrl, 'vimeo.com')) {
                                            $videoId = \Illuminate\Support\Str::afterLast($videoUrl, '/');
                                            $embedUrl = 'https://player.vimeo.com/video/' . $videoId;
                                        }
                                    @endphp

                                    @if ($embedUrl)
                                        <iframe width="750" height="450" src="{{ $embedUrl }}" frameborder="0"
                                            allowfullscreen
                                            style="border-radius: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);">
                                        </iframe>
                                    @endif


                                    <hr>
                                    <!-- About offering Section -->
                                    @if (!empty($offering->summary))
                                        <h5 class="fw-bold mb-4 mt-4">About offering</h5>
                                        <p>{!! $offering->summary !!}</p>
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
                                                <p class="fw-bold">{{ $offering->insight->loan_to_value }} </p>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <p class="mb-1 text-muted">Interest rate</p>
                                                <p class="fw-bold">{{ $offering->insight->interest_rate }} </p>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <p class="mb-1 text-muted">Loan term</p>
                                                <p class="fw-bold">{{ $offering->insight->loan_term }} year(s)</p>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <p class="mb-1 text-muted">Loan assumption</p>
                                                <p class="fw-bold">
                                                    {{ $offering->insight->loan_assumption ? 'Assumed' : 'Not Assumed' }}
                                                </p>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <p class="mb-1 text-muted">Interest-only period</p>
                                                <p class="fw-bold">{{ $offering->insight->interest_only_period }} year(s)
                                                </p>
                                            </div>
                                        </div>
                                        <hr>
                                        <!-- Terms and fees Section -->
                                        <h5 class="fw-bold mb-4 mt-4">Terms and fees</h5>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <p class="mb-1 text-muted">Acquisition fee</p>
                                                <p class="fw-bold">{{ $offering->insight->acquisition_fee }} </p>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <p class="mb-1 text-muted">Asset management fee</p>
                                                <p class="fw-bold">{{ $offering->insight->asset_management_fee }} </p>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <p class="mb-1 text-muted">Construction management fee</p>
                                                <p class="fw-bold">{{ $offering->insight->construction_management_fee }}
                                                </p>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <p class="mb-1 text-muted">Disposition fee</p>
                                                <p class="fw-bold">{{ $offering->insight->disposition_fee }} </p>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <p class="mb-1 text-muted">Refinance fee</p>
                                                <p class="fw-bold">{{ $offering->insight->refinance_fee }} </p>
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
                        <div class="side-bar col-md-4 pt-4">
                            <!-- Test commitment and investment section -->
                            <div id="card2" class="card p-3">
                                @if ($offering->status == 1)
                                    <a class="btn1 mb-2"
                                        href="{{ route('user.offerings.investNow', $offering->id) }}">Test commitment</a>
                                    <a href="{{ route('user.offerings.investNow', $offering->id) }}"
                                        class="btn1 mb-2">Test investment</a>
                                @endif
                                @if ($offering->status == 2)
                                    <a class="btn1 mb-2"
                                        href="{{ route('user.offerings.investNow', $offering->id) }}">Soft commit</a>
                                    <a href="{{ route('user.offerings.investNow', $offering->id) }}"
                                        class="btn1 mb-2">Test investment</a>
                                @endif
                                @if ($offering->status == 3)
                                    <a href="{{ route('user.offerings.investNow', $offering->id) }}"
                                        class="btn1 mb-2">Hard Commit Investment</a>
                                @endif
                                @if ($offering->status == 4)
                                    <a href="{{ route('user.offerings.investNow', $offering->id) }}"
                                        class="btn1 mb-2">Invest now</a>
                                @endif
                                @if ($offering->status == 5)
                                    <a class="btn1 mb-2"
                                        href="{{ route('user.offerings.investNow', $offering->id) }}">Join waitlist</a>
                                @endif
                                @if ($offering->status == 6)
                                @endif
                                <h6 class="mt-3 mb-0">Offering size</h6>
                                <p style="font-size: larger;" class="fw-bold mb-3">
                                    {{ $offering->offering_size ?? 'N/A' }}
                                </p>

                                <h6 class="mb-0">Deal type</h6>
                                <p style="font-size: larger;" class="fw-bold mb-3">
                                    {{ $offering->deal?->type ?? 'N/A' }}
                                </p>
                                <h6 class="mb-0">SEC type</h6>
                                <p style="font-size: larger;" class="fw-bold mb-3">
                                    {{ $offering->deal?->sec_type ?? 'N/A' }}
                                </p>

                                <h6 class="mb-0">Investment type</h6>
                                <p style="font-size: larger;" class="fw-bold mb-3">
                                    {{ $offering->classes->first()?->investment_type ?? 'N/A' }}
                                </p>

                                <h6 class="mb-0">Close date</h6>
                                <p style="font-size: larger;" class="fw-bold mb-3">
                                    {{ $offering->deal?->close_date ?? 'N/A' }}
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('.gallery-carousel').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                fade: true,
                asNavFor: '.gallery-thumbnails',
                prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-angle-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fas fa-angle-right"></i></button>'
            });

            $('.gallery-thumbnails').slick({
                slidesToShow: 6,
                slidesToScroll: 1,
                asNavFor: '.gallery-carousel',
                dots: false,
                arrows: false,
                centerMode: true,
                infinite: true,
                centerPadding: '0px',
                focusOnSelect: true,
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        centerMode: false,
                        variableWidth: false,
                        infinite: false
                    }
                }]
            });

            // Reset carousel position when modal opens
            $('#galleryModal').on('shown.bs.modal', function() {
                $('.gallery-carousel').slick('setPosition');
                $('.gallery-thumbnails').slick('setPosition');
            });
        });

        function updateMainImage(newSrc) {
            $('#mainImage').attr('src', newSrc);
        }

        function resetMainImage() {
            var originalSrc =
                "{{ $offering->assets->isNotEmpty() && $offering->assets->first()->assetMedia->isNotEmpty() ? asset($offering->assets->first()->assetMedia->first()->media_url) : asset('assets/images/default.png') }}";
            $('#mainImage').attr('src', originalSrc);
        }
    </script>
@endpush

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Existing styles */
        .container-fluid {
            /* inthis add 80px padding from left right and bottom and 0 from top*/
            padding: 0 80px 80px 80px;
        }

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

        /* Gallery Modal Styles */
        #galleryModal {
            z-index: 1060 !important;
        }

        .gallery-carousel {
            position: relative;
            margin-bottom: 20px;
        }

        .gallery-carousel .slick-slide img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 8px;
        }

        /* Navigation Arrows */
        .gallery-carousel .slick-prev,
        .gallery-carousel .slick-next {
            font-size: 0;
            line-height: 0;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1070 !important;
            width: 40px;
            height: 40px;
            padding: 0;
            cursor: pointer;
            color: white;
            border: none;
            outline: none;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .gallery-carousel .slick-prev {
            left: 20px;
        }

        .gallery-carousel .slick-next {
            right: 20px;
        }

        .gallery-carousel .slick-prev:before,
        .gallery-carousel .slick-next:before {
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: white;
            -webkit-font-smoothing: antialiased;
            font-size: 24px;
        }

        .gallery-carousel .slick-prev:before {
            content: '\f104';
        }

        .gallery-carousel .slick-next:before {
            content: '\f105';
        }

        /* Thumbnails */
        .gallery-thumbnails {
            margin: 20px auto 0;
            max-width: 90%;
        }

        .gallery-thumbnails .thumbnail-item {
            margin: 0 5px;
            cursor: pointer;
        }

        .gallery-thumbnails .thumbnail-item img {
            height: 60px;
            width: 100%;
            object-fit: cover;
            opacity: 0.6;
            transition: opacity 0.3s;
        }

        .gallery-thumbnails .thumbnail-item.slick-current img {
            opacity: 1;
        }

        .gallery-thumbnails .slick-track {
            margin: 0 auto;
        }

        .gallery-thumbnails .slick-prev,
        .gallery-thumbnails .slick-next {
            z-index: 10 !important;
            background: rgba(0, 0, 0, 0.5) !important;
        }

        /* New layout improvements */
        .page-title {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            padding: 0;
        }

        .offering-container {
            padding-top: 1rem;
        }

        .section-padding {
            padding: 40px 0;
        }

        .breadcrumb-area {
            display: none;
        }

        .gallery-section {
            margin-top: 0;
            margin-bottom: 2rem;
        }

        .main-content {
            padding-top: 1rem;
        }

        .header-top {
            padding: 0.75rem 0;
        }

        .modal-dialog.modal-xl {
            max-width: 1140px;
            margin: 1.75rem auto;
        }

        #galleryModal .modal-content {
            position: relative !important;
            overflow: hidden !important;
        }

        /* Responsive styles for mobile */
        @media (max-width: 767px) {
            .gallery-thumbnails .thumbnail-item {
                width: 70px !important;
                margin: 0 3px !important;
                padding: 0;
            }

            .gallery-thumbnails {
                padding: 0 30px;
                margin: 10px auto 0;
            }

            .gallery-thumbnails .slick-track {
                margin-left: 0 !important;
                display: flex !important;
                gap: 0 !important;
            }

            .gallery-thumbnails .thumbnail-item img {
                width: 100% !important;
                height: 50px !important;
            }

            .responsive-image-container {
                height: 45vh;
            }

        }

        /* Remove side margins */
        .dashboard>.mx-3 {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .responsive-image-container {
            height: 45vh;
        }
    </style>
@endpush

<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-white">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="gallery-carousel">
                    @foreach ($offering->assets as $asset)
                        @foreach ($asset->assetMedia as $media)
                            <div>
                                <img src="{{ asset($media->media_url) }}" alt="Property Image">
                            </div>
                        @endforeach
                    @endforeach
                </div>
                <div class="gallery-thumbnails">
                    @foreach ($offering->assets as $asset)
                        @foreach ($asset->assetMedia as $media)
                            <div class="thumbnail-item">
                                <img src="{{ asset($media->media_url) }}" alt="{{ $asset->name }}">
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
