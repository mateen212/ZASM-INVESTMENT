<template x-if="loading">
        <div class="custom-loader-overlay">
            <div class="custom-loader"></div>
        </div>
</template>

@php
    if(auth('admin')->user()->hasRole('partner')) {
        $prefix = 'partner';
    } else {
        $prefix = 'admin';
    }
@endphp
{{--  sender address modal --}}
<div class="deal-modal modal right fade" id="addSenderAddressModal" tabindex="-1"
    aria-labelledby="addSenderAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content px-2">
            <div class="modal-header row bg-primary text-white" style="height:80px;">
                <h5 class="modal-title col text-white">Add address</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Deal Form Body --}}
                <div>
                    @csrf
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company name</label>
                        <input type="text" id="company_name" name="company_name" class="form-select "
                          x-on:input="senderaddressErrors.company_name = ''"  x-model="addressForm.company_name" required>
                        <span x-text="senderaddressErrors.company_name" x-show="senderaddressErrors.company_name" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Country</label>
                        <select id="country" name="country" class="form-select " x-on:input="senderaddressErrors.country = ''" x-model="addressForm.country"
                            required>
                            <option value="usa">USA</option>
                            <option value="canada">Canada</option>
                        </select>
                        <span x-text="senderaddressErrors.country" x-show="senderaddressErrors.country" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label for="address_line_1" class="form-label">Address line 1</label>
                        <input type="text" id="address_line_1" name="address_line_1" class="form-select "
                           x-on:input="senderaddressErrors.address_line_1 = ''"   x-model="addressForm.address_line_1" required>
                        <span x-text="senderaddressErrors.address_line_1" x-show="senderaddressErrors.address_line_1" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label for="address_line_2" class="form-label">Address line 2</label>
                        <input type="text" id="address_line_2" name="address_line_2" class="form-select "
                            x-model="addressForm.address_line_2">
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" id="city" name="city" class="form-select "
                          x-on:input="senderaddressErrors.city = ''"  x-model="addressForm.city" required>
                        <span x-text="senderaddressErrors.city" x-show="senderaddressErrors.city" class="text-danger"></span>
                    </div>
                    <template x-if="addressForm.country === 'canada'">
                        <div class="mb-3">
                            <label for="province" class="form-label">Province</label>
                            <select id="province" name="province" class="form-select " x-on:input="senderaddressErrors.province = ''" x-model="addressForm.province"
                                required>
                                <option option="">Select province</option>
                                <option value="ab">AB</option>
                                <option value="bc">BC</option>
                                <option value="mb">MB</option>
                                <option value="nb">NB</option>
                                <option value="nl">NL</option>
                                <option value="ns">NS</option>
                                <option value="nt">NT</option>
                                <option value="nu">NU</option>
                                <option value="on">ON</option>
                                <option value="pe">PE</option>
                                <option value="qc">QC</option>
                                <option value="sk">SK</option>
                                <option value="yt">YT</option>
                                <option value="other">Other</option>
                            </select>
                        <span x-text="senderaddressErrors.province" x-show="senderaddressErrors.province" class="text-danger"></span>
                        </div>
                    </template>
                    <template x-if="addressForm.country === 'canada'">
                        <div class="mb-3">
                            <label for="postal_code" class="form-label">Postal code</label>
                            <input type="text" id="postal_code" name="postal_code" class="form-select "
                              x-on:input="senderaddressErrors.postal_code = ''"  x-model="addressForm.postal_code" required>
                        <span x-text="senderaddressErrors.postal_code" x-show="senderaddressErrors.postal_code" class="text-danger"></span>
                        </div>
                    </template>
                    <template x-if="addressForm.country === 'usa'">
                        <div class="mb-3">
                            <label for="state" class="form-label">State</label>
                            <select id="state" name="state" class="form-select " x-on:input="senderaddressErrors.state = ''" x-model="addressForm.state"
                                required>
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
                        <span x-text="senderaddressErrors.state" x-show="senderaddressErrors.state" class="text-danger"></span>
                        </div>
                    </template>
                    <template x-if="addressForm.country === 'usa'">
                        <div class="mb-3">
                            <label for="zip_code" class="form-label">Zip code</label>
                            <input type="text" id="zip_code" name="zip_code" class="form-select "
                               x-on:input="senderaddressErrors.zip_code = ''" x-model="addressForm.zip_code" required>
                        <span x-text="senderaddressErrors.zip_code" x-show="senderaddressErrors.zip_code" class="text-danger"></span>
                        </div>
                    </template>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <span class="btn btn-primary deal-save" @click="submitAddressForm(addressForm)">
                            Add Address
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{--  bank account modal  --}}
<div class="deal-modal modal right fade" id="addBankAccountModal" tabindex="-1"
    aria-labelledby="addBankAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content px-2">
            <div class="modal-header row bg-primary text-white" style="height:80px;">
                <h5 class="modal-title col text-white">Add bank account</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Deal Form Body --}}
                <div>
                    @csrf
                    <div class="mb-3">
                        <label for="account_nick_name" class="form-label">Account nickname</label>
                        <input type="text" id="account_nick_name" name="account_nick_name" class="form-select "
                          x-on:input="bankaccountErrors.account_nick_name = ''"  x-model="bankaccountForm.account_nick_name" required>
                        <span x-text="bankaccountErrors.account_nick_name" x-show="bankaccountErrors.account_nick_name" class="text-danger"></span>
                    </div>
                    <div class="mb-3">
                        <label for="account_type" class="form-label">Account type</label>
                        <select id="account_type" name="account_type" class="form-select "
                            x-on:input="bankaccountErrors.account_type = ''" x-model="bankaccountForm.account_type" required>
                            <option value="">Select account type</option>
                            <option value="checking">Checking</option>
                            <option value="savings">Savings</option>
                        </select>
                        <span x-text="bankaccountErrors.account_type" x-show="bankaccountErrors.account_type" class="text-danger"></span>
                    </div>
                    <div x-data="routingValidation()">
                        <div class="mb-3">
                            <label for="ach_account_number" class="form-label">ACH Routing Number</label>
                            <input 
                                type="number" 
                                id="ach_account_number" 
                                name="ach_account_number" 
                                class="form-select" 
                                x-on:input="bankaccountErrors.ach_account_number = ''"
                                x-model="bankaccountForm.ach_account_number" 
                                @input="validate()" 
                                maxlength="9"
                                oninput="if(this.value.length > 9) this.value = this.value.slice(0, 9)"
                                required>
                            <div x-show="routingNumberError" class="text-danger" style="display: none;">
                                Routing number must be exactly 9 digits.
                            </div>
                            <div x-show="invalidRoutingNumberError" class="text-danger" style="display: none;">
                                Invalid routing number. Please enter a valid one.
                            </div>
                            <span x-text="bankaccountErrors.ach_account_number" x-show="bankaccountErrors.ach_account_number" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="account_number" class="form-label">Account number</label>
                        <input type="number" id="account_number" name="account_number" class="form-select" x-on:input="bankaccountErrors.account_number = ''" 
                          x-model="bankaccountForm.account_number"  oninput="validateAccountNumber()" required>
                        <span x-text="bankaccountErrors.account_number" x-show="bankaccountErrors.account_number" class="text-danger"></span>
                        <div id="account_number_error" class="text-danger" style="display: none;">Account number must be between 4 and 17 digits.</div>
                    </div>
                    <div class="mb-3">
                        <label for="account_number_again" class="form-label">Account number (again)</label>
                        <input type="number" id="account_number_again" name="account_number_again" class="form-select" 
                          x-on:input="bankaccountErrors.account_number_again = ''"  oninput="validateAccountNumber()" required>
                        <span x-text="bankaccountErrors.account_number_again" x-show="bankaccountErrors.account_number_again" class="text-danger"></span>
                        <div id="account_number_again_error" class="text-danger" style="display: none;">Account numbers do not match.</div>
                    </div>
                    <div class="mb-3">
                        <label for="check_signature" class="form-label">Check signature</label>
                        <input type="text" id="check_signature" name="check_signature" class="form-select "
                          x-on:input="bankaccountErrors.check_signature = ''"  x-model="bankaccountForm.check_signature" required>
                        <span x-text="bankaccountErrors.check_signature" x-show="bankaccountErrors.check_signature" class="text-danger"></span>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <span class="btn btn-primary deal-save" @click="submitBankaccountForm(bankaccountForm)">
                            Add Bank Account
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function check_setting_data() {
        return {
            loading: false,
            checksettingErrors: {},
            senderaddressErrors: {},
            bankaccountErrors: {},
            checksettingForm: {
                _token: csrf,
                senderAddress: "{{ $deal->settings->senderAddress }}",
                bankAccount: "{{ $deal->settings->bankAccount }}",
            },
            addressForm: {
                _token: csrf,
                deal_id: "{{$deal->id}}",
                company_name: '',
                country: 'usa',
                address_line_1: '',
                address_line_2: '',
                city: '',
                province: '',
                postal_code: '',
                state: '',
                zip_code: '',
            },
            bankaccountForm: {
                _token: csrf,
                account_nick_name: '',
                account_type: '',
                ach_account_number: '',
                account_number: '',
                account_number_again: '',
                check_signature: '',
            },
            
            
            changeSenderAddress() {
                if (this.checksettingForm.senderAddress == 'add') {
                    $('#addSenderAddressModal').modal('show');
                    this.checksettingForm.senderAddress = '';
                }
            },
            changeBankAccount() {
                if (this.checksettingForm.bankAccount == 'add') {
                    $('#addBankAccountModal').modal('show');
                    this.checksettingForm.bankAccount = '';
                }
            },

            async submitAddressForm(data) {
                this.loading = true;
                let url = "{{ route($prefix . '.deals.storesenderaddress', $deal->id) }}";

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
                        this.senderaddressErrors = responseData.errors;
                        return;
                    }

                    const responseData = await response.json();
                    if (response.status === 200) {
                        this.senderaddresses = responseData.senderaddresses;
                        const modalElement = document.querySelector('.modal.show');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        modalInstance.hide();
                        cosyAlert('<strong>Success</strong><br />Address Added Successfully!', 'success');

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

            async submitBankaccountForm(data) {
                this.loading = true;
                let url = "{{ route($prefix . '.deals.storebankaccount', $deal->id) }}";

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
                        this.bankaccountErrors = responseData.errors;
                        return;
                    }

                    const responseData = await response.json();
                    if (response.status === 200) {
                        this.storebankaccounts = responseData.storebankaccounts;
                        const modalElement = document.querySelector('.modal.show');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        modalInstance.hide();
                        cosyAlert('<strong>Success</strong><br />bank Accounted Added Successfully!', 'success');

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

            async submitchecksettingForm() {
                this.loading = true;
                let url = "{{ route($prefix . '.deals.storeSetting', $deal->id) }}";

                try {

                   
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type':'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify(this.checksettingForm)
                    });
                   // console.log(response)
                    this.loading = false;

                    if (response.status === 422) {
                        const responseData = await response.json();
                        // update errors in alpine data
                        this.checksettingErrors = responseData.errors;
                        return;
                    }

                    const responseData = await response.json();
                    if (response.status === 200) {
                        this.settings = responseData.settings;
                        
                        cosyAlert('<strong>Success</strong><br />Address Added Successfully!', 'success');

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
        };
    }
</script>
<script>
    function validateAccountNumber() {
        const accountNumber = document.getElementById("account_number").value.trim();
        const accountNumberAgain = document.getElementById("account_number_again").value.trim();

        const accountNumberError = document.getElementById("account_number_error");
        const accountNumberAgainError = document.getElementById("account_number_again_error");

        // Reset error messages
        accountNumberError.style.display = "none";
        accountNumberAgainError.style.display = "none";

        // Check if account number length is valid
        if (accountNumber.length < 4 || accountNumber.length > 17) {
            accountNumberError.style.display = "block";
        }

        // Check if account numbers match
        if (accountNumber !== accountNumberAgain && accountNumberAgain !== "") {
            accountNumberAgainError.style.display = "block";
        }
    }

    function routingValidation() {
        return {
            bankaccountForm:{

            ach_account_number: '',
            },
            routingNumberError: false,
            invalidRoutingNumberError: false,

            validate() {
                // Reset errors
                this.routingNumberError = false;
                this.invalidRoutingNumberError = false;

                // Check if the routing number has exactly 9 digits
                if (this.bankaccountForm.ach_account_number.length !== 9) {
                    this.routingNumberError = true;
                    return; // Exit early if the length is incorrect
                }

                // Validate the routing number using the checksum algorithm
                if (!this.isValidRoutingNumber(this.bankaccountForm.ach_account_number)) {
                    this.invalidRoutingNumberError = true;
                }
            },

            isValidRoutingNumber(routingNumber) {
                // ABA Routing Number Checksum Algorithm
                const digits = routingNumber.split('').map(Number);
                if (digits.length !== 9) return false;

                const checksum = 
                    (3 * (digits[0] + digits[3] + digits[6])) +
                    (7 * (digits[1] + digits[4] + digits[7])) +
                    (digits[2] + digits[5] + digits[8]);

                return checksum % 10 === 0;
            }
        };
    }
    
</script>
