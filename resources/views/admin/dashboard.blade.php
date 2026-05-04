@extends('admin.layouts.app')

@section('panel')
<div x-data="dashboard()" x-cloak>
    <div class="container">
        <div class="row">
            <div class="col">
                <!-- Other content here -->
            </div>
            <div class="col d-flex text-right mb-5 justify-content-end">
                
            </div>
        </div>
    </div>
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.all') }}" icon="las la-users" title="Total Users"
                value="{{ $widget['total_users'] }}" bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.active') }}" icon="las la-user-check" title="Active Users"
                value="{{ $widget['verified_users'] }}" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.email.unverified') }}" icon="lar la-envelope"
                title="Email Unverified Users" value="{{ $widget['email_unverified_users'] }}" bg="danger" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.mobile.unverified') }}" icon="las la-comment-slash"
                title="Mobile Unverified Users" value="{{ $widget['mobile_unverified_users'] }}" bg="warning" />
        </div>
    </div>

    <div class="row mt-2 gy-4">
        <div class="col-xxl-6">
            <div class="card box-shadow3 h-100">
                <div class="card-body">
                    <h5 class="card-title">@lang('Deposits')</h5>
                    <div class="widget-card-wrapper">
                        <div class="widget-card bg--success">
                            <a href="{{ route('admin.deposit.list') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ showAmount($deposit['total_deposit_amount']) }}</h6>
                                    <p class="widget-card-title">@lang('Total Deposited')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--warning">
                            <a href="{{ route('admin.deposit.pending') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-spinner"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $deposit['total_deposit_pending'] }}</h6>
                                    <p class="widget-card-title">@lang('Pending Deposits')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--danger">
                            <a href="{{ route('admin.deposit.rejected') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-ban"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $deposit['total_deposit_rejected'] }}</h6>
                                    <p class="widget-card-title">@lang('Rejected Deposits')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--primary">
                            <a href="{{ route('admin.deposit.list') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ showAmount($deposit['total_deposit_charge']) }}</h6>
                                    <p class="widget-card-title">@lang('Deposited Charge')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6">
            <div class="card box-shadow3 h-100">
                <div class="card-body">
                    <h5 class="card-title">@lang('Withdrawals')</h5>
                    <div class="widget-card-wrapper">
                        <div class="widget-card bg--success">
                            <a href="{{ route('admin.withdraw.data.all') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="lar la-credit-card"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ showAmount($withdrawals['total_withdraw_amount']) }}
                                    </h6>
                                    <p class="widget-card-title">@lang('Total Withdrawn')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--warning">
                            <a href="{{ route('admin.withdraw.data.pending') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="fas fa-spinner"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $withdrawals['total_withdraw_pending'] }}</h6>
                                    <p class="widget-card-title">@lang('Pending Withdrawals')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--danger">
                            <a href="{{ route('admin.withdraw.data.rejected') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="las la-times-circle"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ $withdrawals['total_withdraw_rejected'] }}</h6>
                                    <p class="widget-card-title">@lang('Rejected Withdrawals')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                        <div class="widget-card bg--primary">
                            <a href="{{ route('admin.withdraw.data.all') }}" class="widget-card-link"></a>
                            <div class="widget-card-left">
                                <div class="widget-card-icon">
                                    <i class="las la-percent"></i>
                                </div>
                                <div class="widget-card-content">
                                    <h6 class="widget-card-amount">{{ showAmount($withdrawals['total_withdraw_charge']) }}
                                    </h6>
                                    <p class="widget-card-title">@lang('Withdrawal Charge')</p>
                                </div>
                            </div>
                            <span class="widget-card-arrow">
                                <i class="las la-angle-right"></i>
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.manage.property.index') }}" icon="fas fa-industry"
                title="Total Property" value="{{ $property['total_property'] }}" bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.manage.property.index') }}" icon="fas fa-building"
                title="Active Property" value="{{ $property['active_property'] }}" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.manage.property.invested') }}" icon="fas fa-archway"
                title="Investment Running Property" value="{{ $property['total_running_property'] }}" bg="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.manage.property.invested') }}" icon="fas fa-dungeon"
                title="Investment Completed Property" value="{{ $property['total_completed_property'] }}"
                bg="7" />
        </div>
    </div>
    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" outline="true" link="{{ route('admin.invest.all') }}" icon="las la-chart-bar"
                title="Total Invested Property" value="{{ $investStatistics['total_invest'] }}" bg="primary" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" outline="true" link="{{ route('admin.invest.all') }}" icon="las la-wallet"
                title="Total Invested Amount" value="{{ showAmount($investStatistics['total_invest_amount']) }}"
                bg="info" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" outline="true" link="{{ route('admin.invest.all') }}" icon="las la-chart-pie"
                title="Due Amount" value="{{ showAmount($investStatistics['total_due_amount']) }}" bg="danger" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" outline="true" link="{{ route('admin.invest.profit') }}" icon="las la-chart-line"
                title="Users Total Profit" value="{{ showAmount($investStatistics['total_profit']) }}" bg="success" />
        </div>
    </div>

    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Deposit & Withdraw Report')</h5>

                        <div id="dwDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>
                    <div id="dwChartArea"> </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between">
                        <h5 class="card-title">@lang('Transactions Report')</h5>

                        <div id="trxDatePicker" class="border p-1 cursor-pointer rounded">
                            <i class="la la-calendar"></i>&nbsp;
                            <span></span> <i class="la la-caret-down"></i>
                        </div>
                    </div>

                    <div id="transactionChartArea"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-none-30 mt-5">
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Browser') (@lang('Last 30 days'))</h5>
                    <canvas id="userBrowserChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By OS') (@lang('Last 30 days'))</h5>
                    <canvas id="userOsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Country') (@lang('Last 30 days'))</h5>
                    <canvas id="userCountryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @include('admin.partials.cron_modal')
        <!-- Custom CSS for Sidebar -->
    <style>
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

        .step-progress {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .step-progress .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step-progress .step::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e9ecef;
            z-index: 1;
        }

        .step-progress .step.active::before {
            background-color: #007bff;
        }

        .step-progress .step .step-number {
            position: relative;
            z-index: 2;
            background-color: #fff;
            border: 2px solid #e9ecef;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            line-height: 26px;
            display: inline-block;
        }

        .step-progress .step.active .step-number {
            border-color: #007bff;
            color: #007bff;
        }
    </style>

    <style>
        .file-uploader .drop-zone {
            border: 2px dashed #007bff;
            padding: 20px;
            text-align: center;
            cursor: pointer;
        }
        .file-uploader .drop-zone.drag-over {
            background-color: #e9ecef;
        }
        .file-uploader .file-list .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }
    </style>
    
    <!-- Modal Structure -->
    <div class="deal-modal modal right fade" id="addDealModal" tabindex="-1" aria-labelledby="addDealModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content px-2">
                <div class="modal-header row">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="stepper-wrapper">
                        <div class="stepper-item" :class="{ 'active': currentStep === 'deal', 'completed': completedSteps.includes('deal') }">
                          <div class="step-counter">1</div>
                          <div class="step-name">Deal</div>
                        </div>
                        <div class="stepper-item" :class="{ 'active': currentStep === 'assets', 'completed': completedSteps.includes('assets') }">
                          <div class="step-counter">2</div>
                          <div class="step-name">Assets</div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    {{-- Deal Form Body --}}
                    <div x-show="currentStep === 'deal'">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Deal Name*</label>
                            <input type="text" id="name" name="name" class="form-control" x-model="dealForm.name" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Deal Type</label>
                            <select id="type" name="type" class="form-select" x-model="dealForm.type">
                                <option value="">Add Deal Type</option>
                                <option value="Direct syndication">Direct syndication (most common)</option>
                                <option value="Fund syndication">Fund syndication</option>
                                <option value="Customized fund of funds">Customized fund of funds</option>
                                <option value="SPV">SPV Fund</option>
                                <option value="SPV">Flex fund</option>
                                <option value="Angel Investment">Angel Investment</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Deal Stage*</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="raising" name="deal_stage" value="Raising capital" x-model="dealForm.deal_stage">
                                    <label class="form-check-label" for="raising">Raising capital</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="asset" name="deal_stage" value="Asset managing" x-model="dealForm.deal_stage">
                                    <label class="form-check-label" for="asset">Asset managing</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="liquidated" name="deal_stage" value="Liquidated" x-model="dealForm.deal_stage">
                                    <label class="form-check-label" for="liquidated">Liquidated</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="sec_type" class="form-label">SEC Type*</label>
                            {{-- <input type="text" id="sec_type" name="sec_type" class="form-control" required> --}}
                            <select id="sec_type" name="sec_type" class="form-select"
                                x-model="dealForm.sec_type" required>
                            >
                                <option value="">Add SEC Type</option>
                                <option value="506(b)">506(b)</option>
                                <option value="506(c)">506(c)</option>
                                <option value="Intra-state">Intra-state</option>
                                <option value="Joint Venture">Joint Venture</option>
                                <option value="Lending">Lending</option>
                                <option value="Reg A+">Reg A+</option>
                                <option value="Reg CF">Reg CF</option>
                                <option value="Reg D">Reg D</option>
                                <option value="Reg S">Reg S</option>
                                <option value="Regulation A">Regulation A</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="close_date" class="form-label">Close Date</label>
                            <input type="date" onclick="this.showPicker()"  id="close_date" name="close_date" class="form-control" x-model="dealForm.close_date">
                        </div>
                        <div class="mb-3">
                            <label for="owning_entity_name" class="form-label">Owning Entity Name*</label>
                            <input type="text" id="owning_entity_name" name="owning_entity_name" class="form-control" x-model="dealForm.owning_entity_name" required>
                        </div>
                        <div class="mb-3">
                            <label>Funds must be received before GP countersigns</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="funds_yes" name="funds_received_before_gp_countersigns" value="1" x-model="dealForm.funds_received_before_gp_countersigns" :disabled="dealForm.send_funding_instructions_after_gp_countersigns == '1'">
                                    <label class="form-check-label" for="funds_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="funds_no" name="funds_received_before_gp_countersigns" value="0" x-model="dealForm.funds_received_before_gp_countersigns" :disabled="dealForm.send_funding_instructions_after_gp_countersigns == '1'">
                                    <label class="form-check-label" for="funds_no">No (most common)</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Automatically send funding instructions after GP countersigns</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="instructions_yes" name="send_funding_instructions_after_gp_countersigns" value="1" x-model="dealForm.send_funding_instructions_after_gp_countersigns" :disabled="dealForm.funds_received_before_gp_countersigns == '1'">
                                    <label class="form-check-label" for="instructions_yes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="instructions_no" name="send_funding_instructions_after_gp_countersigns" value="0" x-model="dealForm.send_funding_instructions_after_gp_countersigns" :disabled="dealForm.funds_received_before_gp_countersigns == '1'">
                                    <label class="form-check-label" for="instructions_no">No (most common)</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <span class="btn btn--base deal-save"
                                @click="submitDealForm(dealForm)">Next</span>
                        </div>
                    </div>
                    {{-- Assets Form Body --}}
                    <div x-show="currentStep === 'assets'">
                        <div x-show="!assetList">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Property Name*</label>
                                <input type="text" id="name" name="name" class="form-control" x-model="assetForm.name" required>
                            </div>
                            {{-- Address --}}
                            <div class="mb-3">
                                <label for="address" class="form-label">Address*</label>
                                <input type="text" id="address" name="address" class="form-control" x-model="assetForm.address" required>
                            </div>
                            <div class="row">
                                {{-- City --}}
                                <div class="mb-3 col-6">
                                    <label for="city" class="form-label">City*</label>
                                    <input type="text" id="city" name="city" class="form-control" x-model="assetForm.city" required>
                                </div>
                                {{-- State --}}
                                <div class="mb-3 col-6">
                                    <label for="state" class="form-label">State*</label>
                                    <input type="text" id="state" name="state" class="form-control" x-model="assetForm.state" required>
                                </div>
                            </div>
                            <div class="row">
                                {{-- Zip --}}
                                <div class="mb-3 col-6">
                                    <label for="zip" class="form-label">Zip*</label>
                                    <input type="text" id="zip" name="zip" class="form-control" x-model="assetForm.zip" required>
                                </div>
                                {{-- Country --}}
                                <div class="mb-3 col-6">
                                    <label for="country" class="form-label">Country*</label>
                                    <input type="text" id="country" name="country" class="form-control" x-model="assetForm.country" required>
                                </div>
                            </div>
                            {{-- Property Type --}}
                            <div class="mb-3">
                                <label for="property_type" class="form-label mb-2">Property Type*</label>
                                <select id="property_type" name="property_type" class="form-select" x-model="assetForm.property_type" required>
                                    <option value="">Add Property Type</option>
                                    <option value="Single Family">Single Family</option>
                                    <option value="Multi Family">Multi Family</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Hedge Fund">Hedge Fund</option>
                                    <option value="Hospitality">Hospitality</option>
                                    <option value="Industrial">Industrial</option>
                                    <option value="Land">Land</option>
                                    <option value="Logistics">Logistics</option>
                                    <option value="Manufacturing">Manufacturing</option>
                                    <option value="Mobile Home Park">Mobile Home Park</option>
                                    <option value="Multifamily">Multifamily</option>
                                    <option value="Office">Office</option>
                                    <option value="Oil & Gas">Oil & Gas</option>
                                    <option value="Private credit">Private credit</option>
                                    <option value="Retail">Retail</option>
                                </select>
                            </div>
                            {{-- Property Class --}}
                            <div class="mb-3">
                                <label for="property_class" class="form-label mb-2">Property Class*</label>
                                <select id="property_class" name="property_class" class="form-select" x-model="assetForm.property_class" required>
                                    <option value="">Add Property Class</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                            {{-- Number of Units --}}
                            <div class="mb-3">
                                <label for="number_of_units" class="form-label mb-2">Number of Units*</label>
                                <input type="number" id="number_of_units" name="number_of_units" class="form-control" x-model="assetForm.number_of_units" required>
                            </div>
                            {{-- Type of Units --}}
                            <div class="mb-3">
                                <label for="type_of_units" class="form-label mb-2">Type of Units*</label>
                                <select id="type_of_units" name="type_of_units" class="form-select" x-model="assetForm.type_of_units" required>
                                    <option value="Beds">Beds</option>
                                    <option value="Rooms">Rooms</option>
                                    <option value="Parking Spaces">Parking Spaces</option>
                                    <option value="Square Feet">Square Feet</option>
                                    <option value="Square Meters">Square Meters</option>
                                    <option value="Acres">Acres</option>
                                    <option value="Pads">Pads</option>
                                    <option value="Wells">Wells</option>
                                    <option value="Properties">Properties</option>
                                </select>
                            </div>
                            <div class="row">
                                {{-- Acquisition Date --}}
                                <div class="mb-3 col-6">
                                    <label for="acquisition_date" class="form-label mb-2">Acquisition Date*</label>
                                    <input type="date" onclick="this.showPicker()"  id="acquisition_date" name="acquisition_date" class="form-control" x-model="assetForm.acquisition_date" required>
                                </div>
                                {{-- Acquisition Price --}}
                                <div class="mb-3 col-6">
                                    <label for="acquisition_price" class="form-label mb-2">Acquisition Price* ($)</label>
                                    <input type="text" x-mask:dynamic="$money($input)" id="acquisition_price" name="acquisition_price" class="form-control" x-model="assetForm.acquisition_price"  required>
                                </div>
                            </div>
                            <div class="row">
                                {{-- Exit Date --}}
                                <div class="mb-3 col-6">
                                    <label for="exit_date" class="form-label mb-2">Exit Date*</label>
                                    <input type="date" onclick="this.showPicker()"  id="exit_date" name="exit_date" class="form-control" x-model="assetForm.exit_date" required>
                                </div>
                                {{-- Exit Price --}}
                                <div class="mb-3 col-6">
                                    <label for="exit_price" class="form-label mb-2">Exit Price* ($)</label>
                                    <input type="text" x-mask:dynamic="$money($input)" id="exit_price" name="exit_price" class="form-control" x-model="assetForm.exit_price" required>
                                </div>
                            </div>
                            {{-- Year Built --}}
                            <div class="mb-3">
                                <label for="year_built" class="form-label mb-2">Year Built*</label>
                                <input type="number" id="year_built" name="year_built" class="form-control" x-model="assetForm.year_built" required>
                            </div>

                            {{-- Upload Images --}}
                            <div class="mb-3">
                                <label for="property_images" class="form-label">Property Images</label>
                                <div class="file-uploader">
                                    <input type="file" id="property_images" name="property_images[]" class="form-control" multiple @change="handleFiles($event)" hidden>
                                    <div class="drop-zone" @drop.prevent="handleDrop($event)" @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" :class="{ 'drag-over': dragOver }">
                                        <p class="drop-zone-text">Drag & drop files here or click to upload</p>
                                        <button type="button" class="btn btn-primary" @click="document.getElementById('property_images').click()">Select Files</button>
                                    </div>
                                    <div class="file-list mt-3">
                                        <template x-for="file in files" :key="file.name">
                                            <div class="file-item">
                                                <span x-text="file.name"></span>
                                                <button type="button" class="btn btn-danger btn-sm" @click="removeFile(file)">Remove</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                <span class="btn btn--base deal-save"
                                    @click="submitAssetForm(assetForm)">Save</span>
                            </div>
                        </div>
                        <div x-show="assetList">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fs-2">Assets</span>
                                    <button type="button" class="btn btn--base" @click="assetList = false">
                                        <i class="fas fa-plus"></i> Add Asset
                                    </button>
                                </div>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>City</th>
                                            <th>State</th>
                                            <th>Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="asset in assetsAdded" :key="asset.id">
                                            <tr>
                                                <td x-text="asset.name"></td>
                                                <td x-text="asset.address"></td>
                                                <td x-text="asset.city"></td>
                                                <td x-text="asset.state"></td>
                                                <td>
                                                    <button type="button" class="btn" @click="assetEdit(asset)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn" @click="assetDelete(asset)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn--base" @click="resetDealandAsset()" >Done</button>
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

@push('breadcrumb-plugins')
    <!-- Add Deal Button -->
    <button type="button" class="btn btn-outline--primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDealModal">+ Add Deal</button>
    <button class="btn btn-outline--primary btn-sm" data-bs-toggle="modal" data-bs-target="#cronModal">
        <i class="las la-server"></i>@lang('Cron Setup')
    </button>
@endpush


@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush
@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/stepper.css') }}">
@endpush
@push('script')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        var csrf = '{{ csrf_token() }}';
        function dashboard() {
            return {
                currentStep: 'deal',
                completedSteps: [],
                errors: {},
                loading: false,
                dealForm: {
                    _token : csrf,
                    name: '',
                    type: '',
                    deal_stage: '',
                    sec_type: '',
                    close_date: '',
                    owning_entity_name: '',
                    funds_received_before_gp_countersigns: '0',
                    send_funding_instructions_after_gp_countersigns: '0',
                },
                assetForm: {
                    _token : csrf,
                    name: '',
                    address: '',
                    city: '',
                    state: '',
                    zip: '',
                    country: '',
                    property_type: '',
                    property_class: '',
                    number_of_units: '',
                    type_of_units: '',
                    acquisition_date: '',
                    acquisition_price: '',
                    exit_date: '',
                    exit_price: '',
                    year_built: '',
                    deal_id: '',
                },
                assetsAdded:[],
                assetErrors: {},
                assetList: false,  
                files: [],
                dragOver: false,
                handleFiles(event) {
                    const selectedFiles = Array.from(event.target.files);
                    this.files.push(...selectedFiles);
                },
                handleDrop(event) {
                    const droppedFiles = Array.from(event.dataTransfer.files);
                    this.files.push(...droppedFiles);
                    this.dragOver = false;
                },
                removeFile(file) {
                    this.files = this.files.filter(f => f !== file);
                },
                async submitDealForm(data) {
                    this.loading = true;
                    let url = '{{ route('admin.deals.store') }}';

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        this.loading = false;

                        if(response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.errors = responseData.errors;
                            return;
                        }
                
                        const responseData = await response.json();
                        if(response.status === 200) {
                            this.currentStep = 'assets';
                            this.completedSteps.push('deal');
                            this.assetForm.deal_id = responseData.deal.id;
                        } else {
                            // alert(responseData.message);
                            console.log(responseData);
                        }
                        
                    } catch(error) {
                        console.error('Error:', error);
                    }
                },

                async submitAssetForm(data) {
                    this.loading = true;
                    let url = '{{ route('admin.assets.store') }}';

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        this.loading = false;

                        if(response.status === 422) {
                            const responseData = await response.json();
                            // update errors in alpine data
                            this.errors = responseData.errors;
                            return;
                        }
                
                        const responseData = await response.json();
                        if(response.status === 200) {
                            this.completedSteps.push('assets');
                            this.assetsAdded.push(responseData.asset);
                            this.assetList = true;
                            // reset asset form except deal_id
                            this.assetForm = {
                                _token : csrf,
                                name: '',
                                address: '',
                                city: '',
                                state: '',
                                zip: '',
                                country: '',
                                property_type: '',
                                property_class: '',
                                number_of_units: '',
                                type_of_units: '',
                                acquisition_date: '',
                                acquisition_price: '',
                                exit_date: '',
                                exit_price: '',
                                year_built: '',
                                deal_id: responseData.asset.deal_id,
                            };
                            // this.$nextTick(() => {
                            //     document.getElementById('addDealModal').click();
                            // });
                        } else {
                            alert(responseData.message);
                        }
                        
                    } catch(error) {
                        console.error('Error:', error);
                    }
                },
                resetDealandAsset() {
                    this.currentStep = 'deal';
                    this.completedSteps = [];
                    this.errors = {};
                    this.loading = false;
                    this.dealForm = {
                        _token : csrf,
                        name: '',
                        type: '',
                        deal_stage: '',
                        sec_type: '',
                        close_date: '',
                        owning_entity_name: '',
                        funds_received_before_gp_countersigns: '0',
                        send_funding_instructions_after_gp_countersigns: '0',
                    };
                    this.assetForm = {
                        _token : csrf,
                        name: '',
                        address: '',
                        city: '',
                        state: '',
                        zip: '',
                        country: '',
                        property_type: '',
                        property_class: '',
                        number_of_units: '',
                        type_of_units: '',
                        acquisition_date: '',
                        acquisition_price: '',
                        exit_date: '',
                        exit_price: '',
                        year_built: '',
                        deal_id: '',
                    };
                    this.assetsAdded = [];
                    this.assetErrors = {};
                    this.assetList = false;
                    this.files = [];
                    this.dragOver = false;
                }
    
            };
        }
    </script>
@endpush

@push('script')
    <script>
        "use strict";

        const start = moment().subtract(14, 'days');
        const end = moment();

        const dateRangeOptions = {
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')],
                'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('year')],
            },
            maxDate: moment()
        }

        const changeDatePickerText = (element, startDate, endDate) => {
            $(element).html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
        }

        let dwChart = barChart(
            document.querySelector("#dwChartArea"),
            @json(__(gs('cur_text'))),
            [{
                    name: 'Deposited',
                    data: []
                },
                {
                    name: 'Withdrawn',
                    data: []
                }
            ],
            [],
        );

        let trxChart = lineChart(
            document.querySelector("#transactionChartArea"),
            [{
                    name: "Plus Transactions",
                    data: []
                },
                {
                    name: "Minus Transactions",
                    data: []
                }
            ],
            []
        );


        const depositWithdrawChart = (startDate, endDate) => {
            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }

            const url = @json(route('admin.chart.deposit.withdraw'));

            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {
                        dwChart.updateSeries(data.data);
                        dwChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }

        const transactionChart = (startDate, endDate) => {

            const data = {
                start_date: startDate.format('YYYY-MM-DD'),
                end_date: endDate.format('YYYY-MM-DD')
            }

            const url = @json(route('admin.chart.transaction'));


            $.get(url, data,
                function(data, status) {
                    if (status == 'success') {


                        trxChart.updateSeries(data.data);
                        trxChart.updateOptions({
                            xaxis: {
                                categories: data.created_on,
                            }
                        });
                    }
                }
            );
        }



        $('#dwDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#dwDatePicker span',
            start, end));
        $('#trxDatePicker').daterangepicker(dateRangeOptions, (start, end) => changeDatePickerText('#trxDatePicker span',
            start, end));

        changeDatePickerText('#dwDatePicker span', start, end);
        changeDatePickerText('#trxDatePicker span', start, end);

        depositWithdrawChart(start, end);
        transactionChart(start, end);

        $('#dwDatePicker').on('apply.daterangepicker', (event, picker) => depositWithdrawChart(picker.startDate, picker
            .endDate));
        $('#trxDatePicker').on('apply.daterangepicker', (event, picker) => transactionChart(picker.startDate, picker
            .endDate));

        piChart(
            document.getElementById('userBrowserChart'),
            @json(@$chart['user_browser_counter']->keys()),
            @json(@$chart['user_browser_counter']->flatten())
        );

        piChart(
            document.getElementById('userOsChart'),
            @json(@$chart['user_os_counter']->keys()),
            @json(@$chart['user_os_counter']->flatten())
        );

        piChart(
            document.getElementById('userCountryChart'),
            @json(@$chart['user_country_counter']->keys()),
            @json(@$chart['user_country_counter']->flatten())
        );
    </script>
@endpush
@push('style')
    <style>
        .apexcharts-menu {
            min-width: 120px !important;
        }
    </style>
@endpush
