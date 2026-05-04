@push('style')
    <style>
        input:disabled {
            background-color: #fefefe;
        }
    </style>
@endpush

@php
    if (auth('admin')->user()->hasRole('partner')) {
        $prefix = 'partner';
    } else {
        $prefix = 'admin';
    }
@endphp

<div x-data="investmentFormHandler()" x-init="init()" x-cloak>
    <template x-if="loading">
        <div class="custom-loader-overlay">
            <div class="custom-loader"></div>
        </div>
    </template>
    <div class="deal-modal modal right fade" id="addInvestmentModal" tabindex="-1"
        aria-labelledby="addInvestmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white" style="height:80px;">
                    <h5 class="modal-title col text-white">Add Investment</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Deal Form Body --}}
                    <div>
                        @csrf
                        <h5>Investor Detail</h5>
                        <div class="mb-3">
                            <label for="investor_id" class="form-label">Investor <span
                                    style="color: red;">*</span></label>
                            <select id="name" name="investor_id" class="form-control"
                                x-model="investmentForm.investor_id" required @change="changeInvestor()"
                                {{-- @change="investmentForm.investor_id ? enableProfile = true : enableProfile = false" --}}>
                                <option value="">Select Investor</option>
                                <template x-for="(investor, i_index) in investors" :key="investor.id">
                                    <option :value="investor.id"
                                        x-text="`${investor.investor_fname} ${investor.investor_lname} (${investor.investor_email})`">
                                    </option>
                                </template>
                                <option value="add_investor">Add New Investor</option>
                            </select>
                            <span x-show="investmentErrors.investor_id" x-text="investmentErrors.investor_id"
                                class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label for="investor_profile" class="form-label">Profile</label>
                            <select id="investor_profile_id" name="investor_profile" class="form-control"
                                x-model="investmentForm.investor_profile_id" :disabled="!enableProfile"
                                @change="changeProfile()">
                                <option value="">Select Profile</option>
                                <template x-for="(profile, index) in profiles" :key="profile.id">
                                    <option :value="profile.id"
                                        x-text="profile.profile_type === 'join_tenancy'
                                            ? `${profile.profile_fname} ${profile.profile_lname} && ${profile.profile_fname2} ${profile.profile_lname2} (${profile.profile_type})`
                                        : profile.profile_type === 'custodian'
                                            ? `${profile.profile_ira_name} (${profile.profile_type})`
                                        : profile.profile_type === 'lcps_property'
                                            ? `${profile.profile_entity_name} (${profile.profile_type})`
                                        : `${profile.profile_fname} ${profile.profile_lname} (${profile.profile_type})`">
                                    </option>

                                </template>
                                <option value="add_profile">Add New Profile</option>
                            </select>
                        </div>

                        <h5>Investment Detail</h5>
                        <div class="mb-3 mt-2">
                            <label for="deal_class_id" class="form-label">Class <span
                                    style="color: red;">*</span></label>
                            <select id="deal_class_id" name="deal_class_id" class="form-select"
                                x-model="investmentForm.deal_class_id" @change="handleClassChange($event.target.value)"
                                required>
                                <option value="">Select Class</option>
                                @foreach ($deal->classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->equity_class_name }}</option>
                                @endforeach
                                @foreach ($deal->buckets as $bucket)
                                    @foreach ($bucket->classes as $bclass)
                                        <option value="{{ $bclass->id }}">{{ $bclass->equity_class_name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                            <span x-show="investmentErrors.deal_class_id" x-text="investmentErrors.deal_class_id"
                                class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="offering_id" class="form-label">Offering</label>
                            {{-- Select Box with multiple select --}}
                            <select id="offering_id" name="offering_id" class="form-select"
                                x-model="investmentForm.offering_id" required>
                                <option value="">Select Offering </option>
                                @foreach ($deal->offerings as $offering)
                                    <option value="{{ $offering->id }}">{{ $offering->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="investment_amount" class="form-label">Investment Amount ($) <span
                                    style="color: red;">*</span></label>
                            <input type="text" x-on:input="moneyFormat($el)" id="investment_amount"
                                name="investment_amount" class="form-control "
                                x-model="investmentForm.investment_amount" required>
                            <span x-show="investmentErrors.investment_amount"
                                x-text="investmentErrors.investment_amount" class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="pcb_ownership" class="form-label">Percent of class or bucket (ownership)
                                (%)</label>
                            <input type="text" x-on:input="percentFormat($el)" id="pcb_ownership"
                                name="pcb_ownership" class="form-control" x-model="investmentForm.pcb_ownership"
                                required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="op_ownership" class="form-label">Ownership percentage (ownership) (%)</label>
                            <input type="text" x-on:input="percentFormat($el)" id="op_ownership" name="op_ownership"
                                class="form-control" x-model="investmentForm.op_ownership" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="pcb_distribution" class="form-label">Percent of class or bucket (distribution)
                                (%) <span style="color: red;">*</span></label>
                            <input type="text" x-on:input="percentFormat($el)" id="pcb_distribution"
                                name="pcb_distribution" class="form-control"
                                x-model="investmentForm.pcb_distribution" required readonly>
                            <span x-show="investmentErrors.pcb_distribution"
                                x-text="investmentErrors.pcb_distribution" class="text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label for="op_distribution" class="form-label">Ownership percentage (distribution)
                                (%)</label>
                            <input type="text" x-on:input="percentFormat($el)" id="op_distribution"
                                name="op_distribution" class="form-control" x-model="investmentForm.op_distribution"
                                required readonly>
                        </div>

                        {{-- <div class="mb-3" x-data="tagInput()" @keydown.enter.prevent="addTag()">
                            <label for="investment_tags" class="form-label">Investment Tags</label>
                            <div class="form-control d-flex flex-wrap align-items-center">
                                <!-- Display selected tags -->
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="badge me-2"
                                        style="background: none; font-size: 4px; color: inherit;">
                                        <span x-text="tag"></span>
                                        <button type="button" class="btn-close btn-sm ms-1"
                                            @click="removeTag(index)"></button>
                                    </span>
                                </template>

                                <!-- Input for adding tags -->
                                <input type="text" id="investment_tags" class="border-0 flex-grow-1"
                                    placeholder="Add a tag and press Enter" x-model="newTag">
                            </div>
                            <input type="hidden" name="investment_tags" :value="tags.join(',')">
                        </div> --}}

                        <div class="mb3">
                            <label for="investment_tags" class="form-label">Investment Tags</label>
                            <select class="form-control investment-tags" multiple="multiple" name="investment_tags[]"
                                id="select_investment_tags">
                                @foreach ($investment_tags as $tag)
                                    <option value="{{ $tag }}">{{ $tag }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="date_placed" class="form-label">Date placed <span
                                    style="color: red;">*</span></label>
                            <input type="date" onclick="this.showPicker()" min="0" id="date_placed"
                                name="date_placed" class="form-control" x-model="investmentForm.date_placed" required
                                onclick="this.showPicker()">
                            <span x-show="investmentErrors.date_placed" x-text="investmentErrors.date_placed"
                                class="text-danger"></span>
                        </div>

                        <div class="mb-3">
                            <label for="contribution_method" class="form-label">Contribution Method</label>
                            <select min="0" id="contribution_method" name="contribution_method"
                                class="form-control" x-model="investmentForm.contribution_method" required>
                                <option value="check">Check</option>
                                <option value="wire_transfer">Wire Transfer</option>
                                <option value="ach_payment">ACH Payment</option>
                                <option value="direct_deposit">Direct Deposit</option>
                                <option value="none">None/Not Applicable</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="investment_status" class="form-label">Investment status <span
                                    style="color: red;">*</span></label>
                            <select id="investment_status" name="investment_status" class="form-control"
                                x-model="investmentForm.investment_status" required>
                                <option value="soft_committed">Soft Committed</option>
                                <option value="investment_started">Investment started</option>
                                <option value="document_started">Document Signing Started</option>
                                <option value="signed">Signed</option>
                                <option value="counter_signed">Counter-Signed</option>
                                <option value="funding_instructions">Funding Instructions Sent</option>
                                <option value="fund_received">Fund Fully Received(Complete)</option>
                                <option value="inactive_bought_assign_sold">Inactive (bought out, assigned, or sold)
                                </option>
                                <option value="canceled">Canceled (did not complete)</option>
                            </select>
                            <span x-show="investmentErrors.investment_status"
                                x-text="investmentErrors.investment_status" class="text-danger"></span>
                        </div>

                        <!-- Conditional Checkbox and Label -->
                        <template x-if="investmentForm.investment_status == 'document_started'">
                            <div class="form-check" x-transition>
                                <label class="form-label" for="investment_in_progress">
                                    <h5>Notify investor of in-progress investment</h5>
                                </label>
                                <div>
                                    <template x-if="investmentForm.investment_in_progress">
                                        <input class="form-label" type="checkbox" name="investment_in_progress"
                                            id="investment_in_progress"
                                            x-model="investmentForm.investment_in_progress" :value="true">
                                    </template>
                                    <template x-if="!investmentForm.investment_in_progress">
                                        <input class="form-label" type="checkbox" name="investment_in_progress"
                                            id="investment_in_progress"
                                            x-model="investmentForm.investment_in_progress" :value="true">
                                        <input type="hidden" name="investment_in_progress" :value="false">
                                    </template>
                                </div>
                            </div>
                        </template>




                        <!-- Canceled On Date (Visible only if "Canceled" is selected) -->
                        <template x-if="investmentForm.investment_status =='canceled'">
                            <div class="mb-3">
                                <label for="canceled_on" class="form-label">Canceled on*</label>
                                <input type="date" onclick="this.showPicker()" min="0" id="canceled_on"
                                    name="canceled_on" class="form-control" x-model="investmentForm.canceled_on"
                                    required>
                            </div>
                        </template>

                        <template x-if="investmentForm.investment_status =='inactive_bought_assign_sold'">
                            <div class="mb-3">
                                <label for="inactive_since" class="form-label">Inactive Since*</label>
                                <input type="date" onclick="this.showPicker()" min="0" id="inactive_since"
                                    name="inactive_since" class="form-control"
                                    x-model="investmentForm.inactive_since" required>
                            </div>
                        </template>



                        <h5>Sponsor</h5>
                        <div class="mb-3 mt-2">
                            <label for="primary_sponsor">Primary Sponsor*</label>
                            <select id="primary_sponsor" name="primary_sponsor" class="form-select"
                                x-model="investmentForm.primary_sponsor" required>
                                {{--  <option>select your Primary sponsor</option>  --}}
                                <option value="ZASM_INVESTMENT" selected>ZASM INVESTMENT</option>
                                {{-- <option value="s1">Sponsor 1</option> --}}

                            </select>
                        </div>

                        <template x-if="investmentForm.primary_sponsor == 's1'">

                            <div>
                                <h5 class="mb-3">Company member</h5>
                                <label for="primary_company_member">Primary Company member*</label>
                                <select id="primary_company_member" name="primary_company_member" class="form-select"
                                    x-model="investmentForm.primary_company_member" required>
                                    <option value="">Select member</option>
                                </select>
                            </div>
                        </template>


                        <div class="d-flex mt-3 justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save" @click="submitInvestmentForm(investmentForm)">
                                Save
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Investor Form Modal --}}
    <div class="deal-modal modal right fade" id="addInvestorModal" tabindex="-1"
        aria-labelledby="addInvestorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row bg-primary text-white" style="height:80px;">
                    <h5 class="modal-title col text-white">Add Investor</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Deal Form Body --}}
                    <div>
                        @csrf
                        {{--  first name  --}}
                        <div class="mb-3">
                            <label for="investor_fname" class="form-label">First name <span
                                    style="color: red;">*</span></label>
                            <input type="text" id="investor_fname" name="investor_fname" class="form-control"
                                x-model="investorForm.investor_fname">
                            <span x-show="investorErrors.investor_fname" x-text="investorErrors.investor_fname"
                                class="text-danger"></span>
                        </div>
                        {{--  last name  --}}
                        <div class="mb-3">
                            <label for="investor_lname" class="form-label">Last name <span
                                    style="color: red;">*</span></label>
                            <input type="text" id="investor_lname" name="investor_lname" class="form-control"
                                x-model="investorForm.investor_lname">
                            <span x-show="investorErrors.investor_lname" x-text="investorErrors.investor_lname"
                                class="text-danger"></span>
                        </div>
                        {{--  email  --}}
                        <div class="mb-3">
                            <label for="investor_email" class="form-label">Email <span
                                    style="color: red;">*</span></label>
                            <input type="email" id="investor_email" name="investor_email" class="form-control"
                                x-model="investorForm.investor_email">
                            <span x-show="investorErrors.investor_email" x-text="investorErrors.investor_email"
                                class="text-danger"></span>
                        </div>
                        {{--  phone number  --}}
                        <div class="mb-3">
                            <label for="investor_phone_number" class="form-label">Phone Number</label>
                            <input type="number" id="investor_phone_number" name="investor_phone_number"
                                class="form-control" x-model="investorForm.investor_phone_number">
                        </div>
                        {{--  notes  --}}
                        <div class="mb-3">
                            <label for="investor_note" class="form-label">Note</label>
                            <input type="text" id="investor_note" name="investor_note" class="form-control"
                                x-model="investorForm.investor_note">
                        </div>
                        {{--  Tags  --}}
                        <div class="mb3">
                            <label for="investor_tags" class="form-label">Investment Tags</label>
                            <select class="form-control investment-tags" multiple="multiple" name="investor_tags[]"
                                id="select_investor_tags">
                                @foreach ($investor_tags as $tag)
                                    <option value="{{ $tag }}">{{ $tag }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save" @click="submitInvestorForm(investorForm)">
                                Save
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Investor Profile Form Modal  --}}
    <div class="deal-modal modal right fade" id="addProfileModal" tabindex="-1"
        aria-labelledby="addProfileModalLabel" aria-hidden="true">
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
                            <label for="profile_type" class="form-label">Profile Type</label>
                            <select id="profile_type" name="profile_type" class="form-control"
                                x-model="profileForm.profile_type">
                                <option value="">Select Your Profile Type</option>
                                <option id="individual" value="individual">Individual</option>
                                <option id="custodian" value="custodian">Custodian IRA or Custodian based 401(k)
                                </option>
                                <option id="join_tenancy" value="join_tenancy">Joint Tenancy with Right of
                                    Survivorship</option>
                                <option id="lcps_property" value="lcps_property">LLC, Corp, Partnership, Solo 401(K),
                                    or checkbook IRA </option>
                            </select>
                        </div>
                        {{--  first name  --}}
                        <template
                            x-if="profileForm.profile_type == 'individual' || profileForm.profile_type == 'join_tenancy'">
                            <div class="mb-3">
                                <label for="profile_fname" class="form-label">First name <span
                                        style="color: red;">*</span></label>
                                <input type="text" id="profile_fname" name="profile_fname" class="form-control"
                                    x-model="profileForm.profile_fname">
                                <span x-show="profileErrors.profile_fname" x-text="profileErrors.profile_fname"
                                    class="text-danger"></span>
                            </div>
                        </template>
                        {{--  middle name  --}}
                        <template
                            x-if="profileForm.profile_type == 'individual' || profileForm.profile_type == 'join_tenancy'">
                            <div class="mb-3">
                                <label for="profile_mname" class="form-label ">Middle name</label>
                                <input type="text" id="profile_mname" name="profile_mname " class="form-control"
                                    x-model="profileForm.profile_mname">
                            </div>
                        </template>
                        {{--  last name  --}}
                        <template
                            x-if="profileForm.profile_type == 'individual' || profileForm.profile_type == 'join_tenancy'">
                            <div class="mb-3">
                                <label for="profile_lname" class="form-label">Last name <span
                                        style="color: red;">*</span></label>
                                <input type="text" id="profile_lname" name="profile_lname" class="form-control"
                                    x-model="profileForm.profile_lname">
                                <span x-show="profileErrors.profile_lname" x-text="profileErrors.profile_lname"
                                    class="text-danger"></span>
                            </div>
                        </template>
                        {{--  Legal IRA name  --}}
                        <template x-if="profileForm.profile_type == 'custodian'">
                            <div class="mb-3">
                                <label for="profile_ira_name" class="form-label">Legal IRA name <span
                                        style="color: red;">*</span></label>
                                <input type="text" id="profile_ira_name" name="profile_ira_name "
                                    class="form-control" x-model="profileForm.profile_ira_name">
                                <span x-show="profileErrors.profile_ira_name" x-text="profileErrors.profile_ira_name"
                                    class="text-danger"></span>
                            </div>
                        </template>
                        {{--  IRA company  --}}
                        <template x-if="profileForm.profile_type == 'custodian'">
                            <div class="mb-3">
                                <label for="profile_ira_company" class="form-label">IRA Company</label>
                                <select id="profile_ira_company" name="profile_ira_company " class="form-control"
                                    x-model="profileForm.profile_ira_company">
                                    <option id="advanta" value="advanta">Advanta</option>
                                    <option id="altoira" value="altoira">Alto IRA</option>
                                    <option id="cama_plan" value="cama_plan">Cama Plan IRA</option>
                                    <option id="community_national" value="community_national">Community National Bank
                                    </option>
                                    <option id="digital_trust" value="digital_trust">Digital Trust</option>
                                    <option id="direct_ira" value="direct_ira">Directed IRA (Directed Trust Company)
                                    </option>
                                    <option id="equity_trust" value="equity_trust">Equity Trust Company</option>
                                    <option id="forge_trust" value="forge_trust">Forge Trust Company</option>
                                    <option id="horizon_trust" value="horizon_trust">Horizon Trust Company</option>
                                    <option id="inspira" value="inspira">Inspira</option>
                                    <option id="ira_club" value="ira_club">IRA Club</option>
                                    <option id="irar_trust" value="irar_trust">IRAR Trust Company</option>
                                    <option id="madison_trust" value="madison_trust">Madison Trust Company</option>
                                    <option id="mainstar" value="mainstar">Mainstar Trust Company</option>
                                    <option id="mainstar_trust" value="mainstar_trust">Mainstar Trust Company</option>
                                    <option id="midland_trust" value="midland_trust">Midland Trust IRA</option>
                                    <option id="millennium_trust" value="millennium_trust">Millennium Trust Company
                                    </option>
                                    <option id="nuview" value="nuview">NuView Trust Company</option>
                                    <option id="pacific_trust" value="pacific_trust">Pacific Premier Trust</option>
                                    <option id="provident_trust" value="provident_trust">Provident Trust Company
                                    </option>
                                    <option id="quest_trust" value="quest_trust">Quest Trust Company</option>
                                    <option id="specialized_trust" value="specialized_trust">Specialized Trust Company
                                    </option>
                                    <option id="entrust_group" value="entrust_group">The Entrust Group</option>
                                    <option id="vantage_ira" value="vantage_ira">Vantage IRA</option>
                                    <option id="woodtrust_bank" value="woodtrust_bank">WoodTrust Bank IRA</option>
                                    <option id="other" value="other">Other</option>
                                </select>
                        </template>
                        {{--  company name  --}}
                        <template
                            x-if="profileForm.profile_type == 'custodian' && profileForm.profile_ira_company == 'other'">
                            <div class="mb-3">
                                <label for="profile_company_name" class="form-label">Company Name</label>
                                <input type="text" id="profile_company_name" name="profile_company_name "
                                    class="form-control" x-model="profileForm.profile_company_name">
                            </div>
                        </template>
                        {{--  IRA account number  --}}
                        <template x-if="profileForm.profile_type == 'custodian'">
                            <div class="mb-3">
                                <label for="profile_ira_account_number" class="form-label">IRA Account Number</label>
                                <input type="text" id="profile_ira_account_number"
                                    name="profile_ira_account_number " class="form-control" placeholder="1234567"
                                    x-model="profileForm.profile_ira_account_number">
                            </div>
                        </template>
                        {{--  email  --}}
                        <template x-if="profileForm.profile_type == 'join_tenancy'">
                            <div class="mb-3">
                                <label for="investor_email" class="form-label">Email <span
                                        style="color: red;">*</span></label>
                                <input type="email" id="investor_email" name="investor_email" class="form-control"
                                    x-model="profileForm.investor_email">
                                <span x-show="profileErrors.profile_email" x-text="profileErrors.profile_email"
                                    class="text-danger"></span>
                            </div>
                        </template>
                        {{--  first name2  --}}
                        <template x-if="profileForm.profile_type == 'join_tenancy'">
                            <div class="mb-3">
                                <label for="profile_fname2" class="form-label">First name 2 <span
                                        style="color: red;">*</span></label>
                                <input type="text" id="profile_fname2" name="profile_fname2" class="form-control"
                                    x-model="profileForm.profile_fname2">
                                <span x-show="profileErrors.profile_fname2" x-text="profileErrors.profile_fname2"
                                    class="text-danger"></span>
                            </div>
                        </template>
                        {{--  middle name2  --}}
                        <template x-if="profileForm.profile_type == 'join_tenancy'">
                            <div class="mb-3">
                                <label for="profile_mname2" class="form-label">Middle name 2 </label>
                                <input type="text" id="profile_mname2" name="profile_mname2 "
                                    class="form-control" x-model="profileForm.profile_mname2">
                            </div>
                        </template>
                        {{--  Last name 2 --}}
                        <template x-if="profileForm.profile_type == 'join_tenancy'">
                            <div class="mb-3">
                                <label for="profile_lname2" class="form-label">Last name 2 <span
                                        style="color: red;">*</span></label>
                                <input type="text" id="profile_lname2" name="profile_lname2" class="form-control"
                                    x-model="profileForm.profile_lname2">
                                <span x-show="profileErrors.profile_lname2" x-text="profileErrors.profile_lname2"
                                    class="text-danger"></span>
                            </div>
                        </template>
                        {{--  email2  --}}
                        <template x-if="profileForm.profile_type == 'join_tenancy'">
                            <div class="mb-3">
                                <label for="profile_email2" class="form-label">Email 2 <span
                                        style="color: red;">*</span></label>
                                <input type="email" id="profile_email2" name="profile_email2" class="form-control"
                                    x-model="profileForm.profile_email2">
                                <span x-show="profileErrors.profile_email2" x-text="profileErrors.profile_email2"
                                    class="text-danger"></span>
                            </div>
                        </template>
                        {{--  Entity Name  --}}
                        <template x-if="profileForm.profile_type == 'lcps_property'">
                            <div class="mb-3">
                                <label for="profile_entity_name" class="form-label">Entity Name</label>
                                <input type="text" id="profile_entity_name" name="profile_entity_name"
                                    class="form-control " x-model="profileForm.profile_entity_name">
                            </div>
                        </template>
                        {{--  Number of members  --}}
                        <template x-if="profileForm.profile_type == 'lcps_property'">
                            <div class="mb-3">
                                <label for="profile_number_of_members" class="form-label">Number of members</label>
                                <input type="number" id="profile_number_of_members" name="profile_number_of_members"
                                    class="form-control " x-model="profileForm.profile_number_of_members">
                            </div>
                        </template>
                        {{--  distribution method  --}}
                        <div class="mb-3">
                            <label for="Profile_distribution" class="form-label">Distribution method</label>
                            <select id="Profile_distribution" name="Profile_distribution" class="form-control"
                                x-model="profileForm.Profile_distribution">
                                <option id="ach" value="ach">ACH (Recommended)</option>
                                <option id="check" value="check">Check</option>
                                <option id="other" value="other">Other</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2"
                                data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn-primary deal-save" @click="submitProfileForm(profileForm)">
                                Save
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal right fade" id="addInvestorTagModal" tabindex="-1" aria-labelledby="addInvestorTagModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addInvestorTagModalLabel">Add Investor Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label for="investor_tag" class="form-label">Enter a tag</label>
                        <input type="text" id="investor_tag" name="investor_tag" class="form-control"
                            placeholder="Enter a note..." x-model="investortagForm.investor_tag">
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer justify-content-between bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Tag
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('script')
    <script>
        function alpineHelpers() {
            return {
                moneyFormat(el) {
                    let value = el.value;
                    // Remove non-numeric characters except for the decimal point
                    value = value.replace(/[^\d.]/g, '');

                    // Remove leading zeros
                    value = value.replace(/^0+(?=\d)/, '');
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
                percentFormat(el) {
                    let value = el.value;
                    // Remove non-numeric characters except for the decimal point
                    value = value.replace(/[^\d.]/g, '');

                    // Remove leading zeros
                    value = value.replace(/^0+(?=\d)/, '');
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
                    // Ensure that the value ends with % and no other non-numeric characters
                    if (value !== '') {
                        el.value = value + '%';
                    } else {
                        el.value = '0%';
                    }
                    el.setSelectionRange(el.value.length - 1, el.value.length - 1);
                }
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Array of nested modal IDs
            const nestedModals = ['#addInvestorModal', '#addProfileModal'];

            // Function to reopen the main modal
            function reopenMainModal() {
                const mainModal = bootstrap.Modal.getOrCreateInstance(document.querySelector(
                    '#addInvestmentModal'));
                mainModal.show();
            }

            // Attach 'hidden.bs.modal' event listener to each nested modal
            nestedModals.forEach(modalID => {
                const nestedModal = document.querySelector(modalID);
                if (nestedModal) {
                    nestedModal.addEventListener('hidden.bs.modal', reopenMainModal);
                }
            });
        });

        function investmentFormHandler() {
            return {
                ...alpineHelpers(),
                investmentForm: {
                    _token: csrf,
                    deal_id: "{{ $deal->id }}",
                    investor_id: '',
                    investor_profile_id: '',
                    deal_class_id: '',
                    offering_id: '',
                    investment_amount: '0',
                    pcb_ownership: '',
                    op_ownership: '',
                    pcb_distribution: '',
                    op_distribution: '',
                    investment_tags: '',
                    date_placed: '',
                    contribution_method: 'wire_transfer',
                    investment_status: 'investment_started',
                    investment_in_progress: false,
                    canceled_on: '',
                    inactive_since: '',
                    primary_sponsor: 'ZASM_INVESTMENT',
                    primary_company_member: '',

                },
                investmentErrors: {},
                investorForm: {
                    _token: csrf,
                    investor_fname: '',
                    investor_lname: '',
                    investor_email: '',
                    investor_phone_number: '',
                    investor_note: '',
                    investor_tags: '',
                },
                investorErrors: {},
                profileForm: {
                    _token: csrf,
                    profile_type: 'individual',
                    profile_fname: '',
                    profile_mname: '',
                    profile_lname: '',
                    profile_ira_name: '',
                    profile_ira_company: '',
                    profile_company_name: '',
                    profile_ira_account_number: '',
                    profile_email: '',
                    profile_fname2: '',
                    profile_mname2: '',
                    profile_lname2: '',
                    profile_email2: '',
                    profile_entity_name: '',
                    profile_number_of_members: '',
                    Profile_distribution: '',
                },
                profileErrors: {},
                filteredProfiles: [],
                loading: false,
                updateProfiles() {
                    this.filteredProfiles = this.profiles.filter(profile => profile.investor_id === this.investmentForm
                        .investor_id);
                },
                investortagForm: {
                    _token: csrf,
                    investor_tag: '',
                },

                investments: "{{ $deal->investments }}",
                investors: @json($investors),
                profiles: [],
                enableProfile: false,
                enableAddProfile: false,
                deal_classes: @json($deal->classes),
                buckets: @json($deal->buckets),
                all_classes: [],
                selectedclass: {},
                handleAmountChange() {
                    console.log("Investment Amount Changed:", this.investmentForm.investment_amount);
                    // Add your additional logic here (e.g., validation, calculations, etc.)
                },
                handleClassChange(selectedValue) {
                    console.log('Selected Class ID:', selectedValue);
                    const filteredObjects = this.all_classes.filter(obj => obj.id === Number(selectedValue));
                    // console.log(this.$data)
                    console.log(filteredObjects[0]);
                    this.selectedclass = filteredObjects[0];
                    this.investmentForm.investment_amount = 0;
                },
                init() {
                    this.deal_classes.forEach((classItem) => {
                        this.all_classes.push(classItem);
                    });
                    this.buckets.forEach((bucket) => {
                        bucket.classes.forEach((bclass) => {
                            this.all_classes.push(bclass);
                        });
                    });
                    this.$watch('investmentForm.investment_amount', (newValue, oldValue) => {
                        let investment_amount = newValue.replace(/[^0-9.]/g, '');
                        this.investmentForm.pcb_ownership = (Number(investment_amount) * Number(
                            this.selectedclass.entity_legal_ownership.replace('%', ''))) / 100;
                        this.investmentForm.op_ownership = Number(this.selectedclass.entity_legal_ownership.replace(
                            '%', ''))
                        this.investmentForm.pcb_distribution = (Number(investment_amount) *
                            Number(this.selectedclass.distribution_share.replace('%', ''))) / 100;
                        this.investmentForm.op_distribution = Number(this.selectedclass.distribution_share.replace(
                            '%', ''))
                    });
                },
                //submit investment
                async submitInvestmentForm(data) {
                    debugger;
                    this.loading = true;
                    let url = "{{ route($prefix . '.investments.store', $deal->id) }}";
                    try {

                        let formData = new FormData();
                        for (const key in data) {
                            if (data.hasOwnProperty(key)) {
                                formData.append(key, data[key]);
                            }
                        }

                        let tags = $('#select_investment_tags').val();

                        formData.append('investment_tags', tags);

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
                            this.investmentErrors = responseData.error;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            const modalElement = document.querySelector('.modal.show');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            modalInstance.hide();
                            this.resetInvestmentForm()
                            cosyAlert('<strong>Success</strong><br />Investment created Successfully!', 'success');
                            // Reload the page
                            // window.location.reload();

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                    this.loading = false;
                },
                //submit investor
                async submitInvestorForm(data) {
                    this.loading = true;
                    let url = "{{ route($prefix . '.investors.store', $deal->id) }}";

                    try {

                        let formData = new FormData();
                        for (const key in data) {
                            if (data.hasOwnProperty(key)) {
                                formData.append(key, data[key]);
                            }
                        }

                        let tags = $('#select_investor_tags').val();

                        formData.append('investor_tags', tags);


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
                            this.investorErrors = responseData.error;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            this.investors = responseData.investors;
                            const modalElement = document.querySelector('.modal.show');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            modalInstance.hide();
                            this.resetInvestorForm()
                            cosyAlert('<strong>Success</strong><br />Investor created Successfully!', 'success');

                            // Reload the page
                            // window.location.reload();

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                    this.loading = false;
                },
                //submit profile
                async submitProfileForm(data) {
                    this.loading = true;
                    let url = "{{ route($prefix . '.investors.profiles.store', $deal->id) }}";

                    if (this.investmentForm.investor_id === '') {
                        // TODO: Add toast message
                        alert('Please select an investor');
                        return;
                    }

                    try {

                        let formData = new FormData();
                        formData.append('investor_id', this.investmentForm.investor_id);
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
                            this.profileErrors = responseData.error;
                            return;
                        }

                        const responseData = await response.json();
                        if (response.status === 200) {
                            this.profiles = responseData.profiles;
                            const modalElement = document.querySelector('.modal.show');
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            modalInstance.hide();
                            this.resetProfileForm()
                            cosyAlert('<strong>Success</strong><br />Profile created Successfully!', 'success');

                            // Reload the page
                            //  window.location.reload();

                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                    }
                    this.loading = false;
                },
                changeInvestor() {
                    if (this.investmentForm.investor_id === 'add_investor') {
                        $('#addInvestorModal').modal('show');
                        this.investmentForm.investor_id = '';
                        return;
                    }
                    if (this.investmentForm.investor_id === '') {
                        this.enableProfile = false;
                        this.enableAddProfile = false;
                        this.investmentForm.investor_profile_id = '';
                        return;
                    }
                    // Valid investor selected: enable profile dropdown
                    this.enableProfile = true;
                    this.enableAddProfile = true;
                    this.investmentForm.investor_profile_id = '';
                    // Filter profiles based on selected investor
                    const selectedInvestor = this.investors.find(
                        investor => investor.id === Number(this.investmentForm.investor_id)
                    );
                    this.profiles = selectedInvestor ? selectedInvestor.investor_profiles : [];
                },

                changeProfile() {
                    if (this.investmentForm.investor_profile_id === 'add_profile') {
                        $('#addProfileModal').modal('show');
                        this.investmentForm.investor_profile_id = '';
                        return;
                    }
                    // If profile is cleared, do not alter the investor id:
                    if (this.investmentForm.investor_profile_id === '') {
                        return;
                    }
                    // Optionally, you can verify that the selected profile belongs to the investor.
                    // For example:
                    const investorHasProfile = this.profiles.some(
                        profile => profile.id === Number(this.investmentForm.investor_profile_id)
                    );
                    if (!investorHasProfile) {
                        // Optionally alert the user or handle the mismatch here.
                    }
                },

                resetInvestmentForm() {
                    this.investmentForm = {
                        _token: csrf,
                        deal_id: "{{ $deal->id }}",
                        investor_id: '',
                        investor_profile_id: '',
                        deal_class_id: '',
                        offering_id: '',
                        investment_amount: '0',
                        pcb_ownership: '',
                        op_ownership: '',
                        pcb_distribution: '',
                        op_distribution: '',
                        investment_tags: '',
                        date_placed: '',
                        contribution_method: '',
                        investment_status: '',
                        investment_in_progress: '',
                        canceled_on: '',
                        inactive_since: '',
                        primary_sponsor: 'ZASM_INVESTMENT',
                        primary_company_member: '',
                    };
                },
                resetInvestorForm() {
                    this.investorForm = {
                        _token: csrf,
                        investor_fname: '',
                        investor_lname: '',
                        investor_email: '',
                        investor_phone_number: '',
                        investor_note: '',
                        investor_tags: '',
                    };
                },
                resetProfileForm() {
                    this.profileForm = {
                        _token: csrf,
                        profile_type: 'individual',
                        profile_fname: '',
                        profile_mname: '',
                        profile_lname: '',
                        profile_ira_name: '',
                        profile_ira_company: '',
                        profile_company_name: '',
                        profile_ira_account_number: '',
                        profile_email: '',
                        profile_fname2: '',
                        profile_mname2: '',
                        profile_lname2: '',
                        profile_email2: '',
                        profile_entity_name: '',
                        profile_number_of_members: '',
                        Profile_distribution: '',
                    };
                },
                changeInvestorTags() {
                    if (this.investorForm.investor_tags == 'add') {
                        $('#addInvestorTagModal').modal('show');
                        this.investorForm.investor_tags = '';
                    }
                },

            };
        }
    </script>


    <script>
        function tagInput() {
            return {
                tags: [],
                newTag: '',
                addTag() {
                    if (this.newTag.trim() !== '' && !this.tags.includes(this.newTag.trim())) {
                        this.tags.push(this.newTag.trim());
                    }
                    this.newTag = '';
                },
                removeTag(index) {
                    this.tags.splice(index, 1);
                }
            };
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#investment_tags').select2({
                placeholder: 'Search',
                allowClear: true,
                tags: true,
                tokenSeparators: [',', ' ']
            });


        });
        $(document).ready(function() {
            $('#investor_tags').select2({
                placeholder: 'Search',
                allowClear: true,
                tags: true,
                tokenSeparators: [',', ' ']
            });


        });
    </script>
@endpush
