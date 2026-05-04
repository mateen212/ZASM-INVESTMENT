@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .bi {
            width: 1rem;
            height: 1rem;
        }


        .paragraph {
            font-size: 12px;
            font-weight: 600;
            line-height: 1.3;
            color: #808080;
            margin: -4px 0px 8px;
            font-family: Inter, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Oxygen, Ubuntu, Cantarell, Fira Sans, Droid Sans, Helvetica Neue, sans-serif;
        }

        .deal-modal.right .modal-dialog {
            position: fixed;
            margin: auto;
            width: 50rem;
            height: 50%;
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

        #addAddressModal .modal-dialog {
            position: fixed;
            margin: auto;
            width: 30rem;
            max-width: 35rem;
            height: 100%;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 1070;
        }

        #addBeneficialModal .modal-dialog {
            position: fixed;
            margin: auto;
            width: 30rem;
            max-width: 35rem;
            height: 100%;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 1070;
        }

        /* Backdrop styling */
        /* Default Bootstrap modal backdrop uses z-index: 1040, so we add custom classes for better control */

        /* For the primary modal backdrop */
        .modal-backdrop.first {
            z-index: 1040 !important;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* For the nested modal backdrop */
        .modal-backdrop.second {
            z-index: 1060 !important;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* (Optional) If you want to adjust additional properties for nested modals, you could add: */

        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s;
            margin: 0 auto;
            /* Center the circle horizontally */
        }

        .step-circle.blue {
            background-color: #007bff;
            /* Blue color for active step */
            color: #fff;
            /* White text color */
        }

        .step-circle.blue span {
            /* Red color for active step */
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
            background-color: rgb(170, 170, 170);
            color: #aaaaaa;
        }

        .step-text {
            font-size: 14px;
            margin-top: 6px;
            text-align: center;
            /* Center the text */
            width: 100%;
            /* Ensure the text container takes full width */
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
            width: 100%;
            justify-content: space-evenly;
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
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80px;
            /* Set a fixed width for consistent spacing */
        }
    </style>
@endpush
<div class="container">
    <template x-if="loading">
        <div class="custom-loader-overlay">
            <div class="custom-loader"></div>
        </div>
    </template>
    @if ($deal->achSettings && $deal->achSettings->verify_detail == 1 && $deal->achSettings->verify_confirmation == 'review')
        <!-- Pending Review Card -->
        <div class="card w-90 h-30 shadow-sm border-0 p-4 text-center">
            <div class="mt-2">
                <h4 class="fw-bold">Pending review</h4>
            </div>
            <pre>
            <div class="paragraph">
                Review can take 2 - 3 business days. You will be able to add your bank account once your application has been verified.
                You will be notified if additional documents are required.
            </div>
        </pre>
        </div>
    @elseif (
        $deal->achSettings &&
            $deal->achSettings->verify_detail == 1 &&
            $deal->achSettings->verify_confirmation == 'pending')
        <!-- Approved Card -->
        <div class="card w-90 h-30 shadow-sm border-0 p-4 text-center">
            <div class="text-success">
                <i class="bi bi-check-circle fs-1"></i> <!-- Bootstrap icon -->
            </div>
            <div class="mt-2">
                <h4 class="fw-bold">Approved</h4>
            </div>
            <div class="mt-2">
                <p class="text-muted">
                    Your entity has been verified. You can now add a bank account and send distributions directly
                    through Stripe.
                </p>
            </div>
            <div id="v-funding-info">
                <div>

                    <admin-ach-payment :deal="{{ $deal->toJson() }}"
                        :onboarding-status="{{ json_encode($onboardingStatus) }}" />

                </div>
            </div>
        </div>
    @elseif (
        $deal->achSettings &&
            $deal->achSettings->verify_detail == 1 &&
            $deal->achSettings->verify_confirmation == 'completed')
        @if (array_key_exists('onboarding_complete', $onboardingStatus) && $onboardingStatus['onboarding_complete'] == false)
            <!-- Almost Complete -->
            <div class="card w-90 h-30 shadow-sm border-0 p-4 text-center">
                <div class="text-warning">
                    <i class="bi bi-exclamation-circle fs-1"></i>
                </div>
                <div class="mt-2">
                    <h4 class="fw-bold">Almost Complete</h4>
                </div>
                <div class="mt-2">
                    <p class="text-muted">
                        Please complete the onboarding process to add a bank account and pay&send payments.
                    </p>
                </div>
                <div class="mt-2 rounded-9">
                    <button class="btn btn-primary px-4 py-2" @click="completeOnboarding()">
                        Complete onboarding
                    </button>
                </div>
            @else
                <div class="card w-90 h-30 shadow-sm border-0 p-4 text-center">
                    <div class="text-success">
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                    <div class="mt-2">
                        <h4 class="fw-bold">Completed</h4>
                    </div>
                    <div class="mt-2">
                        <p class="text-muted">
                            A Stripe account is successfully onboarded and ready to receive payments.
                        </p>
                    </div>
                </div>
        @endif
    @else
        <!-- Verify Entity Card -->
        <div class="card w-90 h-30 shadow-sm border-0 p-4 text-center">
            <div class="text-primary">
                <i class="bi bi-people fs-1"></i> <!-- Bootstrap icon -->
            </div>
            <div class="mt-2">
                <h4 class="fw-bold">Verify entity</h4>
            </div>
            <div class="mt-2">
                <p class="text-muted">
                    Verify your entity to add a bank account and send distributions directly through Cash Flow Portal.
                </p>
            </div>
            <div class="mt-2">
                <p class="text-muted">
                    We suggest having the <strong>EIN letter</strong> accessible.
                    <a href="#" class="text-primary text-decoration-none">Learn more</a>
                </p>
            </div>
            <div class="mt-2 rounded-9">
                <button class="btn btn-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#addEntityModal">
                    Verify my entity
                </button>
            </div>
        </div>
    @endif


    <div class="deal-modal modal right fade" id="addEntityModal" tabindex="-1" aria-labelledby="addEntityModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white" style="height:80px;">
                    <h5 class="modal-title col text-white" id="addEntityModalLabel">Verify your entity</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Deal Form Body -->
                    <div>
                        @csrf
                        <div class="divider_body">
                            <template x-for="(step, index) in steps" :key="step.id">
                                <div class="items-center">
                                    <div class="step-circle" :class="step.status">
                                        <template x-if="step.status === 'green'">
                                            <i class="fas fa-check"></i>
                                        </template>
                                        <template x-if="step.status !== 'green'">
                                            <span x-text="index + 1"></span>
                                        </template>
                                    </div>
                                    <div class="step-text" x-text="step.label"></div>
                                </div>
                            </template>
                        </div>

                        <div class="content-section" :class="{ 'active': currentStep === 'EntityMethod' }">
                            <h2 class="mb-3">Entity Details</h2>
                            <div class="mb-3">
                                <h3 for="entity_name" class="form-label">Entity Name</h3>
                                <p class="paragraph">Exact entity name as appears on the EIN letter. Specifically, if
                                    there
                                    is
                                    an
                                    extra space or missing space, it won't be accepted!</p>
                                <input type="text" id="entity_name" x-on:input="errors.entity_name = ''"
                                    x-model="achSettingForm.entity_name" class="form-control" />
                                <template x-if="errors.entity_name">
                                    <p class="text-danger" x-text="errors.entity_name"></p>
                                </template>
                            </div>
                            <div class="mb-3">
                                <h3 for="entity_type" class="form-label">Entity Type</h3>
                                <p class="paragraph">Exact entity address as appears on the EIN letter. Specifically, if
                                    there
                                    is
                                    an extra space or missing space, it won't be accepted!</p>
                                <select id="entity_type" x-on:input="errors.entity_type = ''"
                                    x-model="achSettingForm.entity_type" class="form-select">
                                    <option value="">Select entity type</option>
                                    <option value="llc">LLC</option>
                                    <option value="corporation">Corporation</option>
                                    <option value="partnership">Partnership</option>
                                </select>
                                <template x-if="errors.entity_type">
                                    <p class="text-danger" x-text="errors.entity_type"></p>
                                </template>
                            </div>
                            <div class="mb-3">
                                <h3 for="state_registration" class="form-label">State Registration</h3>
                                <p class="paragraph">State which the entity is registered in.</p>
                                <select id="state_registration" x-on:input="errors.state_registration = ''"
                                    x-model="achSettingForm.state_registration" class="form-select">
                                    <option value="">Select state</option>
                                    <!-- List states as needed -->
                                    <option value="ak">Ak</option>
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
                                <template x-if="errors.state_registration">
                                    <p class="text-danger" x-text="errors.state_registration"></p>
                                </template>
                            </div>
                            <div class="mb-3">
                                <h3 for="ein" class="form-label">EIN</h3>
                                <p class="paragraph">EIN can be found on the EIN letter.</p>
                                <input type="text" id="ein" x-on:input="errors.ein = ''"
                                    x-model="achSettingForm.ein" class="form-control" maxlength="10"
                                    @input="achSettingForm.ein = formatEIN($event.target.value)" />
                                <template x-if="errors.ein">
                                    <p class="text-danger" x-text="errors.ein"></p>
                                </template>
                            </div>
                            <div class="mb-3">
                                <h3 for="address" class="form-label fw-bold">Address</h3>
                                <p class="paragraph"><strong>Important:</strong> Usually the home address of the
                                    controller.
                                    Specifically, P.O. Box
                                    and subject property address are <strong>not accepted.</strong></p>
                                <select type="text" class="form-control border-bottom" id="address"
                                    name="address" x-on:input="errors.address = ''"
                                    x-model="achSettingForm.address_id" @change="changeAddress()" required>
                                    <option value="">Select address</option>
                                    <option value="new">+New</option>
                                    {{-- @foreach ($deal->addresses as $address)
                                        <option value="{{ $address->id }}">
                                            {{ $address->address_line_1 }}{{ $address->address_line_2 ? ', ' . $address->address_line_2 : '' }},
                                            {{ $address->city }}, {{ $address->state }}, {{ $address->zip_code }}
                                        </option>
                                    @endforeach --}}
                                    <template x-for="address in addresses" :key="address.id">
                                        <option
                                            x-text="address.address_line_1 + (address.address_line_2 ? ', ' + address.address_line_2 : '') + ', ' + address.city + ', ' + address.state + ', ' + address.zip_code"
                                            :value="address.id"></option>
                                    </template>
                                </select>
                                <template x-if="errors.address">
                                    <p class="text-danger" x-text="errors.address"></p>
                                </template>
                            </div>
                            <h2 class="mb-3">Entity document</h2>
                            <p class="paragraph">Entity document is required for entity verification.</p>
                            <div class="mb-3">
                                <h3 for="ein_letter" class="form-label">Upload EIN Letter</h3>
                                <p class="paragraph">Please upload an <strong>IRS issued SS-4 confirmation
                                        letter.</strong>
                                    <input type="file" id="ein_letter" x-on:input="errors.ein_letter = ''"
                                        @change="handleEntityFile($event)" class="form-control"
                                        accept="image/*,application/pdf" />
                                    <template x-if="errors.ein_letter">
                                        <p class="text-danger" x-text="errors.ein_letter"></p>
                                    </template>
                            </div>
                        </div>
                        <div class="content-section" :class="{ 'active': currentStep === 'ControllerMethod' }">
                            <h1>Controller personal information</h1>
                            <p class="paragraph">A controller is an individual who holds significant responsibilities
                                to
                                manage or direct a company (i.e. CEO, CFO, General Partner, President, etc.). This can
                                be
                                any controller's personal information.</p>
                            <div class="mb-3">
                                <h3 for="first_name" class="form-label">First Name</h3>
                                <input type="text" id="first_name" x-on:input="errors.first_name = ''"
                                    x-model="achSettingForm.first_name" class="form-control" />
                                <template x-if="errors.first_name">
                                    <p class="text-danger" x-text="errors.first_name"></p>
                                </template>
                            </div>
                            <div class="mb-3">
                                <h3 for="last_name" class="form-label">Last Name</h3>
                                <input type="text" id="last_name" x-on:input="errors.last_name = ''"
                                    x-model="achSettingForm.last_name" class="form-control" />
                                <template x-if="errors.last_name">
                                    <p class="text-danger" x-text="errors.last_name"></p>
                                </template>
                            </div>
                            <div class="mb-3">
                                <h3 for="job_title" class="form-label">Job Title</h3>
                                <input type="text" id="job_title" x-on:input="errors.job_title = ''"
                                    x-model="achSettingForm.job_title" class="form-control" />
                                <template x-if="errors.job_title">
                                    <p class="text-danger" x-text="errors.job_title"></p>
                                </template>
                            </div>
                            <div class="mb-3">
                                <h3 for="date_of_birth" class="form-label">Date of Birth</h3>
                                <input type="date" id="date_of_birth" x-on:input="errors.date_of_birth = ''"
                                    x-model="achSettingForm.date_of_birth" onclick="this.showPicker()"
                                    class="form-control" />
                                <template x-if="errors.date_of_birth">
                                    <p class="text-danger" x-text="errors.date_of_birth"></p>
                                </template>
                            </div>
                            <div class="mb-3">
                                <h3 for="ssn" class="form-label">SSN</h3>
                                <p class="paragraph">Enter your Social Security Number (SSN).</p>
                                <input type="text" id="ssn" x-on:input="errors.ssn = ''"
                                    x-model="achSettingForm.ssn" class="form-control" maxlength="11"
                                    @input="achSettingForm.ssn = formatSSN($event.target.value)" />
                                <template x-if="errors.ssn">
                                    <p class="text-danger" x-text="errors.ssn"></p>
                                </template>
                            </div>
                            <div class="mb-3">
                                <h3 for="controller_address" class="form-label">Controller Address</h3>
                                <p class="paragraph">Physical street address where the controller resides.</p>
                                <select id="controller_address" x-on:input="errors.controller_address = ''"
                                    x-model="achSettingForm.controller_address" class="form-select"
                                    @change="checkAddress()">
                                    <option value="">Select address</option>
                                    <option value="new">+New</option>
                                    <template x-for="address in addresses" :key="address.id">
                                        <option
                                            x-text="address.address_line_1 + (address.address_line_2 ? ', ' + address.address_line_2 : '') + ', ' + address.city + ', ' + address.state + ', ' + address.zip_code"
                                            :value="address.id"></option>
                                    </template>
                                </select>
                                <template x-if="errors.controller_address">
                                    <p class="text-danger" x-text="errors.controller_address"></p>
                                </template>
                            </div>
                            <h2 class="mb-3">Controller document</h2>
                            <p class="paragraph">Controller document is required for entity verification.</p>
                            <div class="mb-3">
                                <h3 for="controller_id" class="form-label">Upload Controller ID</h3>
                                <p class="paragraph">Please upload a <strong>non-expired US government issued photo
                                        ID.</strong> E.g. passport or driver's license.
                                    Image must be clear, glare-free, and display all 4 corners of the ID.</p>
                                <input type="file" id="controller_id" x-on:input="errors.controller_id = ''"
                                    @change="handleControllerFile($event)" class="form-control"
                                    accept="image/*,application/pdf" />
                                <template x-if="errors.controller_id">
                                    <p class="text-danger" x-text="errors.controller_id"></p>
                                </template>
                            </div>
                            <div class="mb-3">
                                <h3 for="document_label" class="form-label">Document Label</h3>
                                <select id="document_label" x-on:input="errors.document_label = ''"
                                    x-model="achSettingForm.document_label" class="form-select">
                                    <option value="">Select Label</option>
                                    <option value="license">License</option>
                                    <option value="id_card">ID Card</option>
                                    <option value="passport">Passport</option>
                                </select>
                                <template x-if="errors.document_label">
                                    <p class="text-danger" x-text="errors.document_label"></p>
                                </template>
                            </div>
                        </div>
                        <div class="content-section" :class="{ 'active': currentStep === 'BeneficialMethod' }">
                            <h2 class="mb-3">Beneficial owner information</h2>
                            <p class="paragraph">A beneficial owner is an individual who owns 25% or more of the
                                entity.</p>
                            <div class="mb-3">
                                <h6 for="does_individual">Does any individual (other than the controller from prior
                                    step) own 25% or more of
                                    the entity to verify?</h6>
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="does_individual"
                                            x-on:input="errors.does_individual = ''"
                                            x-model="achSettingForm.does_individual" :value="1"
                                            id="does_individual">
                                        <h3 class="form-check-label" for="does_individual">Yes</h3>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="does_individual"
                                            x-on:input="errors.does_individual = ''"
                                            x-model="achSettingForm.does_individual" :value="0"
                                            id="does_individual" selected>
                                        <h3 class="form-check-label" for="does_individual">No</h3>
                                    </div>
                                </div>
                                <template x-if="errors.does_individual">
                                    <p class="text-danger" x-text="errors.does_individual"></p>
                                </template>
                            </div>
                            <template x-if="achSettingForm.does_individual == '1'">
                                <div class="" id="valuation_form">
                                    <div class="d-flex justify-content-between">
                                        <h3 class="pt-6">Beneficial Owner(S)</h3>
                                        <div>
                                            <button class="btn btn-outline-primary" onclick="beneficialModal()">
                                                Add beneficial owner
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered mt-3" id="offerings-table">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Address</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr x-show="!hideAchRow">
                                                    <td
                                                        x-text="achSettingForm.first_name + ' ' + achSettingForm.last_name">
                                                    </td>
                                                    <td x-text="getAddressById(achSettingForm.controller_address)">
                                                    </td>
                                                    <td>
                                                        <!-- Delete Button -->
                                                        <span role="button" title="delete" @click="confirmDelete()">
                                                            <i class="fas fa-trash"></i>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <template x-for="(beneficial, index) in beneficials"
                                                    :key="beneficial.id">
                                                    <tr>
                                                        <td
                                                            x-text="beneficial.first_name + ' ' + beneficial.last_name">
                                                        </td>
                                                        <td
                                                            x-text="beneficial.address_1 + (beneficial.address_2 ? ', ' + beneficial.address_2 : '') + ', ' + beneficial.city + ', ' + beneficial.state + ', ' + beneficial.zipcode">
                                                        </td>
                                                        <td>
                                                            <!-- Edit Button -->
                                                            <span role="button" title="edit"
                                                                @click="editBeneficial(beneficial)">
                                                                <i class="fas fa-edit"></i>
                                                            </span>
                                                            <!-- Delete Button -->
                                                            <span role="button" title="delete"
                                                                @click="confirmBeneficialDelete('{{ route('admin.deals.edit.destroyBeneficial', ['deal' => $deal->id]) }}?beneficial_id=' + beneficial.id, index)">
                                                                <i class="fas fa-trash"></i>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="content-section" :class="{ 'active': currentStep === 'ReviewMethod' }">
                            <h2 class="mb-3">Review Your Details</h2>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Entity Name:</strong>
                                    <input type="text" class="form-control" x-model="achSettingForm.entity_name"
                                        readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>Entity Type:</strong>
                                    <input type="text" class="form-control" x-model="achSettingForm.entity_type"
                                        readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>State Registration:</strong>
                                    <input type="text" class="form-control"
                                        x-model="achSettingForm.state_registration" readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>EIN:</strong>
                                    <input type="text" class="form-control" x-model="achSettingForm.ein" readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>Address</strong>
                                    <input type="text" class="form-control" x-model="achSettingForm.address_id"
                                        readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>First name</strong>
                                    <input type="text" class="form-control me-2" placeholder="First Name"
                                        x-model="achSettingForm.first_name" readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>Last name</strong>
                                    <input type="text" class="form-control" placeholder="Last Name"
                                        x-model="achSettingForm.last_name" readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>Job Title:</strong>
                                    <input type="text" class="form-control" x-model="achSettingForm.job_title"
                                        readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>Date of Birth:</strong>
                                    <input type="date" class="form-control" onclick="this.showPicker()"
                                        x-model="achSettingForm.date_of_birth" readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>SSN:</strong>
                                    <input type="text" class="form-control" x-model="achSettingForm.ssn" readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>Address</strong>
                                    <input type="text" class="form-control"
                                        x-model="achSettingForm.controller_address" readonly>
                                </li>
                                <li class="list-group-item">
                                    <strong>Beneficial Owner(s) information:</strong>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered mt-3" id="offerings-table">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Address</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr x-show="!hideAchRow">
                                                    <td
                                                        x-text="achSettingForm.first_name + ' ' + achSettingForm.last_name">
                                                    </td>
                                                    <td x-text="getAddressById(achSettingForm.controller_address)">
                                                    </td>
                                                </tr>
                                                <template x-for="beneficial in beneficials" :key="beneficial.id">
                                                    <tr>
                                                        <td
                                                            x-text="beneficial.first_name + ' ' + beneficial.last_name">
                                                        </td>
                                                        <td
                                                            x-text="beneficial.address_1 + (beneficial.address_2 ? ', ' + beneficial.address_2 : '') + ', ' + beneficial.city + ', ' + beneficial.state + ', ' + beneficial.zipcode">
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" x-show="step > 1" @click="prevStep()">
                                Previous
                            </button>
                            <button type="button" class="btn btn-primary" x-show="step < 4" @click="nextStep()">
                                Next
                            </button>
                            <button type="button" class="btn btn-success" x-show="step === 4"
                                @click="submitForm()">
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="deal-modal modal right fade" id="addAddressModal" tabindex="0"
        aria-labelledby="addAddressModalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white" style="height:80px;">
                    <h5 class="modal-title col text-white" id="addAddressModalModalLabel">Add address</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Deal Form Body for Address -->
                    <div>
                        @csrf
                        <div class="mb-3">
                            <h3 for="country" class="form-label">Country</h3>
                            <select id="country" name="country" class="form-control"
                                x-on:input="addressErrors.country = ''" x-model="addressForm.country" required>
                                <option value="usa" selected>United States Of America</option>
                            </select>
                            <span x-text="addressErrors.country" x-show="addressErrors.country"
                                class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <h3 for="address_line_1" class="form-label">Address line 1</h3>
                            <input type="text" id="address_line_1" name="address_line_1" class="form-control "
                                x-on:input="addressErrors.address_line_1 = ''" x-model="addressForm.address_line_1"
                                required>
                            <span x-text="addressErrors.address_line_1" x-show="addressErrors.address_line_1"
                                class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <h3 for="address_line_2" class="form-label">Address line 2</h3>
                            <input type="text" id="address_line_2" name="address_line_2" class="form-select "
                                x-on:input="addressErrors.address_line_2 = ''" x-model="addressForm.address_line_2">
                            <span x-text="addressErrors.address_line_2" x-show="addressErrors.address_line_2"
                                class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <h3 for="city" class="form-label">City</h3>
                            <input type="text" id="city" name="city" class="form-select "
                                x-on:input="addressErrors.city = ''" x-model="addressForm.city" required>
                            <span x-text="addressErrors.city" x-show="addressErrors.city" class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <h3 for="state" class="form-label">State</h3>
                            <select id="state" name="state" class="form-select "
                                x-on:input="addressErrors.state = ''" x-model="addressForm.state" required>
                                <option option="">Select state</option>
                                <option value="ak">Ak</option>
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
                            <span x-text="addressErrors.state" x-show="addressErrors.state"
                                class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <h3 for="zip_code" class="form-label">Zip code</h3>
                            <input type="text" id="zip_code" name="zip_code" class="form-select "
                                x-on:input="addressErrors.zip_code = ''" x-model="addressForm.zip_code" required>
                            <span x-text="addressErrors.zip_code" x-show="addressErrors.zip_code"
                                class="text-danger"></span>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save" @click="saveAddressForm()">
                                Add Address
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="deal-modal modal right fade" id="addBeneficialModal" tabindex="0"
        aria-labelledby="addBeneficialModalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white" style="height:80px;">
                    <h5 class="modal-title col text-white" id="addBeneficialModalModalLabel">Add BeneFicial Owner</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Deal Form Body for Address -->
                    <div>
                        @csrf
                        <h1>BeneFicial Owner information</h1>
                        <h4>BeneFicial Owner information.</h4>
                        <div class="mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-on:input="beneficialErrors.first_name == ''"
                                x-model="beneficialForm.first_name" id="first_name">
                            <span x-text="beneficialErrors.first_name" x-show="beneficialErrors.first_name"
                                class="text-danger"></span>

                        </div>

                        <div class="mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-on:input="beneficialErrors.last_name == ''"
                                x-model="beneficialForm.last_name" id="last_name">
                            <span x-text="beneficialErrors.last_name" x-show="beneficialErrors.last_name"
                                class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" onclick="this.showPicker()"
                                x-on:input="beneficialErrors.dob == ''" x-model="beneficialForm.dob" id="dob">
                            <span x-text="beneficialErrors.dob" x-show="beneficialErrors.dob"
                                class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Social Security Number <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control"
                                x-on:input="beneficialErrors.social_security_number == ''"
                                x-model="beneficialForm.social_security_number" id="ssn"
                                @input="beneficialForm.social_security_number = formatSSN($event.target.value)"
                                maxlength="11" placeholder="123-45-6789">
                            <span x-text="beneficialErrors.social_security_number"
                                x-show="beneficialErrors.social_security_number" class="text-danger"></span>
                        </div>

                        <h1>Address</h1>
                        <h4>Beneficial Owner Address</h4>
                        <div class="mb-3">
                            <p class="paragraph">Must be a physical street address and cannot be a shipping address or
                                a PO box.</p>
                            <label class="form-label">Address Lookup</label>
                            <input type="text" class="form-control"
                                x-on:input="beneficialErrors.address_lookup == ''"
                                x-model="beneficialForm.address_lookup" id="addressLookup"
                                placeholder="Enter a location">
                            <span x-text="beneficialErrors.address_lookup" x-show="beneficialErrors.address_lookup"
                                class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Street Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-on:input="beneficialErrors.address_1 == ''"
                                x-model="beneficialForm.address_1" id="streetAddress1"
                                placeholder="May not be a PO box">
                            <span x-text="beneficialErrors.address_1" x-show="beneficialErrors.address_1"
                                class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Street Address Line 2</label>
                            <input type="text" class="form-control" x-on:input="beneficialErrors.address_2 == ''"
                                x-model="beneficialForm.address_2" id="streetAddress2">
                            <span x-text="beneficialErrors.address_2" x-show="beneficialErrors.address_2"
                                class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-on:input="beneficialErrors.city == ''"
                                x-model="beneficialForm.city" id="city">
                            <span x-text="beneficialErrors.city" x-show="beneficialErrors.city"
                                class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select class="form-select" x-on:input="beneficialErrors.state == ''"
                                x-model="beneficialForm.state" id="state">
                                <option option="">Select state</option>
                                <option value="ak">Ak</option>
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
                            <span x-text="beneficialErrors.state" x-show="beneficialErrors.state"
                                class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Zipcode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" x-on:input="beneficialErrors.zipcode == ''"
                                x-model="beneficialForm.zipcode" id="zipcode">
                            <span x-text="beneficialErrors.zipcode" x-show="beneficialErrors.zipcode"
                                class="text-danger"></span>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save" @click="saveBeneficialForm()">
                                Add Beneficial
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/js/fundingInfo.js'])
    <script>
        window.urls = {
            ach: '{{ route('admin.stripe.ach.initiate', $deal->id) }}',
        }
    </script>
    <script>
        function ach_setting_data() {
            var csrf = '{{ csrf_token() }}';
            return {
                step: 1,
                hideAchRow: false,
                loading: false,
                addresses: @json($deal->addresses),
                beneficials: @json($deal->beneficial_owner_details),
                achSettingForm: {
                    deal_id: '{{ $deal->id }}',
                    entity_name: '',
                    entity_type: '',
                    address_id: '',
                    ein_letter: null,
                    controller_id: '',
                    controller_address: '',
                    state_registration: '',
                    ein: '',
                    first_name: '',
                    last_name: '',
                    job_title: '',
                    ssn: '',
                    date_of_birth: '',
                    document_label: '',
                    does_individual: false,
                    verify_detail: false,
                },
                addressForm: {
                    deal_id: '{{ $deal->id }}',
                    country: 'usa',
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zip_code: ''
                },
                beneficialForm: {
                    deal_id: '{{ $deal->id }}',
                    first_name: '',
                    last_name: '',
                    dob: '',
                    social_security_number: '',
                    address_1: '',
                    address_2: '',
                    address_lookup: '',
                    city: '',
                    state: '',
                    zipcode: ''
                },
                errors: {},
                addressErrors: {},
                beneficialErrors: {},

                dragOver: false,
                files: [],
                getAddressById(id) {
                    let address = this.addresses.find(address => address.id === Number(id));
                    if (!address) {
                        return '';
                    }
                    return address.address_line_1 + (address.address_line_2 ? ', ' + address.address_line_2 : '') + ', ' +
                        address.city + ', ' + address.state + ', ' + address.zip_code;
                },
                async completeOnboarding(){
                    this.loading = true;
                    let url = "{{ route($prefix . '.againOnboarding', $deal->id) }}";

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            }
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
                            window.location.href = responseData;

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                async submitForm() {
                    if (!this.validateStep()) {
                        return;
                    }
                    this.loading = true;
                    let url = "{{ route($prefix . '.deals.edit.storeAchSetting', $deal->id) }}";

                    try {

                        let formData = new FormData();
                        for (const key in this.achSettingForm) {
                            if (this.achSettingForm.hasOwnProperty(key)) {
                                formData.append(key, this.achSettingForm[key]);
                            }
                        }


                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            body: formData
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
                            $('#addEntityModal').modal('hide');
                            window.location.href = window.location.pathname + '#ach-setting';
                            window.location.reload();

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                async saveAddressForm() {
                    this.loading = true;
                    let url = "{{ route($prefix . '.deals.edit.storeAddress', $deal->id) }}";

                    try {

                        let formData = new FormData();
                        for (const key in this.addressForm) {
                            if (this.addressForm.hasOwnProperty(key)) {
                                formData.append(key, this.addressForm[key]);
                            }
                        }


                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            body: formData
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.addressErrors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            // Address to the list
                            // debugger
                            this.addresses = responseData.addresses;

                            $('#addAddressModal').modal('hide');
                            $('#addBeneficialModal').modal('hide');

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

                async saveBeneficialForm() {
                    this.loading = true;
                    let url = "";
                    if (this.beneficialForm.id) {
                        url = "{{ route($prefix . '.deals.edit.updateBeneficialOwnerDetail', [$deal->id, '__id__']) }}"
                            .replace('__id__', this.beneficialForm.id);
                    } else {
                        url = "{{ route($prefix . '.deals.edit.storeBeneficialOwnerDetail', $deal->id) }}";
                    }

                    try {
                        let formData = new FormData();
                        for (const key in this.beneficialForm) {
                            if (this.beneficialForm.hasOwnProperty(key)) {
                                formData.append(key, this.beneficialForm[key]);
                            }
                        }


                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            body: formData
                        });

                        this.loading = false;

                        if (response.status === 422) {
                            const responseData = await response.json();
                            this.beneficialErrors = responseData.errors;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            this.beneficials = responseData.beneficial_owners;
                            $('#addBeneficialModal').modal('hide');
                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },
                // Initial current step id
                currentStep: 'EntityMethod',
                steps: [{
                        id: 'EntityMethod',
                        label: 'Entity',
                        status: 'inactive'
                    },
                    {
                        id: 'ControllerMethod',
                        label: 'Controller',
                        status: 'inactive'
                    },
                    {
                        id: 'BeneficialMethod',
                        label: 'Beneficial',
                        status: 'inactive'
                    },
                    {
                        id: 'ReviewMethod',
                        label: 'Review',
                        status: 'inactive'
                    }
                ],
                completedSteps: [],

                completeStep(stepId) {
                    // Mark the step as complete (green) and update completedSteps
                    this.steps.forEach(item => {
                        if (item.id === stepId) {
                            item.status = 'green';
                            if (!this.completedSteps.includes(item.id)) {
                                this.completedSteps.push(item.id);
                            }
                        }
                    });
                    this.showStep(stepId);
                },
                showStep(stepId) {
                    this.currentStep = stepId;
                    this.steps.forEach((item, index) => {
                        const clickedIndex = this.steps.findIndex(s => s.id === stepId);
                        if (this.steps.findIndex(s => s.id === item.id) < clickedIndex) {
                            item.status = 'green';
                            if (!this.completedSteps.includes(item.id)) {
                                this.completedSteps.push(item.id);
                            }
                        } else if (this.steps.findIndex(s => s.id === item.id) === clickedIndex) {
                            item.status = 'blue';
                        } else {
                            item.status = 'inactive';
                        }
                    });
                },
                goToStep(stepId) {
                    const clickedIndex = this.steps.findIndex(s => s.id === stepId);
                    const currentIndex = this.steps.findIndex(s => s.id === this.currentStep);
                    if (clickedIndex < currentIndex) {
                        this.step = clickedIndex + 1;
                        this.currentStep = stepId;
                    } else {
                        if (this.validateStep()) {
                            this.completeStep(stepId);
                            this.step = clickedIndex + 1;
                            this.currentStep = stepId;
                        }
                    }
                },
                init() {
                    this.currentStep = this.steps[0].id;
                    this.showStep(this.currentStep);
                },
                validateStep() {
                    this.errors = {}; // Reset errors
                    let valid = true;
                    if (this.step === 1) {
                        if (!this.achSettingForm.entity_name) {
                            this.errors.entity_name = 'This field is required';
                            valid = false;
                        }
                        if (!this.achSettingForm.entity_type) {
                            this.errors.entity_type = 'This field is required';
                            valid = false;
                        }
                        if (!this.achSettingForm.state_registration) {
                            this.errors.state_registration = 'This field is required';
                            valid = false;
                        }
                        if (!this.achSettingForm.ein) {
                            this.errors.ein = 'This field is required';
                            valid = false;
                        } else if (!/^\d{2}-\d{7}$/.test(this.achSettingForm.ein)) {
                            this.errors.ein = 'Invalid EIN number';
                            valid = false;
                        } else {
                            delete this.errors.ein;
                        }

                        if (!this.achSettingForm.address_id) {
                            this.errors.address = 'This field is required';
                            valid = false;
                        }
                        if (!this.achSettingForm.ein_letter) {
                            this.errors.ein_letter = 'This field is required';
                            valid = false;
                        }
                    } else if (this.step === 2) {
                        if (!this.achSettingForm.first_name) {
                            this.errors.first_name = 'This field is required';
                            valid = false;
                        }
                        if (!this.achSettingForm.last_name) {
                            this.errors.last_name = 'This field is required';
                            valid = false;
                        }
                        if (!this.achSettingForm.job_title) {
                            this.errors.job_title = 'This field is required';
                            valid = false;
                        }
                        if (!this.achSettingForm.date_of_birth) {
                            this.errors.date_of_birth = 'This field is required';
                            valid = false;
                        } else {
                            const dob = new Date(this.achSettingForm.date_of_birth);
                            const today = new Date();
                            let age = today.getFullYear() - dob.getFullYear();

                            // Check if birthday has occurred this year, if not, subtract 1 from age
                            const monthDiff = today.getMonth() - dob.getMonth();
                            const dayDiff = today.getDate() - dob.getDate();

                            if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                                age--;
                            }

                            if (age < 18) {
                                this.errors.date_of_birth = 'You must be at least 18 years old';
                                valid = false;
                            } else {
                                delete this.errors.date_of_birth; // Remove error if valid
                            }
                        }

                        if (!this.achSettingForm.ssn) {
                            this.errors.ssn = 'This field is required';
                            valid = false;
                        } else if (!/^\d{3}-\d{2}-\d{4}$/.test(this.achSettingForm.ssn)) {
                            this.errors.ssn = 'Invalid  SSN/EIN';
                            valid = false;
                        } else {
                            delete this.errors.ssn;
                        }

                        if (!this.achSettingForm.controller_address) {
                            this.errors.controller_address = 'This field is required';
                            valid = false;
                        }
                        if (!this.achSettingForm.controller_id) {
                            this.errors.controller_id = 'This field is required';
                            valid = false;
                        }
                        if (!this.achSettingForm.document_label) {
                            this.errors.document_label = 'This field is required';
                            valid = false;
                        }
                    } else if (this.step === 3) {
                        if (!this.achSettingForm.does_individual) {
                            this.errors.does_individual = 'This field is required';
                            valid = false;
                        }

                    }
                    return valid;
                },
                formatEIN(value) {
                    value = value.replace(/\D/g, '');
                    if (value.length <= 2) {
                        return value;
                    } else if (value.length <= 9) {
                        return value.replace(/^(\d{2})(\d{0,7})$/, '$1-$2');
                    } else {
                        return value.slice(0, 9).replace(/^(\d{2})(\d{7})$/, '$1-$2');
                    }
                },

                formatSSN(value) {
                    value = value.replace(/\D/g, '');
                    if (value.length <= 3) {
                        return value;
                    } else if (value.length <= 5) {
                        return value.replace(/^(\d{3})(\d{0,2})$/, '$1-$2');
                    } else {
                        return value.replace(/^(\d{3})(\d{2})(\d{0,4})$/, '$1-$2-$3');
                    }
                },
                nextStep() {
                    if (this.validateStep()) {
                        this.step++;
                        this.showStep(this.steps[this.step - 1].id);
                    }
                },
                prevStep() {
                    this.step--;
                    this.showStep(this.steps[this.step - 1].id);
                    this.hideAchRow = false;
                },

                handleEntityFile(event) {
                    const file = event.target.files[0];
                    const allowedTypes = ["image/jpeg", "image/png", "application/pdf"];
                    if (file && allowedTypes.includes(file.type)) {
                        this.achSettingForm.ein_letter = file;
                    } else {
                        this.errors.ein_letter =
                            "Only JPEG, PNG, or PDF files are allowed";
                        this.achSettingForm.ein_letter = null;
                    };
                },
                // Handle file upload for Controller ID (Step 2)
                handleControllerFile(event) {
                    const file = event.target.files[0];
                    const allowedTypes = ["image/jpeg", "image/png", "application/pdf"];
                    if (file && allowedTypes.includes(file.type)) {
                        this.achSettingForm.controller_id = file;
                    } else {
                        this.errors.controller_id =
                            "Only JPEG, PNG, or PDF files are allowed";
                        this.achSettingForm.controller_id = null;
                    };
                },
                changeAddress() {
                    if (this.achSettingForm.address_id === 'new') {
                        this.achSettingForm.address_id = '';
                        $('#addEntityModal').modal('hide');
                        $('#addAddressModal').modal('show');
                    }
                },
                checkAddress() {
                    if (this.achSettingForm.controller_address === 'new') {
                        this.achSettingForm.controller_address = '';
                        $('#addEntityModal').modal('hide');
                        $('#addAddressModal').modal('show');
                    }
                },
                confirmBeneficialDelete(url, index) {
                    Swal.fire({
                        title: 'Delete Beneficial',
                        text: "Are you sure you want to delete this Beneficial? This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Call deleteBeneficial and pass the index so we know which item to remove on success.
                            this.deleteBeneficial(url, index);
                        }
                    });
                },
                deleteBeneficial(url, index) {
                    fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove the beneficial from the array using its index.
                                this.beneficials.splice(index, 1);
                                Swal.fire(
                                    'Deleted!',
                                    'Beneficial has been deleted successfully.',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Error!',
                                    data.message || 'An error occurred while deleting the Beneficial.',
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting the Beneficial.',
                                'error'
                            );
                        });
                },
                editBeneficial(beneficial) {
                    this.beneficialForm = {
                        ...beneficial
                    };
                    beneficialModal();
                },
                confirmDelete() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You won\'t be able to revert this!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.hideAchRow = true; // ✅ Hide row only if confirmed
                            Swal.fire('Deleted!', 'The row has been deleted.', 'success');
                        }
                    });
                },


            }
        }
    </script>
    <script>
        function beneficialModal() {
            $('#addEntityModal').modal('hide'); // Hide the 'addEntityModal'
            $('#addBeneficialModal').modal('show'); // Show the 'addBeneficialModal'
        }
        $('#addAddressModal').on('hide.bs.modal', function() {
            $('#addEntityModal').modal('show');
        });
        $('#addBeneficialModal').on('hide.bs.modal', function() {
            //debugger
            $('#addEntityModal').modal('show');
        });

        // Add script if have # in url than active the tab on document load jquery
        $(document).ready(function() {
            if (window.location.hash) {
                var hash = window.location.hash;
                $('a[data-bs-target="' + hash + '"]').tab('show');
            }
        });
    </script>
@endpush
