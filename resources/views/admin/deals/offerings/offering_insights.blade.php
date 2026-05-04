<div class="">
    <!-- Property Manager Section -->
    <div class="mb-4">
        <label class="fw-bold d-block mb-2">Property Manager</label>
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="propertyManagerName" class="form-label mb-0">Name</label>
                <input type="text" class="form-control w-75" id="propertyManagerName" placeholder="Enter property manager's name"
                    x-model="insightForm.property_manager_name"
                >
            </div>
        </div>
    </div>

    <!-- Sponsorship Team Section -->
    <div class="mb-4">
        <label class="fw-bold d-block mb-2">Sponsorship Team</label>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="fullCycleDeals" class="form-label mb-0">Full-cycle deals</label>
                <input type="number" class="form-control w-75" id="fullCycleDeals" placeholder="deal(s)" x-model="insightForm.full_cycle_deals">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">IRRs of full-cycle deals %</label>
                <input type="text" class="form-control w-75" id="irrFullCycleDeals" x-on:input="percentFormat($el)" placeholder="E.g. 12%, 13%, 14%" x-model="insightForm.irr_full_cycle_deals">
            </div>
        </div>
    </div>

    <!-- Market Section -->
    
    <div class="mb-4">
        <label class="fw-bold d-block mb-2">Market</label>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="fullCycleDeals" class="form-label mb-0">1-mile radius median income</label>
                <input type="text" class="form-control w-75" x-on:input="moneyFormat($el)" id="fullCycleDeals" placeholder="Enter 3-mile radius median income" x-model="insightForm.one_mile_median_income">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">3-mile radius median income</label>
                <input type="text" class="form-control w-75" x-on:input="moneyFormat($el)" id="irrFullCycleDeals" placeholder="Enter 3-mile radius median income" x-model="insightForm.three_mile_median_income">
            </div>
        </div>
    </div>
        
    

    <!-- Debt Financing Section -->
    <div class="mb-4">
        <label class="fw-bold d-block mb-2">Debt financing</label>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="fullCycleDeals" class="form-label mb-0">Financing type</label>
                <select class="form-control w-75" id="fullCycleDeals" x-model="insightForm.financing_type">
                    <option value="agency">Agency</option>
                    <option value="bridge">Bridge</option>
                    <option value="bridge_fix_rate">Bridge - Fix rate</option>
                    <option value="CMBS">CMBS</option>
                    <option value="cash">Cash</option>
                    <option value="construction">Construction</option>
                    <option value="conventional">Conventional</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">Loan-to-value %</label>
                <input type="text" class="form-control w-75"  id="irrFullCycleDeals" x-on:input="percentFormat($el)" placeholder="%" x-model="insightForm.loan_to_value">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">Interest rate %</label>
                <input type="text" class="form-control w-75" id="irrFullCycleDeals" x-on:input="percentFormat($el)" placeholder="%" x-model="insightForm.interest_rate">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">Loan term (years)</label>
                <input type="text" x-mask="9999" class="form-control w-75" id="irrFullCycleDeals" placeholder="year(s)" x-model="insightForm.loan_term">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">Loan assumption</label>
                <select class="form-control w-75" id="irrFullCycleDeals" x-model="insightForm.loan_assumption">
                    <option value="true">Assumed</option>
                    <option value="false">Not assumed</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">Interest-only period (years)</label>
                <input type="text" x-mask="9999" class="form-control w-75" id="irrFullCycleDeals" placeholder="year(s)" x-model="insightForm.interest_only_period">
            </div>
        </div>
    </div>

    <!-- Terms and Fees Section -->
    <div class="mb-4">
        <label class="fw-bold d-block mb-2">Debt financing</label>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="fullCycleDeals" class="form-label mb-0">Acquisition fee %</label>
                <input type="text" class="form-control w-75"  id="fullCycleDeals" x-on:input="percentFormat($el)" placeholder="%" x-model="insightForm.acquisition_fee">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">Asset management fee %</label>
                <input type="text" class="form-control w-75"  id="irrFullCycleDeals" x-on:input="percentFormat($el)" placeholder="%" x-model="insightForm.asset_management_fee">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">Construction management fee %</label>
                <input type="text" class="form-control w-75"  id="irrFullCycleDeals" x-on:input="percentFormat($el)" placeholder="%" x-model="insightForm.construction_management_fee">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">Disposition fee %</label>
                <input type="text" class="form-control w-75"  id="irrFullCycleDeals" x-on:input="percentFormat($el)" placeholder="%" x-model="insightForm.disposition_fee">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">Refinance fee %</label>
                <input type="text" class="form-control w-75"  id="irrFullCycleDeals" x-on:input="percentFormat($el)" placeholder="%" x-model="insightForm.refinance_fee">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <label for="irrFullCycleDeals" class="form-label mb-0">Profit sharing</label>
                <select class="form-control w-75" id="irrFullCycleDeals" x-model="insightForm.profit_sharing">
                    <option value="straight_split">Straight Split</option>
                    <option value="waterfall">Waterfall</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3" @click="submitInsightForm()">Save</button>
    </div>

</div>