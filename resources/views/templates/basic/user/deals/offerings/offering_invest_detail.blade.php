@extends($activeTemplate . 'layouts.master')
@push('style')
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
    </style>
@endpush

@section('content')
    <div class="container my-4" x-data="investAlpine()" x-init="init()">

        <template x-if="investmentSuccess">
            <div class="container">
                <div class="congrats-container">
                    <div class="sparkles">✨ ✨ ✨</div>
                    <div class="check-icon">
                        ✔
                    </div>
                    <h1 class="mt-4">Congratulations!</h1>
                    <p class="mt-3">You have updated a commitment in <strong>{{ $offering->name }}</strong>.</p>
                    <p>Once the offering is open to investments, you can easily turn your commitment into an investment.</p>
                    <a class="btn btn-primary btn-view-offering"
                        href="{{ route('user.offerings.offering', $offering->id) }}">View Offering</a>
                </div>
            </div>
        </template>
        <template x-if="!investmentSuccess">
            <div class="row">
                <!-- Main Content Section -->
                <h2 class="fw-bold ">Invest in {{ $offering->name }}</h2>
                <hr>
                <style>
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
                        /* Blue color for active step */
                        color: #fff;
                        /* White text color */
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

                    /* Tab Content Styling */
                    .content-section {
                        display: none;
                    }

                    .content-section.active {
                        display: block;
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
                </style>
                <!-- Step Circles and Dividers -->
                <div class="divider_body">
                    <template x-for="(step, index) in steps" :key="step.id">
                        <div class="text-center flex flex-col items-center">
                            <div class="step-circle" :class="step.status"
                                :class="{ 'green': completedSteps.includes(step.id) }">
                            </div>
                            <div class="step-text" x-text="step.label"></div>
                            {{--  <div class="step-divider" :class="{ 'active': step.status === 'active' }"></div>  --}}
                        </div>
                    </template>
                </div>
                <div class="col-md-8">
                    <!-- Investor Section -->
                    <div class="content-section" :class="{ 'active': currentStep === 'InvestorMethod' }">
                        <h4 class="mb-3">1. Investor</h4>
                        <p class="mb-3">Select the profile (investment entity) to invest as, and choose the investment
                            class
                            to invest in.</p>
                        <div class="mb-3 row">
                            <label class="col-md-3 col-form-label">Profile <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" x-model="investorForm.investor_profile_id"
                                    @change="changeProfile()">
                                    <option value="" disabled selected>Select a profile</option>
                                    <template x-for="profile in profiles" :key="profile.id">
                                        <option :value="profile.id" x-text="profile.profile_fname"></option>
                                    </template>
                                    <option value="view">+ Create new profile</option>
                                </select>
                            </div>
                        </div>
                        <!-- Investment Class Field -->
                        <div class="mb-3 row">
                            <label class="col-md-3 col-form-label">Investment Class<span
                                    class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" x-model="investorForm.deal_class_id">
                                    <option value="" disabled selected>Select a class</option>
                                    @foreach ($offering->classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->equity_class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" @click="submitInvestorForm()" class="btn btn-primary mt-3"
                            :disabled="!investorForm.investor_profile_id || !investorForm.deal_class_id">Next</button>
                    </div>
                    <!-- Investment Section -->
                    <div class="content-section" :class="{ 'active': currentStep === 'InvestmentMethod' }">
                        <h4 class="mb-3">2. Investment</h4>
                        <p class="mb-3">Input the amount you would like to invest and the method you will use.</p>
                        <!-- Investment Amount Field -->
                        <div class="mb-3 row">
                            <label class="col-md-3 col-form-label">Investment amount<span
                                    class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <input type="text" x-on:input="moneyFormat($el)" id="investment_amount"
                                    name="investment_amount" class="form-control" placeholder="$0"
                                    x-model="investmentForm.investment_amount" required>
                                <p style="font-size: small;" class="text-muted">Minimum is $50,000</p>
                            </div>
                        </div>
                        <!-- Funding Method Field -->
                        <div class="mb-3 row">
                            <label class="col-md-3 col-form-label">Funding method<span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" x-model="investmentForm.funding_method">
                                    <option value="" disabled selected>Select a method</option>
                                    @foreach ($fundingMethods as $method)
                                        <option value="{{ $method }}">{{ camelCaseToTitle($method) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex  mt-3">
                            <button type="button" @click="showStep('InvestorMethod')"
                                class="btn btn-secondary me-3">Previous</button>
                            <button type="submit" @click="submitInvestment()" class="btn btn-primary"
                                :disabled="!investmentForm.investment_amount || !investmentForm.funding_method">Next</button>
                        </div>
                    </div>
                    <!-- Questionnaire Section -->
                    <div class="content-section" :class="{ 'active': currentStep === 'QuestionnaireW9FormMethod' }">
                        <h4 class="mb-3">3.1 Questionnaire</h4>
                        <p class="mb-3">Please complete the investor suitability questionnaire below. This is a
                            requirement
                            from the SEC to collect basic information. Note that questions marked with an asterisk are
                            required
                            fields.</p>
                        <div class="mb-3 row">
                            <label for="name" class="col-md-3 col-form-label">Questionnaire<span
                                    class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" id="investorProfile" class="form-select custom-dropdown"
                                    x-model="investmentForm.questionnaire_id" @change="changeQuestionnaire()">
                                    <option value="" disabled selected>Select a profile</option>
                                    <template x-for="questionnaire in questionnaires" :key="questionnaire.id">
                                        <option :value="questionnaire.id" x-text="questionnaire.first_name"></option>
                                    </template>
                                    <option value="view">+ Add questionnaire</option>
                                </select>
                            </div>
                        </div>
                        <h4 class="mb-3">3.2 W-9 form</h4>
                        <p class="mb-3">Please complete the W-9 form below. This is a requirement from the IRS to collect
                            taxpayer information. Note that questions marked with an asterisk are required fields.</p>
                        <div class="mb-3 row">
                            <label for="name" class="col-md-3 col-form-label">W-9 form<span
                                    class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-control not-allowed" name="w9_form" value="w9_form"
                                    x-model="investmentForm.w9_form" @change="changeW9form">
                                    <option value="" disabled selected>Select a W-9 form</option>
                                    <template x-for="profile in profiles" :key="profile.id">
                                        <option :value="profile.id"
                                            x-text="`${profile.profile_fname} ${profile.profile_lname} W-9 Form`"></option>
                                    </template>
                                    <option value="form">select </option>

                                </select>

                            </div>
                        </div>
                        <div class="d-flex  mt-3">
                            <button type="button" @click="showStep('InvestmentMethod')"
                                class="btn btn-secondary me-3">Previous</button>
                            <button type="submit" @click="submitQuestionnaire()" class="btn btn-primary"
                                :disabled="!investmentForm.questionnaire_id || !investmentForm.w9_form">Next</button>
                        </div>
                    </div>
                    <div class="content-section" :class="{ 'active': currentStep === 'W9FormMethod' }">
                        <h4 class="mb-3">3.2 W-9 form</h4>
                        <p class="mb-3">Please complete the W-9 form below. This is a requirement from the IRS to collect
                            taxpayer information. Note that questions marked with an asterisk are required fields.</p>
                        <div class="mb-3 row">
                            <label for="name" class="col-md-3 col-form-label">W-9 form<span
                                    class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-control not-allowed" name="w9_form" value="w9_form"
                                    x-model="investmentForm.w9_form" @change="changeW9form">
                                    <option value="" disabled selected>Select a W-9 form</option>
                                    <template x-for="profile in profiles" :key="profile.id">
                                        <option :value="profile.id"
                                            x-text="`${profile.profile_fname} ${profile.profile_lname} W-9 Form`"></option>
                                    </template>
                                    <option value="form">select </option>

                                </select>

                            </div>
                        </div>
                        <div class="d-flex  mt-3">
                            <button type="button" @click="showStep('InvestmentMethod')"
                                class="btn btn-secondary me-3">Previous</button>
                            <button type="submit" @click="submitQuestionnairew9()" class="btn btn-primary"
                                :disabled="!investmentForm.w9_form">Next</button>
                        </div>
                    </div>
                    <div class="content-section" :class="{ 'active': currentStep === 'QuestionnaireMethod' }">
                        <h4 class="mb-3">3.1 Questionnaire</h4>
                        <p class="mb-3">Please complete the investor suitability questionnaire below. This is a
                            requirement
                            from the SEC to collect basic information. Note that questions marked with an asterisk are
                            required
                            fields.</p>
                        <div class="mb-3 row">
                            <label for="name" class="col-md-3 col-form-label">Questionnaire<span
                                    class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select" id="investorProfile" class="form-select custom-dropdown"
                                    x-model="investmentForm.questionnaire_id" @change="changeQuestionnaire()">
                                    <option value="" disabled selected>Select a profile</option>
                                    <template x-for="questionnaire in questionnaires" :key="questionnaire.id">
                                        <option :value="questionnaire.id" x-text="questionnaire.first_name"></option>
                                    </template>
                                    <option value="view">+ Add questionnaire</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex  mt-3">
                            <button type="button" @click="showStep('InvestmentMethod')"
                                class="btn btn-secondary me-3">Previous</button>
                            <button type="submit" @click="submitQuestionnaires()" class="btn btn-primary"
                                :disabled="!investmentForm.questionnaire_id">Next</button>
                        </div>
                    </div>
                    <!-- E-Signature Section -->
                    <div class="content-section" :class="{ 'active': currentStep === 'E_signatureMethod' }">
                        <h4 class="mb-3">4. E-Signature</h4>
                        <p class="mb-3">To invest in this offering, please sign this document.</p>
                        <p class="mb-3">It is recommended to disable your browser's autofill while signing.<br>Some
                            fields
                            are pre-populated with information from the previous step. <a href="#"
                                class="text-primary">Learn more</a></p>
                        <div class="border p-4">
                            <button class="btn btn-primary" id="add-signature"
                                style="
                                    background-color: #071251; 
                                    border: none;
                                    border-radius: 25px;
                                    padding: 9px 18px;
                                    font-weight: bold;
                                    font-size: 16px;
                                    color: #fff;
                                    box-shadow: 0 4px 6px rgba(0, 123, 255, 0.4);
                                    transition: all 0.3s ease;
                                    cursor: pointer;
                                "
                                data-bs-toggle="modal" data-bs-target="#addSignatureModal">
                            </button>
                        </div>
                        <div class="d-flex  mt-3">
                            <button type="button" @click="submit_e_sign_previous()"
                                class="btn btn-secondary me-3">Previous</button>
                            <button type="submit" @click="submitEsignature()" class="btn btn-primary"
                            >Next</button>
                        </div>
                    </div>
                    <div class="content-section" :class="{ 'active': currentStep === 'AccreditationMethod' }">
                        <!-- Content for AccreditationMethod -->
                        <div class="card-body">

                            <p class="card-text">Verify your accreditation status for <strong>mateen a. zahid</strong>. <a
                                    href="#">Learn more</a>.</p>
                            <div class="d-flex justify-content-center align-items-center" style="">
                                <img src="https://img.icons8.com/ios-filled/200/000000/document.png" alt="Document Icon">
                            </div>
                            <button class="btn btn-primary mb-3">Connect with Parallel Markets</button>
                            <p>Alternatively, <a href="#" data-bs-toggle="modal"
                                    data-bs-target="#addLetterModal">upload an accreditation letter</a> to be reviewed by
                                your
                                sponsor.</p>
                        </div>
                        <div class="d-flex  mt-3">
                            <button type="button" @click="showStep('E_signatureMethod')"
                                class="btn btn-secondary me-3">Previous</button>
                            <button type="submit" @click="submitAccreditation()" class="btn btn-primary"
                                :disabled="!investmentForm.questionnaire_id || !investmentForm.w9_form">Next</button>
                        </div>
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="side-bar col-md-4">
                    <!-- Test commitment and investment section -->
                    <div id="card2" class="card p-3">
                        <div class="image-box h-100">
                            <div class="d-flex justify-content-center align-items-center bg-light"
                                style="border: 1px solid #ccc;">
                                @php
                                    $image = $offering->assets->first()?->assetMedia->first()?->media_url;
                                    if($image == null){
                                        $image = asset('assets/images/download.svg');
                                    }else{
                                        $image = asset($image);
                                    }
                                @endphp
                                <img src="{{ $image }}"
                                    style="max-height: 100%; max-width: 100%; object-fit: contain;" />
                            </div>
                            </div>
                        </div>
                        <h4 class="mt-3 mb-0">{{ $offering->name }}</h4>
                        <h6 class="mt-3 mb-0">Offering size</h6>
                        <p style="font-size: larger;" class="fw-bold mb-3 primary">${{ $offering->offering_size }}</p>
                        <h6 class="mb-0">SEC type</h6>
                        <p style="font-size: larger;" class="fw-bold mb-3">{{ $offering->deal->sec_type }}</p>
                        <h6 class="mb-0">Investment type</h6>
                        <p style="font-size: larger;" class="fw-bold mb-3">
                            {{ $offering->classes->first()->investment_type }}
                        </p>
                    </div>
                </div>

                <div class="deal-modal modal center fade" id="addSignatureModal" tabindex="-1"
                    aria-labelledby="addSignatureModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h5 class="modal-title" id="addSignatureModalLabel">Add Your Signature</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <!-- Tab Navigation -->
                                <ul class="nav nav-tabs mb-3" id="signatureTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="draw-tab" data-bs-toggle="tab"
                                            data-bs-target="#draw" type="button" role="tab"
                                            @click="setTab('draw')">Draw</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="type-tab" data-bs-toggle="tab"
                                            data-bs-target="#type" type="button" role="tab"
                                            @click="setTab('type')">Type</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="upload-tab" data-bs-toggle="tab"
                                            data-bs-target="#upload" type="button" role="tab"
                                            @click="setTab('upload')">Upload</button>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content" id="signatureTabContent">
                                    <!-- Draw Tab -->
                                    <div class="tab-pane fade show active" id="draw" role="tabpanel">
                                        <!-- OpenSignLabs Widget for Draw -->
                                        <canvas x-ref="canvas" class="signature-pad" id="signature-pad"></canvas>
                                        <div class="mt-3 text-end">
                                            <button @click="clear" class="btn btn-danger">Clear</button>
                                            <button @click="save" class="btn btn-success">Save</button>
                                        </div>
                                        <!-- Preview for the drawn signature -->
                                        <div class="mt-3">
                                            <p class="text-muted">Preview:</p>
                                            <img id="drawPreview" class="border" style="max-width: 100%;" />
                                        </div>
                                        <div id="signature-image" class="mt-3">
                                            <template x-if="signatureDataUrl">
                                                <img :src="signatureDataUrl" class="img-fluid" />
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Type Tab -->
                                    <div class="tab-pane fade" id="type" role="tabpanel">
                                        <input type="text" x-model="typedSignature" class="form-control mt-2"
                                            placeholder="Type your signature" />
                                        <p class="mt-3">Preview: <span x-text="typedSignature"
                                                class="font-cursive text-lg"></span></p>
                                    </div>

                                    <!-- Upload Tab -->
                                    <div class="tab-pane fade" id="upload" role="tabpanel">
                                        <input type="file" id="signatureUpload" class="form-control mt-2"
                                            @change="uploadSignature($event)" />
                                        <p class="mt-3 text-muted">Max file size: 40 MB</p>
                                        <template x-if="uploadedSignature">
                                            <img :src="uploadedSignature" alt="Uploaded Signature"
                                                class="img-fluid mt-3">
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success" @click="insertSignature()">Insert
                                    Signature</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="addLetterModal" aria-labelledby="addLetterModalLabel" tabindex="-1"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Upload Accreditation Letter</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Choose a file to upload your accreditation letter:</p>
                                <input type="file" @change="handleFileUpload" class="form-control">
                                <template x-if="fileName">
                                    <p class="mt-3">Selected File: <strong x-text="fileName"></strong></p>
                                </template>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">Close</button>
                                <button type="button" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="deal-modal modal right fade" id="addInvestorProfileModal" tabindex="-1"
                    aria-labelledby="addInvestorProfileModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content px-2">
                            <div class="modal-header row bg-primary text-white" style="height:80px;">
                                <h5 class="modal-title col text-white">Add Profile</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{-- Deal Form Body --}}
                                <div>
                                    @csrf
                                    <div class="mb-3">
                                        <label for="profileType" class="form-label">Profile Type<span
                                                class="text-danger">*</span></label>
                                        <select id="profileType" name="profileType" class="form-select "
                                            x-model="investorProfileForm.profile_type" required>
                                            <option value="" selected disabled>Select a type</option>
                                            <option value="Individual">Individual</option>
                                            <option value="joint_tenancy">Joint Tenancy</option>
                                            <option value="lcp">LLC,
                                                corporation, partnership, trust, IRA or 401(k)</option>
                                        </select>
                                    </div>

                                    <template x-if="investorProfileForm.profile_type === 'Individual'">
                                        <div class="mb-3">
                                            <label for="profile_fname" class="form-label">first name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="profile_fname" name="profile_fname"
                                                class="form-control " x-model="investorProfileForm.profile_fname"
                                                required>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'Individual'">
                                        <div class="mb-3">
                                            <label for="profile_mname" class="form-label">middle name</label>
                                            <input type="text" id="profile_mname" name="profile_mname"
                                                class="form-control " x-model="investorProfileForm.profile_mname"
                                                required>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'Individual'">
                                        <div class="mb-3">
                                            <label for="profile_lname" class="form-label">last name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="profile_lname" name="profile_lname"
                                                class="form-select " x-model="investorProfileForm.profile_lname" required>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'Individual'">
                                        <div class="mb-3">
                                            <label for="province" class="form-label">Distribution method</label>
                                            <select id="province" name="province" class="form-select "
                                                x-model="investorProfileForm.profile_distribution" required>
                                                <option value="ACH">ACH (recommended)</option>
                                                <option value="Check">Check</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </template>




                                    <!-- </template> -->
                                    <template x-if="investorProfileForm.profile_type === 'joint_tenancy'">
                                        <div class="mb-3">
                                            <label for="profile_fname" class="form-label">Investor 1 first name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="profile_fname" name="profile_fname"
                                                class="form-control " x-model="investorProfileForm.profile_fname"
                                                required>
                                        </div>
                                    </template>

                                    <template x-if="investorProfileForm.profile_type === 'joint_tenancy'">
                                        <div class="mb-3">
                                            <label for="profile_mname" class="form-label">Investor 1 middle name</label>
                                            <input type="text" id="profile_mname" name="profile_mname"
                                                class="form-control " x-model="investorProfileForm.profile_mname"
                                                required>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'joint_tenancy'">
                                        <div class="mb-3">
                                            <label for="profile_lname" class="form-label">Investor 1 last name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="profile_lname" name="profile_lname"
                                                class="form-select " x-model="investorProfileForm.profile_lname" required>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'joint_tenancy'">
                                        <div class="mb-3">
                                            <label for="profile_distribution" class="form-label">Distribution
                                                method</label>
                                            <select id="profile_distribution" name="profile_distribution"
                                                class="form-select " x-model="investorProfileForm.profile_distribution"
                                                required>
                                                <option value="ACH">ACH (recommended)</option>
                                                <option value="Check">Check</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'joint_tenancy'">
                                        <div class="mb-3">
                                            <h6 class="fw-bold mb-0">Joint investor details</h6>
                                            <p class="fw-light">After you complete signing, Investor 2 will receive an
                                                email
                                                with
                                                e-sign invitation</p>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'joint_tenancy'">
                                        <div class="mb-3">
                                            <label for="profile_fname2" class="form-label">Investor 2 first name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="profile_fname2" name="profile_fname2"
                                                class="form-control" x-model="investorProfileForm.profile_fname2"
                                                required>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'joint_tenancy'">
                                        <div class="mb-3">
                                            <label for="profile_mname2" class="form-label">Investor 2 middle name</label>
                                            <input type="text" id="profile_mname2" name="profile_mname2"
                                                class="form-control" x-model="investorProfileForm.profile_mname2"
                                                required>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'joint_tenancy'">
                                        <div class="mb-3">
                                            <label for="profile_lname2" class="form-label">Investor 2 last name<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="profile_lname2" name="profile_lname2"
                                                class="form-control" x-model="investorProfileForm.profile_lname2"
                                                required>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'joint_tenancy'">
                                        <div class="mb-3">
                                            <label for="profile_email2" class="form-label">Investor 2 email address<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="profile_email2" name="profile_email2"
                                                class="form-control" x-model="investorProfileForm.profile_email2"
                                                required>
                                        </div>
                                    </template>
                                    <!-- </template> -->
                                    <template x-if="investorProfileForm.profile_type === 'lcp'">
                                        <div class="mb-3">
                                            <label for="custodian" class="form-label">Is this a custodian based IRA or
                                                401(k)</label>
                                            <select id="custodian" name="custodian" class="form-select "
                                                x-model="investorProfileForm.custodian" required>
                                                <option value="" disabled selected>Select </option>
                                                <option value="true">Yes</option>
                                                <option value="false">No</option>
                                            </select>
                                        </div>
                                    </template>
                                    <template x-if="investorProfileForm.profile_type === 'lcp'">
                                        <div class="mb-3">
                                            <p class="fw-light">Choose 'yes' if a custodian needs to sign, and 'no' if only
                                                the
                                                investor needs to sign. Choose 'no' if this is not an IRA or 401(k).</p>
                                        </div>
                                    </template>
                                    <template
                                        x-if="investorProfileForm.profile_type === 'lcp' && investorProfileForm.custodian === 'false'">
                                        <div class="mb-3">
                                            <label for="profile_entity_name" class="form-label">Entity Name</label>
                                            <input type="text" id="profile_entity_name" name="profile_entity_name"
                                                class="form-control " x-model="investorProfileForm.profile_entity_name">
                                        </div>
                                    </template>
                                    <template
                                        x-if="investorProfileForm.profile_type === 'lcp' && investorProfileForm.custodian == 'true'">
                                        <div class="mb-3">
                                            <label for="profile_ira_name" class="form-label">Legal IRA name</label>
                                            <input type="text" id="profile_ira_name" name="profile_ira_name "
                                                class="form-control" x-model="investorProfileForm.profile_ira_name">
                                        </div>
                                    </template>
                                    {{--  IRA company  --}}
                                    <template
                                        x-if="investorProfileForm.profile_type == 'lcp' && investorProfileForm.custodian == 'true'">
                                        <div class="mb-3">
                                            <label for="profile_ira_company" class="form-label">IRA Company</label>
                                            <select id="profile_ira_company" name="profile_ira_company "
                                                class="form-control" x-model="investorProfileForm.profile_ira_company">
                                                <option value="" disabled selected>Select </option>
                                                <option id="advanta" value="advanta">Advanta</option>
                                                <option id="altoira" value="altoira">Alto IRA</option>
                                                <option id="cama_plan" value="cama_plan">Cama Plan IRA</option>
                                                <option id="community_national" value="community_national">Community
                                                    National
                                                    Bank</option>
                                                <option id="digital_trust" value="digital_trust">Digital Trust</option>
                                                <option id="direct_ira" value="direct_ira">Directed IRA (Directed Trust
                                                    Company)</option>
                                                <option id="equity_trust" value="equity_trust">Equity Trust Company
                                                </option>
                                                <option id="forge_trust" value="forge_trust">Forge Trust Company</option>
                                                <option id="horizon_trust" value="horizon_trust">Horizon Trust Company
                                                </option>
                                                <option id="inspira" value="inspira">Inspira</option>
                                                <option id="ira_club" value="ira_club">IRA Club</option>
                                                <option id="irar_trust" value="irar_trust">IRAR Trust Company</option>
                                                <option id="madison_trust" value="madison_trust">Madison Trust Company
                                                </option>
                                                <option id="mainstar" value="mainstar">Mainstar Trust Company</option>
                                                <option id="mainstar_trust" value="mainstar_trust">Mainstar Trust Company
                                                </option>
                                                <option id="midland_trust" value="midland_trust">Midland Trust IRA
                                                </option>
                                                <option id="millennium_trust" value="millennium_trust">Millennium Trust
                                                    Company</option>
                                                <option id="nuview" value="nuview">NuView Trust Company</option>
                                                <option id="pacific_trust" value="pacific_trust">Pacific Premier Trust
                                                </option>
                                                <option id="provident_trust" value="provident_trust">Provident Trust
                                                    Company
                                                </option>
                                                <option id="quest_trust" value="quest_trust">Quest Trust Company</option>
                                                <option id="specialized_trust" value="specialized_trust">Specialized Trust
                                                    Company</option>
                                                <option id="entrust_group" value="entrust_group">The Entrust Group
                                                </option>
                                                <option id="vantage_ira" value="vantage_ira">Vantage IRA</option>
                                                <option id="woodtrust_bank" value="woodtrust_bank">WoodTrust Bank IRA
                                                </option>
                                                <option id="other" value="other">Other</option>
                                            </select>
                                    </template>
                                    <template
                                        x-if="investorProfileForm.profile_type === 'lcp' && investorProfileForm.custodian === 'false' || investorProfileForm.custodian === 'true' ">
                                        <div class="mb-3">
                                            <label for="province" class="form-label">Distribution method</label>
                                            <select id="province" name="province" class="form-select "
                                                x-model="investorProfileForm.profile_distribution" required>
                                                <option value="ACH">ACH (recommended)</option>
                                                <option value="Check">Check</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </template>
                                    <div class="d-flex justify-content-start">
                                        <button type="submit" class="btn btn-primary me-2"
                                            @click="submitInvestorProfileForm()">Add Profile</button>
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="deal-modal modal right fade" id="addQuestionModal" tabindex="-1"
                    aria-labelledby="addQuestionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content px-2">
                            <div class="modal-header row bg-primary text-white" style="height:80px;">
                                <h5 class="modal-title col text-white">Add questionnaire</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{-- Deal Form Body --}}
                                <div>
                                    <div class="divider">
                                        <div class="circle"></div>
                                        <span>Personal</span>
                                    </div>
                                    <div class="mb-3" style="background-color:aliceblue">
                                        <div class="p-2">
                                            <h6 class="fw-bold">Disclaimer</h6>
                                            <p class="text-black" style="font-size:small;">The information contained
                                                herein is
                                                being furnished in order to enable you to determine whether a sale of Class
                                                A -
                                                Limited partners membership units (“Units”) in Test LLC (the “Company”), may
                                                be
                                                made to the undersigned (the “Investor”) without (i) registration of Units
                                                under
                                                the Securities Act of 1933, as amended, or any applicable state securities
                                                laws
                                                or (ii) registration of the Company under the Investment Company Act of
                                                1940, as
                                                amended. This Questionnaire is not an offer to purchase or acceptance of an
                                                offer to sell Units, but is, in fact, a response to a solicitation of
                                                information to provide you a basis for determining the appropriateness of
                                                any
                                                sale to the undersigned prospective Investor.</p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First name<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="first_name" name="first_name" class="form-control"
                                            x-model="QuestionnaireForm.first_name" @blur="validateField('first_name')"
                                            required>
                                        <span class="text-danger" x-show="errors.first_name"
                                            x-text="errors.first_name"></span>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">Last name</label>
                                        <input type="text" id="last_name" name="last_name" class="form-control"
                                            x-model="QuestionnaireForm.last_name" @blur="validateField('last_name')"
                                            required>
                                        <span class="text-danger" x-show="errors.last_name"
                                            x-text="errors.last_name"></span>
                                    </div>

                                    <!-- Telephone -->
                                    <div class="mb-3">
                                        <label for="telephone" class="form-label">Telephone<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="telephone" name="telephone" class="form-select"
                                            x-model="QuestionnaireForm.telephone" @blur="validateField('telephone')"
                                            required>
                                        <span class="text-danger" x-show="errors.telephone"
                                            x-text="errors.telephone"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <select class="form-select" id="address" class="form-select custom-dropdown"
                                            x-model="QuestionnaireForm.address" @change="changeAddress()">
                                            <option value="" disabled selected>Select a profile</option>
                                            <option value="">Select</option>
                                            <template x-for="address in addresses" :key="address.id">
                                                <option :value="address.id" x-text="formatAddress(address)"></option>
                                            </template>
                                            <option value="address">+ Add address</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="resident_of_usa" class="form-label">How long have you been a resident
                                            of
                                            your state of residence?<span class="text-danger">*</span></label>
                                        <input type="text" id="resident_of_usa" name="resident_of_usa"
                                            class="form-control" x-model="QuestionnaireForm.resident_of_usa"
                                            @blur="validateField('resident_of_usa')" required>
                                        <span class="text-danger" x-show="errors.resident_of_usa"
                                            x-text="errors.resident_of_usa"></span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="birth_date" class="form-label">Birth date</label>
                                        <input type="date" onclick="this.showPicker()" id="birth_date" name="birth_date" class="form-control "
                                            x-model="QuestionnaireForm.birth_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="tax_purpose" class="form-label">Are you a U.S resident and/or citizen
                                            for tax purposes?<span class="text-danger">*</span></label>
                                        <div class="d-flex align-items-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tax_purpose"
                                                    id="tax_purpose" :value="true"
                                                    x-model="QuestionnaireForm.tax_purpose">
                                                <label class="form-check-label" for="tax_purpose">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tax_purpose"
                                                    id="tax_purpose" :value="false" checked
                                                    x-model="QuestionnaireForm.tax_purpose">
                                                <label class="form-check-label" for="tax_purpose">No</label>
                                            </div>
                                        </div>
                                        <p style="font-size: small;" class="text-muted">Note: if you select yes, the W9
                                            form
                                            will be required</p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="social_security_number" class="form-label">Social security number<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="social_security_number" name="social_security_number"
                                            class="form-control" x-model="QuestionnaireForm.social_security_number"
                                            required>
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <button type="submit" class="btn btn-primary me-2"
                                            @click="submitQuestionnaireForm()">Add Questionnaire</button>
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="deal-modal modal right fade" id="addW9FormModal" tabindex="-1"
                    aria-labelledby="addW9FormModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content px-2">
                            <div class="modal-header row bg-primary text-white" style="height:80px;">
                                <h5 class="modal-title col text-white">Edit W-9 form</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{-- Deal Form Body --}}
                                <div>
                                    <div class="divider">
                                        <div class="circle"></div>
                                        <span>Personal</span>
                                    </div>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">First name<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control"
                                            x-model="W9Form.name" required>

                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <select class="form-select" id="address" class="form-select custom-dropdown"
                                            x-model="W9Form.address" @change="changeAddress()">
                                            <option value="" disabled selected>Select a profile</option>
                                            <option value="">Select</option>
                                            <template x-for="address in addresses" :key="address.id">
                                                <option :value="address.id" x-text="formatAddress(address)"></option>
                                            </template>
                                            <option value="address">+ Add address</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="social_security_number" class="form-label">Social security number<span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="social_security_number" name="social_security_number"
                                            class="form-control" x-model="W9Form.social_security_number" required>
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <button type="submit" class="btn btn-primary me-2"
                                            @click="submitW9Form()">Save</button>
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="deal-modal modal right fade" id="addAddressModal" tabindex="-1"
                    aria-labelledby="addAddressModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content px-2">
                            <div class="modal-header row bg-primary text-white" style="height:80px;">
                                <h5 class="modal-title col text-white">Add address</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{-- Deal Form Body --}}
                                <div>
                                    @csrf
                                    <div class="mb-3">
                                        <label for="company_name" class="form-label">Full name/Company name</label>
                                        <input type="text" id="company_name" name="company_name" class="form-control"
                                            x-model="addressForm.company_name" @blur="validateFieldA('company_name')"
                                            required>
                                        <span class="text-danger" x-show="errors.company_name"
                                            x-text="errors.company_name"></span>
                                    </div>

                                    <div x-init="fetchCountries()">
                                        <div class="mb-3">
                                            <label for="country" class="form-label">Country</label>
                                            <select id="country" name="country" class="form-select"
                                                x-model="addressForm.country" required>
                                                <template x-for="country in countries" :key="country.code">
                                                    <option :value="country.name" x-text="country.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address_line_1" class="form-label">Street address line 1</label>
                                        <input type="text" id="address_line_1" name="address_line_1"
                                            class="form-select" x-model="addressForm.address_line_1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="address_line_2" class="form-label">Street address line 2</label>
                                        <input type="text" id="address_line_2" name="address_line_2"
                                            class="form-select" x-model="addressForm.address_line_2">
                                    </div>
                                    <div class="mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" id="city" name="city" class="form-control"
                                            x-model="addressForm.city" @blur="validateFieldA('city')" required>
                                        <span class="text-danger" x-show="errors.city" x-text="errors.city"></span>
                                    </div>

                                    <!-- Province and Postal Code -->
                                    <div x-show="addressForm.country !== 'United States'" x-cloak>
                                        <div class="mb-3">
                                            <label for="province" class="form-label">Province</label>
                                            <select id="province" name="province" class="form-select"
                                                x-model="addressForm.province" @blur="validateFieldA('province')"
                                                required>
                                                <option value="">Select province</option>
                                                <option value="ab">AB</option>
                                                <option value="bc">BC</option>
                                                <option value="mb">MB</option>
                                                <option value="nb">NB</option>
                                                <option value="nl">NL</option>
                                                <option value="ns">NS</option>
                                                <option value="nt">NT</option>
                                                <option value="nu">NU</option>
                                                <option value="on">ON</option>
                                                <option value="pe">PE</option>
                                                <option value="qc">QC</option>
                                                <option value="sk">SK</option>
                                                <option value="yt">YT</option>
                                                <option value="other">Other</option>
                                                <span class="text-danger" x-show="errors.province"
                                                    x-text="errors.province"></span>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="postal_code" class="form-label">Postal Code</label>
                                            <input type="text" id="postal_code" name="postal_code"
                                                class="form-control" x-model="addressForm.postal_code"
                                                @blur="validateFieldA('postal_code')" required>
                                            <span class="text-danger" x-show="errors.postal_code"
                                                x-text="errors.postal_code"></span>
                                        </div>
                                    </div>

                                    <!-- State and Zip Code -->
                                    <div x-show="addressForm.country === 'United States'" x-cloak>
                                        <div class="mb-3">
                                            <label for="state" class="form-label">State</label>
                                            <select id="state" name="state" class="form-select"
                                                x-model="addressForm.state" @blur="validateFieldA('state')" required>
                                                <option value="">Select state</option>
                                                <option value="ak">AK</option>
                                                <option value="al">AL</option>
                                                <option value="ar">AR</option>
                                                <option value="as">AS</option>
                                                <option value="az">AZ</option>
                                                <option value="ca">CA</option>
                                                <option value="co">Co</option>
                                                <option value="ct">CT</option>
                                                <option value="dc">DC</option>
                                                <option value="de">De</option>
                                                <option value="fl">FL</option>
                                                <option value="ga">GA</option>
                                                <option value="gu">GU</option>
                                                <option value="hi">HI</option>
                                                <option value="ia">IA</option>
                                                <option value="id">ID</option>
                                                <option value="il">IL</option>
                                                <option value="in">IN</option>
                                                <option value="ky">KY</option>
                                                <option value="la">LA</option>
                                                <option value="ma">MA</option>
                                                <option value="md">Md</option>
                                                <option value="md">ME</option>
                                                <option value="mi">MI</option>
                                                <option value="mn">MN</option>
                                                <option value="mo">MO</option>
                                                <option value="mp">MP</option>
                                                <option value="ms">MS</option>
                                                <option value="mt">MT</option>
                                                <option value="nc">NC</option>
                                                <option value="nd">ND</option>
                                                <option value="ne">NE</option>
                                                <option value="nh">NH</option>
                                                <option value="nj">NJ</option>
                                                <option value="nm">NM</option>
                                                <option value="nv">NV</option>
                                                <option value="ny">NY</option>
                                                <option value="oh">OH</option>
                                                <option value="ok">OK</option>
                                                <option value="or">OR</option>
                                                <option value="pa">PA</option>
                                                <option value="pr">PR</option>
                                                <option value="ri">Ri</option>
                                                <option value="sc">SC</option>
                                                <option value="sd">SD</option>
                                                <option value="tn">TN</option>
                                                <option value="tx">TX</option>
                                                <option value="um">UM</option>
                                                <option value="ut">UT</option>
                                                <option value="va">VA</option>
                                                <option value="vi">VI</option>
                                                <option value="vt">VT</option>
                                                <option value="wa">WA</option>
                                                <option value="wi">WI</option>
                                                <option value="wv">WV</option>
                                                <option value="wy">WY</option>
                                                <option value="aa">AA</option>
                                                <option value="ae">AE</option>
                                                <option value="ap">AP</option>
                                                <option value="other">Other</option>
                                            </select>
                                            <span class="text-danger" x-show="errors.state" x-text="errors.state"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="zip_code" class="form-label">Zip Code</label>
                                            <input type="text" id="zip_code" name="zip_code" class="form-control"
                                                x-model="addressForm.zip_code" @blur="validateFieldA('zip_code')"
                                                required>
                                            <span class="text-danger" x-show="errors.zip_code"
                                                x-text="errors.zip_code"></span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-secondary me-2"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <span class="btn btn-primary deal-save" @click="submitAddressForm()">Add
                                            Address</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        var csrf = '{{ csrf_token() }}';

        function investAlpine() {
            return {
                loading: false,
                errors: {},
                showErrors: false,
                investmentSuccess: false,
                tab: 'draw',
                isDrawing: false,
                lastX: 0,
                lastY: 0,
                signatureDataUrl: null,
                canvas: null,
                context: null,
                canvasRect: null,
                uploadedSignature: null,
                typedSignature: {},
                profiles: @json($investor->investor_profiles ?? []),
                addresses: @json($offering->questionnaire_addresses ?? []),
                questionnaires: @json($offering->investment_questionnaires ?? []),
                countries: [],
                profileForm: {
                    _token: csrf,

                },
                W9Form: {
                    _token: csrf,
                    name: '',
                    address: '',
                    social_security_number: '',

                },
                investorProfileForm: {
                    _token: csrf,
                    profile_type: '',
                    profile_fname: '',
                    profile_mname: '',
                    profile_lname: '',
                    profile_distribution: '',
                    profile_fname2: '',
                    profile_mname2: '',
                    profile_lname2: '',
                    profile_email2: '',
                    custodian: '',
                    profile_entity_name: '',
                    profile_ira_name: '',
                    profile_ira_company: '',
                },
                QuestionnaireForm: {
                    _token: csrf,
                    first_name: '',
                    last_name: '',
                    telephone: '',
                    address: '',
                    resident_of_usa: '',
                    birth_date: '',
                    tax_purpose: '',
                    social_security_number: '',
                },
                addressForm: {
                    _token: csrf,
                    company_name: '',
                    country: '',
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zip_code: '',
                    province: '',
                    postal_code: '',
                },
                investorForm: {
                    _token: csrf,
                    investor_profile_id: '',
                    deal_class_id: '{{ $offering->classes->first()->id ?? '' }}',

                },
                investmentForm: {
                    investor_profile_id: '',
                    deal_class_id: '',
                    investment_amount: '',
                    funding_method: '',
                    questionnaire_id: '',
                    w9_form: '',
                },

                formatAddress(address) {
                    // Filter out any fields that are null or empty
                    let parts = [];
                    if (address.address_line_1) parts.push(address.address_line_1);
                    if (address.address_line_2) parts.push(address.address_line_2);
                    if (address.city) parts.push(address.city);
                    if (address.state) parts.push(address.state);
                    if (address.zip_code) parts.push(address.zip_code);
                    if (address.province) parts.push(address.province);
                    if (address.postal_code) parts.push(address.postal_code);

                    // Join remaining valid parts with commas
                    return parts.join(', ') || 'No address available'; // Provide default text if empty
                },
                changeInvestorProfile() {

                    if (this.profileForm.investorProfile == 'give') {
                        $('#addQuestionModal').modal('show');
                        this.profileForm.investorProfile = '';
                    }
                },
                changeW9form() {

                    if (this.investmentForm.w9_form == 'form') {
                        $('#addW9FormModal').modal('show');
                        this.investmentForm.w9_form = '';
                    }
                },
                changeProfile() {
                    if (this.investorForm.investor_profile_id == 'view') {
                        $('#addInvestorProfileModal').modal('show');
                        this.investorForm.investor_profile_id = '';
                        this.profileForm.investorProfile = '';
                    }
                },
                fileName: '',
                handleFileUpload(event) {
                    this.fileName = event.target.files[0].name;
                },
                changeQuestionnaire() {
                    //debugger;
                    if (this.investmentForm.questionnaire_id == 'view') {
                        $('#addQuestionModal').modal('show');
                        this.investmentForm.questionnaire_id = '';
                    }
                },
                changeAddress() {
                    if (this.QuestionnaireForm.address == 'address') {
                        $('#addAddressModal').modal('show');
                        this.QuestionnaireForm.address = '';
                    }
                },

                async submitInvestorProfileForm() {
                    this.loading = true;
                    let url = "{{ route('user.offerings.invest.storeProfile', $offering->id) }}";

                    try {


                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.investorProfileForm)
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.errors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            this.profiles = responseData.profiles;
                            $('#addInvestorProfileModal').modal('hide');
                            // Reload the page
                            //  window.location.reload();

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                validateField(field) {
                    if (!this.QuestionnaireForm[field]) {
                        this.errors[field] = `${field.replace(/_/g, ' ').toUpperCase()} is required.`;
                    } else {
                        delete this.errors[field];
                    }
                },

                async submitQuestionnaireForm() {
                    this.loading = true;
                    // Validate fields before submission
                    this.validateField('first_name');
                    this.validateField('last_name');
                    this.validateField('telephone');
                    this.validateField('resident_of_usa');
                    // Check for validation errors
                    if (Object.keys(this.errors).length > 0) {
                        this.loading = false;
                        return;
                    }
                    let url = "{{ route('user.offerings.invest.storeQuestionnaire', $offering->id) }}";
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.QuestionnaireForm)
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.errors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            this.questionnaires = responseData.questionnaires;
                            $('#addQuestionModal').modal('hide');
                            // Reload the page
                            //  window.location.reload();

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                validateFieldA(field) {
                    // Conditional validation based on country selection
                    if (field === 'province' || field === 'postal_code') {
                        // Only validate these fields if the country is not 'United States'
                        if (this.addressForm.country !== 'United States' && !this.addressForm[field]) {
                            this.errors[field] = `${field.replace(/_/g, ' ')} is required.`;
                        } else {
                            delete this.errors[field];
                        }
                    } else if (field === 'state' || field === 'zip_code') {
                        // Only validate state and zip_code if the country is 'United States'
                        if (this.addressForm.country === 'United States' && !this.addressForm[field]) {
                            this.errors[field] = `${field.replace(/_/g, ' ')} is required.`;
                        } else {
                            delete this.errors[field];
                        }
                    } else if (!this.addressForm[field]) {
                        // General validation for all other fields
                        this.errors[field] = `${field.replace(/_/g, ' ')} is required.`;
                    } else {
                        delete this.errors[field];
                    }
                },
                async submitAddressForm() {
                    this.loading = true;
                    // Validate all fields before submission
                    for (let field in this.addressForm) {
                        this.validateFieldA(field);
                    }
                    // If there are validation errors, stop the submission process
                    if (Object.keys(this.errors).length > 0) {
                        this.loading = false;
                        return;
                    }
                    let url = "{{ route('user.offerings.invest.storeQuestionnaireAddress', $offering->id) }}";
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.addressForm)
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            // Update errors with server-side validation errors
                            this.errors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            this.addresses = responseData.addresses;
                            $('#addAddressModal').modal('hide');
                            // Reload the page
                            //  window.location.reload();

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                async submitW9Form() {
                    this.loading = true;
                    let url = "{{ route('user.offerings.invest.storeQuestionnaireForm', $offering->id) }}";

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.W9Form) // Ensure the correct model is used
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            this.errors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            this.wforms = responseData.wforms;
                            $('#addW9FormModal').modal('hide');
                        } else {
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                provinces: [],
                selectedCountry: '',
                province: '',
                postalCode: '',
                state: '',
                zipCode: '',

                async fetchCountries() {
                    try {
                        const response = await fetch('https://restcountries.com/v3.1/all');
                        const data = await response.json();
                        this.countries = data
                            .map(country => ({
                                name: country.name.common,
                                code: country.cca2
                            }))
                            .sort((a, b) => a.name.localeCompare(b.name));

                        // Add top countries at the beginning of the list
                        const topCountries = [{
                                name: 'United States',
                                code: 'US'
                            },
                            {
                                name: 'Canada',
                                code: 'CA'
                            },
                            {
                                name: 'China',
                                code: 'CN'
                            },
                            {
                                name: 'Andorra',
                                code: 'AD'
                            },
                            {
                                name: 'United Arab Emirates',
                                code: 'AE'
                            },
                        ];
                        this.countries = [...topCountries, ...this.countries.filter(country => !topCountries.some(tc =>
                            tc.code === country.code))];
                    } catch (error) {
                        console.error('Error fetching countries:', error);
                    }
                },
                submitInvestorForm() {
                    this.showErrors = true;
                    if (!this.investorForm.investor_profile_id || !this.investorForm.deal_class_id) {
                        return;
                    }
                    this.completedSteps.push(this.currentStep);
                    this.currentStep = 'InvestmentMethod';
                    this.showStep('InvestmentMethod');
                },
                submitInvestment() {
                    if (!this.investmentForm.investment_amount || !this.investmentForm.funding_method) {
                        return;
                    }
                    this.completedSteps.push(this.currentStep);
                    let index = this.steps.findIndex(step => step.id === this.currentStep);
                    this.currentStep = this.steps[index + 1].id;
                    this.showStep(this.currentStep);
                },
                submit_e_sign_previous(){
                    this.completedSteps.push(this.currentStep);
                    let index = this.steps.findIndex(step => step.id === this.currentStep);
                    this.currentStep = this.steps[index - 1].id;
                    this.showStep(this.currentStep);
                },
                submitQuestionnaire() {
                    if (!this.investmentForm.questionnaire_id || !this.investmentForm.w9_form) {
                        return;
                    }

                    this.completedSteps.push(this.currentStep);
                    this.currentStep = 'E_signatureMethod';
                    this.showStep('E_signatureMethod');
                },
                submitQuestionnairew9() {
                    if (!this.investmentForm.w9_form) {
                        return;
                    }

                    this.completedSteps.push(this.currentStep);
                    this.currentStep = 'E_signatureMethod';
                    this.showStep('E_signatureMethod');
                },
                submitQuestionnaires() {
                    if (!this.investmentForm.questionnaire_id) {
                        return;
                    }

                    this.completedSteps.push(this.currentStep);
                    this.currentStep = 'E_signatureMethod';
                    this.showStep('E_signatureMethod');
                },
                submitEsignature() {
                    this.completedSteps.push(this.currentStep);
                    let index = this.steps.findIndex(step => step.id === this.currentStep);

                    // check if currenstep is the last step
                    if (index === this.steps.length - 1) {
                        this.submitAndSaveInvestment();
                    } else {
                        this.currentStep = this.steps[index + 1].id;
                        this.showStep(this.currentStep);
                    }
                },
                submitAccreditation() {
                    this.completedSteps.push(this.currentStep);

                    // find current step and update status
                    this.steps.forEach((item, index) => {
                        if (item.id === this.currentStep) {
                            item.status = 'green'; // Mark current step as completed
                        }
                    });
                    this.submitAndSaveInvestment()
                    // this.currentStep = 'InvestorMethod';
                    // this.showStep('InvestorMethod');
                },
                currentStep: 'InvestorMethod', // Default step
                steps: [{
                        id: 'InvestorMethod',
                        label: 'Investor',
                        status: 'inactive'
                    },
                    {
                        id: 'InvestmentMethod',
                        label: 'Investment',
                        status: 'inactive'
                    },


                    @if ($offering->manageoffering)
                        @if ($offering->manageoffering->questionnaire == true && $offering->manageoffering->require_w9 == true)
                            {
                                id: 'QuestionnaireW9FormMethod',
                                label: 'Questionnaire',
                                status: 'inactive'
                            },
                            @elseif ($offering->manageoffering->questionnaire == true)
                                {
                                    id: 'QuestionnaireMethod',
                                    label: 'Questionnaire',
                                    status: 'inactive'
                                },
                            @elseif ($offering->manageoffering->require_w9 == true) {
                                    id: 'W9FormMethod',
                                    label: 'W9 Form',
                                    status: 'inactive'
                                },
                        @endif

                    @endif


                    {
                        id: 'E_signatureMethod',
                        label: 'E-signature',
                        status: 'inactive'
                    },

                    @if ($offering->manageoffering)
                        @if ($offering->manageoffering->verify_investor == true)
                            {
                                id: 'AccreditationMethod',
                                label: 'Accreditation',
                                status: 'inactive'
                            },
                        @endif
                    @endif
                ],
                completedSteps: [],
                completeStep(step) {
                    // Update the status of the current step
                    this.steps.forEach((item, index) => {
                        if (item.id === step) {
                            item.status = 'green'; // Completed step
                        }
                    });

                    // Move to the next step
                    const nextStep = this.steps[this.steps.indexOf(this.currentStep) + 1];
                    if (nextStep) {
                        this.showStep(nextStep.id);
                    }
                },
                showStep(step) {
                    this.currentStep = step;

                    // Loop through all steps to update their status
                    this.steps.forEach((item, index) => {
                        if (item.id === step) {
                            item.status = 'blue'; // Active step
                        } else if (this.completedSteps.includes(item.id)) {
                            item.status = 'green'; // Completed steps
                        } else {
                            item.status = 'inactive'; // Future steps
                        }
                    });
                },
                init() {
                    this.investmentForm.investment_amount = moneyFormatInit(this.investmentForm.investment_amount);
                    this.showStep(this.currentStep); // Initialize the first step
                    this.$nextTick(() => {
                        this.canvas = this.$refs.canvas;
                        if (this.canvas) {
                            this.context = this.canvas.getContext('2d');
                            this.canvas.width = window.innerWidth; // 100% of window width
                            this.canvas.height = 300; // Fixed height for the canvas
                            this.canvasRect = this.canvas
                                .getBoundingClientRect(); // Get the bounding rect of the canvas

                            // Event listeners for mouse and touch
                            this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e), {
                                passive: false
                            });
                            this.canvas.addEventListener('mousemove', (e) => this.draw(e), {
                                passive: false
                            });
                            this.canvas.addEventListener('mouseup', () => this.stopDrawing(), {
                                passive: false
                            });
                            this.canvas.addEventListener('mouseout', () => this.stopDrawing(), {
                                passive: false
                            });

                            // Mobile event listeners
                            this.canvas.addEventListener('touchstart', (e) => this.startDrawing(e), {
                                passive: false
                            });
                            this.canvas.addEventListener('touchmove', (e) => this.draw(e), {
                                passive: false
                            });
                            this.canvas.addEventListener('touchend', () => this.stopDrawing(), {
                                passive: false
                            });
                            this.canvas.addEventListener('touchcancel', () => this.stopDrawing(), {
                                passive: false
                            });
                        } else {
                            console.error("Canvas element is not found");
                        }
                    });
                },
                moneyFormat(el) {
                    let value = el.value;
                    // Remove non-numeric characters except for the decimal point
                    value = value.replace(/[^\d.]/g, '');
                    // If there's more than one decimal point, keep only the first one
                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    // Limit the decimal part to two digits
                    if (parts[1]) {
                        parts[1] = parts[1].slice(0, 2);
                        value = parts.join('.');
                    }
                    // Add commas for thousands separator
                    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    // Ensure that the value starts with $ and no other non-numeric characters
                    if (value !== '') {
                        el.value = '$' + value;
                    } else {
                        el.value = '$0';
                    }
                }, 
                setTab(tab) {
                    this.tab = tab;

                    // Initialize OpenSignLabs for Draw Signature when tab is active
                    if (tab === 'draw' && !this.drawSignatureInstance) {
                        this.initDrawSignature();
                    }
                },
                startDrawing(e) {
                    this.isDrawing = true;
                    const {
                        offsetX,
                        offsetY
                    } = this.getEventOffset(e);
                    this.context.beginPath();
                    this.context.moveTo(offsetX, offsetY);
                    this.lastX = offsetX;
                    this.lastY = offsetY;
                },
                draw(e) {
                    if (!this.isDrawing) return;
                    const {
                        offsetX,
                        offsetY
                    } = this.getEventOffset(e);
                    this.context.lineTo(offsetX, offsetY);
                    this.context.stroke();
                    this.lastX = offsetX;
                    this.lastY = offsetY;
                },
                stopDrawing() {
                    if (!this.isDrawing) return;
                    this.isDrawing = false;
                    this.context.closePath();
                },
                getEventOffset(e) {
                    // Calculate the offset position relative to the canvas
                    const x = e.clientX || e.touches[0].clientX;
                    const y = e.clientY || e.touches[0].clientY;

                    // Return the offset relative to the canvas area
                    return {
                        offsetX: x - this.canvasRect.left,
                        offsetY: y - this.canvasRect.top
                    };
                },
                clear() {
                    this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
                    this.signatureDataUrl = null;
                },
                save() {
                    this.signatureDataUrl = this.canvas.toDataURL();
                },
                uploadSignature(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.uploadedSignature = URL.createObjectURL(file);
                    }
                },
                insertSignature() {
                    if (this.tab === 'draw' && this.drawSignatureInstance) {
                        this.drawSignatureInstance.save((signatureData) => {
                            alert('Signature saved!'); // Replace with actual save or form submission
                            console.log(signatureData);
                        });
                    } else if (this.tab === 'type') {
                        alert(`Typed signature: ${this.typedSignature}`);
                    } else if (this.tab === 'upload' && this.uploadedSignature) {
                        alert('Uploaded signature inserted!');
                    } else {
                        alert('Please add a signature before inserting.');
                    }
                },
                async submitAndSaveInvestment() {
                    this.investmentForm.deal_class_id = this.investorForm.deal_class_id;
                    this.investmentForm.investor_profile_id = this.investorForm.investor_profile_id;
                    this.investmentForm.funding_method = this.investmentForm.funding_method;
                    //debugger;
                    console.log(this.investmentForm);
                    this.loading = true;
                    // Validate fields before submission
                    let data = {}
                    let url = "{{ route('user.offerings.invest.storeInvestment', $offering->id) }}";
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.investmentForm)
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.errors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            // Reload the page
                            //  window.location.reload();
                            this.investmentForm = {
                                investor_profile_id: '',
                                deal_class_id: '',
                                investment_amount: '',
                                funding_method: '',
                                questionnaire_id: '',
                                w9_form: '',
                            };
                            this.investmentSuccess = true;

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                }
            };
        }
    </script>
@endpush
@push('style')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        #signature-pad {
            border: 2px solid #000;
            width: 100%;
            height: 300px;
            touch-action: none;
            display: block;
            margin: auto;
        }

        .row .header-box {
            white-space: nowrap;
            overflow: hidden;
        }

        .side-bar {
            margin-top: 1.3rem !important;
        }

        #card2 {
            position: sticky;
            top: 0;
            padding: 5px;
            box-shadow: -1px 0 5px rgba(0, 0, 0, 0.1);
            /* Adds a shadow to the left */
        }

        .first-letter {
            width: 50px;
            height: 50px;
            font-size: 24px;
            font-weight: bold;
        }

        .deal-modal.right .modal-dialog {
            position: fixed;
            margin: auto;
            width: 50rem;
            max-width: 50%;
            height: 100%;
            right: 0;
            top: 0;
            bottom: 0;
        }

        .deal-modal.right .modal-content {
            height: 100%;
            overflow-y: auto;
        }

        .deal-modal.right .modal-body {
            padding: 15px 15px 80px;
        }

        .deal-modal.center .modal-dialog {
            width: 50rem;
            max-width: 50%;
            height: 90%;
        }

        .deal-modal.center .modal-content {
            height: 100%;
            overflow-y: auto;
        }

        .deal-modal.center .modal-body {
            padding: 15px 15px 80px;
        }

        /* Add blue shadow on hover */
        .card-hover:hover {
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
            /* Blue shadow */
            border-color: #007bff;
            /* Blue border on hover */
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
            /* Bootstrap primary color */
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
