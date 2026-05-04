    <div class="container mt-4">
            <!-- Deal Members -->
        <h3 style="font-size: large;" class="fw-bolder mb-4">Deal Members</h3>    
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 fw-bold" style="font-size: medium;">
                    Allow Co-Sponsors to View All Deal Investors' Information
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Allow co-sponsors to view all deal investors' information. Turning this off means co-sponsors can only view their own investors' information.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="co_sponser_investor_info" :value="true" id="co_sponser_investor_info" x-model="adminSettingForm.co_sponser_investor_info">
                        <label class="form-check-label" for="co_sponser_investor_info">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="co_sponser_investor_info" :value="false" id="co_sponser_investor_info" x-model="adminSettingForm.co_sponser_investor_info">
                        <label class="form-check-label" for="co_sponser_investor_info">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Allow Co-Sponsors to View the Deal "Members" Tab
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Allow co-sponsors to view other sponsors of the deal, and how much they have raised.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="co_sponser_member_tab" id="co_sponser_member_tab" :value="true" x-model="adminSettingForm.co_sponser_member_tab">
                        <label class="form-check-label" for="co_sponser_member_tab">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="co_sponser_member_tab" id="co_sponser_member_tab" :value="false"  checked x-model="adminSettingForm.co_sponser_member_tab">
                        <label class="form-check-label" for="co_sponser_member_tab">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Copy lead sponsor on investment emails
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Send a copy of all the investment related emails to the lead sponsor.">
                            ?
                        </span>
                    <p style="font-size: small;" class="text-muted">Note: If the sponsor has chosen to hide their investors' email addresses, the lead sponsor will not receive a copy of these emails.</p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <select id="lead_sponser_investment" class="form-select ms-3 custom-dropdown" x-model="adminSettingForm.lead_sponser_investment">
                            <option value="cc">CC</option>
                            <option value="bcc">BCC</option>
                            <option value="off" selected>Off</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Copy lead sponsor on distribution emails
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Send a copy of all the distribution related emails to the lead sponsor.">
                            ?
                        </span>
                    <p style="font-size: small;" class="text-muted">Note: If the sponsor has chosen to hide their investors' email addresses, the lead sponsor will not receive a copy of these emails.</p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <select id="lead_sponser_distribution" class="form-select ms-3 custom-dropdown" x-model="adminSettingForm.lead_sponser_distribution">
                            <option value="cc">CC</option>
                            <option value="bcc">BCC</option>
                            <option value="off" selected>Off</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Send lead sponsor investment details updates
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Lead sponsor will receive investment update emails for all investors in this deal, instead of just their own investors.">
                            ?
                        </span>
                    <p style="font-size: small;" class="text-muted">Note: This can only be toggled if investment detail updates are enabled in your<br><a href="#">email subscription settings.</a></p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="lead_sponser_investment_update" id="lead_sponser_investment_update" :value="true" x-model="adminSettingForm.lead_sponser_investment_update">
                        <label class="form-check-label" for="lead_sponser_investment_update">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="lead_sponser_investment_update" id="lead_sponser_investment_update" :value="false" checked x-model="adminSettingForm.lead_sponser_investment_update">
                        <label class="form-check-label" for="coSponsorMembersNo">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Show {{ $deal->sponsor ?? '' }} branding on co-sponsor portals
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="If an LP views this deal on a co-sponsor's portal, show qwwe's logo as well as their logo.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="co_sponser_portal" id="co_sponser_portal" :value="true" x-model="adminSettingForm.co_sponser_portal">
                        <label class="form-check-label" for="co_sponser_portal">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="co_sponser_portal" id="co_sponser_portal" :value="false" checked x-model="adminSettingForm.co_sponser_portal">
                        <label class="form-check-label" for="co_sponser_portal">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Send sponsors billing notification emails
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Send email notifications when invoices are upcoming, paid, overdue, or if a payment fails.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <select id="sponsers_billing_notification" class="form-select ms-3 custom-dropdown" x-model="adminSettingForm.sponsers_billing_notification">
                        
                            <option value="only_lead_gps">Only Lead GPs</option>
                            <option value="lead_and_admin_gps" selected>Lead and Admin GPs</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Equity raising -->
        <h3 style="font-size: large;" class="mt-4 mb-4 fw-bolder">Equity raising</h3>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Allow LPs to increase their investment in these classes
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="LPs can request to increase their investments if they invested into these classes.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center" style="width: 25rem;">
                    <div class="form-check form-check-inline" style="width: 25rem;">
                        <select id="equity_increase_class" class="form-select js-example-basic-multiple" name="equity_increase_class[]" multiple="multiple">
                            @foreach($deal->classes as $class)
                                <option value="{{ $class->id }}">{{ $class->equity_class_name }}</option>
                            @endforeach 
                        </select>
                    </div>
                </div>
            </div>
            <template x-if="settingClasses > 0">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                        Minimum increase amount
                    </span>
                    <div class="d-flex align-items-center" style="width: 25rem;">
                        <div class="form-check form-check-inline" style="width: 25rem;">
                            <input type="text" x-on:input="moneyFormat($el)" id="min_amount" name="min_amount" class="form-control" x-model="adminSettingForm.min_amount">
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="settingClasses > 0">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                        Maximum increase amount
                    </span>
                    <div class="d-flex align-items-center" style="width: 25rem;">
                        <div class="form-check form-check-inline" style="width: 25rem;">
                        <input type="text" x-on:input="moneyFormat($el)" id="max_amount" name="max_amount" class="form-control" x-model="adminSettingForm.max_amount">
                        </div>
                    </div>
                </div>
            </template>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Investments must be in increments of the price per unit
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="LPs must choose an investment amount that is a multiple of the class’s price per unit. For example, if the price per unit is $16K, LPs can invest $16K, $32K, etc">
                            ?
                        </span>
                    <p style="font-size: small;" class="text-muted">Note: this requires classes to have a price per unit.</p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="equity_investment_increment" id="equity_investment_increment" :value="true" x-model="adminSettingForm.equity_investment_increment">
                        <label class="form-check-label" for="equity_investment_increment">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="equity_investment_increment" id="equity_investment_increment" :value="false" checked x-model="adminSettingForm.equity_investment_increment">
                        <label class="form-check-label" for="equity_investment_increment">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Funds must be received before GP countersigns
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Show the 'Send funding instructions' & 'Confirm funds received' buttons on the list of investors before their subscription documents have been countersigned.">
                            ?
                        </span>
                    <p style="font-size: small;" class="text-muted">Note: This can be toggled only when no investment has been created in this deal.</p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="equity_funds_recieved" id="equity_funds_recieved" :value="true" x-model="adminSettingForm.equity_funds_recieved">
                        <label class="form-check-label" for="equity_funds_recieved">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="equity_funds_recieved" id="equity_funds_recieved" :value="false"  checked x-model="adminSettingForm.equity_funds_recieved">
                        <label class="form-check-label" for="equity_funds_recieved">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Automatically send funding instructions after GP countersigns
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Send the investor funding instructions as soon as their subscription documents have been countersigned.">
                            ?
                        </span>
                    <p style="font-size: small;" class="text-muted">Note: This can only be toggled if investors are not required to complete their wire<br>transfer before countersigning.</p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="equity_funds_instruction" id="equity_funds_instruction" :value="true" x-model="adminSettingForm.equity_funds_instruction">
                        <label class="form-check-label" for="equity_funds_instruction">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="equity_funds_instruction" id="equity_funds_instruction" :value="false" checked x-model="adminSettingForm.equity_funds_instruction">
                        <label class="form-check-label" for="equity_funds_instruction">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Show funding instructions after LP signs
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="After LP signs subscription documents, they will be directed to view the funding instructions on the portal">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="coSponsorMembersTab" id="equity_funds_show_instruction" :value="true" x-model="adminSettingForm.equity_funds_show_instruction">
                        <label class="form-check-label" for="equity_funds_show_instruction">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="coSponsorMembersTab" id="equity_funds_show_instruction" :value="false"  checked x-model="adminSettingForm.equity_funds_show_instruction">
                        <label class="form-check-label" for="equity_funds_show_instruction">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Send sponsors investment notification emails
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Send email notifications when soft commitments are created, new investments are created, or investors sign subscription documents or addendums.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <select id="equity_sponser_email" class="form-select ms-3 custom-dropdown" x-model="adminSettingForm.equity_sponser_email">
                            <option value="off" selected> Off</option>
                            <option value="lead_sponsor_only" >To lead sponser only</option>
                            <option value="lead_and_admin_sponsor" >To lead & admin sponser</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Require ACH details
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Investors will be required to input bank account information for receiving ACH distribution regardless of their preferred distribution method. For this to be required in the investment funnel, the 'Require complete investor profile' option must be enabled for that offering.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="equity_ach_details" id="equity_ach_details" :value="true" x-model="adminSettingForm.equity_ach_details">
                        <label class="form-check-label" for="equity_ach_details">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="equity_ach_details" id="equity_ach_details" :value="false"  checked x-model="adminSettingForm.equity_ach_details">
                        <label class="form-check-label" for="equity_ach_details">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Require GP approval after LP signs
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Once the LP signs their subscription documents, the GP must review and approve their investment before they can proceed. If the investment is waitlisted, approving the investment will also take it off the waitlist.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="equity_gp_approval" id="equity_gp_approval" :value="true" x-model="adminSettingForm.equity_gp_approval">
                        <label class="form-check-label" for="equity_gp_approval">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="equity_gp_approval" id="equity_gp_approval" :value="false"  checked x-model="adminSettingForm.equity_gp_approval">
                        <label class="form-check-label" for="equity_gp_approval">No (most common)</label>
                    </div>
                </div>
            </div>
            <!-- LP Metrics -->
        <h3 style="font-size: large;" class=" fw-bolder mt-4 mb-4">LP metrics</h3>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 fw-bold" style="font-size: medium;">
                    Show investors their ownership percentage
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Allow investors to view what percentage of the deal their investment constitutes.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_ownership_percentage" id="metric_ownership_percentage" :value="true"  x-model="adminSettingForm.metric_ownership_percentage">
                        <label class="form-check-label" for="metric_ownership_percentage">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_ownership_percentage" id="metric_ownership_percentage" :value="false"  checked x-model="adminSettingForm.metric_ownership_percentage">
                        <label class="form-check-label" for="metric_ownership_percentage">No</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Show investors how many shares they own
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Calculated as investment amount divided by class share or unit price.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_investors_share" id="metric_investors_share" :value="true" x-model="adminSettingForm.metric_investors_share">
                        <label class="form-check-label" for="metric_investors_share">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_investors_share" id="metric_investors_share" :value="false" checked x-model="adminSettingForm.metric_investors_share">
                        <label class="form-check-label" for="metric_investors_share">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Show investors their CoC over the last 12 months
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_show_coc" id="metric_show_coc" :value="true" x-model="adminSettingForm.metric_show_coc">
                        <label class="form-check-label" for="metric_show_coc">Yes (most common)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_show_coc" id="metric_show_coc"  :value="false" checked x-model="adminSettingForm.metric_show_coc">
                        <label class="form-check-label" for="metric_show_coc">No</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Show investors their IRR, equity multiple and annualized return<br> after this deal is liquidated
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_investor_liquid" id="metric_investor_liquid" :value="true" x-model="adminSettingForm.metric_investor_liquid">
                        <label class="form-check-label" for="metric_investor_liquid">Yes (most common)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="coSponsorMembersTab" id="metric_investor_liquid" :value="false"  checked x-model="adminSettingForm.metric_investor_liquid">
                        <label class="form-check-label" for="metric_investor_liquid">No</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Show investors their capital balance
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="LPs can see their capital balance. Calculated as the sum of all capital transactions that count toward the capital balance.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_capital_balance" id="metric_capital_balance" :value="true" x-model="adminSettingForm.metric_capital_balance">
                        <label class="form-check-label" for="coSponsorMembersYes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_capital_balance" id="metric_capital_balance" :value="false" checked x-model="adminSettingForm.metric_capital_balance">
                        <label class="form-check-label" for="coSponsorMembersNo">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Show investors their accrued interest and preferred return
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="LPs can see how much pref and/or interest they have accumulated. Investments start accruing pref on the investment's distribution start date. If this is not set, the preferred return start date of the investment's selected class will be used instead. Additionally, if the class has no interest rate set, interest will be calculated as $0.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_investor_return" id="metric_investor_return" :value="true" x-model="adminSettingForm.metric_investor_return">
                        <label class="form-check-label" for="metric_investor_return">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_investor_return" id="metric_investor_return" :value="false" checked x-model="adminSettingForm.metric_investor_return">
                        <label class="form-check-label" for="metric_investor_return">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Show investors their cash balance
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="LPs can see their cash balance. Calculated as the sum of all capital transactions that are not deployed.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_investor_cash_balance" id="metric_investor_cash_balance" :value="true" x-model="adminSettingForm.metric_investor_cash_balance">
                        <label class="form-check-label" for="metric_investor_cash_balance">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="metric_investor_cash_balance" id="metric_investor_cash_balance" :value="false"  checked x-model="adminSettingForm.metric_investor_cash_balance">
                        <label class="form-check-label" for="metric_investor_cash_balance">No (most common)</label>
                    </div>
                </div>
            </div>
            <!-- Distributions -->
        <h3 style="font-size: large;" class="fw-bolder mt-4 mb-4">Distributions</h3>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Investment preferred return start date default
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="The investment's preferred return start date will default to the selected value. This default is overriden by the investment's set preferred return start date. If the default value is not set, then the investment's date placed will be used.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <select id="distribution_investment_return" class="form-select ms-3 custom-dropdown" x-model="adminSettingForm.distribution_investment_return">
                            <option value="preferred_return_date" selected>Class preferred return start date</option>
                            <option value="received_funds" >Investment funds received date</option>
                            <option value="placed_funds" >Investment date placed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Allow distribution reinvestment
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Allow GPs to mark an investment's distribution preference as 'reinvest'. This can be done when editing an investment, and distributions can be marked as reinvested when enabled.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="distribution_reinvestment" id="distribution_reinvestment" :value="true" x-model="adminSettingForm.distribution_reinvestment">
                        <label class="form-check-label" for="distribution_reinvestment">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="distribution_reinvestment" id="distribution_reinvestment" :value="false" checked x-model="adminSettingForm.distribution_reinvestment">
                        <label class="form-check-label" for="distribution_reinvestment">No (most common)</label>
                    </div>
                </div>
            </div>
            <div class="mb-3 d-flex align-items-center justify-content-between">
                <span class="mr-3 mt-4 fw-bold" style="font-size: medium;">
                    Enable tax withholding percentage on distributions
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Allow GPs to set an investment's tax withholding percentage. This can be done when editing an investment, and distributions can be marked as automatically split and withheld based on this percentage.">
                            ?
                        </span>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="distribution_tax_percentage" id="distribution_tax_percentage" :value="true" checked x-model="adminSettingForm.distribution_tax_percentage">
                        <label class="form-check-label" for="distribution_tax_percentage">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="distribution_tax_percentage" id="distribution_tax_percentage" :value="false"  checked checked x-model="adminSettingForm.distribution_tax_percentage">
                        <label class="form-check-label" for="distribution_tax_percentage">No (most common)</label>
                    </div>
                </div>                    
            </div>
        <button type="submit" class="btn btn-primary mt-3" @click="submitAdminSettingForm()">Save</button>
    </div>