@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow border-0 text-center p-5">
                <div class="text-success mb-4">
                    <i class="bi bi-check-circle-fill fs-1"></i>
                </div>

                <h2 class="fw-bold mb-3">Stripe Onboarding Complete</h2>

                <p class="text-muted fs-5">
                    Your Stripe account is successfully onboarded and verified.
                    your Investment amount sent to the deal owner.
                </p>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="{{ route('user.deals.mydeals') }}" class="btn btn-primary px-4 py-2">
                        Go deals Page
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
