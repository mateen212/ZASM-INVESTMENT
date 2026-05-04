@php
    $offeringContent  = getContent('latest_offerings.content', true);
    $latestOfferings = App\Models\Offering::with('deal')
        ->where('public_offering', true)
        ->whereHas('manageoffering', function($query) {
            $query->where('display_offering', true);
        })
        ->orderByDesc('created_at')
        ->limit(4)
        ->get();
@endphp


<section class="latest-property py-120 bg-pattern bg-pattern-bottom-right">
    <div class="container ">
        <div class="section-heading style-left">
            <p class="section-heading__subtitle">Latest Deals</p>
            <div class="section-heading__wrapper">
                <h2 class="section-heading__title">Explore Latest Deals</h2>
                <a class="section-heading__link" href="{{ route('deals') }}">
                    <span>@lang('Explore')</span>
                    <i class="las la-long-arrow-alt-right"></i>
                </a>
            </div>
        </div>
        <div class="row gy-4 g-sm-3 g-md-4 justify-content-center">
            @include($activeTemplate . 'partials.explore_all_deals', ['offerings' => @$latestOfferings, 'col' => '4'])
        </div>
    </div>
</section>

<script>
    
</script>