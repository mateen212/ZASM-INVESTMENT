    <div class="container mt-4">
        <h3 style="font-size: large;" class="mb-4 fw-bolder">Email privacy</h3>
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <span style="font-size: medium;" class="mr-3 fw-bold">
                    Show my investors' email addresses in this deal's investor list
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Show your investors' email addresses when other sponsors view the list of all investors in this deal.">
                            ?
                        </span>
                    <p style="font-size: small;" class="text-muted">Your investors will not appear in other sponsors' contact lists regardless of this setting.</p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="email_privacy_investor" :value="true" id="email_privacy_investor" x-model="personalSettingForm.email_privacy_investor">
                        <label class="form-check-label" for="email_privacy_investor">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="email_privacy_investor" :value="false" id="email_privacy_investor" x-model="personalSettingForm.email_privacy_investor">
                        <label class="form-check-label" for="email_privacy_investor">No</label>
                    </div>
                </div>
            </div>
        <h3 style="font-size: large;" class="mb-4 fw-bolder">Email interception</h3>
            <div class="d-flex align-items-center justify-content-between">
                <span style="font-size: medium;" class="mb-2 mr-3 fw-bold">
                    Require my review on emails sent to my investors in this deal
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="When a deal admin sends an email to all investors in this deal, your investors will not receive the email until you review and optionally edit it. Automated system emails will still be sent directly to your investors.">
                            ?
                        </span>
                    <p style="font-size: small;" class="text-muted">Reviewing the email will also allow you to change the email's reply-to address and sender name.</p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="email_interception_review" id="email_interception_review" :value="true" x-model="personalSettingForm.email_interception_review">
                        <label class="form-check-label" for="email_interception_review">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="email_interception_review" id="email_interception_review" :value="false"  checked x-model="personalSettingForm.email_interception_review">
                        <label class="form-check-label" for="email_interception_review">No</label>
                    </div>
                </div>
            </div>
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <span style="font-size: medium;" class="mr-3 mt-4 fw-bold">
                    Allow lead sponsor to send emails on my behalf
                    <p style="font-size: small;" class="text-muted">Edit your <a href="#">sending email address</a>to be used when sending on your behalf.</p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="email_interception_sponser" id="email_interception_sponser" :value="true" x-model="personalSettingForm.email_interception_sponser">
                        <label class="form-check-label" for="email_interception_sponser">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="email_interception_sponser" id="email_interception_sponser" :value="false"  checked x-model="personalSettingForm.email_interception_sponser">
                        <label class="form-check-label" for="email_interception_sponser">No</label>
                    </div>
                </div>
            </div>
        <h3 style="font-size: large;" class="mb-4 fw-bolder">Notification settings</h3>
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <span style="font-size: medium;" class="mr-3 fw-bold">
                    Copy selected sponsor on investment emails
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Send a copy of all the investment related emails when you are the selected sponsor">
                            ?
                        </span>
                    <p style="font-size: small;" class="text-muted">This will override lead sponsor's investment/distribution notification setting if you are the lead sponsor</p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <select id="notification_selected_sponser" class="form-select ms-3 custom-dropdown" x-model="personalSettingForm.notification_selected_sponser">
                            <option value="cc">CC</option>
                            <option value="bcc">BCC</option>
                            <option value="off" selected>Off</option>
                        </select>
                    </div>
                </div>
            </div>
        <h3 style="font-size: large;" class="mb-4 fw-bolder">Document visibility</h3>
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <span style="font-size: medium;" class="mr-3 fw-bold">
                    Display only my documents to my investors
                        <span class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="This setting will not affect the visibility of investor specific documents and offering documents.">
                            ?
                        </span>
                    <p style="font-size: small;" class="text-muted">Only documents uploaded by you will be visible to your investors.</p>
                </span>
                <div class="d-flex align-items-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="document_visibility_investors" id="document_visibility_investors" :value="true" x-model="personalSettingForm.document_visibility_investors">
                        <label class="form-check-label" for="document_visibility_investors">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="document_visibility_investors" id="document_visibility_investors" :value="false" checked x-model="personalSettingForm.document_visibility_investors">
                        <label class="form-check-label" for="document_visibility_investors">No</label>
                    </div>
                </div>
            </div>
        <button type="submit" class="btn btn-primary mt-3" @click="submitPersonalSettingForm()">Save</button>
    </div>