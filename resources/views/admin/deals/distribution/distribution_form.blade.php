<div x-data="distributionTab()">

    <style>
        .tag-text {
            font-size: 0.7rem;
        }

        .tag-cross {
            width: 1rem;
            height: 1rem;
            padding-left: 10px;
        }

        .btn.btn-link {
            width: 20px;
        }

        .step-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .step-circle.active {
            background-color: #007bff;
            /* Blue color for active */
            color: #fff;
        }

        .step-circle.inactive {
            background-color: #e9ecef;
            /* Gray for inactive */
            color: #6c757d;
        }

        .step-circle.green {
            background-color: #28a745;
            /* Green for completed */
            color: #fff;
            position: relative;
        }

        .step-circle.green::after {
            content: '✔';
            /* Add checkmark */
            position: absolute;
            right: -15px;
            /* Position the arrow */
            font-size: 14px;
            color: #28a745;
        }

        .step-circle.blue {
            background-color: #007bff;
            /* Blue for step 2 */
            color: #fff;
        }

        .step-text {
            font-size: 14px;
            margin-top: 4px;
        }

        .step-divider {
            width: 40px;
            height: 1px;
            background-color: #e9ecef;
            margin: 0 8px;
        }

        .step-divider.active {
            background-color: #007bff;
        }
    </style>
    <div class="d-flex align-items-center justify-content-center">
        <!-- Step 1 -->
        <div class="text-center" id="stepCalculationMethod">
            <div class="step-circle inactive" onclick="showStep('calculationMethod')">
                1
            </div>
            <div class="step-text">
                Calculation method
            </div>
        </div>

        <!-- Divider -->
        <div class="step-divider"></div>

        <!-- Step 2 -->
        <div class="text-center" id="stepDistributionDetails">
            <div class="step-circle inactive" onclick="showStep('distributionDetails')">
                2
            </div>
            <div class="step-text">
                Distribution details
            </div>
        </div>
    </div>


    @php
        if(auth('admin')->user()->hasRole('partner')){
            $prefix = 'partner';
        } else { 
            $prefix = 'admin';
        }
    @endphp

    <div id="calculationMethod" class="step-content">
        <div class="container mt-4">
            <h3>Choose a calculation method</h3>
            <a href="#" class="text-primary">Watch overview</a>
            @csrf
            <!-- Calculation Method Options -->
            <label class="option-card" :class="{ 'selected': isActive('simple_pro_rata') }"
                @click="selectOption('simple_pro_rata')">
                {{--  <input type="radio" name="calculation_method" value="simple_pro_rata" required x-model="distributionForm.calculation_method">  --}}
                <div class="card-body">
                    <h5 class="card-title">Simple pro-rata split on share of distribution</h5>
                    <p class="card-text">
                        Input a dollar amount to be split between chosen classes based on their
                        <a href="#" class="text-primary">distribution share</a>. Within a class, amounts are split
                        pro-rata
                        between investments based on their
                        <a href="#" class="text-primary">capital balance</a>.
                    </p>
                </div>
            </label>

            <!-- Option 2 -->
            <label class="option-card" :class="{ 'selected': isActive('water_fall') }"
                @click="selectOption('water_fall')">
                {{--  <input type="radio" name="calculation_method" value="water_fall" required x-model="distributionForm.calculation_method" checked>  --}}
                <div class="card-body">
                    <h5 class="card-title text-primary">Waterfall based on preferred returns, upside, and share of
                        distribution</h5>
                    <h6 class="text-primary mb-2">(Most common)</h6>
                    <p class="card-text">
                        Input a dollar amount to be split based on the chosen
                        <a href="#" class="text-primary">distribution waterfall</a>. Supports complex waterfall
                        structures with multiple hurdles.
                    </p>
                </div>
            </label>

            <!-- Option 3 -->
            <label class="option-card" :class="{ 'selected': isActive('preferred_return') }"
                @click="selectOption('preferred_return')">
                {{--  <input type="radio" name="calculation_method" value="preferred_return" required x-model="distributionForm.calculation_method">  --}}
                <div class="card-body">
                    <h5 class="card-title">Calculate for preferred return payments</h5>
                    <p class="card-text">
                        Pay out
                        <a href="#" class="text-primary">preferred return</a> accrued in a given period.
                    </p>
                </div>
            </label>

            <!-- Option 4 -->
            <label class="option-card" :class="{ 'selected': isActive('invested_payment') }"
                @click="selectOption('invested_payment')">
                {{--  <input type="radio" name="calculation_method" value="invested_payment" required x-model="distributionForm.calculation_method">  --}}
                <div class="card-body">
                    <h5 class="card-title">Calculate for interest payments</h5>
                    <p class="card-text">
                        Pay out
                        <a href="#" class="text-primary">interest</a> accrued in a given period.
                    </p>
                </div>
            </label>

            <!-- Option 5 -->
            <label class="option-card" :class="{ 'selected': isActive('invested_amount') }"
                @click="selectOption('invested_amount')">
                {{--  <input type="radio" name="calculation_method" value="invested_amount" required x-model="distributionForm.calculation_method">  --}}
                <div class="card-body">
                    <h5 class="card-title">Return invested amounts</h5>
                    <p class="card-text">
                        Pay out each active investment's remaining
                        <a href="#" class="text-primary">capital balance</a>.
                    </p>
                </div>
            </label>

            <!-- Option 6 -->
            <label class="option-card" :class="{ 'selected': isActive('Custom') }" @click="selectOption('Custom')">
                {{--  <input type="radio" name="calculation_method" value="Custom" required x-model="distributionForm.calculation_method">  --}}
                <div class="card-body">
                    <h5 class="card-title">Custom</h5>
                    <p class="card-text">
                        Import a spreadsheet or manually enter payment amounts.
                    </p>
                </div>
            </label>

            <!-- "Next" Button -->
            <button type="button" class="btn btn-primary mt-3" onclick="showStep('distributionDetails')">Next</button>
        </div>
    </div>



    <div id="distributionDetails" class="step-content" style="display: none;">
        {{--  <span x-text=""  --}}
        <div class="container mt-5">
            @csrf
            <template x-if="distributionForm.calculation_method === 'simple_pro_rata'">
                @include('admin.deals.distribution.partials.distribution_for_simplepro')
            </template>
            <template x-if="distributionForm.calculation_method === 'water_fall'">
                @include('admin.deals.distribution.partials.distribution_for_waterfall')
            </template>
            <template x-if="distributionForm.calculation_method === 'preferred_return'">
                @include('admin.deals.distribution.partials.distribution_for_calculatepref')
            </template>
            <template x-if="distributionForm.calculation_method === 'invested_payment'">
                @include('admin.deals.distribution.partials.distribution_for_calculateinterest')
            </template>
            <template x-if="distributionForm.calculation_method === 'invested_amount'">
                @include('admin.deals.distribution.partials.distribution_for_returninvested')
            </template>
            <template x-if="distributionForm.calculation_method === 'Custom'">
                @include('admin.deals.distribution.partials.distribution_for_custom')
            </template>

            <!-- Navigation Buttons -->
            {{--  <div class="d-flex mt-5 justify-content-between">
                <button type="button" class="btn btn-primary" @click="distributionForm.calculation_method = 'calculationMethod'">Previous</button>
                <button type="submit" class="btn btn-secondary" @click="submitDistributionForm(distributionForm)">Create distributions</button>
            </div>  --}}
        </div>
    </div>
</div>


<!-- Distribution Details Section -->
{{--  @include('admin.deals.distribution.partials.distribution_for_simplepro')
    @include('admin.deals.distribution.partials.distribution_for_waterfall')
    @include('admin.deals.distribution.partials.distribution_for_calculatepref')
    @include('admin.deals.distribution.partials.distribution_for_calculateinterest')
    @include('admin.deals.distribution.partials.distribution_for_returninvested')
    @include('admin.deals.distribution.partials.distribution_for_custom')  --}}
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
        function distributionTab() {
            return {
                ...alpineHelpers(),
                distributionErrors: {},
                distributionForm: {
                    deal_id: "{{ $deal->id }}",
                    calculation_method: 'water_fall',
                    source: '',
                    distribution_type: '',
                    count_toward: '',
                    amount: '',
                    included_classes: '',
                    amount: '',
                    start_date: '',
                    end_date: '',
                    distribution_date: '',
                    memo: '',
                    is_visible: 1,
                    distribution_waterfall: "",
                    compounding_period: '',
                    day_count: '',
                    preffered_return: '',
                    import_distribution: ''

                },
                submitDistributionForm(form) {
                    console.log(form);
                    let url = "{{ route($prefix . '.distributions.store') }}";

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify(form)
                        })
                        .then(response => {
                            if (response.ok) {
                                return response.json();
                            }
                            if (response.status === 422) {
                                return response.json().then(errors => {
                                    throw errors;
                                });
                            }
                            throw new Error('Something went wrong.');
                        })
                        .then(data => {
                            console.log(data);
                            this.resetForm();
                            $('#addDistributionModal').modal('hide');
                            // Redirect to the distribution page or show a success message
                        })
                        .catch(error => {
                            if (error.errors) {
                                // Assuming you're using Alpine.js and you have a 'distributionErrors' object in your component
                                this.distributionErrors = error.errors;
                            } else {
                                console.error('Unexpected error:', error);
                            }
                        })
                        .finally(() => {
                            // You can reset some other states if needed
                        });
                },

                resetForm() {
                    this.distributionForm = {
                        deal_id: "{{ $deal->id }}",
                        calculation_method: '',
                        source: '',
                        distribution_type: '',
                        count_toward: '',
                        amount: '',
                        included_classes: '',
                        amount: '',
                        start_date: '',
                        end_date: '',
                        distribution_date: '',
                        memo: '',
                        is_visible: 1,
                    };
                },
                isActive(name) {
                    return (this.distributionForm.calculation_method == name) ? true : false;
                },
                selectOption(option) {
                    this.distributionForm.calculation_method = option;
                    console.log(this.distributionForm.calculation_method);
                },
                init() {

                },

            }
        }
    </script>
    <script>
        // Show specific step and toggle active class in step navigation
        function showStep(step) {
            const calculationMethod = document.getElementById("calculationMethod");
            const distributionDetails = document.getElementById("distributionDetails");
            const stepCalculationMethod = document.getElementById("stepCalculationMethod");
            const stepDistributionDetails = document.getElementById("stepDistributionDetails");
            const step1Circle = stepCalculationMethod.querySelector(".step-circle");
            const step2Circle = stepDistributionDetails.querySelector(".step-circle");
            const stepArrow = document.getElementById("stepArrow");
            const step1Text = document.getElementById("step1Text");

            if (step === "calculationMethod") {
                // Show Calculation Method and hide Distribution Details
                calculationMethod.style.display = "block";
                distributionDetails.style.display = "none";

                // Update step classes
                stepCalculationMethod.classList.add("active");
                stepDistributionDetails.classList.remove("active");

                // Change the color of the circles
                step1Circle.classList.remove("inactive", "green");
                step1Circle.classList.add("blue"); // Step 1 turns blue

                step2Circle.classList.remove("active", "blue");
                step2Circle.classList.add("inactive"); // Step 2 becomes inactive
            } else if (step === "distributionDetails") {
                // Show Distribution Details and hide Calculation Method
                calculationMethod.style.display = "none";
                distributionDetails.style.display = "block";

                // Update step classes
                stepCalculationMethod.classList.remove("active");
                stepDistributionDetails.classList.add("active");

                // Change the color of the circles
                step1Circle.classList.remove("blue");
                step1Circle.classList.add("green"); // Step 1 turns green

                step2Circle.classList.remove("inactive");
                step2Circle.classList.add("blue"); // Step 2 turns blue
            }
        }




        // Highlight selected option card


        document.getElementById("time_span").addEventListener("change", function() {
            const customFields = document.getElementById("custom-date-fields");
            if (this.value === "custom") {
                customFields.style.display = "block"; // Show "From" and "To" fields
            } else {
                customFields.style.display = "none"; // Hide them otherwise
            }
        });

        // Automatically select the default option on page load
    </script>
    <script type="text/javascript">
        document.addEventListener("alpine:init", () => {
            console.log('Alpine Initialized');
            Alpine.data("alpineMuliSelect", (obj) => ({
                elementId: obj.elementId,
                options: [],
                selected: obj.selected,
                selectedElms: [],
                show: false,
                search: '',
                open() {
                    this.show = true
                },
                close() {
                    this.show = false
                },
                toggle() {
                    this.show = !this.show
                },
                isOpen() {
                    return this.show === true
                },

                // Initializing component 
                init() {
                    console.log('Alpine component Initialized');
                    const options = document.getElementById(this.elementId).options;
                    for (let i = 0; i < options.length; i++) {

                        this.options.push({
                            value: options[i].value,
                            text: options[i].innerText,
                            search: options[i].dataset.search,
                            selected: Object.values(this.selected).includes(options[i].value)
                        });

                        if (this.options[i].selected) {
                            this.selectedElms.push(this.options[i])
                        }
                    }

                    // searching for the given value
                    this.$watch("search", (e => {
                        this.options = []
                        const options = document.getElementById(this.elementId).options;
                        Object.values(options).filter((el) => {
                            var reg = new RegExp(this.search, 'gi');
                            return el.dataset.search.match(reg)
                        }).forEach((el) => {
                            let newel = {
                                value: el.value,
                                text: el.innerText,
                                search: el.dataset.search,
                                selected: Object.values(this.selected).includes(
                                    el.value)
                            }
                            this.options.push(newel);
                        })
                    }));

                    this.$watch("selected", (e) => {
                        console.log('dispatching event')
                        this.$dispatch('notify', {
                            classes: this.selected
                        })
                    });
                },
                // clear search field
                clear() {
                    this.search = ''
                },
                // deselect selected options
                deselect() {
                    setTimeout(() => {
                        this.selected = []
                        this.selectedElms = []
                        Object.keys(this.options).forEach((key) => {
                            this.options[key].selected = false;
                        })
                    }, 100)
                },
                // select given option
                select(index, event) {
                    if (!this.options[index].selected) {
                        this.options[index].selected = true;
                        this.options[index].element = event.target;
                        this.selected.push(this.options[index].value);
                        this.selectedElms.push(this.options[index]);

                    } else {
                        this.selected.splice(this.selected.lastIndexOf(index), 1);
                        this.options[index].selected = false
                        Object.keys(this.selectedElms).forEach((key) => {
                            if (this.selectedElms[key].value == this.options[index].value) {
                                setTimeout(() => {
                                    this.selectedElms.splice(key, 1)
                                }, 100)
                            }
                        })
                    }
                },
                // remove from selected option
                remove(index, option) {
                    this.selectedElms.splice(index, 1);
                    Object.keys(this.selected).forEach((skey) => {
                        if (this.selected[skey] == option.value) {
                            this.selected.splice(skey, 1);
                        }
                    });
                    Object.keys(this.options).forEach((key) => {
                        if (this.options[key].value == option.value) {
                            this.options[key].selected = false;
                        }
                    });
                },
                // filter out selected elements
                selectedElements() {
                    return this.options.filter(op => op.selected === true)
                },
                // get selected values
                selectedValues() {
                    return this.options.filter(op => op.selected === true).map(el => el.value)
                },
                selectChange() {
                    console.log(this.selected);
                }
            }));
        });
    </script>
@endpush
