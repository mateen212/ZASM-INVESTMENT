<!-- Alpine.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div x-data="showDeals()" x-init="init()">

    <div class="d-flex justify-content-end mb-3">
        <div class="btn btn--sm btn--base rounded-lg me-2 justify-content-center" title="Grid view" @click="view = 'grid'"
            :class="{ 'active': view === 'grid' }">
            <i class="fas fa-th p-2"></i> <span class="button-label"></span>
        </div>
        <div class="btn btn--sm btn--base rounded-lg " title="Detail view" @click="view = 'detail'"
            :class="{ 'active': view === 'detail' }">
            <i class="fas fa-list p-2"></i> <span class="button-label"></span>
        </div>
    </div>



    <!-- Grid View -->
    <div class="row g-3" x-show="view === 'grid'">
        @foreach ($offerings as $offering)
            <div class="col-md-4">
                <article class="card property--card border-0">
                    @php
                        $firstAsset = $offering->assets->first();
                        $assetImages =
                            $firstAsset && $firstAsset->assetMedia->isNotEmpty()
                                ? $firstAsset->assetMedia->pluck('media_url')->toArray()
                                : ['assets/images/default.png'];
                    @endphp
                    <div class="d-flex justify-content-center align-items-center">
                        <a class="card-img-top" href="{{ route('offering', $offering->uuid) }}">
                            <img id="dealImage_{{ $offering->id }}" src="{{ asset($assetImages[0]) }}"
                                alt="{{ $firstAsset->name ?? 'Deal Image' }}" class="img-fluid asset-hover-image"
                                data-images="{{ json_encode($assetImages) }}"
                                style="width: 100%; height: 180px; object-fit: cover; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                        </a>
                    </div>
                    <div class="card-body px-2 py-3 p-md-3 p-xl-4">
                        <div class="card-body-top">
                            <h5 class="card-title mb-2">
                                <a href="{{ route('offering', $offering->uuid) }}">{{ \Illuminate\Support\Str::title($offering->name) }}</a>
                            </h5>
                            <ul class="card-meta card-meta--one">
                                <li class="card-meta__item card-meta__item__location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span
                                        class="text">{{ implode(
                                            ', ',
                                            array_filter([
                                                @$firstAsset->address,
                                                @$firstAsset->city,
                                                @$firstAsset->state,
                                                @$firstAsset->zip,
                                                @$firstAsset->country,
                                            ]),
                                        ) }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body-middle">
                            <div class="card-progress mb-4">
                                <div class="card-progress__bar">
                                    <div class="card-progress__thumb"
                                        style="width: {{ @$offering->invest_progress }}%;"></div>
                                </div>
                                <span class="card-progress__label fs-12">
                                    {{ @$offering->invests_count }} @lang('Investors') |
                                    {{ showAmount(@$offering->invested_amount) }}
                                    ({{ getAmount(@$offering->invest_progress) }}%)
                                </span>
                            </div>
                            <ul class="card-meta card-meta--two">
                                <li class="card-meta__item">
                                    <span class="subtext">@lang('Property Type')</span>
                                    <div class="text">
                                        {{ @$firstAsset->property_type ?? '--' }}
                                    </div>
                                </li>
                                <li class="card-meta__item">
                                    <span class="subtext">@lang('Profit Schedule')</span>
                                    <div class="text">
                                        {{ @$firstAsset->number_of_units ?? '--' }}
                                    </div>
                                </li>
                                <li class="card-meta__item">
                                    <span class="subtext">@lang('status')</span>
                                    <div class="text">
                                        {{ @$offering->status_text ?? '--' }}
                                    </div>
                                </li>
                                <li class="card-meta__item">
                                    <span class="subtext">@lang('Preferred Return')</span>
                                    <div class="text"> 
                                        {{ @$offering->class->preferred_return_type ?? '--' }}
                                    </div>
                                </li>
                                <li class="card-meta__item">
                                    <span class="subtext">@lang('Holding Period')</span>
                                    <div class="text">
                                        {{ @$offering->asset->exit_date ?? '--' }}
                                    </div>
                                </li>
                                <li class="card-meta__item">
                                    <span class="subtext">@lang('Annualized Return')</span>
                                    <div class="text">
                                        {{ @$offering->preferred_return ?? '--' }}
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body-bottom mb-4">
                            <a class="btn btn--sm btn--base" href="{{ route('offering', $offering->uuid) }}"
                                role="button">@lang('Details')</a>
                            <span class="card-price">{{ $offering->offering_size }}</span>
                        </div>
                    </div>
                </article>
            </div>
        @endforeach
    </div>

    <!-- Detail View -->
    <div class="row g-3" x-show="view === 'detail'">
        @foreach ($offerings as $offering)
            <div class="col-12">
                <article class="card property--card border-0">
                    <div class="row align-items-center">
                        @php
                            $firstAsset = $offering->assets->first();
                            $assetImages =
                                $firstAsset && $firstAsset->assetMedia->isNotEmpty()
                                    ? $firstAsset->assetMedia->pluck('media_url')->toArray()
                                    : ['assets/images/default.png'];
                        @endphp

                        <div class="col-md-3 d-flex justify-content-center align-items-center">
                            <a class="card-img-top" href="{{ route('offering', $offering->uuid) }}">
                                <img id="dealImage_{{ $offering->id }}" src="{{ asset($assetImages[0]) }}"
                                    alt="{{ $firstAsset->name ?? 'Deal Image' }}"
                                    class="img-fluid rounded asset-hover-image"
                                    data-images="{{ json_encode($assetImages) }}"
                                    style="width: 200px; height: 211px; object-fit: cover;">
                            </a>
                        </div>

                        <div class="col-md-7 p-2">
                            <h5 class="card-title mb-2">
                                <a href="{{ route('offering', $offering->uuid) }}">{{ \Illuminate\Support\Str::title($offering->name) }}</a>
                            </h5>
                            <ul class="card-meta card-meta--one">
                                <li class="card-meta__item card-meta__item__location pb-2">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span
                                        class="text">{{ implode(
                                            ', ',
                                            array_filter([
                                                @$firstAsset->address,
                                                @$firstAsset->city,
                                                @$firstAsset->state,
                                                @$firstAsset->zip,
                                                @$firstAsset->country,
                                            ]),
                                        ) }}</span>
                                </li>
                            </ul>

                            <div class="card-progress">
                                <div class="card-progress__bar">
                                    <div class="card-progress__thumb"
                                        style="width: {{ @$offering->invest_progress }}%;"></div>
                                </div>
                                <span class="card-progress__label fs-12">
                                    {{ @$offering->invests_count }} @lang('Investors') |
                                    {{ showAmount(@$offering->invested_amount) }}
                                    ({{ getAmount(@$offering->invest_progress) }}%)
                                </span>
                            </div>

                            <ul class="card-meta card-meta--two row">
                                <li class="card-meta__item col-md-4">
                                    <span class="subtext">@lang('Property Type')</span>
                                    <div class="text">{{ @$firstAsset->property_type ?? '--' }}</div>
                                </li>
                                <li class="card-meta__item col-md-4">
                                    <span class="subtext">@lang('Profit Schedule')</span>
                                    <div class="text">{{ @$firstAsset->number_of_units ?? '--' }}</div>
                                </li>
                                <li class="card-meta__item col-md-4">
                                    <span class="subtext">@lang('status')</span>
                                    <div class="text">{{ @$offering->status_text ?? '--' }}</div>
                                </li>
                                <li class="card-meta__item col-md-4">
                                    <span class="subtext">@lang('Preferred Return')</span>
                                    <div class="text">{{ @$offering->preferred_return_type ?? '--' }}</div>
                                </li>
                                <li class="card-meta__item col-md-4">
                                    <span class="subtext">@lang('Holding Period')</span>
                                    <div class="text">{{ @$offering->asset->exit_date ?? '--' }}</div>
                                </li>
                                <li class="card-meta__item col-md-4">
                                    <span class="subtext">@lang('Annualized Return')</span>
                                    <div class="text">{{ @$offering->preferred_return ?? '--' }}</div>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-2 d-flex flex-column align-items-start">
                            <span class="card-price mb-2">{{ $offering->offering_size }}</span>
                            <a class="btn btn--sm btn--base" href="{{ route('offering', $offering->uuid) }}"
                                role="button">@lang('Details')</a>
                        </div>
                    </div>
                </article>
            </div>
        @endforeach
    </div>

    @if ($offerings->count() == 0)
        <div class="col-12 text-center">
            <p>@lang('No offering found')</p>
        </div>
    @endif

</div>


@push('script')
    <script>
        function showDeals() {
            return {
                view: localStorage.getItem('dealView') || 'grid',
                init() {
                    this.$watch('view', value => localStorage.setItem('dealView', value));
                }
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".asset-hover-image").forEach(img => {
                let images = JSON.parse(img.dataset.images);
                let index = 0;
                let interval;

                img.addEventListener("mouseenter", function() {
                    index = 1;
                    interval = setInterval(() => {
                        if (index >= images.length) index = 0;
                        img.src = images[index];
                        index++;
                    }, 1000);
                });

                img.addEventListener("mouseleave", function() {
                    clearInterval(interval);
                    img.src = images[0];
                });
            });
        });
    </script>
@endpush
@push('style')
    <style>
        .view-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border: 1px solid #0d6efd;
            border-radius: 0.375rem;
            color: #0d6efd;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.1s ease;
        }

        .view-button .button-label {
            display: none;
        }

        .view-button:hover {
            background-color: #0d6efd;
            color: #fff;
        }

        .view-button:active {
            background-color: #0d6efd;
            color: #fff;
        }

        .view-button.active {
            background-color: #0d6efd;
            color: #fff;
        }
    </style>
@endpush
