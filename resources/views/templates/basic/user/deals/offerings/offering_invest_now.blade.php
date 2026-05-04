@extends($activeTemplate . 'layouts.master')


@push('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/userDocument.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        .congrats-container {
            text-align: center;
            padding: 50px 20px;
            margin-top: 100px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .check-icon {
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 40px;
        }

        .sparkles {
            color: #c9d1f5;
            font-size: 20px;
        }

        .btn-view-offering {
            margin-top: 20px;
        }

        /* Step Circle and Divider Styling */
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            cursor: not-allowed;
            transition: all 0.3s;
        }

        .step-circle.blue {
            background-color: #007bff;
            color: #fff;
        }

        .step-circle.green {
            background-color: #28a745;
            color: #fff;
            position: relative;
        }

        .step-circle.green::after {
            content: '✔';
            position: absolute;
            font-size: 14px;
            color: #fff;
        }

        .step-circle.inactive {
            background-color: rgb(239, 233, 233);
            color: #6c757d;
        }

        .step-text {
            font-size: 14px;
            margin-top: 6px;
        }

        .step-divider {
            width: 50px;
            height: 2px;
            background-color: #e9ecef;
            margin: 0 10px;
        }

        .step-divider.active {
            background-color: #007bff;
        }

        .divider_body {
            display: flex;
            white-space: nowrap;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            width: 80%;
            justify-content: space-between;
        }

        .content-section {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 20px;
        }

        .items-center {
            justify-items: center;
        }

        #signature-pad {
            border: 2px solid #000;
            width: 100%;
            height: 300px;
            touch-action: none;
            display: block;
            margin: auto;
        }

        .side-bar {
            margin-top: 1.3rem !important;
        }

        #card2 {
            position: sticky;
            top: 0;
            padding: 5px;
            box-shadow: -1px 0 5px rgba(0, 0, 0, 0.1);
        }

        .first-letter {
            width: 50px;
            height: 50px;
            font-size: 24px;
            font-weight: bold;
        }

        .card-hover:hover {
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
            border-color: #007bff;
        }

        .divider {
            position: relative;
            display: flex;
            align-items: center;
            margin: 3rem 2rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #ddd;
        }

        .circle {
            width: 20px;
            height: 20px;
            background-color: #007bff;
            border-radius: 50%;
            position: relative;
            z-index: 1;
        }

        .divider span {
            position: absolute;
            top: 18px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 13px;
            color: #007bff;
        }
    </style>
@endpush

@section('content')
    <div id="v-user-document">
        <retrieve-template :offering="{{ json_encode($offering) }}" :investor="{{ json_encode($investor) }}"
            :funding-methods="{{ json_encode($fundingMethods) }}"
            :user="{{ ($user) }}"
            :investment="{{ $investment ? json_encode($investment) : 'null' }}"
            :classes="{{ $investment ? json_encode($investment->offering->classes) : json_encode($offering->classes) }}"
            :deal="{{ $investment ? json_encode($investment->offering->deal) : json_encode($offering->deal) }}"
            :csrf="'{{ csrf_token() }}'">
        </retrieve-template>

    </div>
@endsection

@push('script')
    <script>
        window.documensoToken = @json($documentToken);
        window.urls = {
            investorSave: "{{ route('user.offerings.invest.storeProfile', $offering->id) }}",
            questionnaireAddress: "{{ route('user.offerings.invest.storeQuestionnaireAddress', $offering->id) }}",
            questionnaire: "{{ route('user.offerings.invest.storeQuestionnaire', $offering->id) }}",
            questionnaireForm: "{{ route('user.offerings.invest.storeQuestionnaireForm', $offering->id) }}",
            investmentSave: "{{ route('user.offerings.invest.storeInvestment', $offering->id) }}",
            investmentUpdate: "{{ route('user.investment.updateInvestment', ['id' => ':id']) }}",
            downloadInvoice: "{{ route('user.downloadInvoice') }}",
            ach: "{{ route('user.stripe.ach.initiate', ['id' => ':id']) }}",
            checkBankAccount: "{{ route('user.checkBankAccount') }}",
            initiateACHPayment: "{{ route('user.initiateACHPayment', ['id' => ':id']) }}",
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="{{ asset('assets/admin/js/app.js') }}"></script>
    @vite(['resources/js/userDocument.js'])
@endpush
