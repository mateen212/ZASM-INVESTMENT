@extends('admin.layouts.app') <!-- Assuming you have a main app layout -->
@section('panel')
    <div class="card">
        <div class="card-body">
            <div class="admin-offering-detail" x-data="offering_manage()" x-cloak>

                <template x-if="loading">
                    <div class="custom-loader-overlay">
                        <div class="custom-loader"></div>
                    </div>
                </template>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs" id="deal-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#general" data-bs-toggle="tab">General</a></li>
                    <li class="nav-item"><a class="nav-link" href="#offering_nda" data-bs-toggle="tab">Offering NDA</a></li>

                </ul>
                <!-- Tab Contents -->
                <div class="tab-content mt-4">
                    {{--  Overview  --}}
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="container mt-5">
                            <!-- general Section -->
                            <div class="mb-4">
                                <h4 class="fw-bold" style="font-size: large;">General</h4>
                                <div class="row g-3">
                                    <!-- Allow investments under minimum amount -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Allow
                                                investments
                                                under minimum
                                                amount</label>

                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="min_investment"
                                                        :value="1" x-model="generalForm.min_investment">
                                                    Yes (most common)
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="min_investment"
                                                        :value="0" x-model="generalForm.min_investment">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Allow investments above maximum amount -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Allow
                                                investments
                                                above maximum amount</label>
                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="max_investment"
                                                        :value="1" x-model="generalForm.max_investment">
                                                    Yes (most common)
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="max_investment"
                                                        :value="0" x-model="generalForm.max_investment">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Require account creation before soft committing -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Require
                                                account
                                                creation before soft committing</label>
                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="account_creation"
                                                        :value="1" x-model="generalForm.account_creation">
                                                    Yes
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="account_creation"
                                                        :value="0" x-model="generalForm.account_creation">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Prompt LPs to complete profile information -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Prompt LPs to
                                                complete profile information</label>
                                        </div>
                                        <div style="width:45%;">
                                            <select class="form-select" x-model="generalForm.prompt_lp">
                                                <option value="lp_must_require_fields">Lps must complete required fields
                                                </option>
                                                <option>Prompt Lps; all fields optional (recommended)</option>
                                                <option value="do_not_prompt">Do not prompt LPs</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Require IRA signed subscription document -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Require IRA
                                                signed
                                                subscription document</label>
                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="ira_document"
                                                        :value="1" x-model="generalForm.ira_document">
                                                    Yes
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="ira_document"
                                                        :value="0" x-model="generalForm.ira_document">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Allowed profile types -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                                            Allowed profile types
                                            <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Allowed profile types">
                                                ?
                                            </span>
                                        </span>
                                        <div class="d-flex align-items-center" style="width: 25rem;">
                                            <div class="form-check form-check-inline" style="width: 25rem;">
                                                <select id="allowed_profile_types"
                                                    class="form-select js-example-basic-multiple"
                                                    name="allowed_profile_types[]"
                                                    x-model="generalForm.allowed_profile_types">
                                                    <option value="all">All (most common)</option>
                                                    <option value="lcpts_cs">LLC,Corp,Partnership,trust,solo 401(K),
                                                        Checkbook
                                                        IRA</option>
                                                    <option value="individual">Individual</option>
                                                    <option value="ci_cb">Custodian IRA or custodian based 401(k)</option>
                                                    <option value="join_tenancy">Join Tenancy</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- E-sign Templates Section -->
                            <div class="mb-4">
                                <h4 class="fw-bold" style="font-size: large;">E-sign templates</h4>
                                <div class="row g-3">
                                    <!-- Require investor questionnaire -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <div>
                                                <div>
                                                    <label class=""
                                                        style="line-height: 6px; font-weight: 500;">Require
                                                        investor questionnaire <button class="btn btn-link px-0">Customize
                                                            questionnaire</button> </label>
                                                    <p>Note: This can only be toggled when there are no existing e-sign
                                                        templates.
                                                    </p>

                                                </div>
                                            </div>
                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input"
                                                        name="require_questionnaire" :value="1"
                                                        x-model="generalForm.questionnaire">
                                                    Yes (most common)
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input"
                                                        name="require_questionnaire" :value="0"
                                                        x-model="generalForm.questionnaire">

                                                    No
                                                </label>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Require investor questionnaire when soft committing -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Require
                                                investor
                                                questionnaire when soft committing</label>
                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input"
                                                        name="questionnaire_soft" :value="1"
                                                        x-model="generalForm.questionnaire_soft">
                                                    Yes
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input"
                                                        name="questionnaire_soft" :value="0"
                                                        x-model="generalForm.questionnaire_soft">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Require investor W-9 form -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <div>
                                                <label class="mr-3" style="line-height: 32px; font-weight: 500;">Require
                                                    investor W-9 form</label>
                                                <p>Note: This can only be toggled when there are no existing e-sign
                                                    templates
                                                </p>
                                            </div>
                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="require_w9"
                                                        :value="1" x-model="generalForm.require_w9">
                                                    Yes (most common)
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="require_w9"
                                                        :value="0" x-model="generalForm.require_w9">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Default signature text -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <div>
                                                <label class="mr-3" style="line-height: 32px; font-weight: 500;">Default
                                                    signature
                                                    text</label>
                                                <p>Note: This does not affect signed documents in progress.</p>
                                            </div>
                                        </div>
                                        <div style="width:45%;">
                                            <input type="text" class="form-select" placeholder="Enter signature text"
                                                x-model="generalForm.signature_text">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Accreditation Section -->
                            <div class="mb-4">
                                <h4 class="fw-bold" style="font-size: large;">Accreditation</h4>
                                <div class="row g-3">
                                    <!-- Verify investor accreditation -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Verify
                                                investor
                                                accreditation</label>
                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="verify_investor"
                                                        :value="1" x-model="generalForm.verify_investor">
                                                    Yes
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="verify_investor"
                                                        :value="0" x-model="generalForm.verify_investor">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>



                                    <!-- Verify investor accreditation when soft committing -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Verify
                                                investor
                                                accreditation when soft committing</label>
                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input"
                                                        name="verify_accreditation_soft" :value="1"
                                                        x-model="generalForm.verify_accreditation_soft">
                                                    Yes
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input"
                                                        name="verify_accreditation_soft" :value="0"
                                                        x-model="generalForm.verify_accreditation_soft">

                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Require
                                                accreditation verification before LP signs</label>
                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="rav_blp"
                                                        :value="1" x-model="generalForm.rav_blp">
                                                    Yes
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="rav_blp"
                                                        :value="0" x-model="generalForm.rav_blp">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Allow
                                                investors
                                                to complete verification later</label>
                                        </div>
                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="ait_cvl"
                                                        :value="1" x-model="generalForm.ait_cvl">
                                                    Yes
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="ait_cvl"
                                                        :value="0" x-model="generalForm.ait_cvl">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Accreditation methods -->
                                    <div class="mb-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <div>
                                                <label class="mr-3"
                                                    style="line-height: 32px; font-weight: 500;">Accreditation
                                                    methods</label>
                                                <p>Note: Each completed verification via Parallel Markets will incur a $45
                                                    charge to
                                                    the deal.</p>
                                            </div>
                                        </div>

                                        <div style="width:45%;">
                                            <select class="form-select" x-model="generalForm.methods">
                                                <option value="both">Both</option>
                                                <option value="accreditation_via_parallel_market">Accreditation via
                                                    Parallel Markets</option>
                                                <option value="manual_accreditation">Manual Accreditation letter upload
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="mb-4">
                                    <h4 class="fw-bold" style="font-size: large;">Identity verification</h4>
                                    <div class="mb-3 d-flex align-items-center justify-content-between">

                                        <div style="width:45%;">
                                            <div>
                                                <label class="mr-3" style="line-height: 32px; font-weight: 500;">Verify
                                                    investor
                                                    identity</label>
                                                <p>Note: Each investor will incur a $15 charge to the deal. You will only be
                                                    charged
                                                    once per investor across all deals</p>
                                            </div>
                                        </div>

                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input"
                                                        name="verify_accreditation_identity" :value="1"
                                                        x-model="generalForm.verify_accreditation_identity">
                                                    Yes
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input"
                                                        name="verify_accreditation_identity" :value="0"
                                                        x-model="generalForm.verify_accreditation_identity">

                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <h4 class="fw-bold" style="font-size: large;">KYC documents</h4>
                                    <div class="mb-3 d-flex align-items-center justify-content-between">

                                        <div style="width:45%;">
                                            <label class="mr-3" style="line-height: 32px; font-weight: 500;">Require KYC
                                                supporting documents</label>
                                        </div>

                                        <div style="width:45%;">
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="require_kyc"
                                                        :value="1" x-model="generalForm.require_kyc">
                                                    Yes
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="require_kyc"
                                                        :value="0" x-model="generalForm.require_kyc">

                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex align-items-center justify-content-between">
                                        <div style="width:45%;">
                                            <small>Display offering in Homepage & Deals listing page</small>
                                        </div>
                                        <div class="d-flex align-items-center" style="width:45%;">
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="display_offering"
                                                    :value="1" x-model="generalForm.display_offering">
                                                Yes
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="display_offering"
                                                    :value="0" x-model="generalForm.display_offering">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2">Cancel</button>
                            <button type="submit" class="btn btn-primary deal-save"
                                @click="submitGeneralForm(generalForm)" style="width:100px;">Save</button>
                        </div>
                    </div>

                </div>
                <!-- Key Metrics -->
                <div class="tab-pane fade" id="offering_nda" role="tabpanel" aria-labelledby="offering_nda-tab">
                    <div class="container mt-5" x-data="{ notifyGP: false, }">
                        <div class="mt-8 mb-6">
                            <div class="mb-8">
                                <h5 class="fw-bold mb-3" style="font-size: large;">NDA Template</h5>
                                <p class="text-muted">Configure an NDA to be signed by potential LPs before they view the
                                    offering documents or create investments</p>
                            </div>
                            <div class="d-flex align-items-center  mb-6 flex-direction-between">
                                <div style="width:20%;"><span class="fw-bold">Status:</span></div>
                                <div style="width:70%;"><span class=" ">NDA template not created</span></div>
                            </div>
                            <div class="d-flex align-items-center  mb-6 flex-direction-between">
                                <div style="width:20%;"><span class=" fw-bold">Actions:</span></div>
                                <div style="width:70%;">
                                    <button class="btn btn-primary px-4" data-bs-toggle="modal"
                                        data-bs-target="#addConfigureModal">Configure from template</button>
                                    <button class="btn btn-outline-secondary px-4">Upload NDA</button>
                                </div>
                            </div>
                        </div>
                        <h5 class="fw-bold mt-4 mb-4" style="font-size: large;">NDA Notifications</h5>
                        <div class="mb-3 d-flex align-items-center mt-8 mb-6 flex-direction-between">
                            <div style="width:45%;">
                                <p class="text-muted">Send GPs an email on completed NDA signature <span
                                        class="text-muted" title="generalForm Partners">?</span></p>
                            </div>
                            <div style="width:45%;">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="notifyGP" id="notifyYes"
                                        :value="1" x-model="notifyGP">
                                    <label class="form-check-label" for="notifyYes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="notifyGP" id="notifyNo"
                                        :value="0" x-model="notifyGP">
                                    <label class="form-check-label" for="notifyNo">No</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-4">Signed NDAs</h5>
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>IP Address</th>
                                        <th>Signed at</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No signed NDAs</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="deal-modal modal right fade" id="addConfigureModal" tabindex="-1"
                    aria-labelledby="addConfigureModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Configure Test Deal NDA Template</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form Section -->
                                <iframe src="https://www.orimi.com/pdf-test.pdf"
                                    style="width: 100%; height: 400px; border: none;"></iframe>
                                <h5 class="mt-4">Setup</h5>
                                <div class="mb-3">
                                    <p>Pursuant to Section 16, this Agreement shall be governed by the laws of the following
                                        jurisdiction (check only one):</p>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="jurisdiction1"
                                            value="State of Incorporation" x-model="offerNDAForm.jurisdiction">
                                        <label class="form-check-label" for="jurisdiction1">U.S. State in which Sponsor is
                                            incorporated or formed</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="jurisdiction2"
                                            value="State of Headquarters" x-model="offerNDAForm.jurisdiction">
                                        <label class="form-check-label" for="jurisdiction2">U.S. State in which Sponsor is
                                            headquartered or conducts most of its operations</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="jurisdiction3" value="Other"
                                            x-model="offerNDAForm.jurisdiction">
                                        <label class="form-check-label" for="jurisdiction3">Other Jurisdiction</label>
                                    </div>
                                </div>
                                <div class="mb-3  d-flex align-items-center justify-content-between">
                                    <label style="width:30%" for="specifyJurisdiction" class="form-label">Specify
                                        jurisdiction <span class="text-danger">*</span></label>
                                    <input style="width:70%" style="width:70%" type="text" id="specifyJurisdiction"
                                        class="form-control" x-model="offerNDAForm.jurisdiction">
                                </div>
                                <h5>Your Signature</h5>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label style="width:30%" for="agreeingParty" class="form-label">Agreeing party <span
                                            class="text-danger">*</span></label>
                                    <input style="width:70%" type="text" id="agreeingParty" class="form-control"
                                        x-model="offerNDAForm.party">
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label style="width:30%" for="title" class="form-label">Title (if entity)</label>
                                    <input style="width:70%" type="text" id="title" class="form-control"
                                        x-model="offerNDAForm.title">
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label style="width:30%" for="address1" class="form-label">Address line 1 <span
                                            class="text-danger">*</span></label>
                                    <input style="width:70%" type="text" id="address1" class="form-control"
                                        x-model="offerNDAForm.address1">
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label style="width:30%" for="address2" class="form-label">Address line 2</label>
                                    <input style="width:70%" type="text" id="address2" class="form-control"
                                        x-model="offerNDAForm.address2">
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label style="width:30%" for="email" class="form-label">Email address <span
                                            class="text-danger">*</span></label>
                                    <input style="width:70%" type="email" id="email" class="form-control"
                                        x-model="offerNDAForm.email">
                                </div>
                                <div class="form-check mb-3">
                                    <input type="checkbox" id="acceptTerms" class="form-check-input"
                                        x-model="offerNDAForm.accepted">
                                    <label for="acceptTerms" class="form-check-label">I accept the terms in this agreement
                                        <span class="text-danger">*</span></label>
                                </div>
                                <div class="mb-3 d-flex align-items-center justify-content-between">
                                    <label style="width:30%" for="signature" class="form-label">Signature <span
                                            class="text-danger">*</span></label>
                                    <input style="width:70%" type="text" id="signature" class="form-control"
                                        x-model="offerNDAForm.signature">
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary"
                                        @click="console.log(offerNDAForm)">Configure</button>
                                    <button type="button" class="btn btn-secondary ms-2"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    </div>
    </div>
@endsection

@push('script')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        var csrf = '{{ csrf_token() }}';

        function offering_manage() {
            return {
                errors: {},
                loading: false,
                generalForm: {
                    min_investment: {!! $offering->manageOffering?->min_investment ? 1 : 0 !!},
                    max_investment: {!! $offering->manageOffering?->min_investment ? 1 : 0 !!},
                    account_creation: {!! $offering->manageOffering?->account_creation ? 1 : 0 !!},
                    prompt_lp: '{!! $offering->manageOffering?->prompt_lp !!}',
                    ira_document: {!! $offering->manageOffering?->ira_document ? 1 : 0 !!},
                    allowed_profile_types: '{!! $offering->manageOffering?->allowed_profile_types !!}',
                    questionnaire: {!! $offering->manageOffering?->questionnaire ? 1 : 0 !!},
                    questionnaire_soft: {!! $offering->manageOffering?->questionnaire_soft ? 1 : 0 !!},
                    require_w9: {!! $offering->manageOffering?->require_w9 ? 1 : 0 !!},
                    signature_text: '{!! $offering->manageOffering?->signature_text !!}',
                    verify_investor: {!! $offering->manageOffering?->verify_investor ? 1 : 0 !!},
                    verify_accreditation_soft: {!! $offering->manageOffering?->verify_accreditation_soft ? 1 : 0 !!},
                    methods: '{!! $offering->manageOffering?->methods !!}',
                    verify_accreditation_identity: {!! $offering->manageOffering?->verify_accreditation_identity ? 1 : 0 !!},
                    require_kyc: {!! $offering->manageOffering?->require_kyc ? 1 : 0 !!},
                    ait_cvl: {!! $offering->manageOffering?->ait_cvl ? 1 : 0 !!},
                    rav_blp: {!! $offering->manageOffering?->rav_blp ? 1 : 0 !!},
                    display_offering: {!! $offering->manageOffering?->display_offering ? 1 : 0 !!},
                },
                offerNDAForm: {
                    jurisdiction: '',
                    party: '',
                    title: '',
                    address1: '',
                    address2: '',
                    email: '',
                    accepted: false,
                    signature: '',
                },



                async submitGeneralForm(data) {
                    this.loading = true;
                    let url = "{{ route('admin.deals.offerings.storeManageOffering', $offering->id) }}";

                    try {

                        let formData = new FormData();
                        for (const key in data) {
                            if (data.hasOwnProperty(key)) {
                                formData.append(key, data[key]);
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
                            this.generals = responseData.generals;

                            cosyAlert('<strong>Success</strong><br />Offering Updated Successfully!', 'success');

                            // Reload the page
                            // window.location.reload();

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                },

            }
        }
    </script>


    <style>
        @media screen and (-webkit-min-device-pixel-ratio:0) and (min-resolution: .001dpcm) {

            /* Target Chrome browsers */
            .container {
                margin-top: 40px;
                margin-bottom: 40px;
            }
        }
    </style>
@endpush
