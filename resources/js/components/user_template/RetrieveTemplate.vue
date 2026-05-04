<template>
  <div class="container my-4">

    <div v-if="investmentSuccess" class="container">
      <div class="congrats-container">
        <div class="sparkles">✨ ✨ ✨</div>
        <div class="check-icon">✔</div>
        <h1 class="mt-4">Congratulations!</h1>
        <p class="mt-3">
          You have updated a commitment in <strong>{{ offering.name }}</strong>.
        </p>
        <p>Once the offering is open to investments, you can easily turn your commitment into an investment.</p>
        <router-link class="btn btn-primary btn-view-offering"
          :to="{ name: 'user.offerings.offering', params: { id: offering.id } }">
          View Offering
        </router-link>
      </div>
    </div>
    <div v-if="loading" class="custom-loader-overlay">
      <div class="custom-loader"></div>
    </div>
    <div v-else class="row">
      <h2 class="fw-bold">Invest in {{ offering.name }}</h2>
      <hr />

      <!-- Step Circles and Dividers -->
      <div class="divider_body">
        <div v-for="(step, index) in steps" :key="step.id" class="text-center flex flex-col items-center">
          <div class="step-circle" :class="[
            step.status,
            { green: completedSteps.includes(step.id) },
          ]">
          </div>
          <div class="step-text">{{ step.label }}</div>
        </div>
      </div>

      <div class="col-md-8">
        <!-- Investor Section -->
        <div class="content-section" :class="{ active: currentStep === 'InvestorMethod' }">
          <h4 class="mb-3">1. Investor</h4>
          <p class="mb-3">
            Select the profile (investment entity) to invest as, and choose the investment class to invest
            in.
          </p>
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label">
              Profile <span class="text-danger">*</span>
            </label>
            <div class="col-md-9">
              <select class="form-select" v-model="investorForm.investor_profile_id" @change="changeProfile">

                <option value="" disabled>Select a profile</option>
                <div v-for="profile in profiles" :key="profile.id" :value="profile.id">
                  <option :value="profile.id">{{ profile.profile_fname }}</option>
                </div>
                <option value="view">+ Create new profile</option>
              </select>
            </div>
          </div>
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label">
              Investment Class <span class="text-danger">*</span>
            </label>
            <div class="col-md-9">
              <select class="form-select" v-model="investorForm.deal_class_id">
                <option value="" disabled>Select a class</option>
                <option v-for="classItem in offering.classes" :key="classItem.id" :value="classItem.id">
                  {{ classItem.equity_class_name }}
                </option>
              </select>
            </div>

          </div>
          <button type="submit" @click="submitInvestorForm" class="btn btn-primary mt-3"
            :disabled="!investorForm.investor_profile_id || !investorForm.deal_class_id">
            Next
          </button>
        </div>

        <!-- Investment Section -->
        <div class="content-section" :class="{ active: currentStep === 'InvestmentMethod' }">
          <h4 class="mb-3">2. Investment</h4>
          <p class="mb-3">Input the amount you would like to invest and the method you will use.</p>
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label">
              Investment amount <span class="text-danger">*</span>
            </label>
            <div class="col-md-9">
              <input type="text" @input="moneyFormat($event.target)" id="investment_amount" name="investment_amount"
                class="form-control" placeholder="$0" v-model="investmentForm.investment_amount" required />
              <p style="font-size: small" class="text-muted">Minimum is $50,000</p>
            </div>
          </div>
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label">
              Funding method <span class="text-danger">*</span>
            </label>
            <div class="col-md-9">
              <select class="form-select" v-model="investmentForm.funding_method">
                <option value="" disabled>Select a method</option>
                <option v-for="method in fundingMethods" :key="method" :value="method">
                  {{ camelCaseToTitle(method) }}
                </option>
              </select>
            </div>
          </div>
          <div class="d-flex mt-3">
            <button type="button" @click="showStep('InvestorMethod')" class="btn btn-secondary me-3">
              Previous
            </button>
            <button type="submit" @click="submitInvestment" class="btn btn-primary"
              :disabled="!investmentForm.investment_amount || !investmentForm.funding_method">
              Next
            </button>
          </div>
        </div>

        <!-- Questionnaire Section -->
        <div class="content-section" :class="{ active: currentStep === 'QuestionnaireW9FormMethod' }">
          <h4 class="mb-3">3.1 Questionnaire</h4>
          <p class="mb-3">
            Please complete the investor suitability questionnaire below. This is a requirement from the SEC
            to collect basic information.
          </p>
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label">
              Questionnaire <span class="text-danger">*</span>
            </label>
            <div class="col-md-9">
              <select class="form-select" v-model="investmentForm.questionnaire_id" @change="changeQuestionnaire">
                <option value="" disabled>Select a profile</option>
                <div v-for="questionnaire in questionnaires" :key="questionnaire.id" :value="questionnaire.id">
                  <option :value="questionnaire.id">{{ questionnaire.first_name }} {{ questionnaire.last_name }}
                  </option>
                </div>
                <option value="view">+ Add questionnaire</option>
              </select>
            </div>
          </div>
          <h4 class="mb-3">3.2 W-9 form</h4>
          <p class="mb-3">
            Please complete the W-9 form below. This is a requirement from the IRS to collect taxpayer
            information.
          </p>
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label">
              W-9 form <span class="text-danger">*</span>
            </label>
            <div class="col-md-9">
              <select class="form-control not-allowed" v-model="investmentForm.w9_form" @change="changeW9form">
                <option value="" disabled>Select a W-9 form</option>
                <option v-for="profile in profiles" :key="profile.id" :value="profile.id">
                  {{ `${profile.profile_fname} ${profile.profile_lname} W-9 Form` }}
                </option>
                <option value="form">select</option>
              </select>
            </div>
          </div>
          <div class="d-flex mt-3">
            <button type="button" @click="showStep('InvestmentMethod')" class="btn btn-secondary me-3">
              Previous
            </button>
            <button type="submit" @click="submitQuestionnaire" class="btn btn-primary"
              :disabled="!investmentForm.questionnaire_id || !investmentForm.w9_form">
              Next
            </button>
          </div>
        </div>

        <!-- W-9 Form Section -->
        <div class="content-section" :class="{ active: currentStep === 'W9FormMethod' }">
          <h4 class="mb-3">3.2 W-9 form</h4>
          <p class="mb-3">
            Please complete the W-9 form below. This is a requirement from the IRS to collect taxpayer
            information.
          </p>
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label">
              W-9 form <span class="text-danger">*</span>
            </label>
            <div class="col-md-9">
              <select class="form-control not-allowed" v-model="investmentForm.w9_form" @change="changeW9form">
                <option value="" disabled>Select a W-9 form</option>
                <option v-for="profile in profiles" :key="profile.id" :value="profile.id">
                  {{ `${profile.profile_fname} ${profile.profile_lname} W-9 Form` }}
                </option>
                <option value="form">select</option>
              </select>
            </div>
          </div>
          <div class="d-flex mt-3">
            <button type="button" @click="showStep('InvestmentMethod')" class="btn btn-secondary me-3">
              Previous
            </button>
            <button type="submit" @click="submitQuestionnairew9" class="btn btn-primary"
              :disabled="!investmentForm.w9_form">
              Next
            </button>
          </div>
        </div>

        <!-- Questionnaire Only Section -->
        <div class="content-section" :class="{ active: currentStep === 'QuestionnaireMethod' }">
          <h4 class="mb-3">3.1 Questionnaire</h4>
          <p class="mb-3">
            Please complete the investor suitability questionnaire below. This is a requirement from the SEC
            to collect basic information.
          </p>
          <div class="mb-3 row">
            <label class="col-md-3 col-form-label">
              Questionnaire <span class="text-danger">*</span>
            </label>
            <div class="col-md-9">
              <select class="form-select" v-model="investmentForm.questionnaire_id" @change="changeQuestionnaire">
                <option value="" disabled>Select a profile</option>
                <option v-for="questionnaire in questionnaires" :key="questionnaire.id" :value="questionnaire.id">
                  {{ questionnaire.first_name }}
                </option>
                <option value="view">+ Add questionnaire</option>
              </select>
            </div>
          </div>
          <div class="d-flex mt-3">
            <button type="button" @click="showStep('InvestmentMethod')" class="btn btn-secondary me-3">
              Previous
            </button>
            <button type="submit" @click="submitQuestionnaires" class="btn btn-primary"
              :disabled="!investmentForm.questionnaire_id">
              Next
            </button>
          </div>
        </div>

        <!-- E-Signature Section -->
        <div class="content-section" :class="{ active: currentStep === 'E_signatureMethod' }">
          <h4 class="mb-3">4. E-Signature</h4>
          <p class="mb-3">To invest in this offering, please sign this document.</p>
          <p class="mb-3">It is recommended to disable your browser's autofill while signing.<br>Some
            fields
            are pre-populated with information from the previous step. <a href="#" class="text-primary">Learn more</a>
          </p>
          <div class="border p-4">
            <button class="btn btn-primary" id="add-signature" style="
                  border: none;
                  border-radius: 10px;
                  padding: 12px 12px;
                  font-weight: bold;
                  font-size: 16px;
                  color: #fff;
                  box-shadow: 0 4px 6px rgba(0, 123, 255, 0.4);
                  transition: all 0.3s ease;
                  cursor: pointer;
              " @click="openSignatureModal">
              Add Signature
            </button>
          </div>
          <div class="d-flex mt-3">
            <button type="button" @click="submit_e_sign_previous" class="btn btn-secondary me-3">
              Previous
            </button>
            <button type="submit" @click="submitEsignature" class="btn btn-primary">
              Next
            </button>
          </div>
        </div>

        <!-- Accreditation Section -->
        <div class="content-section" :class="{ active: currentStep === 'AccreditationMethod' }">
          <div class="card-body">
            <p class="card-text">
              Verify your accreditation status for <strong>mateen a. zahid</strong>.
              <a href="#">Learn more</a>.
            </p>
            <div class="d-flex justify-content-center align-items-center">
              <img src="https://img.icons8.com/ios-filled/200/000000/document.png" alt="Document Icon" />
            </div>
            <button class="btn btn-primary mb-3">Connect with Parallel Markets</button>
            <p>
              Alternatively,
              <a href="#" data-bs-toggle="modal" data-bs-target="#addLetterModal">
                upload an accreditation letter
              </a>
              to be reviewed by your sponsor.
            </p>
          </div>
          <div class="d-flex mt-3">
            <button type="button" @click="showStep('E_signatureMethod')" class="btn btn-secondary me-3">
              Previous
            </button>
            <button type="submit" @click="submitAccreditation" class="btn btn-primary">
              Next
            </button>
          </div>
        </div>
        <div class="content-section" :class="{ active: currentStep === 'FundingMethod' }">
          <div class="card-body">
            <h5 class="mb-3">4. Funding</h5>
            <p class="card-text mb-4">
              Please review the information below.
            </p>
            <div v-if="investmentForm.funding_method === 'achPayment'">

              <div v-if="user.stripe_customer_id === '' && user.stripe_account_id === ''">
                <div class="mb-3">
                  <label>Account Holder Name</label>
                  <input type="text" v-model="achForm.name" class="form-control" />
                </div>
                <div class="mb-3">
                  <label>Routing Number</label>
                  <input type="text" v-model="achForm.routing_number" class="form-control" />
                </div>
                <div class="mb-3">
                  <label>Account Number</label>
                  <input type="text" v-model="achForm.account_number" class="form-control" />
                </div>
                <div class="mb-3">
                  <label>Account Type</label>
                  <select v-model="achForm.account_type" class="form-control">
                    <option value="checking">Checking</option>
                    <option value="savings">Savings</option>
                  </select>
                </div>
              </div>
              <div v-if="user.stripe_customer_id !== '' && user.stripe_account_id !== ''">
                <div>
                  <p class="text-black mb-2">
                    You have already linked your bank account. Please select a funding method.
                  </p>
                </div>
              </div>
            </div>

            <div v-if="investmentForm.funding_method === 'wireTransfer'">
              <div v-if="fundingInfo">
                <div class="border rounded">
                  <div class="d-flex border-bottom p-3">
                    <div class="w-50 text-muted">Payment method</div>
                    <div class="w-50 text-end">{{ 'Wire transfer' }}</div>
                  </div>
                  <div class="d-flex border-bottom p-3">
                    <div class="w-50 text-muted">Bank name</div>
                    <div class="w-50 text-end">{{ fundingInfo.receiving_bank }}</div>
                  </div>
                  <div class="d-flex border-bottom p-3">
                    <div class="w-50 text-muted">Bank address</div>
                    <div class="w-50 text-end">{{ fundingInfo.bank_address }}</div>
                  </div>
                  <div class="d-flex border-bottom p-3">
                    <div class="w-50 text-muted">Routing number</div>
                    <div class="w-50 text-end">{{ fundingInfo.routing_no }}</div>
                  </div>
                  <div class="d-flex border-bottom p-3">
                    <div class="w-50 text-muted">Account number</div>
                    <div class="w-50 text-end">{{ fundingInfo.account_no }}</div>
                  </div>
                  <div class="d-flex border-bottom p-3">
                    <div class="w-50 text-muted">Account type</div>
                    <div class="w-50 text-end">{{ fundingInfo.account_type }}</div>
                  </div>
                  <div class="d-flex p-3 border-bottom">
                    <div class="w-50 text-muted">Beneficiary</div>
                    <div class="w-50 text-end">{{ fundingInfo.beneficiary_account_name }}
                    </div>
                  </div>
                  <div class="d-flex p-3">
                    <div class="w-50 text-muted">Beneficiary address</div>
                    <div class="w-50 text-end">{{ fundingInfo.beneficiary_address }}
                    </div>
                  </div>
                </div>
                <div class="text-end mt-4">
                  <button class="btn btn-primary" @click="downloadInvoice">Download</button>
                </div>
                <div class="mt-4 row">
                  <div class="col-6">
                    <label for="wireDate" class="form-label">
                      <h6>When will you initiate your wire transfer?</h6>
                    </label>
                  </div>
                  <div class="col-6">
                    <input type="date" id="wireDate" class="form-control"
                      v-model="investmentForm.initiate_wire_transfer_date" />
                  </div>
                </div>


              </div>
            </div>
          </div>

          <div class="d-flex mt-3">
            <button type="button" @click="showStep('E_signatureMethod')" class="btn btn-secondary me-3">
              Previous
            </button>
            <button type="submit" @click="submitfundingMethod" class="btn btn-primary">
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="side-bar col-md-4">
        <div id="card2" class="card p-3">
          <div class="image-box h-100">
            <div class="d-flex justify-content-center align-items-center bg-light" style="border: 1px solid #ccc">
              <img :src="offeringImage" style="max-height: 100%; max-width: 100%; object-fit: contain" />
            </div>
          </div>
          <h4 class="mt-3 mb-0">{{ offering.name }}</h4>
          <h6 class="mt-3 mb-0">Offering size</h6>
          <p style="font-size: larger" class="fw-bold mb-3 primary">
            ${{ offering.offering_size }}
          </p>
          <h6 class="mb-0">SEC type</h6>
          <p style="font-size: larger" class="fw-bold mb-3">
            {{ offering.deal.sec_type }}
          </p>
          <h6 class="mb-0">Investment type</h6>
          <p style="font-size: larger" class="fw-bold mb-3">
            {{ offering.classes[0].investment_type }}
          </p>
        </div>
      </div>
      <div v-if="showAddSignatureDocumentModal" class="vue-modal-overlay-s" @click.self="closeAddSignatureModal">
        <div class="vue-modal-s">
          <div class="vue-modal-header-s">
            <h5 class="vue-modal-header-title-s">Edit W-9 Form</h5>
            <button @click="closeAddSignatureModal" class="close-btn">×</button>
          </div>
          <div class="vue-modal-body-s">
            <EmbedSignDocument :token="token" :host="url" :css="customCss" :cssVars="cssVars"
              :darkModeDisabled="true" />
          </div>
        </div>
      </div>

      <!-- Vue-based Modal for Add Investor Profile -->
      <div v-if="showAddInvestorProfileModal" class="vue-modal-overlay" @click.self="closeAddInvestorProfileModal">
        <div class="vue-modal">
          <div class="vue-modal-header">
            <h5 class="vue-modal-header-title">Add Profile</h5>
            <button @click="closeAddInvestorProfileModal" class="close-btn">×</button>
          </div>
          <div class="vue-modal-body">
            <form>
              <div class="mb-3">
                <label for="profileType" class="form-label">
                  Profile Type <span class="text-danger">*</span>
                </label>
                <select id="profileType" name="profileType" class="form-select"
                  v-model="investorProfileForm.profile_type" required>
                  <option value="" disabled>Select a type</option>
                  <option value="Individual">Individual</option>
                  <option value="joint_tenancy">Joint Tenancy</option>
                  <option value="lcp">
                    LLC, corporation, partnership, trust, IRA or 401(k)
                  </option>
                </select>
              </div>

              <div v-if="investorProfileForm.profile_type === 'Individual'">
                <div class="mb-3">
                  <label for="profile_fname" class="form-label">
                    First name <span class="text-danger">*</span>
                  </label>
                  <input type="text" id="profile_fname" name="profile_fname" class="form-control"
                    v-model="investorProfileForm.profile_fname" required />
                </div>
                <div class="mb-3">
                  <label for="profile_mname" class="form-label">Middle name</label>
                  <input type="text" id="profile_mname" name="profile_mname" class="form-control"
                    v-model="investorProfileForm.profile_mname" />
                </div>
                <div class="mb-3">
                  <label for="profile_lname" class="form-label">
                    Last name <span class="text-danger">*</span>
                  </label>
                  <input type="text" id="profile_lname" name="profile_lname" class="form-control"
                    v-model="investorProfileForm.profile_lname" required />
                </div>
                <div class="mb-3">
                  <label for="province" class="form-label">Distribution method</label>
                  <select id="province" name="province" class="form-select"
                    v-model="investorProfileForm.profile_distribution" required>
                    <option value="ACH">ACH (recommended)</option>
                    <option value="Check">Check</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>

              <div v-if="investorProfileForm.profile_type === 'joint_tenancy'">
                <div class="mb-3">
                  <label for="profile_fname" class="form-label">
                    Investor 1 first name <span class="text-danger">*</span>
                  </label>
                  <input type="text" id="profile_fname" name="profile_fname" class="form-control"
                    v-model="investorProfileForm.profile_fname" required />
                </div>
                <div class="mb-3">
                  <label for="profile_mname" class="form-label">
                    Investor 1 middle name
                  </label>
                  <input type="text" id="profile_mname" name="profile_mname" class="form-control"
                    v-model="investorProfileForm.profile_mname" />
                </div>
                <div class="mb-3">
                  <label for="profile_lname" class="form-label">
                    Investor 1 last name <span class="text-danger">*</span>
                  </label>
                  <input type="text" id="profile_lname" name="profile_lname" class="form-control"
                    v-model="investorProfileForm.profile_lname" required />
                </div>
                <div class="mb-3">
                  <label for="profile_distribution" class="form-label">
                    Distribution method
                  </label>
                  <select id="profile_distribution" name="profile_distribution" class="form-select"
                    v-model="investorProfileForm.profile_distribution" required>
                    <option value="ACH">ACH (recommended)</option>
                    <option value="Check">Check</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
                <div class="mb-3">
                  <h6 class="fw-bold mb-0">Joint investor details</h6>
                  <p class="fw-light">
                    After you complete signing, Investor 2 will receive an email with e-sign
                    invitation
                  </p>
                </div>
                <div class="mb-3">
                  <label for="profile_fname2" class="form-label">
                    Investor 2 first name <span class="text-danger">*</span>
                  </label>
                  <input type="text" id="profile_fname2" name="profile_fname2" class="form-control"
                    v-model="investorProfileForm.profile_fname2" required />
                </div>
                <div class="mb-3">
                  <label for="profile_mname2" class="form-label">
                    Investor 2 middle name
                  </label>
                  <input type="text" id="profile_mname2" name="profile_mname2" class="form-control"
                    v-model="investorProfileForm.profile_mname2" />
                </div>
                <div class="mb-3">
                  <label for="profile_lname2" class="form-label">
                    Investor 2 last name <span class="text-danger">*</span>
                  </label>
                  <input type="text" id="profile_lname2" name="profile_lname2" class="form-control"
                    v-model="investorProfileForm.profile_lname2" required />
                </div>
                <div class="mb-3">
                  <label for="profile_email2" class="form-label">
                    Investor 2 email address <span class="text-danger">*</span>
                  </label>
                  <input type="text" id="profile_email2" name="profile_email2" class="form-control"
                    v-model="investorProfileForm.profile_email2" required />
                </div>
              </div>

              <div v-if="investorProfileForm.profile_type === 'lcp'">
                <div class="mb-3">
                  <label for="custodian" class="form-label">
                    Is this a custodian based IRA or 401(k)
                  </label>
                  <select id="custodian" name="custodian" class="form-select" v-model="investorProfileForm.custodian"
                    required>
                    <option value="" disabled>Select</option>
                    <option value="true">Yes</option>
                    <option value="false">No</option>
                  </select>
                </div>
                <div class="mb-3">
                  <p class="fw-light">
                    Choose 'yes' if a custodian needs to sign, and 'no' if only the investor
                    needs to sign. Choose 'no' if this is not an IRA or 401(k).
                  </p>
                </div>
                <div v-if="
                  investorProfileForm.profile_type === 'lcp' &&
                  investorProfileForm.custodian === 'false'
                " class="mb-3">
                  <label for="profile_entity_name" class="form-label">Entity Name</label>
                  <input type="text" id="profile_entity_name" name="profile_entity_name" class="form-control"
                    v-model="investorProfileForm.profile_entity_name" />
                </div>
                <div v-if="
                  investorProfileForm.profile_type === 'lcp' &&
                  investorProfileForm.custodian === 'true'
                " class="mb-3">
                  <label for="profile_ira_name" class="form-label">Legal IRA name</label>
                  <input type="text" id="profile_ira_name" name="profile_ira_name" class="form-control"
                    v-model="investorProfileForm.profile_ira_name" />
                </div>
                <div v-if="
                  investorProfileForm.profile_type === 'lcp' &&
                  investorProfileForm.custodian === 'true'
                " class="mb-3">
                  <label for="profile_ira_company" class="form-label">IRA Company</label>
                  <select id="profile_ira_company" name="profile_ira_company" class="form-control"
                    v-model="investorProfileForm.profile_ira_company">
                    <option value="" disabled>Select</option>
                    <option value="advanta">Advanta</option>
                    <option value="altoira">Alto IRA</option>
                    <option value="cama_plan">Cama Plan IRA</option>
                    <option value="community_national">Community National Bank</option>
                    <option value="digital_trust">Digital Trust</option>
                    <option value="direct_ira">Directed IRA (Directed Trust Company)</option>
                    <option value="equity_trust">Equity Trust Company</option>
                    <option value="forge_trust">Forge Trust Company</option>
                    <option value="horizon_trust">Horizon Trust Company</option>
                    <option value="inspira">Inspira</option>
                    <option value="ira_club">IRA Club</option>
                    <option value="irar_trust">IRAR Trust Company</option>
                    <option value="madison_trust">Madison Trust Company</option>
                    <option value="mainstar">Mainstar Trust Company</option>
                    <option value="mainstar_trust">Mainstar Trust Company</option>
                    <option value="midland_trust">Midland Trust IRA</option>
                    <option value="millennium_trust">Millennium Trust Company</option>
                    <option value="nuview">NuView Trust Company</option>
                    <option value="pacific_trust">Pacific Premier Trust</option>
                    <option value="provident_trust">Provident Trust Company</option>
                    <option value="quest_trust">Quest Trust Company</option>
                    <option value="specialized_trust">Specialized Trust Company</option>
                    <option value="entrust_group">The Entrust Group</option>
                    <option value="vantage_ira">Vantage IRA</option>
                    <option value="woodtrust_bank">WoodTrust Bank IRA</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div v-if="
                  investorProfileForm.profile_type === 'lcp' &&
                  (investorProfileForm.custodian === 'false' ||
                    investorProfileForm.custodian === 'true')
                " class="mb-3">
                  <label for="province" class="form-label">Distribution method</label>
                  <select id="province" name="province" class="form-select"
                    v-model="investorProfileForm.profile_distribution" required>
                    <option value="ACH">ACH (recommended)</option>
                    <option value="Check">Check</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>

              <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-primary me-2" @click.prevent="submitInvestorProfileForm">
                  Add Profile
                </button>
                <button class="btn btn-secondary" @click="closeAddInvestorProfileModal">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Vue-based Modal for Add Questionnaire -->
      <div v-if="showAddQuestionnaireModal" class="vue-modal-overlay" @click.self="closeAddQuestionnaireModal">
        <div class="vue-modal">
          <div class="vue-modal-header">
            <h5 class="vue-modal-header-title">Add Questionnaire</h5>
            <button @click="closeAddQuestionnaireModal" class="close-btn">×</button>
          </div>
          <div class="vue-modal-body">
            <div>
              <div class="divider">
                <div class="circle"></div>
                <span>Personal</span>
              </div>
              <div class="mb-3" style="background-color: aliceblue">
                <div class="p-2">
                  <h6 class="fw-bold">Disclaimer</h6>
                  <p class="text-black" style="font-size: small">
                    The information contained herein is being furnished in order to enable you
                    to determine whether a sale of Class A - Limited partners membership units
                    (“Units”) in Test LLC (the “Company”), may be made to the undersigned (the
                    “Investor”) without (i) registration of Units under the Securities Act of
                    1933, as amended, or any applicable state securities laws or (ii)
                    registration of the Company under the Investment Company Act of 1940, as
                    amended. This Questionnaire is not an offer to purchase or acceptance of an
                    offer to sell Units, but is, in fact, a response to a solicitation of
                    information to provide you a basis for determining the appropriateness of
                    any sale to the undersigned prospective Investor.
                  </p>
                </div>
              </div>
              <div class="mb-3">
                <label for="first_name" class="form-label">
                  First name <span class="text-danger">*</span>
                </label>
                <input type="text" id="first_name" name="first_name" class="form-control"
                  v-model="QuestionnaireForm.first_name" @blur="validateField('first_name')" required />
                <span class="text-danger" v-if="errors.first_name" v-text="errors.first_name"></span>
              </div>
              <div class="mb-3">
                <label for="last_name" class="form-label">Last name</label>
                <input type="text" id="last_name" name="last_name" class="form-control"
                  v-model="QuestionnaireForm.last_name" @blur="validateField('last_name')" required />
                <span class="text-danger" v-if="errors.last_name" v-text="errors.last_name"></span>
              </div>
              <div class="mb-3">
                <label for="telephone" class="form-label">
                  Telephone <span class="text-danger">*</span>
                </label>
                <input type="text" id="telephone" name="telephone" class="form-control"
                  v-model="QuestionnaireForm.telephone" @blur="validateField('telephone')" required />
                <span class="text-danger" v-if="errors.telephone" v-text="errors.telephone"></span>
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <select class="form-select" id="address" v-model="QuestionnaireForm.address" @change="changeAddress">
                  <option value="" disabled>Select a profile</option>
                  <option value="">Select</option>
                  <option v-for="address in addresses" :key="address.id" :value="address.id">
                    {{ formatAddress(address) }}
                  </option>
                  <option value="address">+ Add address</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="resident_of_usa" class="form-label">
                  How long have you been a resident of your state of residence?
                  <span class="text-danger">*</span>
                </label>
                <input type="text" id="resident_of_usa" name="resident_of_usa" class="form-control"
                  v-model="QuestionnaireForm.resident_of_usa" @blur="validateField('resident_of_usa')" required />
                <span class="text-danger" v-if="errors.resident_of_usa" v-text="errors.resident_of_usa"></span>
              </div>
              <div class="mb-3">
                <label for="birth_date" class="form-label">Birth date</label>
                <input type="date" id="birth_date" name="birth_date" class="form-control"
                  v-model="QuestionnaireForm.birth_date" required />
              </div>
              <div class="mb-3">
                <label for="tax_purpose" class="form-label">
                  Are you a U.S resident and/or citizen for tax purposes?
                  <span class="text-danger">*</span>
                </label>
                <div class="d-flex align-items-center">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tax_purpose" id="tax_purpose_yes" :value="true"
                      v-model="QuestionnaireForm.tax_purpose" />
                    <label class="form-check-label" for="tax_purpose_yes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tax_purpose" id="tax_purpose_no" :value="false"
                      v-model="QuestionnaireForm.tax_purpose" />
                    <label class="form-check-label" for="tax_purpose_no">No</label>
                  </div>
                </div>
                <p style="font-size: small" class="text-muted">
                  Note: if you select yes, the W9 form will be required
                </p>
              </div>
              <div class="mb-3">
                <label for="social_security_number" class="form-label">
                  Social security number <span class="text-danger">*</span>
                </label>
                <input type="text" id="social_security_number" name="social_security_number" class="form-control"
                  v-model="QuestionnaireForm.social_security_number" required />
              </div>
              <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-primary me-2" @click.prevent="submitQuestionnaireForm">
                  Add Questionnaire
                </button>
                <button class="btn btn-secondary" @click="closeAddQuestionnaireModal">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vue-based Modal for Add W-9 Form -->
      <div v-if="showAddW9FormModal" class="vue-modal-overlay" @click.self="closeAddW9FormModal">
        <div class="vue-modal">
          <div class="vue-modal-header">
            <h5 class="vue-modal-header-title">Edit W-9 Form</h5>
            <button @click="closeAddW9FormModal" class="close-btn">×</button>
          </div>
          <div class="vue-modal-body">
            <div>
              <div class="divider">
                <div class="circle"></div>
                <span>Personal</span>
              </div>
              <div class="mb-3">
                <label for="name" class="form-label">
                  First name <span class="text-danger">*</span>
                </label>
                <input type="text" id="name" name="name" class="form-control" v-model="W9Form.name" required />
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <select class="form-select" id="address" v-model="W9Form.address" @change="changeAddress">
                  <option value="" disabled>Select a profile</option>
                  <option value="">Select</option>
                  <option v-for="address in addresses" :key="address.id" :value="address.id">
                    {{ formatAddress(address) }}
                  </option>
                  <option value="address">+ Add address</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="social_security_number" class="form-label">
                  Social security number <span class="text-danger">*</span>
                </label>
                <input type="text" id="social_security_number" name="social_security_number" class="form-control"
                  v-model="W9Form.social_security_number" required />
              </div>
              <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-primary me-2" @click.prevent="submitW9Form">
                  Save
                </button>
                <button class="btn btn-secondary" @click="closeAddW9FormModal">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Vue-based Modal for Add Address -->
      <div v-if="showAddAddressModal" class="vue-modal-overlay" @click.self="closeAddAddressModal">
        <div class="vue-modal">
          <div class="vue-modal-header">
            <h5 class="vue-modal-header-title">Add Address</h5>
            <button @click="closeAddAddressModal" class="close-btn">×</button>
          </div>
          <div class="vue-modal-body">
            <form>
              <div class="mb-3">
                <label for="company_name" class="form-label">
                  Full name/Company name
                </label>
                <input type="text" id="company_name" name="company_name" class="form-control"
                  v-model="addressForm.company_name" @blur="validateFieldA('company_name')" required />
                <span class="text-danger" v-if="errors.company_name" v-text="errors.company_name"></span>
              </div>
              <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <select id="country" name="country" class="form-select" v-model="addressForm.country" required>
                  <option v-for="country in countries" :key="country.code" :value="country.name">
                    {{ country.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label for="address_line_1" class="form-label">
                  Street address line 1
                </label>
                <input type="text" id="address_line_1" name="address_line_1" class="form-control"
                  v-model="addressForm.address_line_1" required />
              </div>
              <div class="mb-3">
                <label for="address_line_2" class="form-label">
                  Street address line 2
                </label>
                <input type="text" id="address_line_2" name="address_line_2" class="form-control"
                  v-model="addressForm.address_line_2" />
              </div>
              <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" id="city" name="city" class="form-control" v-model="addressForm.city"
                  @blur="validateFieldA('city')" required />
                <span class="text-danger" v-if="errors.city" v-text="errors.city"></span>
              </div>
              <div v-if="addressForm.country !== 'United States'">
                <div class="mb-3">
                  <label for="province" class="form-label">Province</label>
                  <select id="province" name="province" class="form-select" v-model="addressForm.province"
                    @blur="validateFieldA('province')" required>
                    <option value="">Select province</option>
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
                  <span class="text-danger" v-if="errors.province" v-text="errors.province"></span>
                </div>
                <div class="mb-3">
                  <label for="postal_code" class="form-label">Postal Code</label>
                  <input type="text" id="postal_code" name="postal_code" class="form-control"
                    v-model="addressForm.postal_code" @blur="validateFieldA('postal_code')" required />
                  <span class="text-danger" v-if="errors.postal_code" v-text="errors.postal_code"></span>
                </div>
              </div>
              <div v-if="addressForm.country === 'United States'">
                <div class="mb-3">
                  <label for="state" class="form-label">State</label>
                  <select id="state" name="state" class="form-select" v-model="addressForm.state"
                    @blur="validateFieldA('state')" required>
                    <option value="">Select state</option>
                    <option value="ak">AK</option>
                    <option value="al">AL</option>
                    <option value="ar">AR</option>
                    <option value="as">AS</option>
                    <option value="az">AZ</option>
                    <option value="ca">CA</option>
                    <option value="co">CO</option>
                    <option value="ct">CT</option>
                    <option value="dc">DC</option>
                    <option value="de">DE</option>
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
                    <option value="md">MD</option>
                    <option value="me">ME</option>
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
                    <option value="ri">RI</option>
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
                  <span class="text-danger" v-if="errors.state" v-text="errors.state"></span>
                </div>
                <div class="mb-3">
                  <label for="zip_code" class="form-label">Zip Code</label>
                  <input type="text" id="zip_code" name="zip_code" class="form-control" v-model="addressForm.zip_code"
                    @blur="validateFieldA('zip_code')" required />
                  <span class="text-danger" v-if="errors.zip_code" v-text="errors.zip_code"></span>
                </div>
              </div>
              <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-secondary me-2" @click="closeAddAddressModal">
                  Cancel
                </button>
                <button type="button" class="btn btn-primary deal-save" @click.prevent="submitAddressForm">
                  Add Address
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  ref,
  computed,
  onMounted,
  watch,h,render
} from 'vue';
import { EmbedSignDocument } from '@documenso/embed-vue'
import Toast from '../Toast.vue';
// Props
const props = defineProps({
  offering: Object,
  investor: Object,
  investment: Object,
  fundingMethods: Array,
  user: Object,
  csrf: String,
});
const open = ref(false);
// State
const loading = ref(false);
const errors = ref({});
const showErrors = ref(false);
const investmentSuccess = ref(false);
const tab = ref('draw');
const isDrawing = ref(false);
const lastX = ref(0);
const lastY = ref(0);
const signatureDataUrl = ref(null);
const canvas = ref(null);
const context = ref(null);
const canvasRect = ref(null);
const uploadedSignature = ref(null);
const typedSignature = ref({});
const profiles = ref(props.investor?.investor_profiles || []);
const addresses = ref(props.offering?.questionnaire_addresses || []);
const questionnaires = ref(props.offering?.investment_questionnaires || []);
const countries = ref([]);
const fileName = ref('');
const investmentId = ref(null); // Add to existing state variables
const achForm = ref({
  name: '',
  routing_number: '',
  account_number: '',
  account_type: 'checking', // Default to 'checking' or leave empty
});
const investorProfileForm = ref({
  _token: props.csrf,
  profile_type: '',
  profile_fname: '',
  profile_mname: '',
  profile_lname: '',
  profile_distribution: '',
  profile_fname2: '',
  profile_mname2: '',
  profile_lname2: '',
  profile_email2: '',
  custodian: '',
  profile_entity_name: '',
  profile_ira_name: '',
  profile_ira_company: '',
});

const QuestionnaireForm = ref({
  _token: props.csrf,
  first_name: '',
  last_name: '',
  telephone: '',
  address: '',
  resident_of_usa: '',
  birth_date: '',
  tax_purpose: '',
  social_security_number: '',
});

const addressForm = ref({
  _token: props.csrf,
  company_name: '',
  country: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  zip_code: '',
  province: '',
  postal_code: '',
});

const investorForm = ref({
  _token: props.csrf,
  investor_profile_id: '',
  deal_class_id: props.offering?.classes[0]?.id || '',
});

const investmentForm = ref({
  investor_profile_id: '',
  deal_class_id: '',
  investment_amount: '',
  funding_method: '',
  questionnaire_id: '',
  w9_form: '',
  initiate_wire_transfer_date: '',
});

const W9Form = ref({
  _token: props.csrf,
  name: '',
  address: '',
  social_security_number: '',
});

const currentStep = ref('InvestorMethod');
const completedSteps = ref([]);
const steps = ref([{
  id: 'InvestorMethod',
  label: 'Investor',
  status: 'inactive'
},
{
  id: 'InvestmentMethod',
  label: 'Investment',
  status: 'inactive'
},
...(props.offering?.manageoffering?.questionnaire &&
  props.offering?.manageoffering?.require_w9 ?
  [{
    id: 'QuestionnaireW9FormMethod',
    label: 'Questionnaire',
    status: 'inactive',
  },] :
  []),
...(props.offering?.manageoffering?.questionnaire &&
  !props.offering?.manageoffering?.require_w9 ?
  [{
    id: 'QuestionnaireMethod',
    label: 'Questionnaire',
    status: 'inactive',
  },] :
  []),
...(props.offering?.manageoffering?.require_w9 &&
  !props.offering?.manageoffering?.questionnaire ?
  [{
    id: 'W9FormMethod',
    label: 'W9 Form',
    status: 'inactive'
  }] :
  []),
...(props.offering?.status !== 1 && props.offering?.status !== 2 ?
  [{
    id: 'E_signatureMethod',
    label: 'E-signature',
    status: 'inactive'
  }] :
  []),
...(props.offering?.manageoffering?.verify_investor &&
  props.offering?.status !== 1 &&
  props.offering?.status !== 2 ?
  [{
    id: 'AccreditationMethod',
    label: 'Accreditation',
    status: 'inactive',
  },] :
  []),
{
  id: 'FundingMethod',
  label: 'Funding',
  status: 'inactive'
},
]);

// Computed
const offeringImage = computed(() => {
  const image = props.offering?.assets?.[0]?.assetMedia?.[0]?.media_url;
  return image ?
    `/storage/${image}` :
    '/assets/images/download.svg';
});

// Methods
const formatAddress = (address) => {
  const parts = [];
  if (address.address_line_1) parts.push(address.address_line_1);
  if (address.address_line_2) parts.push(address.address_line_2);
  if (address.city) parts.push(address.city);
  if (address.state) parts.push(address.state);
  if (address.zip_code) parts.push(address.zip_code);
  if (address.province) parts.push(address.province);
  if (address.postal_code) parts.push(address.postal_code);
  return parts.join(', ') || 'No address available';
};

const changeProfile = () => {
  if (investorForm.value.investor_profile_id === 'view') {
    openAddInvestorProfileModal();
    investorForm.value.investor_profile_id = '';
  }
};

const openSignatureModal = () => {
  openAddSignatureModal();
  console.log('openSignatureModal');
};


const changeQuestionnaire = () => {
  if (investmentForm.value.questionnaire_id === 'view') {
    openAddQuestionnaireModal();
    investmentForm.value.questionnaire_id = '';
  }
};
const changeW9form = () => {
  if (investmentForm.value.w9_form === 'form') {
    openAddW9FormModal();
    investmentForm.value.w9_form = '';
  }
};
const changeAddress = () => {
  if (
    QuestionnaireForm.value.address === 'address' ||
    W9Form.value.address === 'address'
  ) {
    openAddAddressModal();
    QuestionnaireForm.value.address = '';
    W9Form.value.address = '';
  }
};

const handleFileUpload = (event) => {
  fileName.value = event.target.files[0]?.name || '';
};

const validateField = (field) => {
  if (!QuestionnaireForm.value[field]) {
    errors.value[field] = `${field.replace(/_/g, ' ').toUpperCase()} is required.`;
  } else {
    delete errors.value[field];
  }
};

const validateFieldA = (field) => {
  if (field === 'province' || field === 'postal_code') {
    if (
      addressForm.value.country !== 'United States' &&
      !addressForm.value[field]
    ) {
      errors.value[field] = `${field.replace(/_/g, ' ')} is required.`;
    } else {
      delete errors.value[field];
    }
  } else if (field === 'state' || field === 'zip_code') {
    if (
      addressForm.value.country === 'United States' &&
      !addressForm.value[field]
    ) {
      errors.value[field] = `${field.replace(/_/g, ' ')} is required.`;
    } else {
      delete errors.value[field];
    }
  } else if (!addressForm.value[field]) {
    errors.value[field] = `${field.replace(/_/g, ' ')} is required.`;
  } else {
    delete errors.value[field];
  }
};

const fetchCountries = async () => {
  try {
    const response = await fetch('https://restcountries.com/v3.1/all');
    const data = await response.json();
    countries.value = data
      .map((country) => ({
        name: country.name.common,
        code: country.cca2,
      }))
      .sort((a, b) => a.name.localeCompare(b.name));

    const topCountries = [{
      name: 'United States',
      code: 'US'
    },
    {
      name: 'Canada',
      code: 'CA'
    },
    {
      name: 'China',
      code: 'CN'
    },
    {
      name: 'Andorra',
      code: 'AD'
    },
    {
      name: 'United Arab Emirates',
      code: 'AE'
    },
    ];
    countries.value = [
      ...topCountries,
      ...countries.value.filter(
        (country) => !topCountries.some((tc) => tc.code === country.code)
      ),
    ];
  } catch (error) {
    console.error('Error fetching countries:', error);
  }
};


const submitInvestorProfileForm = async () => {
  loading.value = true;
  const url = window.urls.investorSave;
  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': props.csrf,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(investorProfileForm.value),
    });

    loading.value = false;

    if (response.status === 422) {
      const responseData = await response.json();
      errors.value = responseData.errors;
      return;
    }

    const responseData = await response.json();
    if (response.status === 200) {
      profiles.value = responseData.profiles;
      closeAddInvestorProfileModal();
    } else {
      console.log(responseData);
    }
  } catch (error) {
    console.error('Error:', error);
    loading.value = false;
  }
};

const submitQuestionnaireForm = async () => {
  loading.value = true;
  validateField('first_name');
  validateField('last_name');
  validateField('telephone');
  validateField('resident_of_usa');

  if (Object.keys(errors.value).length > 0) {
    loading.value = false;
    return;
  }

  const url = window.urls.questionnaire;

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': props.csrf,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(QuestionnaireForm.value),
    });

    loading.value = false;

    if (response.status === 422) {
      const responseData = await response.json();
      errors.value = responseData.errors;
      return;
    }

    const responseData = await response.json();
    if (response.status === 200) {
      questionnaires.value = responseData.questionnaires;
      closeAddQuestionnaireModal();
    } else {
      console.log(responseData);
    }
  } catch (error) {
    console.error('Error:', error);
    loading.value = false;
  }
};

const submitAddressForm = async () => {
  loading.value = true;
  for (let field in addressForm.value) {
    validateFieldA(field);
  }

  if (Object.keys(errors.value).length > 0) {
    loading.value = false;
    return;
  }

  const url = window.urls.questionnaireAddress;
  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': props.csrf,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(addressForm.value),
    });

    loading.value = false;

    if (response.status === 422) {
      const responseData = await response.json();
      errors.value = responseData.errors;
      return;
    }

    const responseData = await response.json();
    if (response.status === 200) {
      addresses.value = responseData.addresses;
      closeAddAddressModal();
    } else {
      console.log(responseData);
    }
  } catch (error) {
    console.error('Error:', error);
    loading.value = false;
  }
};

const submitW9Form = async () => {
  loading.value = true;
  const url = window.urls.questionnaireForm;

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': props.csrf,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(W9Form.value),
    });

    loading.value = false;

    if (response.status === 422) {
      const responseData = await response.json();
      errors.value = responseData.errors;
      return;
    }

    const responseData = await response.json();
    if (response.status === 200) {
      closeAddW9FormModal();
    } else {
      console.log(responseData);
    }
  } catch (error) {
    console.error('Error:', error);
    loading.value = false;
  }
};

const handleNextStep = (currentStepId) => {
  const index = steps.value.findIndex((step) => step.id === currentStepId);
  if (index === -1) return;

  completedSteps.value.push(currentStepId);
  steps.value[index].status = 'green';

  if (index < steps.value.length - 1) {
    const nextStep = steps.value[index + 1].id;
    if (nextStep === 'FundingMethod') {
      submitAndSaveInvestment(); // Create investment before funding
    }
    currentStep.value = nextStep;
    showStep(nextStep);
  } else {
    submitAndSaveInvestment(); // Fallback for last step
  }
};
const submitInvestorForm = () => {
  showErrors.value = true;
  if (
    !investorForm.value.investor_profile_id ||
    !investorForm.value.deal_class_id
  ) {
    return;
  }
  handleNextStep('InvestorMethod');
};

const submitInvestment = () => {
  if (
    !investmentForm.value.investment_amount ||
    !investmentForm.value.funding_method
  ) {
    return;
  }

  handleNextStep('InvestmentMethod');
};

const submit_e_sign_previous = () => {
  completedSteps.value.push(currentStep.value);
  const index = steps.value.findIndex(
    (step) => step.id === currentStep.value
  );
  currentStep.value = steps.value[index - 1].id;
  showStep(currentStep.value);
};

const submitQuestionnaire = () => {
  if (
    !investmentForm.value.questionnaire_id ||
    !investmentForm.value.w9_form
  ) {
    return;
  }

  handleNextStep('QuestionnaireW9FormMethod');
};

const submitQuestionnairew9 = () => {
  if (!investmentForm.value.w9_form) {
    return;
  }

  handleNextStep('W9FormMethod');
};

const submitQuestionnaires = () => {
  if (!investmentForm.value.questionnaire_id) {
    return;
  }

  handleNextStep('QuestionnaireMethod');
};

const submitEsignature = () => {
  handleNextStep('E_signatureMethod');
};

const submitAccreditation = () => {
  handleNextStep('AccreditationMethod');
};
const fundingInfo = computed(() => props.offering?.funding_info || {});

const submitfundingMethod = () => {
  if (investmentForm.value.funding_method === 'wireTransfer') {
    if (!investmentForm.value.initiate_wire_transfer_date) {
      return;
    }
  }

  completedSteps.value.push(currentStep.value);
  const index = steps.value.findIndex((step) => step.id === currentStep.value);

  if (index === steps.value.length - 1) {
    updateInvestment(); // Update the existing investment
  } else {
    currentStep.value = steps.value[index + 1].id;
    showStep(currentStep.value);
  }
};
const showStep = (step) => {
  currentStep.value = step;
  steps.value.forEach((item) => {
    if (item.id === step) {
      item.status = 'blue';
    } else if (completedSteps.value.includes(item.id)) {
      item.status = 'green';
    } else {
      item.status = 'inactive';
    }
  });
};

const moneyFormat = (el) => {
  let value = el.value;
  value = value.replace(/[^\d.]/g, '');
  const parts = value.split('.');
  if (parts.length > 2) {
    value = parts[0] + '.' + parts.slice(1).join('');
  }
  if (parts[1]) {
    parts[1] = parts[1].slice(0, 2);
    value = parts.join('.');
  }
  value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  el.value = value !== '' ? '$' + value : '$0';
};

const submitAndSaveInvestment = async () => {
  investmentForm.value.deal_class_id = investorForm.value.deal_class_id;
  investmentForm.value.investor_profile_id =
    investorForm.value.investor_profile_id;
  loading.value = true;

  const url = window.urls.investmentSave;

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': props.csrf,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(investmentForm.value),
    });

    loading.value = false;

    if (response.status === 422) {
      const responseData = await response.json();
      errors.value = responseData.errors;
      return;
    }

    const responseData = await response.json();
    if (response.status === 200) {
      investmentId.value = responseData.investment.id;
      const preservedFundingMethod = investmentForm.value.funding_method; // Preserve funding_method
      investmentForm.value = {
        investor_profile_id: '',
        deal_class_id: '',
        investment_amount: '',
        funding_method: preservedFundingMethod,
        questionnaire_id: '',
        w9_form: '',
        initiate_wire_transfer_date: '',
      };
    } else {
      console.log(responseData);
    }
  } catch (error) {
    console.error('Error:', error);
    loading.value = false;
  }
};

const updateInvestment = async () => {
  // debugger;
  if (investmentForm.value.funding_method === 'wireTransfer') {
    if (!investmentId.value || !investmentForm.value.initiate_wire_transfer_date) {
      errors.value.general = !investmentId.value
        ? 'Investment not created. Please try again.'
        : 'Wire transfer date is required.';
      return;
    }
  }
  if (investmentForm.value.funding_method === 'achPayment') {
    if (props.user?.stripe_customer_id === '' && props.user?.stripe_account_id === '') {
      submitACH();
      return;
    }
  }
  loading.value = true;
  const url = `${window.urls.investmentUpdate.replace(':id', investmentId.value)}`;

  try {
    const response = await fetch(url, {
      method: 'POST', // Changed from PUT to POST
      headers: {
        'X-CSRF-TOKEN': props.csrf,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        initiate_wire_transfer_date: investmentForm.value.initiate_wire_transfer_date,
      }),
    });

    loading.value = false;

    if (response.status === 422) {
      const responseData = await response.json();
      errors.value = responseData.errors;
      return;
    }

    const responseData = await response.json();
    if (response.status === 200) {
      investmentSuccess.value = true;
      investmentForm.value.initiate_wire_transfer_date = '';
      investmentSuccess.value = true;

    } else {
      console.log(responseData);
      errors.value.general = 'Failed to update investment';
    }
  } catch (error) {
    console.error('Error updating investment:', error);
    errors.value.general = 'An error occurred while updating the investment';
    loading.value = false;
  }
};
const submitACH = async () => {
  const url = `${window.urls.ach.replace(':id', investmentId.value)}`;
  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, // Include CSRF token
      },
      body: JSON.stringify({
        achForm: achForm.value
      }),
    });

    const responseData = await response.json(); // 👈 parse response body

    if (responseData.status === 'pending') {
      alert('Micro-deposits sent. Please check your bank account in 1-2 days.');
      window.location.href = responseData.data.onboarding_url;
    } else {
      console.log('ACH response:', responseData);
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

const downloadInvoice = () => {
  console.log('investmentId:', investmentId.value);

  if (!investmentId.value) {
    console.error('Investment ID not available');
    return;
  }

  // Prepare data to send to the backend
  const invoiceData = {
    investment_id: investmentId.value,
    receiving_bank: fundingInfo.value.receiving_bank,
    bank_address: fundingInfo.value.bank_address,
    routing_no: fundingInfo.value.routing_no,
    account_no: fundingInfo.value.account_no,
    account_type: fundingInfo.value.account_type,
    beneficiary_account_name: fundingInfo.value.beneficiary_account_name,
    beneficiary_address: fundingInfo.value.beneficiary_address,
  };

  // Send a POST request to the Laravel endpoint
  const url = window.urls.downloadInvoice;
  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, // Include CSRF token
    },
    body: JSON.stringify(invoiceData),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error('Failed to generate invoice');
      }
      return response.blob();
    })
    .then((blob) => {
      // Create a URL for the blob and trigger download
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `Investment_${investmentId.value}_Invoice.pdf`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);
    })
    .catch((error) => {
      console.error('Error downloading invoice:', error);
    });
};
const camelCaseToTitle = (str) => {
  return str
    .replace(/([A-Z])/g, ' $1')
    .replace(/^./, (s) => s.toUpperCase());
};
const showToast = (message, color = '#28a745') => {
  const container = document.createElement('div');
  document.body.appendChild(container);

  const vnode = h(Toast, {
    message,
    bgColor: color,
    onClose: () => {
      render(null, container);
      document.body.removeChild(container);
    },
  });

  render(vnode, container);
};

const showSuccessToast = (msg) => showToast(msg, '#28a745');
const showErrorToast = (msg) => showToast(msg, '#dc3545');

// Lifecycle
onMounted(() => {
  investmentForm.value.investment_amount = investmentForm.value.investment_amount ?
    '$' + investmentForm.value.investment_amount.replace(/\B(?=(\d{3})+(?!\d))/g, ',') :
    '$0';
  showStep(currentStep.value);
  fetchCountries();
});

// State for Vue-based modals
const showAddInvestorProfileModal = ref(false);
const showAddQuestionnaireModal = ref(false);
const showAddW9FormModal = ref(false);
const showAddAddressModal = ref(false);
const showAddSignatureDocumentModal = ref(false);
// Methods to open/close modals
const openAddInvestorProfileModal = () => {
  showAddInvestorProfileModal.value = true;
};
const openAddSignatureModal = () => {
  showAddSignatureDocumentModal.value = true;
};
const closeAddSignatureModal = () => {
  showAddSignatureDocumentModal.value = false;
};
const closeAddInvestorProfileModal = () => {
  showAddInvestorProfileModal.value = false;
};

const openAddQuestionnaireModal = () => {
  showAddQuestionnaireModal.value = true;
};
const closeAddQuestionnaireModal = () => {
  showAddQuestionnaireModal.value = false;
};

const openAddW9FormModal = () => {
  showAddW9FormModal.value = true;
};
const closeAddW9FormModal = () => {
  showAddW9FormModal.value = false;
};

const openAddAddressModal = () => {
  showAddAddressModal.value = true;
};
const closeAddAddressModal = () => {
  showAddAddressModal.value = false;
};

// Watcher to toggle body scroll when modals are open
watch(
  () => showAddAddressModal.value || showAddInvestorProfileModal.value || showAddQuestionnaireModal.value || showAddW9FormModal.value,
  (isModalOpen) => {
    document.body.style.overflow = isModalOpen ? 'hidden' : '';
  }
);


const token = ref(window.documensoToken);
const url = ref('https://isign.click')

const customCss = `
  .documenso-embed {
    width: 100%;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  .embed--Root {
    background-color: #F5F5F5;
    width: 100%;
    border-radius: 8px;
  }
`

const cssVars = {
  primary: '#0000FF',
  background: '#F5F5F5',
  radius: '8px',
}
</script>

<style scoped>
body {
  background-color: #f9f9f9;
  font-family: Arial, sans-serif;
}

.congrats-container {
  text-align: center;
  padding: 50px 20px;
  margin-top: 100px;
  background-color: #fff;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}

.check-icon {
  background-color: #007bff;
  color: white;
  border-radius: 50%;
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  font-size: 40px;
}

.sparkles {
  color: #c9d1f5;
  font-size: 20px;
}

.btn-view-offering {
  margin-top: 20px;
}

.step-circle {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: bold;
  cursor: not-allowed;
  transition: all 0.3s;
}

.step-circle.blue {
  background-color: #007bff;
  color: #fff;
}

.step-circle.green {
  background-color: #28a745;
  color: #fff;
  position: relative;
}

.step-circle.green::after {
  content: '✔';
  position: absolute;
  font-size: 14px;
  color: #fff;
}

.step-circle.inactive {
  background-color: rgb(239, 233, 233);
  color: #6c757d;
}

.step-text {
  font-size: 14px;
  margin-top: 6px;
}

.step-divider {
  width: 50px;
  height: 2px;
  background-color: #e9ecef;
  margin: 0 10px;
}

.step-divider.active {
  background-color: #007bff;
}

.divider_body {
  display: flex;
  white-space: nowrap;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  width: 80%;
  justify-content: space-between;
}

.content-section {
  display: none;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 5px;
  margin-top: 20px;
}

.content-section.active {
  display: block;
}

.items-center {
  justify-items: center;
}

#signature-pad {
  border: 2px solid #000;
  width: 100%;
  height: 300px;
  touch-action: none;
  display: block;
  margin: auto;
}

.row .header-box {
  white-space: nowrap;
  overflow: hidden;
}

.side-bar {
  margin-top: 1.3rem !important;
}

#card2 {
  position: sticky;
  top: 0;
  padding: 5px;
  box-shadow: -1px 0 5px rgba(0, 0, 0, 0.1);
}

.first-letter {
  width: 50px;
  height: 50px;
  font-size: 24px;
  font-weight: bold;
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

.deal-modal.center .modal-dialog {
  width: 50rem;
  max-width: 50%;
  height: 90%;
}

.deal-modal.center .modal-content {
  height: 100%;
  overflow-y: auto;
}

.deal-modal.center .modal-body {
  padding: 15px 15px 80px;
}

.card-hover:hover {
  box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
  border-color: #007bff;
}

.divider {
  position: relative;
  display: flex;
  align-items: center;
  margin: 3rem 2rem;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background-color: #ddd;
}

.circle {
  width: 20px;
  height: 20px;
  background-color: #007bff;
  border-radius: 50%;
  position: relative;
  z-index: 1;
}

.divider span {
  position: absolute;
  top: 18px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 13px;
  color: #007bff;
}

/* Vue-based modal styles */
.vue-modal-overlay {
  position: fixed;
  top: 0;
  /* height of your fixed header */
  right: 0;
  bottom: 0;
  left: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 1000;
  /* lower than header */
  display: flex;
  justify-content: flex-end;
  align-items: stretch;
}

.vue-modal {
  background: #fff;
  width: 50%;
  height: 100%;
  box-shadow: -4px 0 12px rgba(0, 0, 0, 0.15);
  border-top-left-radius: 10px;
  border-bottom-left-radius: 10px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  animation: slideInRight 0.3s ease-out;
}

.vue-modal-header {
  background: #4a90e2;
  color: white;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top-left-radius: 10px;
}

.vue-modal-header-title {
  font-size: 18px;
  font-weight: bold;
  margin: 0;
}

.close-btn {
  background: transparent;
  border: none;
  font-size: 24px;
  color: white;
  cursor: pointer;
}

.vue-modal-body {
  padding: 20px;
  background-color: #fdfdfd;
  flex: 1;
  overflow-y: auto;
}

/* Slide-in animation */
@keyframes slideInRight {
  from {
    transform: translateX(100%);
  }

  to {
    transform: translateX(0);
  }
}

.close-btn {
  background: transparent;
  border: none;
  font-size: 24px;
  color: #fff;
  cursor: pointer;
}

.vue-modal-body {
  padding: 20px;
  background: #f9f9f9;
  height: calc(100% - 80px);
  /* full height minus header */
  overflow-y: auto;
}

/* Optional: slide animation */
@keyframes slideInRight {
  from {
    transform: translateX(100%);
  }

  to {
    transform: translateX(0);
  }
}

.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

.vue-modal-overlay-s {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.vue-modal-s {
  background: #fff;
  width: 90%;
  max-width: 900px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.vue-modal-header-s {
  background-color: #f1f1f1;
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.vue-modal-header-title-s {
  margin: 0;
  font-size: 18px;
}

.close-btn {
  background: transparent;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: black;
}

.vue-modal-body-s {
  padding: 16px;
  background: #f9f9f9;
  max-height: 80vh;
  overflow-y: auto;
}

iframe {
  width: 100%;
  height: 100vh;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border: none;
}
</style>
