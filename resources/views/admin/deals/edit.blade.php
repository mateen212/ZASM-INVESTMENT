@extends('admin.layouts.app') <!-- Assuming you have a main app layout -->

@push('style')
        <style>
            .square {
                position: relative;
                width: 100%;
                padding-top: 100%; /* 1:1 Aspect Ratio */
            }
            .square img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            /* Add right margin to radio buttons */
            .form-check-inline {
                margin-right: 90px;  /* Adjust the space as needed */
            }

        

            .info-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            font-size: 14px;
            font-weight: bold;
            color: #888; /* Grey Question Mark */
            background-color: #fff; /* White Circle */
            border: 1px solid #ccc; /* Light Grey Border */
            border-radius: 50%;
            text-align: center;
            cursor: pointer;
            margin-left: 5px;
        }

        .info-icon:hover {
            color: #555; /* Darker Grey on Hover */
            border-color: #aaa; /* Slightly Darker Border */
        }


        </style>

        <style>
            .custom-dropdown {
                width: 300px; /* Adjust the width as needed */
                border: none;
                border-bottom: 2px solid #0B5ED7; /* Set the bottom border color to #0B5ED7 */
                background-color: transparent;
                box-shadow: none; /* Remove the default box-shadow */
            }
            
            .custom-dropdown:focus {
                outline: none; /* Remove the outline on focus */
            }
        </style>

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
@endpush

@php
    if(auth('admin')->user()->hasRole('partner')){
        $prefix = 'partner';
    }else{
        $prefix = 'admin';
    }
@endphp
@section('panel')
    <div class="card">
        <div class="card-body">
            <div class="edit-deal" x-data="dealEdit()" x-cloak>
                <template x-if="loading">
                        <div class="custom-loader-overlay">
                            <div class="custom-loader"></div>
                        </div>
                </template>
                <nav aria-label="breadcrumbs">
                    <ol class="breadcrumbs align-items-center">
                        <li class="breadcrumbs-item">
                            <a href="{{ route($prefix . '.dashboard') }}" class="home-icon"><i class="fas fa-home"
                                    title="Dashboard"></i></a>
                        </li>
                        <li class="breadcrumbs-item" onclick="window.location.href='{{ route($prefix . '.deals.index') }}'">Deals
                        </li>
                        <li class="breadcrumbs-items">></li>
                        <li class="breadcrumbs-item"
                            onclick="window.location.href='{{ route($prefix . '.deals.summary', $deal->id) }}'">
                            {{ $deal->name }}
                        </li>
                        <li class="breadcrumbs-items">></li>
                        <li class="breadcrumbs-item" >Manage deal</li>
                    </ol>
                </nav>
                <hr>
                <div class="d-flex justify-content-between mt-4 align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-outline-primary rounded-lg" onclick="window.history.back();">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </button>
                        <h2 class="mb-0 fw-semibold" style="font-size: 24px;">Manage deal</h2>
                    </div>
                </div>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mt-5" id="deal-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#editdeal" data-bs-toggle="tab">Edit deal</a></li>
                    <li class="nav-item"><a class="nav-link" href="#admin-settinggs" data-bs-toggle="tab">Admin Settings</a></li>
                    <li class="nav-item"><a class="nav-link" href="#personal-settings" data-bs-toggle="tab">Personal Settings</a></li>
                    <li class="nav-item"><a class="nav-link" href="#ach-setting" data-bs-toggle="tab">ACH Settings</a></li>
                    <li class="nav-item"><a class="nav-link" href="#check-settings" data-bs-toggle="tab">Check Settings</a></li>
                </ul>

                <!-- Tab Contents -->
                <div class="tab-content mt-4">
                    {{--  Edit deal content   --}}
                    <div class="tab-pane fade show active" id="editdeal">
                        <div class="container mt-5">
                            <div x-data="deal">
                                <div class="mb-3 col-md-6">
                                    <label for="dealStage" class="form-label fw-bold fs-6">Deal Stage</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="dealStage" id="raisingCapital" value="Raising capital" x-model="deal.deal_stage" @change="updateDeal()">
                                            <label class="form-check-label" for="raisingCapital">Raising Capital</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="dealStage" id="assetManaging" value="Asset managing" x-model="deal.deal_stage" @change="updateDeal()">
                                            <label class="form-check-label" for="assetManaging">Asset Managing</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="dealStage" id="liquidated" value="Liquidated" x-model="deal.deal_stage" @change="updateDeal()">
                                            <label class="form-check-label" for="liquidated">Liquidated</label>
                                        </div>
                                    </div>
                                </div>
                    
                                <div class="mb-3 col-md-6">
                                    <label for="dealName" class="form-label">Deal Name</label>
                                    <input type="text" class="form-control" id="dealName" x-model="deal.name"
                                        x-on:input="dealErrors.name = ''" @input.debounce.500ms="updateDeal()">
                                    <span x-text="dealErrors.name" x-show="dealErrors.name" class="text-danger"></span>
                                </div>
                    
                                <div class="mb-3 col-md-6">
                                    <label for="leadSponsor" class="form-label fw-bold">Lead Sponsor</label>
                                    <input type="text" class="form-control" id="leadSponsor" x-model="deal.sponsor" disabled>
                                </div>
                    
                                <div class="mb-3 col-md-6">
                                    <label for="dealType" class="form-label fw-bold">Deal Type</label>
                                    <select class="form-select" id="dealType" x-model="deal.type" @change="updateDeal()">
                                        <option value="Direct syndication">Direct syndication (most common)</option>
                                        <option value="Fund syndication">Fund syndication</option>
                                        <option value="Customized fund of funds">Customized fund of funds</option>
                                        <option value="SPV">SPV Fund</option>
                                        <option value="SPV">Flex fund</option>
                                        <option value="Angel Investment">Angel Investment</option>
                                    </select>
                                </div>
                    
                                <div class="mb-3 col-md-6">
                                    <label for="secType" class="form-label fw-bold">SEC Type</label>
                                    <select class="form-select" id="secType" x-model="deal.sec_type" @change="updateDeal()">
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
                    
                                <div class="mb-3 col-md-6">
                                    <label for="closeDate" class="form-label fw-bold">Close Date</label>
                                    <input type="date" onclick="this.showPicker()"  class="form-control" id="closeDate" x-model="deal.close_date" @change="updateDeal()">
                                </div>
                    
                                <div class="mb-3 col-md-6">
                                    <label for="exitDate" class="form-label fw-bold">Exit Date</label>
                                    <input type="date" onclick="this.showPicker()"  class="form-control" id="exitDate" x-model="exitDate" @change="updateDeal()">
                                </div>
                    
                                <div class="mb-3 col-md-6">
                                    <label for="owningEntity" class="form-label fw-bold">Owning Entity</label>
                                    {{-- <div class="d-flex align-items-center">
                                        <input type="text" class="form-control w-75" id="owningEntity" x-model="deal.owning_entity_name">
                                        <button type="button" class="btn btn-primary mt-2">Edit</button>
                                    </div> --}}
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="owningEntity" x-model="deal.owning_entity_name" readonly>
                                        <button class="btn btn-outline-secondary" type="button" id="button-addon2" onclick="window.location.href='{{ route($prefix . '.deals.edit.EntityDetail', [$deal->id]) }}'">Edit</button>
                                    </div>
                                </div>
                    
                                {{-- <div>
                                    <button type="button" class="btn btn-outline-danger" @click="deal.isArchived = !deal.isArchived">
                                        <span x-text="isArchived ? 'Unarchive Deal' : 'Archive Deal'"></span>
                                    </button>
                                </div> --}}
                            </div>
                        </div>
                    </div>

                        {{-- Admin settings content --}}
                    <div class="tab-pane fade" id="admin-settinggs">
                        <div>
                            @include('admin.deals.admin_setting')
                        </div>
                    </div>
                        {{--  Personal Settings content   --}}
                    <div class="tab-pane fade" id="personal-settings">
                        <div>
                            @include('admin.deals.personal_setting')
                        </div>
                    </div>
                    {{--  ACH settings content   --}}
                    <div class="tab-pane fade" id="ach-setting">
                       <div x-data="ach_setting_data()" x-init="init()">
                            @include('admin.deals.payment.ach_settings')
                       </div> 
                    </div>

                    {{--  Check settings content   --}}
                    <div class="tab-pane fade" id="check-settings">
                        <div class="form-section" x-data="check_setting_data()">
                            <p>
                                Please select a sender address and a bank account for sending check distributions. Currently, we
                                support sending checks in the USA and Canada.
                                <a href="#" style="color: #69AEFF; text-decoration: none;">View instructions</a>
                            </p>

                            <div class="mb-3 d-flex align-items-center justify-content-between">
                                <span class="me-3 mt-4 fw-bold">Sender address<span style="color: red;">*</span></span>

                                <div class="d-flex justify-content-center flex-grow-1">
                                    <select id="senderAddress" class="form-select custom-dropdown"
                                        x-on:input="checksettingErrors.senderAddress = ''" x-model="checksettingForm.senderAddress" @change="changeSenderAddress()"
                                        style="width: 700px; height: 55px; border: none; border-bottom: 2px solid #0B5ED7; background-color: transparent; box-shadow: none; -webkit-appearance: none; -moz-appearance: none; appearance: none;">
                                        <!-- Placeholder option -->
                                        <option value=""  selected style="color: #D3D3D3;">Search</option>
                                        @foreach ($deal->senderaddresses as $senderaddress)
                                            <option value="{{ $senderaddress->id }}">
                                                {{ $senderaddress->company_name }}
                                                {{ $senderaddress->address_line_1 }} 
                                                {{ $senderaddress->address_line_2 }} 
                                                {{ $senderaddress->city }} 
                                                {{ $senderaddress->province }} 
                                                {{ $senderaddress->postal_code }} 
                                                {{ $senderaddress->state }} 
                                                {{ $senderaddress->zip_code }} 
                                                {{ $senderaddress->country }}
                                            </option>
                                        @endforeach
                                        <!-- Other options -->
                                        <option value="add">+ Add New</option>
                                    </select>
                                    <span x-text="checksettingErrors.senderAddress" x-show="checksettingErrors.senderAddress" class="text-danger"></span>
                                </div>
                            </div>

                            <div class="mb-3 d-flex align-items-center justify-content-between">
                                <span class="me-3 mt-4 fw-bold">Bank account<span style="color: red;">*</span></span>

                                <div class="d-flex justify-content-center flex-grow-1">
                                    <select id="equity_increase_class" class="form-select custom-dropdown"
                                        x-on:input="checksettingErrors.bankAccount = ''"   x-model="checksettingForm.bankAccount" @change="changeBankAccount()"
                                        style="width: 700px; height: 55px; border: none; border-bottom: 2px solid #0B5ED7; background-color: transparent; box-shadow: none; -webkit-appearance: none; -moz-appearance: none; appearance: none;">
                                        <!-- Placeholder option styled with lighter grey -->
                                        <option value="" selected style="color: #B0B0B0;">Search</option>
                                        @foreach ($deal->bankaccounts as $bankaccount)
                                            <option value="{{ $bankaccount->id }}">
                                                {{ $bankaccount->account_nick_name }},
                                                ending in {{ substr((string) $bankaccount->account_number, -4) }}
                                            </option>
                                        @endforeach
                                        <!-- Add Option (First selectable option) -->
                                        <option value="add">+ Add New</option>
                                        <!-- Other options can follow here -->
                                    </select>
                                    <span x-text="checksettingErrors.bankAccount" x-show="checksettingErrors.bankAccount" class="text-danger"></span>
                                </div>
                            </div>
                            @include('admin.deals.check_settings')
                            <button type="submit" class="btn btn-primary mt-3"
                                @click="submitchecksettingForm">Save</button>
                        </div>
                    </div>

                <style>
                    /* Style each option-card as clickable */
                    .option-card {
                        display: block;
                        padding: 20px;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        margin-bottom: 15px;
                        cursor: pointer;
                        transition: box-shadow 0.3s ease, border-color 0.3s ease;
                    }

                    /* Apply blue shadow and border on hover */
                    .option-card:hover {
                        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
                        border-color: #007bff;
                    }

                    /* Apply border and shadow to the entire option-card when selected */
                    .option-card input[type="radio"]:checked + .option-card {
                        border-color: #007bff;
                        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
                    }

                    /* Hide default radio button */
                    .option-card input[type="radio"] {
                        display: none;
                    }

                    /* Blue border on the entire card when selected */
                    .option-card.selected {
                        border: 2px solid #007bff;
                        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
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
                    /* Add blue shadow on hover */
                    .card-hover:hover {
                        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3); /* Blue shadow */
                        border-color: #007bff; /* Blue border on hover */
                    }
                </style>
                {{--  style for investment table   --}}
                <style>
                    /* Make the first column sticky */
                    #investments-table tbody tr td:first-child,
                    #investments-table th:first-child {
                        position: sticky;
                        left: 0;
                        background-color: white; /* Optional: To keep background color consistent */
                        z-index: 1 ;
                    }

                    /* Make the last column sticky */
                    #investments-table tbody tr td:last-child,
                    #investments-table th:last-child {
                        position: sticky;
                        right: 0;
                        background-color: white; /* Optional: To keep background color consistent */
                        
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
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoU4olE2B6fMxyn6e+5m5iNqXG3yjZrM+2V7ef5azJ0z3Qf" crossorigin="anonymous"></script>

    <!-- Custom JS for Dynamic Interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handling tab navigation (Bootstrap requires data-bs-toggle)
            const tabs = document.querySelectorAll('.nav-link');
            tabs.forEach(tab => {
                tab.addEventListener('click', function (e) {
                    e.preventDefault();
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

        });
    </script>
@endsection

@push('script')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        var csrf = '{{ csrf_token() }}';
        function dealEdit() {
            return {
                ...alpineHelpers(),
                errors: {},
                dealErrors: {},
                loading: false,
                settingClasses: 0,
                deal : {
                    name: '{{ $deal->name ?? '' }}',
                    deal_stage: '{{ $deal->deal_stage ?? '' }}',
                    sponsor: '{{ $deal->sponsor ?? '' }}',
                    type: '{{ $deal->type ?? '' }}',
                    sec_type: '{{ $deal->sec_type ?? '' }}',
                    close_date: '{{ $deal->close_date ?? '' }}',
                    exit_date: '{{ $deal->exit_date ?? '' }}',
                    owning_entity_name: '{{ $deal->owning_entity_name ?? '' }}',
                },
                adminSettingForm: @json($deal->admin_setting),
                personalSettingForm: @json($deal->personal_setting),
                checkClasses() {
                    let values = $('#equity_increase_class').val();
                    if(values.length){
                        return true;
                    }else{
                        return false;
                    }
                },
                updateDeal() {
                        this.errors = {};
                        this.loading = true;

                        fetch('/{{$prefix}}/deals/update/{{ $deal->id }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: JSON.stringify(this.deal)
                            })
                            .then(async response => {
                                this.loading = false;

                                if (response.status === 422) {
                                    const responseData = await response.json();
                                    // Update errors in Alpine.js data
                                    this.dealErrors = responseData.errors;
                                } else if (response.ok) {
                                    const data = await response.json();
                                    if (data.status === 'success') {
                                        cosyAlert('Deal updated successfully', 'success');
                                    }
                                } else {
                                    throw new Error('An unexpected error occurred');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    },

                    async submitAdminSettingForm() {
                        console.log("done");
                        this.errors = {};
                        this.loading = true;
                        this.adminSettingForm.equity_increase_class = $('#equity_increase_class').val();
                        fetch('/{{$prefix}}/deals/setting/{{ $deal->id }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: JSON.stringify(this.adminSettingForm)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    cosyAlert('Admin Settings updated successfully', 'success');
                                } else {
                                    this.errors = data.errors;
                                }
                                this.loading = false;
                            });
                    },
                    async submitPersonalSettingForm() {
                        this.errors = {};
                        this.loading = true;
                        fetch('/{{$prefix}}/deals/personal/{{ $deal->id }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: JSON.stringify(this.personalSettingForm)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    cosyAlert('Admin Settings updated successfully', 'success');
                                } else {
                                    this.errors = data.errors;
                                }
                                this.loading = false;
                            });
                    },
                };
            }

        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });



</script>
        <script>
        // Jquery document load
        $(document).ready(function() {
            
            // Initialize Select2
            $('#equity_increase_class').select2({
                placeholder: 'Select Classes',
            });

            var selectedClassesforSetting = '{!! $deal->admin_setting->equity_increase_class !!}';

            $('#equity_increase_class').val(
                {!! $deal->admin_setting->equity_increase_class !!}
            ).trigger('change');

            if(selectedClassesforSetting?.length){
                let AlpineObj = document.querySelector('[x-data="dealEdit()"]');
                let dataStack = AlpineObj._x_dataStack[0];
                dataStack.settingClasses = JSON.parse(selectedClassesforSetting).length
            }

            $('#equity_increase_class').on('change', function() {
                const selectedOptions = $(this).val();
                let AlpineObj = document.querySelector('[x-data="dealEdit()"]');
                let dataStack = AlpineObj._x_dataStack[0];
                dataStack.settingClasses = selectedOptions.length

                dataStack.submitAdminSettingForm();
            });
        });
        
    </script>
@endpush