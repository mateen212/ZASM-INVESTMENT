<div class="container mt-4">
    <h4 class="mb-3">Funding Info</h4>
    <!-- Funding Method -->
    <div class="container mt-4">
        <div class="funding mb-3">
            <div class="funding-left">
                <label class="form-label fw-bold">Funding method<span class="text-danger">*</span></label>
                <p class="text-muted">
                    You can select multiple ACH payments made through the portal will incur fees.
                    <a href="#">Learn more</a>.
                </p>
            </div>

            <div id="v-funding-info">
                <div>

                    {{--  <funding-info />  --}}

                </div>
            </div>

            <div class="funding-right">
                <div class="funding_method d-flex gap-2">
                    <div>
                        <input type="checkbox" class="btn-check" id="wireTransfer"
                            x-model="fundingForm.funding_methods.wireTransfer">
                        <label class="btn btn-outline-success" for="wireTransfer">Wire transfer</label>
                    </div>
                    <div>
                        <input type="checkbox" class="btn-check" id="check"
                            x-model="fundingForm.funding_methods.check">
                        <label class="btn btn-outline-secondary" for="check">Check</label>
                    </div>
                    @php
                        $missingStripeDetails = !$deal->achsettings?->stripe_customer_id || !$deal->achsettings?->stripe_account_id;
                    @endphp

                    <div x-data>
                        <input type="checkbox" class="btn-check" id="achPayment"
                            @change.prevent="
                            if ({{ $missingStripeDetails ? 'true' : 'false' }}) {
                                $el.checked = false;
                                window.location.href = '{{ route($prefix . '.deals.edit', $deal->id) }}?tab=ach-setting';
                            } else {
                                fundingForm.funding_methods.achPayment = $el.checked;
                            }
                        ">
                        <label class="btn btn-outline-secondary" for="achPayment">ACH payment</label>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Wire Transfer Details -->
    <h5 class="mb-3">Wire Transfer Details</h5>
    <div>
        <h5 class="text-primary mb-3">Account Details</h5>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label">Receiving bank</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" x-model="fundingForm.receiving_bank"
                    placeholder="Ask your sponsor">
                <template x-if="errors.receiving_bank">
                    <p class="text-danger" x-text="errors.receiving_bank"></p>
                </template>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label">Bank address</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" x-model="fundingForm.bank_address"
                    placeholder="Ask your sponsor">
                <template x-if="errors.bank_address">
                    <p class="text-danger" x-text="errors.bank_address"></p>
                </template>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label">Routing number</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" x-model="fundingForm.routing_no"
                    placeholder="Ask your sponsor">
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label">Account number</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" x-model="fundingForm.account_no"
                    placeholder="Ask your sponsor">
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label">Account type</label>
            <div class="col-sm-8">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="accountType" x-model="fundingForm.account_type"
                        id="checking" value="checking">
                    <label class="form-check-label" for="checking">Checking</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="accountType" x-model="fundingForm.account_type"
                        id="savings" value="savings">
                    <label class="form-check-label" for="savings">Savings</label>
                </div>
            </div>
        </div>
        <h5 class="text-primary mb-3">Beneficiary info</h5>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label">Beneficiary account name</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" x-model="fundingForm.beneficiary_account_name"
                    placeholder="Ask your sponsor">
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label">Beneficiary Address</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" x-model="fundingForm.beneficiary_address"
                    placeholder="Ask your sponsor">
            </div>
        </div>
        <h5 class="text-primary mb-3">Other info</h5>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label">Reference</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" x-model="fundingForm.reference_info">
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label">Other instructions</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" x-model="fundingForm.instruction_info">
            </div>
        </div>
    </div>

    <!-- Check Mailing -->
    <h4 class="col-sm-3">Instructions to mail a check</h4>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label">Mailing Address</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" x-model="fundingForm.mail_address">
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label">Beneficiary</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" x-model="fundingForm.mail_beneficiary">
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label">Beneficiary Address</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" x-model="fundingForm.mail_beneficiary_address">
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label">Other instructions</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" x-model="fundingForm.mail_instructions">
        </div>
    </div>
    <h4 class="col-sm-3">Investment fee details (advanced)</h4>
    <div class="mb-3 row">
        <label class="col-sm-3  col-form-label">Investment fee method</label>
        <div class="col-sm-8">
            <select type="text" class="form-control" x-model="fundingForm.investment_fee_type">
                <option value="">Select fee type</option>
                <option value="no_fee">No fee</option>
                <option value="amount">amount</option>
                <option value="percentage">Percentage</option>
            </select>
            <p>An additional fee charged to the investor upon funding the investment. This is not
                for use as an acquisition fee.</p>
        </div>
    </div>
    <div x-show="fundingForm.investment_fee_type === 'amount' || fundingForm.investment_fee_type === 'percentage'"
        class="mb-3 row" x-cloak>
        <label class="col-sm-3 col-form-label">Fee handling method</label>
        <div class="col-sm-8">
            <select type="text" class="form-control" id="investment_fee_method"
                x-model="fundingForm.investment_fee_method">
                <option value="investment_addition">In addition to investment amount(most common)</option>
                <option value="investment_included">Included in investment amount</option>
            </select>
            <p>Choose whether the LP's specified investment amount includes the fee or if the fee is added on top.</p>
        </div>
    </div>
    <h3 x-show="fundingForm.investment_fee_type === 'amount' || fundingForm.investment_fee_type === 'percentage'"
        x-cloak class="col-sm-3">New class investment fees</h3>
    <div x-show="fundingForm.investment_fee_type === 'amount' || fundingForm.investment_fee_type === 'percentage'"
        class="mb-3 row" x-cloak>
        <label class="col-sm-3 col-form-label">Fee amount</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" x-on:input="moneyFormat($el)" id="investment_fee_amount"
                x-model="fundingForm.investment_fee_amount">
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3" @click="submitFundingForm()">Save</button>
</div>
