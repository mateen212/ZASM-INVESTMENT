<div class="container mt-5">
    
    @csrf
    <!-- Distribution Source -->
    <div class="form-group mb-3">
        <label for="distribution_source">Source <span class="text-danger">*</span></label>
        <select class="form-control" id="distribution_source" x-on:input="distributionErrors.source =''" x-model="distributionForm.source">
            <option value="">Select source</option>
            <option value="Operating Income">Operating Income</option>
            <option value="Proceeds from refinance">Proceeds from refinance</option>
            <option value="Gain from Sale">Gain from Sale</option>
            <option value="Return from initial investment">Return from initial investment</option>
            <option value="interest">interest</option>
            <option value="Accured interest">Accured interest</option>
            <option value="GP payments">GP payments</option>
            <option value="Redemption">Redemption</option>
            
            <!-- Additional options here -->
        </select>
        <span x-show="distributionErrors.source" x-text="distributionErrors.source" class="text-danger"></span>

    </div>
    <div class="form-group mb-3">
        <label for="distribution_type">Type <span class="text-danger">*</span></label>
        <select class="form-control" id="distribution_type" x-model="distributionForm.distribution_type">
            <option value="Return on capital (most common)">Return on capital (most common)</option>
            <option value="Return of capital">Return of capital</option>
            <option value="Return of principle">Return of principle </option>
            <option value="Upside">Upside</option>
            <option value="interest">interest</option>
            <option value="Catchup">Catchup</option>
            <option value="Preffered return">Preffered return</option>
            <option value="Acquisition fee">Acquisition fee</option>
            <option value="Refinance fee">Refinance fee</option>
            <option value="Deposition fee">Deposition fee</option>
            <option value="Asset management fee">Asset management fee</option>
            <option value="Other fee">Other fee</option>
            <option value="Other">Other</option>
            <!-- Additional options here -->
        </select>
    </div>
    <div class="form-group mb-3">
        <label for="deduct_from">Deducts from <span class="text-danger">*</span></label>
        <select class="form-control" id="deduct_from" x-model="distributionForm.count_toward">
            <option value="None/not applicable">None/not applicable</option>
            <option value="accrued_pref">Accrued Pref</option>

            <!-- Additional options here -->
        </select>
    </div>
    <div class="form-group mb-3">
        <label for="included_classes">Included classes <span class="text-danger">*</span></label>
        <div class="container" x-data="alpineMuliSelect({selected:[], elementId:'multSelect'})">
                <!-- Select Options -->
                <select class="d-none" id="multSelect" @change="selectChange()">
                    @foreach($deal->classes as $class)
                        <option value="{{$class->id}}" data-search="{{$class->equity_class_name}}">{{$class->equity_class_name}}</option>
                    @endforeach
                    @foreach($deal->buckets as $bucket)
                        @foreach ($bucket->classes as $bclass)
                            <option value="{{ $bclass->id }}" data-search="{{$class->equity_class_name}}" data-search="{{$bclass->equity_class_name}}">{{ $bclass->equity_class_name }}</option>
                        @endforeach
                    @endforeach
                </select>

            <div class="w-100 d-flex flex-column align-items-center h-64 mx-auto" @keyup.alt="toggle">
                <!-- Selected Teams -->
                <input name="teams[]" type="hidden" x-bind:value="selectedValues()">

                <div class="position-relative w-100">

                    <div class="d-flex flex-column align-items-center position-relative">

                        <!-- Selected elements container -->
                        <div class="w-100">
                            <div class="my-2 p-1 d-flex border border-gray-200 bg-white rounded">
                                <div class="d-flex flex-auto flex-wrap w-100" x-on:click="open">
                                    <!-- iterating over selected elements -->
                                    <template x-for="(option, index) in selectedElms" :key="option.value">
                                        <div x-show="index > 0"
                                            class="d-flex justify-content-center align-items-center m-1 font-medium py-1 px-2 rounded text-primary bg-light border border-primary">
                                            <div class="tag-text font-normal leading-none max-w-full flex-initial"
                                                x-model="selectedElms[option]" x-text="option.text"></div>
                                            <div class="d-flex flex-auto flex-row-reverse">
                                                <div x-on:click.stop="remove(index, option)" class="tag-cross d-flex justify-content-center align-items-center" style="width: 24px; height: 24px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-100 h-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <!-- None items selected -->
                                    <div x-show="selectedElms.length == 0" class="flex-1 w-100">
                                        <input placeholder="Select teams" class="bg-transparent p-1 px-2 appearance-none outline-none h-full w-100 text-gray-800" x-bind:value="selectedElements()">
                                    </div>
                                </div>
                                <!-- Drop down toggle with icons-->
                                <div class="text-muted py-1 px-2 border-start d-flex align-items-center border-secondary">
                                    <button type="button" x-show="!isOpen()" x-on:click="open()" class="btn btn-link p-0 text-secondary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chevron-down" fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </button>
                                    <button type="button" x-show="isOpen()" x-on:click="close()" class="btn btn-link p-0 text-secondary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-chevron-up" fill="currentColor" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M1.646 11.354a.5.5 0 0 1 .708 0L8 6.707l5.646 5.647a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1-.708 0l-6 6a.5.5 0 0 1 0 .708z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown container -->
                        <div class="w-100">
                            <div x-show.transition.origin.top="isOpen()" x-trap="isOpen()" class="position-absolute shadow-lg top-100 bg-white z-40 w-100 left-0 rounded max-h-80" x-on:click.away="close">
                                <div class="d-flex flex-column w-100">

                                    {{-- <div class="px-2 py-1 border-bottom">
                                        <!-- Search input-->
                                        <div class="mt-1 position-relative rounded shadow-sm">
                                            <div class="position-absolute inset-y-0 start-0 ps-3 d-flex align-items-center pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                            </div>
                                            <input type="text" name="search" autocomplete="off" id="search" x-model.debounce.750ms="search" class="form-control focus:ring-primary focus:border-primary block w-100 ps-10 sm:text-sm border border-primary rounded h-10" placeholder="" @keyup.escape="clear"
                                                @keyup.delete="deselect">
                                        </div>
                                    </div> --}}
                                    <!-- Options container -->
                                    <ul class="z-10 mt-0 w-100 bg-white shadow-lg max-h-80 rounded py-0 text-base ring-1 ring-black ring-opacity-5 focus:outline-none overflow-y-auto sm:text-sm" tabindex="-1" role="listbox" @keyup.delete="deselect">
                                        <template x-for="(option,index) in options" :key="option.text+Math.random().toString(36).substring(2, 9 + 2)">
                                            <li class="text-gray-900 cursor-default select-none relative"
                                                role="option">
                                                <div class="cursor-pointer w-100 border-gray-100 border-bottom hover:bg-slate-100"
                                                    x-bind:class="option.selected ? 'bg-primary' : 'text-dark'"
                                                    @click="select(index,$event)">
                                                    <div x-bind:class="option.selected ? 'border-primary' : ''"
                                                        class="d-flex w-100 align-items-center p-2 ps-2 border-transparent border-start-2 position-relative">
                                                        <div class="w-100 align-items-center d-flex">
                                                            <div class="mx-2 leading-6 .text-dark" x-model="option"
                                                                x-text="option.text"></div>
                                                            <span
                                                                class="position-absolute inset-y-0 end-0 d-flex align-items-center pe-4 text-primary"
                                                                x-show="option.selected">

                                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                                                    viewBox="0 0 20 20" fill="currentColor"
                                                                    aria-hidden="true">
                                                                    <path fill-rule="evenodd"
                                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <span x-show="distributionErrors.included_classes" x-text="distributionErrors.included_classes" class="text-danger"></span>
        </div>                     
    </div>
    <div class="form-group mb-3">
        <label for="compounding_period">Compounding period</label>        
        <select class="form-control" id="compounding_period" name="compounding_period" x-on:input="distributionErrors.compounding_period =''" x-model="distributionForm.compounding_period">
            <option value="">Select compounding period</option>
            <option value="no_compounding">No Compounding (simple interest)</option>
            <option value="monthly">Monthly</option>
            <option value="quarterly">Quarterly</option>
            <option value="semi-annually">Biyearly</option>
            <option value="annually">Yearly</option>

        </select>
        <span x-show="distributionErrors.compounding_period" x-text="distributionErrors.compounding_period" class="text-danger"></span>
    </div>
    <div class="form-group mb-3">
        <label for="day_count">Day count convention</label>
        <select class="form-control" id="day_count" name="day_count" x-model="distributionForm.day_count">
            <option value="">Select day count convention</option>
            <option value="30/360">30/360</option>    
            <option value="30/365">30/365</option>
            <option value="actual/360">Actual/360</option>
            <option value="actual/360">Actual/365 (most common)</option>
            <option value="actual/actual">Actual/Actual</option>
        </select>
    </div>
    <div class="form-group mb-3">
        <label value="preffered_return">Preferred return start date</label>
        <select class="form-control" id="preffered_return" name="preffered_return" x-model="distributionForm.preffered_return">
            <option value="">Select preferred return start date</option>
            <option value="from_period">From period start date</option>
            <option value="from_each_investment">From each investment's pref return start date</option>
            <option value="from_either">From either, whichever comes last (most common)</option>

        </select>

    </div>
    <div class="form-group mb-3">
        <label for="period_start_date">Period start date <span class="text-danger">*</span></label>
        <input type="date" onclick="this.showPicker()"  class="form-control" id="period_start_date" x-on:input="distributionErrors.start_date =''" x-model="distributionForm.start_date">
        <span x-show="distributionErrors.start_date" x-text="distributionErrors.start_date" class="text-danger"></span>
    </div>
    <div class="form-group mb-3">
        <label for="period_end_date">Period end date <span class="text-danger">*</span></label>
        <input type="date" onclick="this.showPicker()"  class="form-control" id="period_end_date" x-on:input="distributionErrors.end_date =''" x-model="distributionForm.end_date">
        <span x-show="distributionErrors.end_date" x-text="distributionErrors.end_date" class="text-danger"></span>
    </div>
    <div class="form-group mb-3">
        <label for="distribution_date">Distribution date <span class="text-danger">*</span></label>
        <input type="date" onclick="this.showPicker()"  class="form-control" id="distribution_date" x-on:input="distributionErrors.distribution_date =''" x-model="distributionForm.distribution_date">
        <span x-show="distributionErrors.distribution_date" x-text="distributionErrors.distribution_date" class="text-danger"></span>
    </div>
    {{--  memo  --}}
    <div class="form-group mb-3">
        <label for="memo">Memo</label>
        <input type="text" class="form-control" id="memo" placeholder="Enter memo" x-model="distributionForm.memo">
    </div>
    
    <label>Investor visibility <span class="text-danger">*</span></label>
    <div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="visibility" id="visible" :value="1" x-model="distributionForm.is_visible">
            <label class="form-check-label" for="visible">Visible</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="visibility" id="hidden" :value="0" x-model="distributionForm.is_visible">
            <label class="form-check-label" for="hidden">Hidden</label>
        </div>
    </div>
    <div x-data="{ expanded: false }">
        <div id="advancedSettings">
            <button class="btn btn-primary " type="button" @click="expanded = ! expanded">Advanced settings</button>
            <div class="form-group mb-3"  x-show="expanded" x-collapse>
            <label for="investment_tags">Investment has tag(s) <span class="text-danger">*</span></label>
            <select class="form-control" id="investment_tags">
                <option>Class A - Limited partners</option>
                <!-- Additional options here -->
            </select>                      
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex mt-5 justify-content-between">
        <button type="button" class="btn btn-primary" onclick="showStep('calculationMethod')">Previous</button>
        <button type="submit" class="btn btn-secondary" @click="submitDistributionForm(distributionForm)">Create distribution</button>
    </div>
</div>